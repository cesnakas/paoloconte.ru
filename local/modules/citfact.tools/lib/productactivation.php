<?

namespace Citfact;

class ProductActivation
{
    public static function setProductsPropertyBySection($IBLOCK_ID, $arSections, $propertyToUpdate){
        \CModule::IncludeModule("iblock");
        set_time_limit(36000);

        $arSectionIds = array();

        if (!empty($arSections)){
            $arSectionFilter = Array('IBLOCK_ID'=>$IBLOCK_ID, 'CODE'=>$arSections);
            $resSection = \CIBlockSection::GetList(array(), $arSectionFilter, false, array('ID'));
            while($secResult = $resSection->GetNext())
            {
                $arSectionIds[] = $secResult['ID'];
            }
        }

        $arFilter = array(
            'IBLOCK_ID'=>$IBLOCK_ID,
            'INCLUDE_SUBSECTIONS' => 'Y',
            '!PROPERTY_'.$propertyToUpdate['CODE'].'_VALUE' => $propertyToUpdate['NAME']
        );
        if (!empty($arSectionIds)){
            $arFilter['SECTION_ID'] = $arSectionIds;
        }

        $arSelect = array(
            'ID',
            'NAME',
            'PROPERTY_'.$propertyToUpdate['CODE']
        );

        $dbList = \CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
        $arElementsToUpdate = array();
        while($arResult = $dbList->GetNext())
        {
            $arElementsToUpdate[] = $arResult['ID'];
        }

        foreach ($arElementsToUpdate as $elementId){
            \CIBlockElement::SetPropertyValuesEx($elementId, false, array($propertyToUpdate['CODE'] => $propertyToUpdate['VALUE_ID']));
        }
    }


    public static function activateSkuCron($params = array())
    {
        $idPrice = ((int)$params['PRICE_ID'] > 0) ? $params['PRICE_ID'] : 2;
        $storeID = ((int)$params['STORE_ID'] > 0) ? $params['STORE_ID'] : 5;
        set_time_limit(36000);

        \CModule::IncludeModule("iblock");
        \CModule::IncludeModule("catalog");
        \CModule::IncludeModule('sale');

        $rsGetItems = \CIBlockElement::GetList(
            array(),
            array(
                "IBLOCK_ID" => IBLOCK_SKU,
                "ACTIVE" => "N",
                '=CATALOG_AVAILABLE' => "Y",
                ">CATALOG_STORE_AMOUNT_" . $storeID => "0",
                '>CATALOG_PRICE_' . $idPrice => 0,
                '!PROPERTY_BLOK_TOVARA_VALUE' => 'Одежда'
            ),
            false,
            false,
            array(
                "ID",
                "NAME"
            )
        );


        while ($obGetItems = $rsGetItems->GetNext()) {
            $el = new \CIBlockElement;
            $arLoadProductArray = array("ACTIVE" => "Y");

            if ($res = $el->Update($obGetItems['ID'], $arLoadProductArray)) {
                $str = "Update (activate) element SKU: " . $obGetItems['NAME'] . ' - ' . $obGetItems['ID'] . '<br>' . "\r\n";
                echo $str;
                file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log", print_r($str, true), FILE_APPEND);
            } else {
                $str = "Error Update (activate) element SKU: " . $obGetItems['NAME'] . ' - ' . $obGetItems['ID'] . ' - ' . $el->LAST_ERROR . '<br>';
                echo $str;
                file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log", print_r($str, true), FILE_APPEND);
            }
        }
        echo '<br><br>activateSkuCron finished<br><br>';
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log", print_r(array('activateSkuCron finished'), true), FILE_APPEND);
    }

