<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<script>
	if ( "<?= $arResult["NAME"] ?>" != "") {
		app.setPageTitle({"title" : "<?=CUtil::JSEscape(htmlspecialcharsback($arResult["NAME"]))?>"});
	} else {
		app.setPageTitle({"title" : "Каталог"});
	}
</script>

<?/*$count_path = count($arResult['PATH']);
$path_prev_index = $count_path - 2;
$prev_path = $arResult['PATH'][$path_prev_index]['SECTION_PAGE_URL'];
if ($prev_path != ''){
	$this->SetViewTarget("mobile_catalog_prev_path");?>
	<div class="title-link">
		<a href="<?=$prev_path;?>"><i class="fa fa-caret-left"></i> <span>Назад</span></a>
	</div>
	<?$this->EndViewTarget();
}
*/?>

	<?$this->SetViewTarget("mobile_catalog_sort");?>
		<div class="count-box align-center">
			Сортировать:
			<select class="style2" name="sort_catalog" id="sort_catalog">
				<option value="<?=$arResult['SORT']['DEFAULT']['URL']?>" <?=($_REQUEST['by'] == ''? 'selected':'')?>>По-умолчанию</option>
				<option value="<?=$arResult['SORT']['PRICE']['URL']?>" <?=($_REQUEST['by'] == 'PRICE'? 'selected':'')?>>По цене</option>
				<option value="<?=$arResult['SORT']['RATE']['URL']?>" <?=($_REQUEST['by'] == 'PROPERTY_rating'? 'selected':'')?>>По рейтингу</option>
				<option value="<?=$arResult['SORT']['POPULAR']['URL']?>" <?=($_REQUEST['by'] == 'PROPERTY_POPULAR'? 'selected':'')?>>По популярности</option>
			</select>
		</div>
	<?$this->EndViewTarget();?>


<?if ($arParams["DISPLAY_TOP_PAGER"]){?>
<div class="pagination-wrap">
	<div class="pagination-box align-center">
		<? echo $arResult["NAV_STRING"]; ?>
	</div>
</div>
<?}?>

<div class="catalog-wrap-mobile clear-after">
	<div class="count-str"><?=$arResult['COUNT_STR']?></div>
	<div style="clear:both;"></div>

	<?foreach ($arResult['ITEMS'] as $key => $arItem) {?>
		<div class="item-wrap">
			<div class="item catalog-item">
				<div class="item-body">
					<a href="/paoloconte_app<?=$arItem["DETAIL_PAGE_URL"]?>" id="">
						<div class="image">
						<?if($arItem['CATALOG_PHOTO']):?>
							<img src="<?=$arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']?>" alt="<?=$arItem['NAME']?>">
						<?else:?>
							<img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
						<?endif;?>
						</div>
					</a>
					<div class="price-box">
						<div class="old-price <?=$arItem['OLD_PRICE'] != ''? 'rouble':''?>"><?=$arItem['OLD_PRICE'];?></div>
						<div class="new-price rouble"><?=$arItem['NEW_PRICE']?></div>
					</div>
				</div>
			</div>

		</div>
	<? } ?>
</div>


<div class="pagination-wrap border-top">

	<div class="pagination-box align-center">
		<?/*<a href="#" class="prev">
			<i class="icon-arrow-left"></i>
		</a>
		<a href="#" class="page">1</a>
		<a href="#" class="page active">2</a>
		<a href="#" class="page">3</a>
		<a href="#" class="next">
			<i class="icon-arrow-right"></i>
		</a>*/?>
		<?if ($arParams["DISPLAY_BOTTOM_PAGER"])
		{
			?><? echo $arResult["NAV_STRING"]; ?><?
		}?>
	</div>

</div>

<?if (false):?>
<?if ($arResult['SECTION_SEO_TEXT']):?>
	<?$this->SetViewTarget("catalog_section_seotext");?>
	<?//данный код будет перемещен в контейнер "catalog_section_seotext" в footer.php?>
	<?=$arResult['SECTION_SEO_TEXT']?>
	<?$this->EndViewTarget();?>
<?endif?>

