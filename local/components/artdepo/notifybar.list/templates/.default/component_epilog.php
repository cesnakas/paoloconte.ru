<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arPage = parse_url($APPLICATION->GetCurPage(false));
$page = $arPage['path'];
foreach($arResult['ELEMENTS'] as $el){
    $arPageElement = parse_url($arResult['LINK'][$el]);
    $pageElement = $arPageElement['path'];
    if($pageElement === $page){
        // hidden banner to landing page
        ?>
        <script>
            $('[data-id="<?=$el?>"]').css('display','none');
        </script>
        <?
    }
}
?>