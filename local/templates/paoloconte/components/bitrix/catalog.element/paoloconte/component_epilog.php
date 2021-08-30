<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;
global $APPLICATION;
$APPLICATION->SetAdditionalCSS('/local/templates/paoloconte/components/citfact/reserve/.default/style.css');
$APPLICATION->AddHeadScript('/local/templates/paoloconte/components/citfact/reserve/.default/script.js');
if (isset($templateData['TEMPLATE_THEME']))
{
	$APPLICATION->SetAdditionalCSS($templateData['TEMPLATE_THEME']);
}
if (isset($templateData['TEMPLATE_LIBRARY']) && !empty($templateData['TEMPLATE_LIBRARY']))
{
	$loadCurrency = false;
	if (!empty($templateData['CURRENCIES']))
		$loadCurrency = Loader::includeModule('currency');
	CJSCore::Init($templateData['TEMPLATE_LIBRARY']);
	if ($loadCurrency)
	{
	?>
	<script type="text/javascript">
		BX.Currency.setCurrencies(<? echo $templateData['CURRENCIES']; ?>);
	</script>
<?
	}
}
if (isset($templateData['JS_OBJ']))
{
?><script type="text/javascript">
BX.ready(BX.defer(function(){
	if (!!window.<? echo $templateData['JS_OBJ']; ?>)
	{
		window.<? echo $templateData['JS_OBJ']; ?>.allowViewedCount(true);
	}
}));
</script><?
}

use \Bitrix\Catalog\CatalogViewedProductTable as CatalogViewedProductTable;
CatalogViewedProductTable::refresh($arResult['ID'], CSaleBasket::GetBasketUserID());

$url = URL_FULL_VERSION . $APPLICATION->GetCurPage(false);
$APPLICATION->AddHeadString('<meta property="og:url" content="'. $url . '"/> ');
$APPLICATION->AddHeadString('<meta property="og:title" content="'. $arResult['NAME'] . '"/> ');
$APPLICATION->AddHeadString('<meta property="og:description" content="'. ($arResult['DETAIL_TEXT'] != '' ? $arResult['DETAIL_TEXT']:$arResult['NAME']) . '"/> ');
$APPLICATION->AddHeadString('<meta property="og:image" content="'. URL_FULL_VERSION . $arResult['CATALOG_IMG']['PHOTO'][0]['REPOST'] . '"/> ');


if( $arResult["CANONICAL_PAGE_URL"] != '' ){
	$APPLICATION->AddHeadString('<link rel="canonical" href="' . $arResult["CANONICAL_PAGE_URL"] . '" />');
}

//Следующий и предыдущий элементы
$arrSortAlown = array('price'=> 'catalog_PRICE_1' , 'name'=> 'NAME', 'rating' => 'PROPERTY_RATING' , 'artnumber'=> 'PROPERTY_ARTNUMBER');

$_sort = isset($arrSortAlown[$_GET['sort']]) ? $arrSortAlown[$_GET['sort']] : 'NAME';
$_order = isset($_GET['order']) && $_GET['order']=='desc' ? 'DESC' : 'ASC';

$sort_url = 'sort=' .( isset($_GET['sort'])? $_GET['sort'] : 'name')
    .'&order='. (isset($_GET['order'])? $_GET['order'] : 'asc');
if($arParams['SECTION_ID'] > 0){
    $SECTION_ID = $arParams['SECTION_ID'];
}else{
    $SECTION_ID = $arResult["IBLOCK_SECTION_ID"];
}
$res = CIBlockElement::GetList(
    array("$_sort" => $_order),
    Array(
        "IBLOCK_ID"=>$arResult["IBLOCK_ID"],
        "ACTIVE_DATE"=>"Y",
        "ACTIVE"=>"Y" ,
        "SECTION_ID" =>$SECTION_ID,
        '>CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID'] => 0,
        '>PROPERTY_OFFERS_AMOUNT' => 0,
        'PROPERTY_HAS_PHOTO' => 'Y',
    ),
    false,
    array("nPageSize" => "1","nElementID" => $arResult["ID"]),
    array_merge(Array("ID", "NAME","DETAIL_PAGE_URL","DETAIL_PICTURE"), array_values($arrSortAlown))
);

$arNav = array();
while($ob = $res->GetNext())
{
    if($ob['ID'] == $arResult['ID'] )
        $type = 'current';
    else
        $type = '';

    if($ob['IBLOCK_SECTION_ID'] == $arResult['IBLOCK_SECTION_ID'] )
        $use = 'Y';
    else
        $use = 'N';

    if($ob['DETAIL_PICTURE'] != '') $file = CFile::ResizeImageGet($ob['DETAIL_PICTURE'], array('width'=>150, 'height'=>150), BX_RESIZE_IMAGE_PROPORTIONAL, true);

    $arNav[] = array(
        'NAME'=>$ob['NAME'],
        'PICTURE'=>$file['src'],
        'URL'=>$ob['DETAIL_PAGE_URL'],
        'USE'=>$use,
        'CURRENT'=>$type,
    );
}


?>

<noindex>
    <div id="replaceBox">
        <div class="product-arrows">
            <?
            
            if($arNav[0]['USE'] == 'Y' && $arNav[0]['CURRENT'] != 'current'): ?>
                <a href="<?=$arNav[0]['URL']?>?<?=$sort_url?>" class="product-arrows__prev"></a>
            <? endif;
            
            ?>
            <?
            
            if($arNav[2]['USE'] == 'Y' && $arNav[2]['CURRENT'] != 'current'): ?>
                <a href="<?=$arNav[2]['URL']?>?<?=$sort_url?>" class="product-arrows__next"></a>
            <? endif;
            
            // Формируем хлебные крошки:
            // берем первое заполненное значение в порядке приоритета:
            // 1. значение доп. св-ва раздела UF_BREADCRUMB,
            // 2. Название раздела
            if (is_array($arResult['PATH'])) {
                foreach ($arResult['PATH'] as $path) {
                    if (array_key_exists($path["ID"], $arSectionBreadcrumb)) {
                        $APPLICATION->AddChainItem($arSectionBreadcrumb[$path["ID"]], $path['~SECTION_PAGE_URL']);
                        //} elseif ($path['IPROPERTY_VALUES']['SECTION_PAGE_TITLE'] != '') {
                        //    $APPLICATION->AddChainItem($path['IPROPERTY_VALUES']['SECTION_PAGE_TITLE'], $path['~SECTION_PAGE_URL']);
                    } else {
                        $APPLICATION->AddChainItem($path['NAME'], $path['~SECTION_PAGE_URL']);
                    }
                }
            }
            ?>
        </div>
    </div>
</noindex>

<script>
    $(function() {
//        $('#nextPrevBox').append( $('#replaceBox').html() );
        $('.product').before( $('#replaceBox').html() );
    });
</script>

<?
//#55794# Редирект на базовую страницу товара
if ($APPLICATION->GetCurPage(false) != "/catalog/".$arParams["ELEMENT_CODE"]."/"  )
{
	LocalRedirect("/catalog/".$arParams["ELEMENT_CODE"]."/", false, "301 Moved permanently");
}
?>
