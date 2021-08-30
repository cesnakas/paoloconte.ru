<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props_format.php");

$countRelatedProp = 0;
if (is_array($arResult["ORDER_PROP"]["RELATED"])) {
    foreach ($arResult["ORDER_PROP"]["RELATED"] as $prop) {
        if (in_array($prop['CODE'], ['ADDRESS', 'LOCATION'])) {
            continue;
        }
        $countRelatedProp++;
    }
}

$style = ($countRelatedProp > 0) ? "" : "display:none";
?>
<div class="bx_section" style="<?=$style?>">
	<h4><?=GetMessage("SOA_TEMPL_RELATED_PROPS")?></h4>
	<br />
	<?=PrintPropsForm($arResult["ORDER_PROP"]["RELATED"], $arParams["TEMPLATE_LOCATION"])?>
</div>