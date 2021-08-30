<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Sale\Order;

unset($_SESSION['CATALOG_USED_COUPONS']);
unset($_SESSION['CATALOG_USER_COUPONS']);
$db_sales = CSaleBasket::GetList(array(), array("ORDER_ID" => $arResult["ORDER_ID"]));
while ($ar_sales = $db_sales->Fetch()) {
    $products[$ar_sales['PRODUCT_ID']] = $ar_sales;
}

$db_props = CSaleOrderPropsValue::GetOrderProps($arResult["ORDER_ID"]);
while ($arProps = $db_props->Fetch()) {
    if ($arProps["CODE"] == "CUP")
        $cup = $arProps["VALUE"];
}

foreach ($products as $product) {
    $res = CCatalogSku::GetProductInfo($product['PRODUCT_ID'], IBLOCK_SKU);
    $elementsIdByOffer[$product['PRODUCT_ID']] = $res['ID'];
}
$offersIdByElement = array_flip($elementsIdByOffer);

$res = CIBlockElement::GetList([], ['IBLOCK_ID' => IBLOCK_CATALOG, 'ID' => $elementsIdByOffer], false, false, ['ID', 'IBLOCK_SECTION_ID', 'PROPERTY_CML2_ARTICLE']);
while ($element = $res->fetch()) {
    $products[$offersIdByElement[$element['ID']]]['PRODUCT_CML2_ARTICLE'] = $element['PROPERTY_CML2_ARTICLE_VALUE'];
    $sectionsIdByOffer[$offersIdByElement[$element['ID']]] = $element['IBLOCK_SECTION_ID'];
}
$offersIdBySection = array_flip($sectionsIdByOffer);

$res = CIBlockSection::GetList([], ['IBLOCK_ID' => IBLOCK_CATALOG, 'ID' => $sectionsIdByOffer], false, ['ID', 'NAME'], false);
while ($section = $res->fetch()) {
    $products[$offersIdBySection[$section['ID']]]['PRODUCT_SECTION_NAME'] = $section['NAME'];
}

?>

<?
$order = Order::load($arResult["ORDER_ID"]);
global $USER;
if ($USER->IsAdmin()) {
    ?>
    <script>
      $(function () {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
          "event": "gtm.dom",
          "ecommerce": {
            "purchase": {
              "actionField": {
                "id": "<?=$arResult["ORDER_ID"]?>",
                'revenue': '<?= $order->getPrice() ?>',
                'shipping': '<?= $order->getDeliveryPrice() ?>',
                "goal_id": "19231550",
                  <? if ($cup) { ?>
                "coupon": "<?= $cup ?>"
                  <? } ?>
              },
              "products": [
                  <? foreach ($products as $product) { ?>
                {
                  'id': '<?= $product["PRODUCT_ID"] ?>',
                  'name': '<?= $product["NAME"] ?>',
                  'sku': '<?= $product["PRODUCT_CML2_ARTICLE"] ?>',
                  'category': '<?= $product["PRODUCT_SECTION_NAME"] ?>',
                  'price': '<?= $product["PRICE"] ?>',
                  'quantity': '<?= $product["QUANTITY"] ?>'
                },
                  <? } ?>
              ]
            }
          }
        });
      });
    </script>
<? } else { ?>
    <script>
      $(function () {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
          "event": "gtm.dom",
          "ecommerce": {
            "purchase": {
              "actionField": {
                "id": "<?=$arResult["ORDER_ID"]?>",
                "goal_id": "19231550",
                  <? if ($cup) { ?>
                "coupon": "<?= $cup ?>"
                  <? } ?>
              },
              "products": [
                  <? foreach ($products as $product) { ?>
                {
                  "id": "<?= $product["PRODUCT_ID"] ?>",
                  "name": '<?= $product["NAME"] ?>',
                  "price": "<?= $product["PRICE"] ?>",
                  "quantity": 1
                },
                  <? } ?>
              ]
            }
          }
        });
      });
    </script>
<? } ?>

<script>
    window.gtmRemarketingTag = {
        pagetype: 'purchase',
        prodid: <?=json_encode(array_column($products, 'PRODUCT_CML2_ARTICLE'))?>,
        totalvalue: <?=$order->getPrice()?>,
    };
</script>

