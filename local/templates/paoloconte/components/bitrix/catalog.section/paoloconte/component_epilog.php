<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */

use Bitrix\Main\Loader,
    Bitrix\Main\Page\Asset,
    Citfact\Tools;

if ($arResult['COUNT_ITEMS'] == 0 && $arParams['IS_CATALOG_PAGE'] == 'Y') {
    @define("ERROR_404", "Y");
    CHTTP::SetStatus("404 Not Found");
    LocalRedirect("/404/", "404 Not Found");
}

 if (!$arResult['COUNT_ITEMS']  || $arResult['COUNT_ITEMS'] == 0) {
	@define("ERROR_404", "Y");
    CHTTP::SetStatus("404 Not Found");
 }

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

// добавляем link rel="canonical" - фомируем по правилу: /catalog/{код_последнего_раздела_из_цепочки}
if (!empty($arResult["PATH"])) {

    if ($lastSectionCode = end($arResult["PATH"])["CODE"]) {
        $canonical = 'https://'.$_SERVER["SERVER_NAME"].'/catalog/' . $lastSectionCode . '/';
        //#57986 - установка мета-тега canonical в соответствии с установками модуля sotbit.seometa
        $definedCanonical = $APPLICATION->GetPageProperty('canonicalurl');
        if ($definedCanonical) $canonical = 'https://'.$_SERVER["SERVER_NAME"]. $definedCanonical;

        Asset::getInstance()->addString('<link rel="canonical" href="' . $canonical . '">', true);
    }
}

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

// Обход композитного кеша при включении обработчика состояния фильтра
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/javascript/filter-condition.js");

if(!$this->__template)  $this->InitComponentTemplate();
$this->__template->SetViewTarget('showRRscript');
if ($arResult['ID'] > 0 && Tools::hasChildSection($arResult['IBLOCK_ID'],$arResult['ID']) == false) {?>
    <div>
        <script type="text/javascript">
            (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function () {
                rrApi.categoryView("<?=trim($arResult['SECTION_PAGE_URL'], '/');?>");
                retailrocket.categories.post({
                    "categoryPath": "<?=trim($arResult['SECTION_PAGE_URL'], '/');?>",
                    "url": "http://<?=SITE_SERVER_NAME;?><?=$arResult['SECTION_PAGE_URL'];?>"
                });
            });
        </script>
    </div>
<? } ?>
<? $this->__template->EndViewTarget(); ?>