<?if (count($arResult['ITEMS']) >0 && $APPLICATION->GetCurDir() != '/search/'):?>
<div class="sort-box align-right">
	<?$updown = '';
	if ($_REQUEST["order"] == 'asc') $updown = 'up';
	if ($_REQUEST["order"] == 'desc') $updown = 'down';
	?>
	<span class="text">СОРТИРОВАТЬ ПО:</span>
	<a href="<?=$arResult['SORT']['PRICE']['URL']?>" class="<?=$arResult['SORT']['PRICE']['ACTIVE']?>"><?if ($arResult['SORT']['PRICE']['ACTIVE'] != ''):?><i class="fa fa-angle-<?=$updown?>"></i><?endif?> ЦЕНЕ</a>
	<a href="<?=$arResult['SORT']['RATE']['URL']?>" class="<?=$arResult['SORT']['RATE']['ACTIVE']?>"><?if ($arResult['SORT']['RATE']['ACTIVE'] != ''):?><i class="fa fa-angle-<?=$updown?>"></i><?endif?> РЕЙТИНГУ</a>
	<a href="<?=$arResult['SORT']['POPULAR']['URL']?>" class="<?=$arResult['SORT']['POPULAR']['ACTIVE']?>"><?if ($arResult['SORT']['POPULAR']['ACTIVE'] != ''):?><i class="fa fa-angle-<?=$updown?>"></i><?endif?> ПОПУЛЯРНОСТИ</a>
</div>
<?endif?>

