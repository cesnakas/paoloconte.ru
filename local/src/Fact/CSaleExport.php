<?php
/**
 * Created by PhpStorm.
 * User: TP
 * Date: 22.12.2016
 * Time: 17:08
 */

namespace Fact;


class CSaleExport  extends \CSaleExport
{
    /*
     * просто копия фукнкции, чтобы заработала кастомная getXmlBasketItems и renameDeliveries
     * */
    static function ExportOrders2Xml($arFilter = Array(), $nTopCount = 0, $currency = "", $crmMode = false, $time_limit = 0, $version = false, $arOptions = Array())
    {
        $lastOrderPrefix = '';
        $arCharSets = array();
        $lastDateUpdateOrders = array();
        $entityMarker = static::getEntityMarker();

        self::setVersionSchema($version);
        self::setCrmMode($crmMode);
        self::setCurrencySchema($currency);

        $count = false;
        if(IntVal($nTopCount) > 0)
            $count = Array("nTopCount" => $nTopCount);

        $end_time = self::getEndTime($time_limit);

        if(IntVal($time_limit) > 0)
        {
            if(self::$crmMode)
            {
                $lastOrderPrefix = md5(serialize($arFilter));
                if(!empty($_SESSION["BX_CML2_EXPORT"][$lastOrderPrefix]) && IntVal($nTopCount) > 0)
                    $count["nTopCount"] = $count["nTopCount"]+count($_SESSION["BX_CML2_EXPORT"][$lastOrderPrefix]);
            }
        }

        if(!self::$crmMode)
        {
            $arFilter = static::prepareFilter($arFilter);
            $timeUpdate = isset($arFilter[">=DATE_UPDATE"])? $arFilter[">=DATE_UPDATE"]:'';
            $lastDateUpdateOrders = static::getLastOrderExported($timeUpdate);
        }

        self::$arResultStat = array(
            "ORDERS" => 0,
            "CONTACTS" => 0,
            "COMPANIES" => 0,
        );

        $bExportFromCrm = self::isExportFromCRM($arOptions);

        $arStore = self::getCatalogStore();
        $arMeasures = self::getCatalogMeasure();
        self::setCatalogMeasure($arMeasures);
        $arAgent = self::getSaleExport();

        if (self::$crmMode)
        {
            self::setXmlEncoding("UTF-8");
            $arCharSets = self::getSite();
        }

        echo self::getXmlRootName();?>

        <<?=CSaleExport::getTagName("SALE_EXPORT_COM_INFORMATION")?> <?=self::getCmrXmlRootNameParams()?>><?

        $arOrder = array("DATE_UPDATE" => "ASC", "ID"=>"ASC");

        $arSelect = array(
            "ID", "LID", "PERSON_TYPE_ID", "PAYED", "DATE_PAYED", "EMP_PAYED_ID", "CANCELED", "DATE_CANCELED",
            "EMP_CANCELED_ID", "REASON_CANCELED", "STATUS_ID", "DATE_STATUS", "PAY_VOUCHER_NUM", "PAY_VOUCHER_DATE", "EMP_STATUS_ID",
            "PRICE_DELIVERY", "ALLOW_DELIVERY", "DATE_ALLOW_DELIVERY", "EMP_ALLOW_DELIVERY_ID", "PRICE", "CURRENCY", "DISCOUNT_VALUE",
            "SUM_PAID", "USER_ID", "PAY_SYSTEM_ID", "DELIVERY_ID", "DATE_INSERT", "DATE_INSERT_FORMAT", "DATE_UPDATE", "USER_DESCRIPTION",
            "ADDITIONAL_INFO",
            "COMMENTS", "TAX_VALUE", "STAT_GID", "RECURRING_ID", "ACCOUNT_NUMBER", "SUM_PAID", "DELIVERY_DOC_DATE", "DELIVERY_DOC_NUM", "TRACKING_NUMBER", "STORE_ID",
            "ID_1C", "VERSION",
            "USER.XML_ID", "USER.TIMESTAMP_X"
        );

        $bCrmModuleIncluded = false;
        if ($bExportFromCrm)
        {
            $arSelect[] = "UF_COMPANY_ID";
            $arSelect[] = "UF_CONTACT_ID";
            if (IsModuleInstalled("crm") && CModule::IncludeModule("crm"))
                $bCrmModuleIncluded = true;
        }

        $arFilter['RUNNING'] = 'N';

        $filter = array(
            'select' => $arSelect,
            'filter' => $arFilter,
            'order'  => $arOrder,
            'limit'  => $count["nTopCount"]
        );

        if (!empty($arOptions['RUNTIME']) && is_array($arOptions['RUNTIME']))
        {
            $filter['runtime'] = $arOptions['RUNTIME'];
        }

        $entity = static::getParentEntityTable();

        $dbOrderList = $entity::getList($filter);

        while($arOrder = $dbOrderList->Fetch())
        {
            if(!self::$crmMode && static::exportedLastExport($arOrder, $lastDateUpdateOrders))
            {
                continue;
            }

            static::$documentsToLog = array();
            $contentToLog = '';

            $order = static::load($arOrder['ID']);
            $arOrder['DATE_STATUS'] = $arOrder['DATE_STATUS']->toString();
            $arOrder['DATE_INSERT'] = $arOrder['DATE_INSERT']->toString();
            $arOrder['DATE_UPDATE'] = $arOrder['DATE_UPDATE']->toString();

            foreach($arOrder as $field=>$value)
            {
                if(self::isFormattedDateFields('Order', $field))
                {
                    $arOrder[$field] = self::getFormatDate($value);
                }
            }

            if (self::$crmMode)
            {
                if(self::getVersionSchema() > self::DEFAULT_VERSION && is_array($_SESSION["BX_CML2_EXPORT"][$lastOrderPrefix]) && in_array($arOrder["ID"], $_SESSION["BX_CML2_EXPORT"][$lastOrderPrefix]) && empty($arFilter["ID"]))
                    continue;
                ob_start();
            }

            self::$arResultStat["ORDERS"]++;

            $agentParams = (array_key_exists($arOrder["PERSON_TYPE_ID"], $arAgent) ? $arAgent[$arOrder["PERSON_TYPE_ID"]] : array() );

            $arResultPayment = self::getPayment($arOrder);
            $paySystems = $arResultPayment['paySystems'];
            $arPayment = $arResultPayment['payment'];

            $arResultShipment = self::getShipment($arOrder);
            $arShipment = $arResultShipment['shipment'];
            $delivery = $arResultShipment['deliveryServices'];

            self::setDeliveryAddress('');
            self::setSiteNameByOrder($arOrder);

            $arProp = self::prepareSaleProperty($arOrder, $bExportFromCrm, $bCrmModuleIncluded, $paySystems, $delivery, $locationStreetPropertyValue, $order);
            $agent = self::prepareSalePropertyRekv($order, $agentParams, $arProp, $locationStreetPropertyValue);

            $arOrderTax = CSaleExport::getOrderTax($order);
            $xmlResult['OrderTax'] = self::getXMLOrderTax($arOrderTax);
            self::setOrderSumTaxMoney(self::getOrderSumTaxMoney($arOrderTax));

            $xmlResult['Contragents'] = self::getXmlContragents($arOrder, $arProp, $agent, $bExportFromCrm ? array("EXPORT_FROM_CRM" => "Y") : array());            
            $xmlResult['OrderDiscount'] = self::getXmlOrderDiscount($arOrder);
            $xmlResult['SaleStoreList'] = $arStore;
            $xmlResult['ShipmentsStoreList'] = self::getShipmentsStoreList($order);
            // self::getXmlSaleStoreBasket($arOrder,$arStore);
            $basketItems = self::getXmlBasketItems('Order', $arOrder, array('ORDER_ID'=>$arOrder['ID']), array(), $arShipment);            

            $numberItems = array();
            foreach($basketItems['result'] as $basketItem)
            {
                $number = self::getNumberBasketPosition($basketItem["ID"]);

                if(in_array($number, $numberItems))
                {
                    $r = new \Bitrix\Sale\Result();
                    $r->addWarning(new \Bitrix\Main\Error(GetMessage("SALE_EXPORT_REASON_MARKED_BASKET_PROPERTY").'1C_Exchange:Order.export.basket.properties', 'SALE_EXPORT_REASON_MARKED_BASKET_PROPERTY'));
                    $entityMarker::addMarker($order, $order, $r);
                    $order->setField('MARKED','Y');
                    $order->setField('DATE_UPDATE',null);
                    $order->save();
                    break;
                }
                else
                {
                    $numberItems[] = $number;
                }
            }

            $xmlResult['BasketItems'] = $basketItems['outputXML'];
            $xmlResult['SaleProperties'] = self::getXmlSaleProperties($arOrder, $arShipment, $arPayment, $agent, $agentParams, $bExportFromCrm);
            $xmlResult['RekvProperties'] = self::getXmlRekvProperties($agent, $agentParams);

            if(self::getVersionSchema() >= self::CONTAINER_VERSION)
            {
                ob_start();
                echo '<'.CSaleExport::getTagName("SALE_EXPORT_CONTAINER").'>';
            }

            self::OutputXmlDocument('Order', $xmlResult, $arOrder);

            if(self::getVersionSchema() >= self::PARTIAL_VERSION)
            {
                self::OutputXmlDocumentsByType('Payment',$xmlResult, $arOrder, $arPayment, $order, $agentParams, $arProp, $locationStreetPropertyValue);
                self::OutputXmlDocumentsByType('Shipment',$xmlResult, $arOrder, $arShipment, $order, $agentParams, $arProp, $locationStreetPropertyValue);
                self::OutputXmlDocumentRemove('Shipment',$arOrder);
            }

            if(self::getVersionSchema() >= self::CONTAINER_VERSION)
            {
                echo '</'.CSaleExport::getTagName("SALE_EXPORT_CONTAINER").'>';
                $contentToLog = ob_get_contents();
                ob_end_clean();
                echo $contentToLog;
            }

            if (self::$crmMode)
            {
                $c = ob_get_clean();
                $c = CharsetConverter::ConvertCharset($c, $arCharSets[$arOrder["LID"]], "utf-8");
                echo $c;
                $_SESSION["BX_CML2_EXPORT"][$lastOrderPrefix][] = $arOrder["ID"];
            }
            else
            {
                static::saveExportParams($arOrder);
            }

            ksort(static::$documentsToLog);

            foreach (static::$documentsToLog as $entityTypeId=>$documentsToLog)
            {
                foreach ($documentsToLog as $documentToLog)
                {
                    $fieldToLog = $documentToLog;
                    $fieldToLog['ENTITY_TYPE_ID'] = $entityTypeId;
                    if(self::getVersionSchema() >= self::CONTAINER_VERSION)
                    {
                        if($entityTypeId == \Bitrix\Sale\Exchange\EntityType::ORDER )
                            $fieldToLog['MESSAGE'] = $contentToLog;
                    }
                    static::log($fieldToLog);
                }
            }

            if(self::checkTimeIsOver($time_limit, $end_time))
            {
                break;
            }
        }
        ?>

        </<?=CSaleExport::getTagName("SALE_EXPORT_COM_INFORMATION")?>><?

        return self::$arResultStat;
    }


