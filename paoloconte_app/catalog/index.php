<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<div class="catalog-wrap">
	<?
	//переменная $catalogMode задается в header.php шаблона и имеет всего два положения для элементов - ELEMENT, для раздела SECTION
	global $catalogMode;

	if($catalogMode == 'ELEMENT'){
		include_once('detail.php');
	}
	else{
		// Проверяем наличие такого раздела
		$arFilter = Array("IBLOCK_ID"=>IBLOCK_CATALOG, "CODE"=>$_REQUEST["CATALOG_CODE"], "ACTIVE_DATE"=>"Y", "GLOBAL_ACTIVE"=>"Y");
		$res = CIBlockSection::GetList(Array(), $arFilter, false, Array("nPageSize"=>1), Array("ID", "IBLOCK_ID"));
		if($ob = $res->GetNextElement(false, false)){
			include_once('section.php');
		}
		else{?>
			<?$APPLICATION->SetPageProperty("NOT_SHOW_NAV_CHAIN", "Y");?>
			<?$APPLICATION->IncludeFile(
			    SITE_DIR."include/not_found_catalog.php",
			    Array(),
			    Array("MODE"=>"text")
			);?>
		<?}
	}
	?>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>