<div class="personal-box ">
    <? if (!empty($arResult["ORDER"])) {
        $arItemRR = array();
        $dbBasketItems = CSaleBasket::GetList(array("ID" => "ASC"), array("ORDER_ID" => $arResult['ORDER']['ID']), false, false, array('ID', "PRODUCT_ID", "QUANTITY", "PRICE"));
        while ($arItem = $dbBasketItems->Fetch()) {
            $dbItemsCart = CCatalogSKU::getProductList($arItem['PRODUCT_ID'], IBLOCK_SKU);
            if (!empty($dbItemsCart) && !empty($dbItemsCart[$arItem['PRODUCT_ID']])) {
                if (empty($arItemRR[$dbItemsCart[$arItem['PRODUCT_ID']]['ID']])) {
                    $arItemRR[$dbItemsCart[$arItem['PRODUCT_ID']]['ID']] = array('QUANTITY' => $arItem['QUANTITY'], 'PRICE' => $arItem['PRICE']);
                } else {
                    $arItemRR[$dbItemsCart[$arItem['PRODUCT_ID']]['ID']]['QUANTITY'] = $arItemRR[$dbItemsCart[$arItem['PRODUCT_ID']]['ID']]['QUANTITY'] + $arItem['QUANTITY'];
                    $arItemRR[$dbItemsCart[$arItem['PRODUCT_ID']]['ID']]['PRICE'] = $arItem['PRICE'];
                }
            }
        }

    if (!empty($arItemRR)) { ?>
        <script type="text/javascript">
            <? if(!empty($arResult['ORDER']['USER_EMAIL'])) { ?>
            (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function () {
              rrApi.setEmail("<?=$arResult['ORDER']['USER_EMAIL'];?>");
            });
            <? } ?>
            rrApiOnReady.push(function () {
              try {
                rrApi.order({
                  transaction: <?=$arResult['ORDER']['ID']?>,
                  items: [
                      <?foreach ($arItemRR as $id => $val) {?>
                    {id: <?=$id?>, qnt: 1, price: <?printf('%d', $val['PRICE'])?>},
                      <?}?>
                  ]
                });
              } catch (e) {
              }
            })
        </script>
    <? } ?>

        <table class="sale_order_full_table">
            <tr>
                <td>
                    <?= GetMessage("SOA_TEMPL_ORDER_SUC", Array("#ORDER_DATE#" => $arResult["ORDER"]["DATE_INSERT"], "#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"])) ?>
                    <br/>
                    Бонусы будут начислены в течение 14 дней после выкупа заказа.
                    <br/><br/>
                    <?= GetMessage("SOA_TEMPL_ORDER_SUC1", Array("#LINK#" => $arParams["PATH_TO_PERSONAL"])) ?>
                </td>
            </tr>
        </table>

    <? if (!empty($arResult["PAY_SYSTEM"])) { ?>
    <br/><br/>

        <table class="sale_order_full_table">
            <tr>
                <td class="ps_logo">
                    <div class="pay_name"><?= GetMessage("SOA_TEMPL_PAY") ?></div>
                    <?= CFile::ShowImage($arResult["PAY_SYSTEM"]["LOGOTIP"], 100, 100, "border=0", "", false); ?>
                    <div class="paysystem_name"><?= $arResult["PAY_SYSTEM"]["NAME"] ?></div>
                    <br>
                </td>
            </tr>

            <? // global $USER; ?>
            <? if (strlen($arResult["PAY_SYSTEM"]["ACTION_FILE"]) > 0 /*&& $USER->IsAdmin()*/) { ?>
                <tr>
                    <td>
                        <? if ($arResult["PAY_SYSTEM"]["NEW_WINDOW"] == "Y" ) {
                        if ( $arResult["PAY_SYSTEM"]["CODE"] == 'bank_card') {
                            LocalRedirect($arParams["PATH_TO_PAYMENT"] . "?ORDER_ID=" . urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"])));
                        } else {?>
                            <script language="JavaScript">
                                window.open('<?=$arParams["PATH_TO_PAYMENT"]?>?ORDER_ID=<?=urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]))?>');
                            </script><?}?>
                        <?= GetMessage("SOA_TEMPL_PAY_LINK", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"] . "?ORDER_ID=" . urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"])))) ?>
                        <? if (CSalePdf::isPdfAvailable() && CSalePaySystemsHelper::isPSActionAffordPdf($arResult['PAY_SYSTEM']['ACTION_FILE'])) { ?>
                        <br/>
                            <? echo GetMessage("SOA_TEMPL_PAY_PDF", Array("#LINK#" => $arParams["PATH_TO_PAYMENT"] . "?ORDER_ID=" . urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"])) . "&pdf=1&DOWNLOAD=Y"));
                        }
                        } else {
                            if (strlen($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]) > 0) {
                                include($arResult["PAY_SYSTEM"]["PATH_TO_ACTION"]);
                            }
                        } ?>
                    </td>
                </tr>
            <? } ?>
        </table>
    <? } ?>
    <? } else { ?>
        <b><?= GetMessage("SOA_TEMPL_ERROR_ORDER") ?></b><br/><br/>

        <table class="sale_order_full_table">
            <tr>
                <td>
                    <?= GetMessage("SOA_TEMPL_ERROR_ORDER_LOST", Array("#ORDER_ID#" => $arResult["ACCOUNT_NUMBER"])) ?>
                    <?= GetMessage("SOA_TEMPL_ERROR_ORDER_LOST1") ?>
                </td>
            </tr>
        </table>
    <? } ?>
</div>
