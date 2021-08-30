<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
CModule::IncludeModule("iblock");
global $USER;
if (!$USER->IsAdmin())
    header('Location: http://paoloconte.ru/');
$IBLOCK_ID = 10;
$i=0;
$arOrder = array();
$arFilter = array('IBLOCK_ID' => $IBLOCK_ID, "ACTIVE"=>"Y"/*, "ID"=>array(79421, 36647, 35596)*/);
$arSelectFields = array("ID", "NAME", "SECTION_CODE");


$step = 1;
if ($_REQUEST["step"])
    $step = intval($_REQUEST["step"]);

$page_count = 50;
if ($_REQUEST["page_count"])
    $page_count = intval($_REQUEST["page_count"]);

$rsElements = CIBlockElement::GetList($arOrder, $arFilter, FALSE,  Array("nPageSize"=>$page_count, "iNumPage"=>$step), $arSelectFields);

$elements = $rsElements->SelectedRowsCount();
$pages = ceil($elements/$page_count);
?>
<div id="progress_bar" style="    background-color: #E1E1E1;
    width: 100%;
    height: 30px;">
    <div id="progress_bar_bar" style="    background-color: #FF6C6C;
    width: 0%;
    height: 30px;">
</div>
</div>

<?
echo "Всего старниц = ".$pages."<br>";
echo "Всего товаров = ".$elements."<br>";
echo "Товаров на странице = ".$page_count."<br>";
echo "<b>Текущая страница = ".$step."</b><br>";
?>
<div>
    <form action="/rr_sent_prices/?step=1&auto_set=Y">
        <span>ва</span>
        <input name="page_count" value="50">
        <input type="hidden" name="step" value="1">
        <input type="hidden" name="auto_set" value="Y">
        <button type='submit'>Start</button>

    </form>
</div>
<?

if (!$_REQUEST["pause"] and $_REQUEST["auto_set"]){
?>
        <button type='submit'><a href="<?=$APPLICATION->GetCurPageParam("pause=Y")?>">Пауза</a></button>
<?
}

if ($_REQUEST["pause"]){
?>
        <button type='submit'><a href="<?=$APPLICATION->GetCurPageParam("", array("pause"))?>">Продолжить</a></button>
<?
die();
}





if ($pages<$step){
    die();    
}


