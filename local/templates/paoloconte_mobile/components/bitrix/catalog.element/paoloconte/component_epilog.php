<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $templateData */
/** @var @global CMain $APPLICATION */
use Bitrix\Main\Loader;
global $APPLICATION;
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
</script>
<?
}

if($arParams['SECTION_ID'] > 0){
    $SECTION_ID = $arParams['SECTION_ID'];
}else{
    $SECTION_ID = $arResult["IBLOCK_SECTION_ID"];
}

$rsNavElem = CIBlockElement::GetList(array('ID' => 'ASC'), array(
'IBLOCK_ID' => $arResult['IBLOCK_ID'],
"ACTIVE_DATE"=>"Y",
'ACTIVE' => 'Y',
'IBLOCK_SECTION_ID' => $arResult['IBLOCK_SECTION_ID'],
'SECTION_ID' =>$SECTION_ID,
'>CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID'] => 0,
'>PROPERTY_OFFERS_AMOUNT' => 0,
'PROPERTY_HAS_PHOTO' => 'Y',
'SECTION_GLOBAL_ACTIVE' => 'Y'),
false, array('nPageSize' => 1, 'nElementID' => $arResult['ID']),
array('ID', 'DETAIL_PAGE_URL'));


while($ob = $rsNavElem->GetNextElement()){
$arFields = $ob->GetFields();

if($arFields['ID'] == $arResult['ID'] )
$type = 'current';
else
$type = '';

if($arFields['IBLOCK_SECTION_ID'] == $arResult['IBLOCK_SECTION_ID'] )
   	$use = 'Y';
else
    $use = 'N';

$arNav[] = array(
'NAME'=>$arFields['NAME'],
'URL'=>$arFields['DETAIL_PAGE_URL'],
'USE'=>$use,
'CURRENT'=>$type,
);
}


if($arNav[0]['CURRENT'] == 'current' && isset($arNav[1]))
{
$arNav[2] = $arNav[1];
}

?>

<? if($arNav[2]['USE'] == 'Y' && $arNav[2]['CURRENT'] != 'current'): ?>
	<script>$('<a href="<?=$arNav[2]['URL']?>" class="item alt iconPrev"></a>').appendTo('.main-menu');</script>
<? else: ?>
	<script>$('<a href="#" class="item alt iconPrevNA"></a>').appendTo('.main-menu');</script>
<? endif; ?>

<script>
	$('<a href="<?=$arResult['SECTION']['SECTION_PAGE_URL']?>" class="item alt iconBack"></a>').appendTo('.main-menu');
</script>

<? if($arNav[0]['USE'] == 'Y' && $arNav[0]['CURRENT'] != 'current'): ?>
	<script>$('<a href="<?=$arNav[0]['URL']?>" class="item alt iconNext"></a>').appendTo('.main-menu');</script>
<? else: ?>
	<script>$('<a href="#" class="item alt iconNextNA"></a>').appendTo('.main-menu');</script>
<? endif; ?>