    /*
     * для профилей доставки достает own_name
     * наприме, вместо "СДЭК (Пункт самовывоза. Оплата онлайн)" будет "Пункт самовывоза. Оплата онлайн"
     * */
    function renameDeliveries($arResultShipment)
    {
        $arDeliveries = \Bitrix\Sale\Delivery\Services\Manager::getActiveList();
        foreach ($arResultShipment as $key => $shipment) {
            if (!empty($arDeliveries[$shipment['DELIVERY_ID']]['NAME'])) {
                $arResultShipment[$key]['DELIVERY_NAME'] = $arDeliveries[$shipment['DELIVERY_ID']]['NAME'];
            }
        }
        unset($arDeliveries);
        return $arResultShipment;
    }


    /*
     * в свойствах корзины вывод вида: СвойствоКорзины#RAZMER заменили на "Размер"
     * */
    static function getXmlBasketItems($type, $arOrder, $arFilter, $arSelect=array(), $arShipment=array())
    {
        $bufer = '';
        $result = array();
        ob_start();
        ?><<?=CSaleExport::getTagName("SALE_EXPORT_ITEMS")?>><?

        $select = array("ID", "NOTES", "PRODUCT_XML_ID", "CATALOG_XML_ID", "NAME", "PRICE", "QUANTITY", "DISCOUNT_PRICE", "VAT_RATE", "MEASURE_CODE", "SET_PARENT_ID", "TYPE");
        if(count($arSelect)>0)
            $select = array_merge($arSelect,$select);
        $dbBasket = \Bitrix\Sale\Internals\BasketTable::getList(array(
            'select' => $select,
            'filter' => $arFilter,
            'order' => array("NAME" => "ASC")
        ));

        $basketSum = 0;
        $priceType = "";
        $bVat = false;
        $vatRate = 0;
        $vatSum = 0;
        while ($arBasket = $dbBasket->fetch())
        {
            if(strval($arBasket['TYPE'])!='' && $arBasket['TYPE']== \Bitrix\Sale\BasketItemBase::TYPE_SET)
                continue;

            $result[] = $arBasket;

            if(strlen($priceType) <= 0)
                $priceType = $arBasket["NOTES"];
            ?>
            <<?=CSaleExport::getTagName("SALE_EXPORT_ITEM")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_ID")?>><?=htmlspecialcharsbx($arBasket["PRODUCT_XML_ID"])?></<?=CSaleExport::getTagName("SALE_EXPORT_ID")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_CATALOG_ID")?>><?=htmlspecialcharsbx($arBasket["CATALOG_XML_ID"])?></<?=CSaleExport::getTagName("SALE_EXPORT_CATALOG_ID")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>><?=htmlspecialcharsbx($arBasket["NAME"])?></<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>>
            <?
            if(self::getVersionSchema() > self::DEFAULT_VERSION)
            {
                if(IntVal($arBasket["MEASURE_CODE"]) <= 0)
                    $arBasket["MEASURE_CODE"] = 796;
                ?>
                <<?=CSaleExport::getTagName("SALE_EXPORT_UNIT")?>>
                <<?=CSaleExport::getTagName("SALE_EXPORT_CODE")?>><?=$arBasket["MEASURE_CODE"]?></<?=CSaleExport::getTagName("SALE_EXPORT_CODE")?>>
                <<?=CSaleExport::getTagName("SALE_EXPORT_FULL_NAME_UNIT")?>><?=htmlspecialcharsbx(self::$measures[$arBasket["MEASURE_CODE"]])?></<?=CSaleExport::getTagName("SALE_EXPORT_FULL_NAME_UNIT")?>>
                </<?=CSaleExport::getTagName("SALE_EXPORT_UNIT")?>>
                <<?=CSaleExport::getTagName("SALE_EXPORT_KOEF")?>>1</<?=CSaleExport::getTagName("SALE_EXPORT_KOEF")?>>
                <?
            }
            else
            {
                ?>
                <<?=CSaleExport::getTagName("SALE_EXPORT_BASE_UNIT")?> <?=CSaleExport::getTagName("SALE_EXPORT_CODE")?>="796" <?=CSaleExport::getTagName("SALE_EXPORT_FULL_NAME_UNIT")?>="<?=CSaleExport::getTagName("SALE_EXPORT_SHTUKA")?>" <?=CSaleExport::getTagName("SALE_EXPORT_INTERNATIONAL_ABR")?>="<?=CSaleExport::getTagName("SALE_EXPORT_RCE")?>"><?=CSaleExport::getTagName("SALE_EXPORT_SHT")?></<?=CSaleExport::getTagName("SALE_EXPORT_BASE_UNIT")?>>
                <?
            }
            if(DoubleVal($arBasket["DISCOUNT_PRICE"]) > 0)
            {
                ?>
                <<?=CSaleExport::getTagName("SALE_EXPORT_DISCOUNTS")?>>
                <<?=CSaleExport::getTagName("SALE_EXPORT_DISCOUNT")?>>
                <<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>><?=CSaleExport::getTagName("SALE_EXPORT_ITEM_DISCOUNT")?></<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>>
                <<?=CSaleExport::getTagName("SALE_EXPORT_AMOUNT")?>><?=$arBasket["DISCOUNT_PRICE"]?></<?=CSaleExport::getTagName("SALE_EXPORT_AMOUNT")?>>
                <<?=CSaleExport::getTagName("SALE_EXPORT_IN_PRICE")?>>true</<?=CSaleExport::getTagName("SALE_EXPORT_IN_PRICE")?>>
                </<?=CSaleExport::getTagName("SALE_EXPORT_DISCOUNT")?>>
                </<?=CSaleExport::getTagName("SALE_EXPORT_DISCOUNTS")?>>
                <?
            }
            ?>
            <?if(self::getVersionSchema() >= self::PARTIAL_VERSION && $type == 'Shipment')
        {?>
            <<?=CSaleExport::getTagName("SALE_EXPORT_PRICE_PER_ITEM")?>><?=$arBasket["PRICE"]?></<?=CSaleExport::getTagName("SALE_EXPORT_PRICE_PER_ITEM")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_QUANTITY")?>><?=$arBasket["SALE_INTERNALS_BASKET_SHIPMENT_ITEM_QUANTITY"]?></<?=CSaleExport::getTagName("SALE_EXPORT_QUANTITY")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_AMOUNT")?>><?=$arBasket["PRICE"]*$arBasket["SALE_INTERNALS_BASKET_SHIPMENT_ITEM_QUANTITY"]?></<?=CSaleExport::getTagName("SALE_EXPORT_AMOUNT")?>>
        <?}
        else{
            ?>
            <<?=CSaleExport::getTagName("SALE_EXPORT_PRICE_PER_ITEM")?>><?=$arBasket["PRICE"]?></<?=CSaleExport::getTagName("SALE_EXPORT_PRICE_PER_ITEM")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_QUANTITY")?>><?=$arBasket["QUANTITY"]?></<?=CSaleExport::getTagName("SALE_EXPORT_QUANTITY")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_AMOUNT")?>><?=$arBasket["PRICE"]*$arBasket["QUANTITY"]?></<?=CSaleExport::getTagName("SALE_EXPORT_AMOUNT")?>>
        <?}?>
            <<?=CSaleExport::getTagName("SALE_EXPORT_PROPERTIES_VALUES")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>><?=CSaleExport::getTagName("SALE_EXPORT_TYPE_NOMENKLATURA")?></<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=CSaleExport::getTagName("SALE_EXPORT_ITEM")?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
            </<?=CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>><?=CSaleExport::getTagName("SALE_EXPORT_TYPE_OF_NOMENKLATURA")?></<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=CSaleExport::getTagName("SALE_EXPORT_ITEM")?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
            </<?=CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE")?>>

            <?
            $number = self::getNumberBasketPosition($arBasket["ID"]);
            ?>
            <<?=CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>><?=CSaleExport::getTagName("SALE_EXPORT_BASKET_NUMBER")?></<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=$number?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
            </<?=CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE")?>>
            <?
            $dbProp = \CSaleBasket::GetPropsList(Array("SORT" => "ASC", "ID" => "ASC"), Array("BASKET_ID" => $arBasket["ID"]), false, false, array("NAME", "VALUE", "CODE"));
            while($arPropBasket = $dbProp->Fetch())
            {
                ?>
                <<?=CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE")?>>
                <?// customization?>
                <<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>><?=htmlspecialcharsbx($arPropBasket["NAME"])?></<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>>
                <?/*?>
                <<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>><?=CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE_BASKET")?>#<?=($arPropBasket["CODE"] != "" ? $arPropBasket["CODE"]:htmlspecialcharsbx($arPropBasket["NAME"]))?></<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>>
                <?*/?>
                <?// end customization?>
                <<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($arPropBasket["VALUE"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
                </<?=CSaleExport::getTagName("SALE_EXPORT_PROPERTY_VALUE")?>>
                <?
            }
            ?>
            </<?=CSaleExport::getTagName("SALE_EXPORT_PROPERTIES_VALUES")?>>
            <?if(DoubleVal($arBasket["VAT_RATE"]) > 0)
        {
            $bVat = true;
            $vatRate = DoubleVal($arBasket["VAT_RATE"]);
            $basketVatSum = (($arBasket["PRICE"] / ($arBasket["VAT_RATE"]+1)) * $arBasket["VAT_RATE"]);
            $vatSum += roundEx($basketVatSum * $arBasket["QUANTITY"], 2);
            ?>
            <<?=CSaleExport::getTagName("SALE_EXPORT_TAX_RATES")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_TAX_RATE")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>><?=CSaleExport::getTagName("SALE_EXPORT_VAT")?></<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_RATE")?>><?=$arBasket["VAT_RATE"] * 100?></<?=CSaleExport::getTagName("SALE_EXPORT_RATE")?>>
            </<?=CSaleExport::getTagName("SALE_EXPORT_TAX_RATE")?>>
            </<?=CSaleExport::getTagName("SALE_EXPORT_TAX_RATES")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_TAXES")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_TAX")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>><?=CSaleExport::getTagName("SALE_EXPORT_VAT")?></<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_IN_PRICE")?>>true</<?=CSaleExport::getTagName("SALE_EXPORT_IN_PRICE")?>>
            <<?=CSaleExport::getTagName("SALE_EXPORT_AMOUNT")?>><?=roundEx($basketVatSum, 2)?></<?=CSaleExport::getTagName("SALE_EXPORT_AMOUNT")?>>
            </<?=CSaleExport::getTagName("SALE_EXPORT_TAX")?>>
            </<?=CSaleExport::getTagName("SALE_EXPORT_TAXES")?>>
            <?
        }
            ?>
            <?//=self::getXmlSaleStoreBasket($arOrder,$arStore)?>
            </<?=CSaleExport::getTagName("SALE_EXPORT_ITEM")?>>
            <?
            $basketSum += $arBasket["PRICE"]*$arBasket["QUANTITY"];
        }

        if(self::getVersionSchema() >= self::PARTIAL_VERSION)
        {
            if(count($arShipment)>0)
            {
                foreach($arShipment as $shipment)
                {
                    self::getOrderDeliveryItem($shipment, $bVat, $vatRate, $vatSum);
                }
            }
        }
        else
            self::getOrderDeliveryItem($arOrder, $bVat, $vatRate, $vatSum);

        ?>
        </<?=CSaleExport::getTagName("SALE_EXPORT_ITEMS")?>><?

        $bufer = ob_get_clean();
        return array('outputXML'=>$bufer,'result'=>$result);
    }
    
    
    	static function getXmlContragents($arOrder = array(), $arProp = array(), $agent = array(), $arOptions = array())
	{
		ob_start();
		self::ExportContragents($arOrder, $arProp, $agent, $arOptions);
		$ec_bufer = ob_get_clean();
		return $ec_bufer;
	}
	static function ExportContragents($arOrder = array(), $arProp = array(), $agent = array(), $arOptions = array())
	{
		$bExportFromCrm = (isset($arOptions["EXPORT_FROM_CRM"]) && $arOptions["EXPORT_FROM_CRM"] === "Y");
		?>
		<<?=CSaleExport::getTagName("SALE_EXPORT_CONTRAGENTS")?>>
			<<?=CSaleExport::getTagName("SALE_EXPORT_CONTRAGENT")?>>
		<?
		if ($bExportFromCrm):
			$xmlId = htmlspecialcharsbx(substr($arProp["CRM"]["CLIENT_ID"]."#".$arProp["CRM"]["CLIENT"]["LOGIN"]."#".$arProp["CRM"]["CLIENT"]["LAST_NAME"]." ".$arProp["CRM"]["CLIENT"]["NAME"]." ".$arProp["CRM"]["CLIENT"]["SECOND_NAME"], 0, 40));
		else:
			$xmlId = static::getUserXmlId($arOrder, $arProp);
		endif; ?>
                <<?=CSaleExport::getTagName("SALE_EXPORT_ID")?>><?=$xmlId?></<?=CSaleExport::getTagName("SALE_EXPORT_ID")?>>

				<<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>><?=htmlspecialcharsbx($agent["AGENT_NAME"])?></<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>>
				<?
				self::setDeliveryAddress($agent["ADDRESS_FULL"]);

				//region address
				$address = "";
				if(strlen($agent["ADDRESS_FULL"])>0)
				{
				    $address .= "<".CSaleExport::getTagName("SALE_EXPORT_PRESENTATION").">".htmlspecialcharsbx($agent["ADDRESS_FULL"])."</".CSaleExport::getTagName("SALE_EXPORT_PRESENTATION").">";
				}
				else
				{
					$address .= "<".CSaleExport::getTagName("SALE_EXPORT_PRESENTATION")."></".CSaleExport::getTagName("SALE_EXPORT_PRESENTATION").">";
				}
				if(strlen($agent["INDEX"])>0)
				{
					$address .= "<".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">
								<".CSaleExport::getTagName("SALE_EXPORT_TYPE").">".CSaleExport::getTagName("SALE_EXPORT_POST_CODE")."</".CSaleExport::getTagName("SALE_EXPORT_TYPE").">
								<".CSaleExport::getTagName("SALE_EXPORT_VALUE").">".htmlspecialcharsbx($agent["INDEX"])."</".CSaleExport::getTagName("SALE_EXPORT_VALUE").">
							</".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">";
				}
				if(strlen($agent["COUNTRY"])>0)
				{
					$address .= "<".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">
									<".CSaleExport::getTagName("SALE_EXPORT_TYPE").">".CSaleExport::getTagName("SALE_EXPORT_COUNTRY")."</".CSaleExport::getTagName("SALE_EXPORT_TYPE").">
									<".CSaleExport::getTagName("SALE_EXPORT_VALUE").">".htmlspecialcharsbx($agent["COUNTRY"])."</".CSaleExport::getTagName("SALE_EXPORT_VALUE").">
								</".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">";
				}
				if(strlen($agent["REGION"])>0)
				{
					$address .= "<".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">
								<".CSaleExport::getTagName("SALE_EXPORT_TYPE").">".CSaleExport::getTagName("SALE_EXPORT_REGION")."</".CSaleExport::getTagName("SALE_EXPORT_TYPE").">
								<".CSaleExport::getTagName("SALE_EXPORT_VALUE").">".htmlspecialcharsbx($agent["REGION"])."</".CSaleExport::getTagName("SALE_EXPORT_VALUE").">
							</".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">";
				}
				if(strlen($agent["STATE"])>0)
				{
					$address .= "<".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">
								<".CSaleExport::getTagName("SALE_EXPORT_TYPE").">".CSaleExport::getTagName("SALE_EXPORT_STATE")."</".CSaleExport::getTagName("SALE_EXPORT_TYPE").">
								<".CSaleExport::getTagName("SALE_EXPORT_VALUE").">".htmlspecialcharsbx($agent["STATE"])."</".CSaleExport::getTagName("SALE_EXPORT_VALUE").">
							</".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">";
				}
				if(strlen($agent["TOWN"])>0)
				{
					$address .= "<".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">
								<".CSaleExport::getTagName("SALE_EXPORT_TYPE").">".CSaleExport::getTagName("SALE_EXPORT_SMALL_CITY")."</".CSaleExport::getTagName("SALE_EXPORT_TYPE").">
								<".CSaleExport::getTagName("SALE_EXPORT_VALUE").">".htmlspecialcharsbx($agent["TOWN"])."</".CSaleExport::getTagName("SALE_EXPORT_VALUE").">
							</".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">";
				}
				if(strlen($agent["CITY"])>0)
				{
					$address .= "<".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">
								<".CSaleExport::getTagName("SALE_EXPORT_TYPE").">".CSaleExport::getTagName("SALE_EXPORT_CITY")."</".CSaleExport::getTagName("SALE_EXPORT_TYPE").">
								<".CSaleExport::getTagName("SALE_EXPORT_VALUE").">".htmlspecialcharsbx($agent["CITY"])."</".CSaleExport::getTagName("SALE_EXPORT_VALUE").">
							</".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">";
				}
				if(strlen($agent["STREET"])>0)
				{
					$address .= "<".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">
								<".CSaleExport::getTagName("SALE_EXPORT_TYPE").">".CSaleExport::getTagName("SALE_EXPORT_STREET")."</".CSaleExport::getTagName("SALE_EXPORT_TYPE").">
								<".CSaleExport::getTagName("SALE_EXPORT_VALUE").">".htmlspecialcharsbx($agent["STREET"])."</".CSaleExport::getTagName("SALE_EXPORT_VALUE").">
							</".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">";
				}
				if(strlen($agent["HOUSE"])>0)
				{
					$address .= "<".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">
								<".CSaleExport::getTagName("SALE_EXPORT_TYPE").">".CSaleExport::getTagName("SALE_EXPORT_HOUSE")."</".CSaleExport::getTagName("SALE_EXPORT_TYPE").">
								<".CSaleExport::getTagName("SALE_EXPORT_VALUE").">".htmlspecialcharsbx($agent["HOUSE"])."</".CSaleExport::getTagName("SALE_EXPORT_VALUE").">
							</".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">";
				}
				if(strlen($agent["BUILDING"])>0)
				{
					$address .= "<".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">
								<".CSaleExport::getTagName("SALE_EXPORT_TYPE").">".CSaleExport::getTagName("SALE_EXPORT_BUILDING")."</".CSaleExport::getTagName("SALE_EXPORT_TYPE").">
								<".CSaleExport::getTagName("SALE_EXPORT_VALUE").">".htmlspecialcharsbx($agent["BUILDING"])."</".CSaleExport::getTagName("SALE_EXPORT_VALUE").">
							</".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">";
				}
				if(strlen($agent["FLAT"])>0)
				{
					$address .= "<".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">
								<".CSaleExport::getTagName("SALE_EXPORT_TYPE").">".CSaleExport::getTagName("SALE_EXPORT_FLAT")."</".CSaleExport::getTagName("SALE_EXPORT_TYPE").">
								<".CSaleExport::getTagName("SALE_EXPORT_VALUE").">".htmlspecialcharsbx($agent["FLAT"])."</".CSaleExport::getTagName("SALE_EXPORT_VALUE").">
							</".CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD").">";
				}
				//endregion

				if($agent["IS_FIZ"]=="Y")
				{
					self::$arResultStat["CONTACTS"]++;
					?>
					<<?=CSaleExport::getTagName("SALE_EXPORT_FULL_NAME")?>><?=htmlspecialcharsbx($agent["FULL_NAME"])?></<?=CSaleExport::getTagName("SALE_EXPORT_FULL_NAME")?>>
					<?
					if(strlen($agent["SURNAME"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_SURNAME")?>><?=htmlspecialcharsbx($agent["SURNAME"])?></<?=CSaleExport::getTagName("SALE_EXPORT_SURNAME")?>><?
					}
					if(strlen($agent["NAME"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_NAME")?>><?=htmlspecialcharsbx($agent["NAME"])?></<?=CSaleExport::getTagName("SALE_EXPORT_NAME")?>><?
					}
					if(strlen($agent["SECOND_NAME"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_MIDDLE_NAME")?>><?=htmlspecialcharsbx($agent["SECOND_NAME"])?></<?=CSaleExport::getTagName("SALE_EXPORT_MIDDLE_NAME")?>><?
					}
					if(strlen($agent["BIRTHDAY"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_BIRTHDAY")?>><?=htmlspecialcharsbx($agent["BIRTHDAY"])?></<?=CSaleExport::getTagName("SALE_EXPORT_BIRTHDAY")?>><?
					}
					if(strlen($agent["MALE"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_SEX")?>><?=htmlspecialcharsbx($agent["MALE"])?></<?=CSaleExport::getTagName("SALE_EXPORT_SEX")?>><?
					}
                    //---Добавляем поле с картой лояльности
                    if ($arProp["PROPERTY"][ORDER_PROP_CLOUD_LOYALTY_SCORES])
                    {
                        ?><БонусныеБаллы><?=intval($arProp["PROPERTY"][ORDER_PROP_CLOUD_LOYALTY_SCORES])?></БонусныеБаллы><?
                    }
                    
					if(strlen($agent["INN"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_INN")?>><?=htmlspecialcharsbx($agent["INN"])?></<?=CSaleExport::getTagName("SALE_EXPORT_INN")?>><?
					}
					if(strlen($agent["KPP"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_KPP")?>><?=htmlspecialcharsbx($agent["KPP"])?></<?=CSaleExport::getTagName("SALE_EXPORT_KPP")?>><?
					}
					if(strlen($address)>0)
                    {
						?><<?=CSaleExport::getTagName("SALE_EXPORT_REGISTRATION_ADDRESS")?>>
						<?=$address?>
                        </<?=CSaleExport::getTagName("SALE_EXPORT_REGISTRATION_ADDRESS")?>>
						<?
                    }
				}
				else
				{
					self::$arResultStat["COMPANIES"]++;
					?>
					<<?=CSaleExport::getTagName("SALE_EXPORT_OFICIAL_NAME")?>><?=htmlspecialcharsbx($agent["FULL_NAME"])?></<?=CSaleExport::getTagName("SALE_EXPORT_OFICIAL_NAME")?>>
					<?
					if(strlen($address)>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_UR_ADDRESS")?>>
						<?=$address?>
						</<?=CSaleExport::getTagName("SALE_EXPORT_UR_ADDRESS")?>><?
					}
					if(strlen($agent["INN"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_INN")?>><?=htmlspecialcharsbx($agent["INN"])?></<?=CSaleExport::getTagName("SALE_EXPORT_INN")?>><?
					}
					if(strlen($agent["KPP"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_KPP")?>><?=htmlspecialcharsbx($agent["KPP"])?></<?=CSaleExport::getTagName("SALE_EXPORT_KPP")?>><?
					}
					if(strlen($agent["EGRPO"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_EGRPO")?>><?=htmlspecialcharsbx($agent["EGRPO"])?></<?=CSaleExport::getTagName("SALE_EXPORT_EGRPO")?>><?
					}
					if(strlen($agent["OKVED"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_OKVED")?>><?=htmlspecialcharsbx($agent["OKVED"])?></<?=CSaleExport::getTagName("SALE_EXPORT_OKVED")?>><?
					}
					if(strlen($agent["OKDP"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_OKDP")?>><?=htmlspecialcharsbx($agent["OKDP"])?></<?=CSaleExport::getTagName("SALE_EXPORT_OKDP")?>><?
					}
					if(strlen($agent["OKOPF"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_OKOPF")?>><?=htmlspecialcharsbx($agent["OKOPF"])?></<?=CSaleExport::getTagName("SALE_EXPORT_OKOPF")?>><?
					}
					if(strlen($agent["OKFC"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_OKFC")?>><?=htmlspecialcharsbx($agent["OKFC"])?></<?=CSaleExport::getTagName("SALE_EXPORT_OKFC")?>><?
					}
					if(strlen($agent["OKPO"])>0)
					{
						?><<?=CSaleExport::getTagName("SALE_EXPORT_OKPO")?>><?=htmlspecialcharsbx($agent["OKPO"])?></<?=CSaleExport::getTagName("SALE_EXPORT_OKPO")?>><?
						?><<?=CSaleExport::getTagName("SALE_EXPORT_OKPO_CODE")?>><?=htmlspecialcharsbx($agent["OKPO"])?></<?=CSaleExport::getTagName("SALE_EXPORT_OKPO_CODE")?>><?
					}
					if(strlen($agent["ACCOUNT_NUMBER"])>0)
					{
						?>
						<<?=CSaleExport::getTagName("SALE_EXPORT_MONEY_ACCOUNTS")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_MONEY_ACCOUNT")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_ACCOUNT_NUMBER")?>><?=htmlspecialcharsbx($agent["ACCOUNT_NUMBER"])?></<?=CSaleExport::getTagName("SALE_EXPORT_ACCOUNT_NUMBER")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_BANK")?>>
						  <<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>><?=htmlspecialcharsbx($agent["B_NAME"])?></<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>>
						  <<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS")?>>
						    <<?=CSaleExport::getTagName("SALE_EXPORT_PRESENTATION")?>><?=htmlspecialcharsbx($agent["B_ADDRESS_FULL"])?></<?=CSaleExport::getTagName("SALE_EXPORT_PRESENTATION")?>>
						<?
						if(strlen($agent["B_INDEX"])>0)
						{
							?><<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_POST_CODE")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["B_INDEX"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
							</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>><?
						}
						if(strlen($agent["B_COUNTRY"])>0)
						{
							?><<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_COUNTRY")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["B_COUNTRY"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
							</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>><?
						}
						if(strlen($agent["B_REGION"])>0)
						{
							?><<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_REGION")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["B_REGION"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
							</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>><?
						}
						if(strlen($agent["B_STATE"])>0)
						{
							?><<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_STATE")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["B_STATE"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
							</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>><?
						}
						if(strlen($agent["B_TOWN"])>0)
						{
							?><<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_SMALL_CITY")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["B_TOWN"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
							</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>><?
						}
						if(strlen($agent["B_CITY"])>0)
						{
							?><<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_CITY")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["B_CITY"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
							</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>><?
						}
						if(strlen($agent["B_STREET"])>0)
						{
							?><<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_STREET")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["B_STREET"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
							</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>><?
						}
						if(strlen($agent["B_HOUSE"])>0)
						{
							?><<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_HOUSE")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["B_HOUSE"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
							</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>><?
						}
						if(strlen($agent["B_BUILDING"])>0)
						{
							?><<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_BUILDING")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["B_BUILDING"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
							</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>><?
						}
						if(strlen($agent["B_FLAT"])>0)
						{
							?><<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_FLAT")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
							<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["B_FLAT"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
							</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>><?
						}
						?>
						    </<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS")?>>
						<?
						if(strlen($agent["B_BIK"])>0)
						{
							?><<?=CSaleExport::getTagName("SALE_EXPORT_BIC")?>><?=htmlspecialcharsbx($agent["B_BIK"])?></<?=CSaleExport::getTagName("SALE_EXPORT_BIC")?>><?
						}
						?>
						</<?=CSaleExport::getTagName("SALE_EXPORT_BANK")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_MONEY_ACCOUNT")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_MONEY_ACCOUNTS")?>>
					<?
					}
				}

				if(strlen($agent["F_ADDRESS_FULL"])>0)
				{
					self::setDeliveryAddress($agent["F_ADDRESS_FULL"]);
					?>
					<<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS")?>>
					<<?=CSaleExport::getTagName("SALE_EXPORT_PRESENTATION")?>><?=htmlspecialcharsbx($agent["F_ADDRESS_FULL"])?></<?=CSaleExport::getTagName("SALE_EXPORT_PRESENTATION")?>>
					<?
					if(strlen($agent["F_INDEX"])>0)
					{
						?>
						<<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_POST_CODE")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["F_INDEX"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
					<?
					}
					if(strlen($agent["F_COUNTRY"])>0)
					{
						?>
						<<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_COUNTRY")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["F_COUNTRY"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
					<?
					}
					if(strlen($agent["F_REGION"])>0)
					{
						?>
						<<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_REGION")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["F_REGION"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
					<?
					}
					if(strlen($agent["F_STATE"])>0)
					{
						?>
						<<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_STATE")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["F_STATE"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
					<?
					}
					if(strlen($agent["F_TOWN"])>0)
					{
						?>
						<<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_SMALL_CITY")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["F_TOWN"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
					<?
					}
					if(strlen($agent["F_CITY"])>0)
					{
						?>
						<<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_CITY")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["F_CITY"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
					<?
					}
					if(strlen($agent["F_STREET"])>0)
					{
						?>
						<<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_STREET")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["F_STREET"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
					<?
					}
					if(strlen($agent["F_HOUSE"])>0)
					{
						?>
						<<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_HOUSE")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["F_HOUSE"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
					<?
					}
					if(strlen($agent["F_BUILDING"])>0)
					{
						?>
						<<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_BUILDING")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["F_BUILDING"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
					<?
					}
					if(strlen($agent["F_FLAT"])>0)
					{
						?>
						<<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=CSaleExport::getTagName("SALE_EXPORT_FLAT")?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["F_FLAT"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS_FIELD")?>>
					<?
					}
					?>
					</<?=CSaleExport::getTagName("SALE_EXPORT_ADDRESS")?>>
				<?
				}

				if(strlen($agent["PHONE"])>0 || strlen($agent["EMAIL"])>0)
				{
					?>
					<<?=CSaleExport::getTagName("SALE_EXPORT_CONTACTS")?>>
					<?
					if(strlen($agent["PHONE"])>0)
					{
						?>
						<<?=CSaleExport::getTagName("SALE_EXPORT_CONTACT")?>>
						  <<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=(self::getVersionSchema() > self::DEFAULT_VERSION ? CSaleExport::getTagName("SALE_EXPORT_WORK_PHONE_NEW") : CSaleExport::getTagName("SALE_EXPORT_WORK_PHONE"))?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
						  <<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["PHONE"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_CONTACT")?>>
					<?
					}
					if(strlen($agent["EMAIL"])>0)
					{
						?>
						<<?=CSaleExport::getTagName("SALE_EXPORT_CONTACT")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>><?=(self::getVersionSchema() > self::DEFAULT_VERSION ? CSaleExport::getTagName("SALE_EXPORT_MAIL_NEW") : CSaleExport::getTagName("SALE_EXPORT_MAIL"))?></<?=CSaleExport::getTagName("SALE_EXPORT_TYPE")?>>
						<<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>><?=htmlspecialcharsbx($agent["EMAIL"])?></<?=CSaleExport::getTagName("SALE_EXPORT_VALUE")?>>
						</<?=CSaleExport::getTagName("SALE_EXPORT_CONTACT")?>>
					<?
					}
					?>
					</<?=CSaleExport::getTagName("SALE_EXPORT_CONTACTS")?>>
				<?
				}
				if(strlen($agent["CONTACT_PERSON"])>0)
				{
					?>
					<<?=CSaleExport::getTagName("SALE_EXPORT_REPRESENTATIVES")?>>
					  <<?=CSaleExport::getTagName("SALE_EXPORT_REPRESENTATIVE")?>>
					    <<?=CSaleExport::getTagName("SALE_EXPORT_CONTRAGENT")?>>
					      <<?=CSaleExport::getTagName("SALE_EXPORT_RELATION")?>><?=CSaleExport::getTagName("SALE_EXPORT_CONTACT_PERSON")?></<?=CSaleExport::getTagName("SALE_EXPORT_RELATION")?>>
					      <<?=CSaleExport::getTagName("SALE_EXPORT_ID")?>><?=md5($agent["CONTACT_PERSON"])?></<?=CSaleExport::getTagName("SALE_EXPORT_ID")?>>
					      <<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>><?=htmlspecialcharsbx($agent["CONTACT_PERSON"])?></<?=CSaleExport::getTagName("SALE_EXPORT_ITEM_NAME")?>>
					    </<?=CSaleExport::getTagName("SALE_EXPORT_CONTRAGENT")?>>
					  </<?=CSaleExport::getTagName("SALE_EXPORT_REPRESENTATIVE")?>>
					</<?=CSaleExport::getTagName("SALE_EXPORT_REPRESENTATIVES")?>>
				<?
				}?>
				<<?=CSaleExport::getTagName("SALE_EXPORT_ROLE")?>><?=CSaleExport::getTagName("SALE_EXPORT_BUYER")?></<?=CSaleExport::getTagName("SALE_EXPORT_ROLE")?>>
			</<?=CSaleExport::getTagName("SALE_EXPORT_CONTRAGENT")?>>
		</<?=CSaleExport::getTagName("SALE_EXPORT_CONTRAGENTS")?>>
		<?

		$filedsTolog = array(
			'ENTITY_ID' => $arOrder["USER_ID"],
			'PARENT_ID' => $arOrder['ID'],
			'ENTITY_DATE_UPDATE' => static::getUserTimeStapmX($arOrder),
			'XML_ID' => $xmlId
		);

		static::$documentsToLog[\Bitrix\Sale\Exchange\EntityType::USER_PROFILE][] = $filedsTolog;
	}




    protected static function exportedLastExport($arOrder, array $lastDateUpdateOrders)
    {
        $dateUpdate = $arOrder["DATE_UPDATE"]->toString();

        $result = (isset($lastDateUpdateOrders[$dateUpdate]) &&
            in_array($arOrder['ID'], $lastDateUpdateOrders[$dateUpdate]));

        return $result;
    }
    
    
    
    
    
}