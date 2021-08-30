<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
//favorite and buy icon

if(!empty($arResult['CATALOG_IMG']['PHOTO']))
	$productPhoto = $arResult['CATALOG_IMG']['PHOTO'][0]['BIG'];
else
	$productPhoto = $arResult['NOPHOTO'];
?>

	<script type="text/javascript">
		(window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
			retailrocket.products.post({
				"id": <?=$arResult['ID']?>,
				"name": "<?=$arResult['NAME']?>",
				<?if(!empty($arResult['NEW_PRICE'])){?>
				"price": <?=str_replace(" ","",substr($arResult['NEW_PRICE'],0,-5))?>,
				<?}else{?>
		 		"price": <?=str_replace(" ","",substr($arResult['OLD_PRICE'],0,-5))?>,
				<?}?>
				"pictureUrl": "http://<?=$_SERVER['SERVER_NAME'].$productPhoto?>",
				"url": "http://<?=$_SERVER['SERVER_NAME'].$arResult['DETAIL_PAGE_URL']?>",
				"isAvailable": <?if ($arResult['PROPERTIES']['OFFERS_AMOUNT']['VALUE'] > 0){echo 'true';}else{echo 'false';}?>,
				"description": "",
				"categoryPaths": [<?=$arResult['ELEMENT_ALL_GROUPS'];?>],
				<?if(!empty($arResult['PROPERTIES']['CML2_MANUFACTURER']['VALUE'])){?>
				"vendor": "<?=$arResult['PROPERTIES']['CML2_MANUFACTURER']['VALUE']?>",
				<?}?>
				<?if(!empty($arResult['PROPERTIES']['NAIMENOVANIE_MARKETING']['VALUE'])){?>
				"model": "<?=$arResult['PROPERTIES']['NAIMENOVANIE_MARKETING']['VALUE']?>",
				<?}?>
				<?if(!empty($arResult['PROPERTIES']['STIL']['VALUE'])){?>
				"typePrefix": "<?=$arResult['PROPERTIES']['STIL']['VALUE']?>",
				<?}?>
				<?if(!empty($arResult['NEW_PRICE'])){?>
				"oldPrice": <?=str_replace(" ","",substr($arResult['OLD_PRICE'],0,-5))?>,
				<?}else{?>
                "oldPrice": 0,
                <?}?>
				<?if(!empty($arResult['OTHER_COLORS'][$arResult['ID']]['COLOR']['NAME'])){?>
				"color": "<?=$arResult['OTHER_COLORS'][$arResult['ID']]['COLOR']['NAME']?>"
				<?}?>

		})
		rrApi.view(<?=$arResult['ID']?>);
		});

	</script>
