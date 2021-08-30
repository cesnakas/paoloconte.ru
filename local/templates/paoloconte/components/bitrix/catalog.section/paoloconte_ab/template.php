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
<?if (!isset($_GET['set_filter'])):?>
	<?$section_url = trim($arResult['SECTION_PAGE_URL'], '/');?>
	<div data-retailrocket-markup-block="567292709872e52a3cbd9a51" data-category-name="<?=$arResult['NAME'];?>" data-category-path="<?=$section_url;?>"></div>
<?endif;?>
<div class="main-title pts-bold<? if ($error_page==true) {?> align-center<?}?>">
    <span class="h1 fix"><?=GetMessage('CT_BCS_CATALOG_H1_PREV');?></span><h1 class="title-fx"><?=$arResult['NAME'];?></h1>
</div>
<?$frame = $this->createFrame()->begin();?>
<script>
	if (window.frameCacheVars !== undefined)
	{
		BX.addCustomEvent("onFrameDataReceived" , function(json) {
			window.Application.Components.Main.labelcheck();
		});
	} else {
		BX.ready(function() {

		});
	}
</script>
<?$frame->end();?>

<?if ($arResult['SECTION_SEO_TEXT'] && !isset($_REQUEST['PAGEN_3'])):?>
	<?$this->SetViewTarget("catalog_section_seotext");?>
	<?//данный код будет перемещен в контейнер "catalog_section_seotext" в footer.php?>
	<?=$arResult['SECTION_SEO_TEXT']?>
	<?$this->EndViewTarget();?>
<?endif?>

<?if (count($arResult['ITEMS']) == 0):?>
	<div class="error-404-wrap">
		<div class="top-text align-center">
			К сожалению, в данном разделе товаров пока нет.</br>Попробуйте вернуться на главную или воспользуйтесь поиском, если ищете что-то конкретное.
		</div>
		<div class="search-wrap">
			<?
            $page_url  = $APPLICATION->GetCurPage();
            $page_url = explode("/", $page_url);
            if($page_url[1] != 'search'):
                $APPLICATION->IncludeComponent(
                    "bitrix:search.form",
                    "404",
                    array(
                        "PAGE" => "#SITE_DIR#search/"
                    ),
                    false
                );
            endif;?>
		</div>
		<div class="btn-box align-center">
			<a href="/" class="btn btn-gray-dark mode2 icon-arrow-right">Вернуться на главную</a>
		</div>
	</div>
<?endif?>

<?if (count($arResult['ITEMS']) >0 && $APPLICATION->GetCurDir() != '/search/'):?>
<?
	if($arResult['ID']>0){?>
		<script type="text/javascript">
			(window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {      
				rrApi.categoryView("<?=trim($arResult['SECTION_PAGE_URL'], '/');?>");    
				retailrocket.categories.post({
					"categoryPath": "<?=trim($arResult['SECTION_PAGE_URL'], '/');?>",
					"url": "http://<?=SITE_SERVER_NAME;?><?=$arResult['SECTION_PAGE_URL'];?>"
				});
			});
		</script>
	<?}
?>
<div class="sort-box align-right">
	<?$updown = '';
	if ($_REQUEST["order"] == 'asc') $updown = 'up';
	if ($_REQUEST["order"] == 'desc') $updown = 'down';
	?>
	<span class="text">СОРТИРОВАТЬ ПО:</span>
	<a href="<?=$arResult['SORT']['PRICE']['URL']?>" class="<?=$arResult['SORT']['PRICE']['ACTIVE']?>"><?if ($arResult['SORT']['PRICE']['ACTIVE'] != ''):?><i class="fa fa-angle-<?=$updown?>"></i><?endif?> ЦЕНЕ</a>
	<a href="<?=$arResult['SORT']['RATE']['URL']?>" class="<?=$arResult['SORT']['RATE']['ACTIVE']?>"><?if ($arResult['SORT']['RATE']['ACTIVE'] != ''):?><i class="fa fa-angle-<?=$updown?>"></i><?endif?> РЕЙТИНГУ</a>
	<a href="<?=$arResult['SORT']['POPULAR']['URL']?>" class="<?=$arResult['SORT']['POPULAR']['ACTIVE']?>"><?if ($arResult['SORT']['POPULAR']['ACTIVE'] != ''):?><i class="fa fa-angle-<?=$updown?>"></i><?endif?> ПОПУЛЯРНОСТИ</a>
	<?if (count($arResult['ITEMS']) > 0):?>
        <div class="sort">
            <span class="sort-btn<?=($arParams['CATALOG_ROW'] == 3 ? ' active' : '')?>" id="sort3"><img src="/local/templates/paoloconte/images/svg/3х3.svg" alt="3x3"></span>
            <span class="sort-btn<?=($arParams['CATALOG_ROW'] == 5 ? ' active' : '')?>" id="sort5"><img src="/local/templates/paoloconte/images/svg/5х5.svg" alt="5x5"></span>
        </div>
    <?endif?>
</div>
<?endif?>

<div class="catalog-box clear-after<?=($arParams['CATALOG_ROW'] == 3 ? ' resized' : '')?>">
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
							<?$frame = $this->createFrame()->begin();?>
							<div class="old-price old-price-ab <?=$arItem['OLD_PRICE'] != ''? 'rouble':''?>"><?=$arItem['OLD_PRICE'];?></div>
							<div class="new-price new-price-ab rouble"><?=$arItem['NEW_PRICE']?></div>
							<?$frame->end();?>
						</div>
					</div>
				</div>

				<div class="overlay">
					<form action="#">
						<span class="to-favorite label-icon" data-image="<?=$favoritePhoto?>" data-product-id="<?=$productIdToFav?>">
							<i class="fa fa-heart"></i>
							<i class="fa active fa-heart-o"></i>
							<div class="label-icon-text">Добавить в список желаний</div>
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
							<?$frame = $this->createFrame()->begin();?>
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
							<?$frame->end();?>
						<?else:?>
						<div class="size"></div>
						<?endif?>

						<div class="btn-box">
							<a href="#" onmousedown="try { rrApi.addToBasket(<?=$arItem['ID']?>) } catch(e) {}" class="btn btn-green full fa mode1 fa-shopping-cart btn-tobasket" data-product-id="<?=$productId?>" data-item-id="<?=$arItem['ID']?>" data-product-name="<?=$arItem['NAME']?>" data-product-price="<?=$arItem['CATALOG_PRICE_5']?$arItem['CATALOG_PRICE_5']:$arItem['CATALOG_PRICE_2']?>" data-type="getMovedModalPanel" data-target="#side-cart"><span>В корзину</span></a>
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
	<?if (count($arResult['ITEMS']) > 0):?>
	<div class="count-box emulate-cell align-left valign-middle">
		Показать:
		<select class="pageElementCount">
			<?foreach ($arResult['SHOW_ELEMENTS'] as $key=>$elCount):?>
				<option value="<?=$elCount['URL']?>" <?=$elCount['ACTIVE']?>><?=$key?></option>
			<?endforeach?>
		</select>
	</div>
	<?endif?>
	<?if ($arParams["DISPLAY_BOTTOM_PAGER"])
	{
		?><? echo $arResult["NAV_STRING"]; ?><?
	}?>
</div>