echo "<br><br>";
while($arElement = $rsElements->GetNext())
{
    
   //  $arFields = $arElement->GetFields(); 
	/*echo "<pre>"; 
    print_r($arElement); 
    echo "</pre>";*/
    echo "<span style='color:green'>Товар:".(($step*$page_count)+$i+1)." ".$arElement["NAME"]." - ".$arElement["ID"]."<span><br>";
   $APPLICATION->IncludeComponent(
        "bitrix:catalog.element",
        "rrSent",
        array(
            "IBLOCK_TYPE" => IBLOCK_CATALOG_TYPE,
            "IBLOCK_ID" => IBLOCK_CATALOG,
            "ELEMENT_ID" => $arElement["ID"],
           // "ELEMENT_CODE" => $_REQUEST["CATALOG_CODE"],
         //   "SECTION_ID" => $_REQUEST["SECTION_ID"],
          //  "SECTION_CODE" => $_REQUEST["CATALOG_CODE"],
            "HIDE_NOT_AVAILABLE" => "N",
            "PROPERTY_CODE" => array(
                0 => "MATERIAL_VERKHA_MARKETING",
                1 => "MATERIAL_PODKLADKI_MARKETING",
                3 => "CML2_MANUFACTURER",
                4 => "SEZONNOST",
                5 => "STIL",
                6 => "TREND",
                7 => "TSVET_MARKETING",
                8 => "VYSOTA_KABLUKA",
                9 => "VYSOTA_GOLENISHCHA_PROIZVODSTVO",
            ),
            "OFFERS_LIMIT" => "0",
            "TEMPLATE_THEME" => "blue",
            "DISPLAY_NAME" => "Y",
            "DETAIL_PICTURE_MODE" => "IMG",
            "ADD_DETAIL_TO_SLIDER" => "N",
            "DISPLAY_PREVIEW_TEXT_MODE" => "E",
            "PRODUCT_SUBSCRIPTION" => "N",
            "SHOW_DISCOUNT_PERCENT" => "Y",
            "SHOW_OLD_PRICE" => "Y",
            "SHOW_MAX_QUANTITY" => "N",
            "SHOW_CLOSE_POPUP" => "N",
            "MESS_BTN_BUY" => "Купить",
            "MESS_BTN_ADD_TO_BASKET" => "В корзину",
            "MESS_BTN_SUBSCRIBE" => "Подписаться",
            "MESS_NOT_AVAILABLE" => "Нет в наличии",
            "USE_VOTE_RATING" => "Y",
            "USE_COMMENTS" => "Y",
            "BRAND_USE" => "N",
            "SECTION_URL" => "",
            "DETAIL_URL" => "",
            "SECTION_ID_VARIABLE" => "SECTION_CODE",
            "CHECK_SECTION_ID_VARIABLE" => "N",
            "CACHE_TYPE" => "N",
            "CACHE_TIME" => "36000000",
            "CACHE_GROUPS" => "N",
            "SET_TITLE" => "N",
            "SET_BROWSER_TITLE" => "Y",
            "BROWSER_TITLE" => "-",
            "SET_META_KEYWORDS" => "Y",
            "META_KEYWORDS" => "-",
            "SET_META_DESCRIPTION" => "Y",
            "META_DESCRIPTION" => "-",
            "SET_STATUS_404" => "Y",
            "ADD_SECTIONS_CHAIN" => "N",
            "ADD_ELEMENT_CHAIN" => "N",
            "USE_ELEMENT_COUNTER" => "Y",
            "ACTION_VARIABLE" => "action",
            "PRODUCT_ID_VARIABLE" => "id",
            "DISPLAY_COMPARE" => "N",
            "PRICE_CODE" => $_SESSION['GEO_PRICES']['TO_PARAMS'],
            "USE_PRICE_COUNT" => "N",
            "SHOW_PRICE_COUNT" => "1",
            "PRICE_VAT_INCLUDE" => "Y",
            "PRICE_VAT_SHOW_VALUE" => "N",
            "CONVERT_CURRENCY" => "N",
            "BASKET_URL" => BASKET_URL,
            "USE_PRODUCT_QUANTITY" => "N",
            "ADD_PROPERTIES_TO_BASKET" => "Y",
            "PRODUCT_PROPS_VARIABLE" => "prop",
            "PARTIAL_PRODUCT_PROPERTIES" => "N",
            "PRODUCT_PROPERTIES" => array(
            ),
            "ADD_TO_BASKET_ACTION" => array(
                0 => "BUY",
            ),
            "LINK_IBLOCK_TYPE" => "",
            "LINK_IBLOCK_ID" => "",
            "LINK_PROPERTY_SID" => "",
            "LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
            "OFFERS_FIELD_CODE" => array(
                0 => "",
                1 => "",
            ),
            "OFFERS_PROPERTY_CODE" => array(
                0 => "RAZMER",
                1 => "",
            ),
            "OFFERS_SORT_FIELD" => "sort",
            "OFFERS_SORT_ORDER" => "asc",
            "OFFERS_SORT_FIELD2" => "id",
            "OFFERS_SORT_ORDER2" => "asc",
            "ADD_PICT_PROP" => "-",
            "LABEL_PROP" => "-",
            "OFFER_ADD_PICT_PROP" => "-",
            "OFFER_TREE_PROPS" => array(
                0 => "RAZMER",
            ),
            "MESS_BTN_COMPARE" => "Сравнить",
            "OFFERS_CART_PROPERTIES" => array(
                0 => "RAZMER",
            ),
            "VOTE_DISPLAY_AS_RATING" => "rating",
            "BLOG_USE" => "N",
            "VK_USE" => "N",
            "FB_USE" => "N",
            "PRODUCT_QUANTITY_VARIABLE" => "quantity",
            'SET_CANONICAL_URL' => 'Y',
        ),
        false
    );

$i++;
	//break;
    
}

echo "<br>".$i;

if ($_REQUEST["auto_set"] and !$_REQUEST["pause"]){

?>
<script>
$( document ).ready(function() {
    $("#progress_bar_bar").animate({
        width: "100%",
        }, 3000, function() {
                    location.href="<?=$APPLICATION->GetCurPageParam("step=".($step+1), array("step"))?>";
                }
    );    


});


</script>


<?}?>