    public static function activateProductsByOffersActivityCron()
    {

        \CModule::IncludeModule("iblock");
        \CModule::IncludeModule("catalog");
        \CModule::IncludeModule('sale');

        $rsGetItems = \CIBlockElement::GetList(
            array(),
            array(
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "ACTIVE" => "N"
            ),
            false,
            false,
            array(
                "ID",
                "NAME",
                "PROPERTY_HAS_PHOTO",
            )
        );

        while ($obGetItems = $rsGetItems->GetNext()) {
            $el = new \CIBlockElement;
            if ($obGetItems['PROPERTY_HAS_PHOTO_VALUE'] != 'Y') {
                $el->Update($obGetItems['ID'], ['ACTIVE' => 'N']);
            } else {
                $offers_db = \CIBlockElement::GetList(
                    array(),
                    array(
                        "IBLOCK_ID" => IBLOCK_SKU,
                        "PROPERTY_CML2_LINK" => $obGetItems['ID'],
                        'ACTIVE' => 'Y'
                    ),
                    false,
                    false,
                    array(
                        "ID",
                        "NAME",
                        "CODE"
                    )
                );
                $arLoadProductArray = array("ACTIVE" => "Y");
                $el = new \CIBlockElement;
                if ($offers_db->AffectedRowsCount()) {
                    if ($res = $el->Update($obGetItems['ID'], $arLoadProductArray)) {
                        $str = "Update (activate) catalog element: " . $obGetItems['NAME'] . ' - ' . $obGetItems['ID'] . '<br>' . "\r\n";
                        echo $str;
                        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log", print_r($str, true), FILE_APPEND);
                    } else {
                        $str = "Error Update (activate) catalog element: " . $obGetItems['NAME'] . ' - ' . $obGetItems['ID'] . ' - ' . $el->LAST_ERROR . '<br>';
                        echo $str;
                        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log", print_r($str, true), FILE_APPEND);
                    }
                } else {
                    $ar_res = \CCatalogProduct::GetByID($obGetItems['ID']);
                    if ((int)$ar_res['TYPE'] === 1) {
                        if ($res = $el->Update($obGetItems['ID'], $arLoadProductArray)) {
                            $str = "Update catalog element: " . $obGetItems['NAME'] . ' - ' . $obGetItems['ID'] . '<br>' . "\r\n";
                            echo $str;
                            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log", print_r($str, true), FILE_APPEND);
                        }
                    }
                }
            }
        }
        echo '<br><br>activateProductsByOffersActivityCron finished<br><br>';
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log", print_r(array('activateProductsByOffersActivityCron finished'), true), FILE_APPEND);
    }

    public static function deActivateSkuCron($params = array())
    {
        $storeID = ((int)$params['STORE_ID'] > 0) ? $params['STORE_ID'] : 5;

        \CModule::IncludeModule("iblock");
        \CModule::IncludeModule("catalog");
        \CModule::IncludeModule('sale');


        $rsGetItems = \CIBlockElement::GetList(
            array(),
            array(
                "IBLOCK_ID" => IBLOCK_SKU,
                "ACTIVE" => "Y",
                '=CATALOG_AVAILABLE' => "Y",
                "=CATALOG_STORE_AMOUNT_" . $storeID => "0",
                '!PROPERTY_BLOK_TOVARA_VALUE' => 'Одежда'
            ),
            false,
            false,
            array(
                "ID",
                "NAME"
            )
        );


        while ($obGetItems = $rsGetItems->GetNext()) {
            $el = new \CIBlockElement;
            $arLoadProductArray = array("ACTIVE" => "N");

            if ($res = $el->Update($obGetItems['ID'], $arLoadProductArray)) {
                $str = "Update (deactivate) element SKU: " . $obGetItems['NAME'] . ' - ' . $obGetItems['ID'] . '<br>' . "\r\n";
                echo $str;
                file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log", print_r($str, true), FILE_APPEND);
            } else {
                $str = "Error Update (deactivate) element SKU: " . $obGetItems['NAME'] . ' - ' . $obGetItems['ID'] . ' - ' . $el->LAST_ERROR . '<br>';
                echo $str;
                file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log", print_r($str, true), FILE_APPEND);
            }
        }
        echo '<br><br>deactivateSkuCron finished<br><br>';
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log", print_r(array('deactivateSkuCron finished'), true), FILE_APPEND);
    }

    public static function activateTradeOffersAndStoreAmount()
    {
        $arSelect = Array(
            "IBLOCK_ID",
            "ID",
            "NAME",
            "CATALOG_QUANTITY",
            "CATALOG_GROUP_2",
            "ACTIVE"
        );

        $arFilter = Array(
            "IBLOCK_ID" => IBLOCK_SKU,
            "ACTIVE" => "N",
            array(
                "LOGIC" => "AND",
                array("!=CATALOG_STORE_AMOUNT_5" => false),
                array(">CATALOG_STORE_AMOUNT_5" => 0),
            ),
            array(
                "LOGIC" => "AND",
                array("!=CATALOG_PRICE_2" => false),
                array(">CATALOG_PRICE_2" => 0),
            ),
        );

        $arActive = Array(
            "ACTIVE" => "Y"
        );

        $rsGet = \CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

        $el = new \CIblockElement;

        while ($ob = $rsGet->GetNext()) {
            if ($res = $el->Update($ob['ID'], $arActive)) {
                $str = "Update (activate) element: " . $ob['ID'] . ' / ' . $ob['NAME'] . ' / ' . $ob['CATALOG_PRICE_2'] . ' / ' . $ob['CATALOG_STORE_AMOUNT_5'] . '<br>';
                echo $str;
            } else {
                $str = "Error Update (activate) element: " . $ob['ID'] . ' / ' . $ob['NAME'] . ' / ' . $ob['CATALOG_PRICE_2'] . ' / ' . $ob['CATALOG_STORE_AMOUNT_5'] . ' / ' . $el->LAST_ERROR . '<br>';
                echo $str;
            }
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log", print_r($str, true), FILE_APPEND);
        }
        echo 'END';
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/cron/active_offers.log", print_r(array('activateTradeOffersAndStoreAmount finished'), true), FILE_APPEND);
    }
}