<div class="catalog-box clear-after">
	<?foreach ($arResult['ITEMS'] as $key => $arItem)
	{
		$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
		$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
		$strMainID = $this->GetEditAreaId($arItem['ID']);

		//product id for favorite and add cart
		$productId = '';
		if (!empty($arItem['OFFERS'])){
			//$productId = $arItem['OFFERS'][0]['ID'];
			reset($arItem['OFFERS']);
			$first = current($arItem['OFFERS']);
			//$productId = $first['ID'];
			$productIdToFav = $first['ID'];
		}
		else {
			$productId = $arItem['ID'];
			$productIdToFav = $arItem['ID'];
		}

		//favoriteIcon
		if(!empty($arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']))
			$favoritePhoto = $arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL'];
		else
			$favoritePhoto = $arResult['NOPHOTO'];
		?>

		<?// Sale percent
		$sale_percent = 0;
		if ($arItem['NEW_PRICE'] != '' && $arItem['OLD_PRICE'] != ''){

		}
		?>

		<div class="item catalog-item bx_catalog_item_container" id="<? echo $strMainID; ?>">
			<div class="item-body">
				<div class="image">
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" id="">
						<?if($arItem['CATALOG_PHOTO']):?>
							<img src="<?=$arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']?>" alt="<?=$arItem['NAME']?>">
						<?else:?>
							<img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
						<?endif;?>
					</a>
				</div>
				<div class="labels">
					<?if ($arItem['PROPERTIES']['HIT']['VALUE'] != ''):?><span class="hit">HIT</span><?endif?>
					<?if ($arItem['PROPERTIES']['NEW']['VALUE'] != ''):?><span class="new">NEW</span><?endif?>
					<?if (false && $arItem['PROPERTIES']['SALE']['VALUE'] != ''):?><span class="sell"><?=$arItem['PROPERTIES']['SALE']['VALUE']?>%</span><?endif?>
					<?if ($arItem['SALE_PERCENT'] > 0):?><span class="sell"><?=$arItem['SALE_PERCENT']?>%</span><?endif?>
				</div>

				<div class="price-box">
					<div class="bx_catalog_item_price">
						<div class="bx_price">
							<?
							// БЛОК ОТКЛЮЧЕН !
							if (false && !empty($arItem['MIN_PRICE']))
							{
								if ('N' == $arParams['PRODUCT_DISPLAY_MODE'] && isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
								{
									/*echo GetMessage(
                                        'CT_BCS_TPL_MESS_PRICE_SIMPLE_MODE',
                                        array(
                                            '#PRICE#' => $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'],
                                            '#MEASURE#' => GetMessage(
                                                'CT_BCS_TPL_MESS_MEASURE_SIMPLE_MODE',
                                                array(
                                                    '#VALUE#' => $arItem['MIN_PRICE']['CATALOG_MEASURE_RATIO'],
                                                    '#UNIT#' => $arItem['MIN_PRICE']['CATALOG_MEASURE_NAME']
                                                )
                                            )
                                        )
                                    );*/
									echo '<div class="new-price">'.$arItem['MIN_PRICE']['CATALOG_MEASURE_RATIO'].' руб.</div>';
								}
								else
								{
									if ('Y' == $arParams['SHOW_OLD_PRICE'] && $arItem['MIN_PRICE']['DISCOUNT_VALUE'] < $arItem['MIN_PRICE']['VALUE'])
									{
										?> <div class="old-price"><? echo $arItem['MIN_PRICE']['PRINT_VALUE']; ?></div><?
									}
									echo '<div class="new-price">'.$arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'].'</div>';
								}
							}
							?>
							<div class="old-price rouble"><?=$arItem['OLD_PRICE'];?></div>
							<div class="new-price rouble"><?=$arItem['NEW_PRICE']?></div>
						</div>
					</div>
				</div>

				<div class="overlay">
					<form action="#">
						<span class="to-favorite" data-image="<?=$favoritePhoto?>" data-product-id="<?=$productIdToFav?>">
							<i class="fa fa-heart"></i>
							<i class="fa active fa-heart-o"></i>
						</span>

						<div class="rating">
							<div class="rating_stars">
								<?for($i=1; $i<=5; $i++):?>
									<?$checked = ($i==$arItem['RATING'])? 'checked' : '';?>
									<input type="radio" title="<?=$i?>" value="<?=$i?>" class="star" disabled="disabled" <?=$checked?>/>
								<?endfor?>
							</div>
						</div>

						<?if (!empty($arItem['OFFERS'])):?>
							<div class="size">
								Выберите размер:
								<div class="size-box">
									<?
									$offerId = '';
									$i=0;
									foreach ($arItem['OFFERS'] as $key => $arOffer):?>
										<?$str_price = 'CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID'];
										if ($arOffer[$str_price] > 0 && $arResult['OFFERS_AMOUNT'][$arOffer['ID']] > 0):?>
											<?
											$active = ($i == 0)? true : false;
											if($active) $offerId = $arOffer['ID'];
											?>
											<label class="<?=$arOffer['CAN_BUY'] == '1'? '':'lost'?> <?//if($active) print 'active'?>">
												<?=$arOffer['PROPERTIES']['RAZMER']['VALUE']?>
												<input
													type="radio"
													value="<?=$arOffer['ID']?>"
													name="r<?echo $arItem['ID'];?>"
													<?=$arOffer['CAN_BUY'] == '1'? '':'disabled'?>
													data-item-id="<?=$arItem['ID']?>"
													<?//if($active) print 'checked'?>
													class="radio-offer"
													>
											</label>
											<?$i++;?>
										<?endif;?>
									<?endforeach?>
								</div>
							</div>
						<?else:?>
						<div class="size"></div>
						<?endif?>

						<div class="btn-box">
							<a href="#" class="btn btn-green full fa mode1 fa-shopping-cart btn-tobasket" data-product-id="<?=$productId?>" data-item-id="<?=$arItem['ID']?>" data-type="getMovedPanel" data-target="#side-cart"><span>В корзину</span></a>
							<a href="#" class="btn btn-gray-dark full fa mode1 fa-eye fastViewButton" data-item-id="<?=$arItem['ID']?>" data-toggle="modal" data-target="#fastViewModal"><span>Быстрый просмотр</span></a>
						</div>
					</form>

				</div>
			</div>
		</div>
	<? } ?>
</div>

<script type="text/javascript">
	BX.message({
		BTN_MESSAGE_BASKET_REDIRECT: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_BASKET_REDIRECT'); ?>',
		BASKET_URL: '<? echo $arParams["BASKET_URL"]; ?>',
		ADD_TO_BASKET_OK: '<? echo GetMessageJS('ADD_TO_BASKET_OK'); ?>',
		TITLE_ERROR: '<? echo GetMessageJS('CT_BCS_CATALOG_TITLE_ERROR') ?>',
		TITLE_BASKET_PROPS: '<? echo GetMessageJS('CT_BCS_CATALOG_TITLE_BASKET_PROPS') ?>',
		TITLE_SUCCESSFUL: '<? echo GetMessageJS('ADD_TO_BASKET_OK'); ?>',
		BASKET_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCS_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
		BTN_MESSAGE_SEND_PROPS: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_SEND_PROPS'); ?>',
		BTN_MESSAGE_CLOSE: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE') ?>',
		BTN_MESSAGE_CLOSE_POPUP: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_CLOSE_POPUP'); ?>',
		BTN_MESSAGE_BASKET_REDIRECT: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_BASKET_REDIRECT') ?>',
		COMPARE_MESSAGE_OK: '<? echo GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_OK') ?>',
		COMPARE_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_UNKNOWN_ERROR') ?>',
		COMPARE_TITLE: '<? echo GetMessageJS('CT_BCS_CATALOG_MESS_COMPARE_TITLE') ?>',
		BTN_MESSAGE_COMPARE_REDIRECT: '<? echo GetMessageJS('CT_BCS_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT') ?>',
		SITE_ID: '<? echo SITE_ID; ?>',

		BUY_URL: '<?=$arResult['~ADD_URL_TEMPLATE']?>'
	});
</script>

<div class="pagination-wrap emulate-table full">
	<div class="count-box emulate-cell align-left valign-middle">
		Показать:
		<select class="pageElementCount">
			<?foreach ($arResult['SHOW_ELEMENTS'] as $key=>$elCount):?>
				<option value="<?=$elCount['URL']?>" <?=$elCount['ACTIVE']?>><?=$key?></option>
			<?endforeach?>
		</select>
	</div>
	<?if ($arParams["DISPLAY_BOTTOM_PAGER"])
	{
		?><? echo $arResult["NAV_STRING"]; ?><?
	}?>
</div>
<?endif?>
