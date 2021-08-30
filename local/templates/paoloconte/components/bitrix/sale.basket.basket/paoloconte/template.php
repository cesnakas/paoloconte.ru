<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixBasketComponent $component */
$curPage = $APPLICATION->GetCurPage().'?'.$arParams["ACTION_VARIABLE"].'=';
$arUrls = array(
	"delete" => $curPage."delete&id=#ID#",
	"delay" => $curPage."delay&id=#ID#",
	"add" => $curPage."add&id=#ID#",
);
unset($curPage);

$arBasketJSParams = array(
	'SALE_DELETE' => GetMessage("SALE_DELETE"),
	'SALE_DELAY' => GetMessage("SALE_DELAY"),
	'SALE_TYPE' => GetMessage("SALE_TYPE"),
	'TEMPLATE_FOLDER' => $templateFolder,
	'DELETE_URL' => $arUrls["delete"],
	'DELAY_URL' => $arUrls["delay"],
	'ADD_URL' => $arUrls["add"]
);

use Citfact\CloudLoyalty\DataLoyalty; ?>
<script type="text/javascript">
	var basketJSParams = <?=CUtil::PhpToJSObject($arBasketJSParams);?>
</script>
<?
$APPLICATION->AddHeadScript($templateFolder."/script.js");
$count_items = count($arResult["ITEMS"]["AnDelCanBuy"]);

// Если пустая корзина, показываем сообщение и ссылку на каталог
if ($count_items == 0 && $_REQUEST['ORDER_ID'] == ''){
?>
    <div class="basket-empty">
        <div>Вы еще ничего не положили в корзину :(</div>
        <a href="/catalog/" class="btn btn--black">
            <span>Начать покупки</span>
        </a>
    </div>
<?
}
else if ($_REQUEST['ORDER_ID'] == ''){
	if (strlen($arResult["ERROR_MESSAGE"]) <= 0) {
	    $strBonusData = '';
	    $bonusData = DataLoyalty::getInstance()->getBonusData();
		$titles = ['бонус', 'бонуса', 'бонусов'];
		if (array_key_exists('applied', $bonusData)) {
			$strBonusData.= '<div><span>Использовано </span><span>' . \Citfact\Tools::declension($bonusData['applied'], $titles).'</span></div>';
		}
		if (array_key_exists('collected', $bonusData)) {
			$strBonusData.= '<div><span>Накоплено всего </span><span>' . \Citfact\Tools::declension($bonusData['collected'], $titles).'</span></div>';
		}
		if (array_key_exists('maxToApply', $bonusData)) {
			$strBonusData.= '<div><span>Доступно </span><span>' . \Citfact\Tools::declension($bonusData['maxToApply'], $titles).'</span></div>';
		}
        if (array_key_exists('maxToApply', $bonusData)) {
            $maxToApplyForThisOrder = $bonusData['maxToApplyForThisOrder'];
			$strBonusData.= '<div><span>Доступно для текущего заказа </span><span>' . \Citfact\Tools::declension($maxToApplyForThisOrder, $titles).'</span></div>';
		}

		?>

        <script type="text/javascript">
            var basketProducts = document.getElementById('basket_products');
            if (basketProducts) {
                basketProducts.innerHTML = '<a href="#order_form_content" class="btn btn--transparent">'
                +'<span>Перейти к оформлению заказа</span>'
                +'<span>Оформить заказ</span>'
                +'</a>';
                <?if ($strBonusData) {?>
                    basketProducts.outerHTML += '<div class="basket-bottom__info loyalty" style="display: none;">'
                        + '<?=$strBonusData?>'
                        + '</div>'
                <?}?>
            }
            var basketProducts = document.getElementById('basket_products_mobile');
            if (basketProducts) {
                <?if ($strBonusData) {?>
                basketProducts.outerHTML += '<div class="basket-bottom__info loyalty" style="display: none;">'
                    + '<?=$strBonusData?>'
                    + '</div>'
                <?}?>
            }
        </script>

		<div id="warning_message">
			<?
			$strBonusData ='';
			if (!empty($arResult["WARNING_MESSAGE"]) && is_array($arResult["WARNING_MESSAGE"])) {
				foreach ($arResult["WARNING_MESSAGE"] as $v)
					ShowError($v);
			}
			?>
		</div>
		<?

		$normalCount = count($arResult["ITEMS"]["AnDelCanBuy"]);
		$normalHidden = ($normalCount == 0) ? 'style="display:none;"' : '';

		$delayCount = count($arResult["ITEMS"]["DelDelCanBuy"]);
		$delayHidden = ($delayCount == 0) ? 'style="display:none;"' : '';

		$subscribeCount = count($arResult["ITEMS"]["ProdSubscribe"]);
		$subscribeHidden = ($subscribeCount == 0) ? 'style="display:none;"' : '';

		$naCount = count($arResult["ITEMS"]["nAnCanBuy"]);
		$naHidden = ($naCount == 0) ? 'style="display:none;"' : '';

		?>
		

        <form method="post" action="<?= POST_FORM_ACTION_URI ?>" name="basket_form" id="basket_form">
            <? include($_SERVER["DOCUMENT_ROOT"] . $templateFolder . "/basket_items.php"); ?>
            <input type="hidden" name="BasketOrder" value="BasketOrder"/>
        </form>
		<?
		$arItemIds = array();
		foreach($arResult["ITEMS"]["AnDelCanBuy"] as $item){
			$arItemIds[] = $item["PRODUCT_ID"];
		}
		$dbItemsCart = CCatalogSKU::getProductList($arItemIds, IBLOCK_SKU);
		foreach ($arItemIds as $k => $id) {
			if (!empty($dbItemsCart) && !empty($dbItemsCart[$id]['ID'])) {
				if (array_search($dbItemsCart[$id]['ID'], $arItemIds)===false) {
					$arItemIds[$k] = $dbItemsCart[$id]['ID'];
				}else{
					unset($arItemIds[$k]);
				}
			}
		}
		?>
		<div data-retailrocket-markup-block="56729d709872e52a3cbd9a70" data-product-id="<?=implode(",", $arItemIds);?>" style="display: none;"></div>
	<?
	} else if($count_items != 0) {
		ShowError($arResult["ERROR_MESSAGE"]);
	}
}
?>