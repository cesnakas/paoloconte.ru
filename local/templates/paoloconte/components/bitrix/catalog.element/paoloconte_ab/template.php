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
$templateLibrary = array('popup');

?>
<style>
	.zoomContainer {
         z-index: 19 !important;
     }
</style>
<?$frame = $this->createFrame()->begin();?>
	<script>
		if (window.frameCacheVars !== undefined)
		{
			BX.addCustomEvent("onFrameDataReceived" , function(json) {
				window.Application.Components.Main.labelcheck();
				window.Application.Components.Main.runStaticModals();
				window.Application.Components.Sliders.runAddDetailCarousel();
			});
		} else {
			BX.ready(function() {

			});
		}
	</script>
<?$frame->end();?>

<?
$currencyList = '';
if (!empty($arResult['CURRENCIES']))
{
	$templateLibrary[] = 'currency';
	$currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}
$templateData = array(
	'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/style.css',
	'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME'],
	'TEMPLATE_LIBRARY' => $templateLibrary,
	'CURRENCIES' => $currencyList
);
unset($currencyList, $templateLibrary);

$strMainID = $this->GetEditAreaId($arResult['ID']);
$arItemIDs = array(
	'ID' => $strMainID,
	'PICT' => $strMainID.'_pict',
	'DISCOUNT_PICT_ID' => $strMainID.'_dsc_pict',
	'STICKER_ID' => $strMainID.'_sticker',
	'BIG_SLIDER_ID' => $strMainID.'_big_slider',
	'BIG_IMG_CONT_ID' => $strMainID.'_bigimg_cont',
	'SLIDER_CONT_ID' => $strMainID.'_slider_cont',
	'SLIDER_LIST' => $strMainID.'_slider_list',
	'SLIDER_LEFT' => $strMainID.'_slider_left',
	'SLIDER_RIGHT' => $strMainID.'_slider_right',
	'OLD_PRICE' => $strMainID.'_old_price',
	'PRICE' => $strMainID.'_price',
	'DISCOUNT_PRICE' => $strMainID.'_price_discount',
	'SLIDER_CONT_OF_ID' => $strMainID.'_slider_cont_',
	'SLIDER_LIST_OF_ID' => $strMainID.'_slider_list_',
	'SLIDER_LEFT_OF_ID' => $strMainID.'_slider_left_',
	'SLIDER_RIGHT_OF_ID' => $strMainID.'_slider_right_',
	'QUANTITY' => $strMainID.'_quantity',
	'QUANTITY_DOWN' => $strMainID.'_quant_down',
	'QUANTITY_UP' => $strMainID.'_quant_up',
	'QUANTITY_MEASURE' => $strMainID.'_quant_measure',
	'QUANTITY_LIMIT' => $strMainID.'_quant_limit',
	'BASIS_PRICE' => $strMainID.'_basis_price',
	'BUY_LINK' => $strMainID.'_buy_link',
	'ADD_BASKET_LINK' => $strMainID.'_add_basket_link',
	'BASKET_ACTIONS' => $strMainID.'_basket_actions',
	'NOT_AVAILABLE_MESS' => $strMainID.'_not_avail',
	'COMPARE_LINK' => $strMainID.'_compare_link',
	'PROP' => $strMainID.'_prop_',
	'PROP_DIV' => $strMainID.'_skudiv',
	'DISPLAY_PROP_DIV' => $strMainID.'_sku_prop',
	'OFFER_GROUP' => $strMainID.'_set_group_',
	'BASKET_PROP_DIV' => $strMainID.'_basket_prop',
);
$strObName = 'ob'.preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);
$templateData['JS_OBJ'] = $strObName;

$strTitle = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"] != ''
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
	: $arResult['NAME']
);
$strAlt = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"] != ''
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
	: $arResult['NAME']
);
?>
<?/***************************************************************************************************************************/?>
<?
//product id for favorite and cart
$productId = '';
if (!empty($arResult['OFFERS'])){
	//$productId = $arItem['OFFERS'][0]['ID'];
	reset($arResult['OFFERS']);
	$first = current($arResult['OFFERS']);
	//$productId = $first['ID'];
	$productIdToFav = $first['ID'];
}
else {
	$productId = $arResult['ID'];
	$productIdToFav = $arResult['ID'];
}

//favorite and buy icon
if(!empty($arResult['CATALOG_IMG']['PHOTO']))
	$productPhoto = $arResult['CATALOG_IMG']['PHOTO'][0]['BIG'];
else
	$productPhoto = $arResult['NOPHOTO'];
?>

<?$frame = $this->createFrame()->begin('');?>
    <? $setRRPrice = 0;
    if (!empty($arResult['NEW_PRICE'])) {
        $setRRPrice = str_replace(" ","", $arResult['NEW_PRICE']);
    } elseif(!empty($arResult['OLD_PRICE'])) {
        $setRRPrice = str_replace(" ","", $arResult['OLD_PRICE']);
    } ?>
	<script type="text/javascript">
		(window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
			retailrocket.products.post({
				"id": <?=$arResult['ID']?>,
				"name": "<?=$arResult['NAME']?>",
				"price": <?= $setRRPrice; ?>,
				"pictureUrl": "<?=URL_FULL_VERSION.$productPhoto?>",
				"url": "<?=URL_FULL_VERSION.$arResult['DETAIL_PAGE_URL']?>",
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
<?$frame->end();?>

<div class="detail-wrap clear-after test" id="<? echo $arItemIDs['ID']; ?>" xmlns="http://www.w3.org/1999/html" itemscope itemtype="http://schema.org/Product">
	<div class="container">


        <noindex>
            <div id="nextPrevBox">

            </div>
        </noindex>


		<div class="detail-item-image-wrap">
			<div class="detail-item-image-color-item">
				<span class="to-favorite label-icon" data-image="<?=$productPhoto?>" data-product-id="<?=$productIdToFav?>" data-text="Добавить в список желаний" data-toggle="modal">
					<i class="fa fa-heart"></i>
					<i class="fa active fa-heart-o"></i>
				</span>
				<div class="detail-item-big-wrap">
					<ul class="detail-item-big">
						<?if(!empty($arResult['CATALOG_IMG']['360'])):?>
							<li class="emulate-table full">
								<?
								$images360 = '';
								foreach($arResult['CATALOG_IMG']['360'] as $photo) {
									$images360 .= $photo['BIG'].',';
								}
								$images360 = substr($images360,0,-1);
								?>
								<div class="emulate-cell valign-middle image preloader">
									<div class="catalog_image_360" data-images="<?=$images360?>"></div>
								</div>
							</li>
						<?endif?>
						<?if(!empty($arResult['CATALOG_IMG']['PHOTO'])):?>
							<?foreach($arResult['CATALOG_IMG']['PHOTO'] as $photo) { ?>
								<li class="emulate-table full">
									<div class="emulate-cell valign-middle image">
										<?/*<a class="fancybox-detail" rel="gallery_1" href="<?=$photo['BIGGEST']?>" onclick="return false;">*/?>
											<img src="<?=$photo['BIG']?>" alt="<?=$arResult['NAME']?>" class="silder_image elevate-zoom" data-zoom-image="<?=$photo['BIGGEST']?>">
										<?/*</a>*/?>
									</div>
								</li>
							<? } ?>
						<?else:?>
							<li class="emulate-table full">
								<div class="emulate-cell valign-middle image">
									<img src="<?=$arResult['NOPHOTO']?>" alt="Нет фото">
								</div>
							</li>
						<?endif?>
					</ul>
				</div>
				<?$countPhotos = count($arResult['CATALOG_IMG']['PHOTO']) + count($arResult['CATALOG_IMG']['360']);?>
				<?if((!empty($arResult['CATALOG_IMG']['PHOTO']) || !empty($arResult['CATALOG_IMG']['360'])) && $countPhotos > 1):?>
					<div class="detail-item-small-wrap">
						<ul class="detail-item-small base">
							<?if(!empty($arResult['CATALOG_IMG']['360'])):?>
								<li class="image">
									<?/*?><img src="/local/templates/paoloconte/images/3d.png" alt="<?=$arResult['NAME']?>">
									<img src="<?=$arResult['CATALOG_IMG']['360'][0]['SMALL']?>" alt="<?=$arResult['NAME']?>"><?*/?>
									<div class="image_360"></div>
								</li>
							<?endif?>
							<?foreach($arResult['CATALOG_IMG']['PHOTO'] as $photo) { ?>
								<li class="image"><img src="<?=$photo['SMALL']?>" alt="<?=$arResult['NAME']?>"></li>
							<? } ?>
						</ul>
					</div>
				<?endif?>
			</div>
		</div>

		<?
		$count_for_pointers = count($arResult['CATALOG_IMG']['PHOTO']);
		if (count($arResult['CATALOG_IMG']['360']) > 0) $count_for_pointers++;?>
		<?if ($count_for_pointers < 6):?>
			<style>
				.owl-nav{
					display: none;
				}
			</style>
		<?endif?>


		<div class="detail-item-text-wrap">
			<div class="item-title main-title pts-bold">
				<h1 itemprop="name"><?
					echo (
					isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != ''
						? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
						: $arResult["NAME"]
					); ?></h1>
				<?/*<h1><?$APPLICATION->ShowTitle();?></h1>*/?>
				<?if(!empty($arResult['REVIEWS'])){
					$summStarsValue = 0;
					$maxCountStars = 5;
					$countStars = count($arResult['REVIEWS']);
					foreach ($arResult['REVIEWS'] as $reviewItem) {
						$summStarsValue = $summStarsValue + intval($reviewItem['PROPERTY_STARS_VALUE']);?>
						<div style="display: none;" itemprop="review" itemscope itemtype="http://schema.org/Review">
							<span class="review-name" itemprop="author" content="<?=$reviewItem['USERNAME']?>"></span>
							<meta itemprop="datePublished" content="<?=$reviewItem['DATE_CREATE_META']?>">
							<div style="display: none;" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
								<meta itemprop="worstRating" content = "1" />
								<span itemprop="ratingValue" content = "<?=$reviewItem['PROPERTY_STARS_VALUE'];?>"></span>
								<span itemprop="bestRating" content = "<?=$maxCountStars;?>"></span>
						    </div>
					    </div>
					<?}?>
					<div itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
						<span itemprop="ratingValue" content="<?=round($summStarsValue/$countStars);?>"></span>
						<span itemprop="reviewCount" content="<?=$countStars;?>"></span>
					</div>
				<?}?>
				<a href="#go_review_block">
				<div class="rating_stars indetail">
					<?for($i=1; $i<=5; $i++):?>
						<?$checked = ($i==$arResult['RATING'])? 'checked' : '';?>
						<input type="radio" title="<?=$i?>" value="<?=$i?>" class="star" disabled="disabled" <?=$checked?>/>
					<?endfor?>
				</div>
				</a>
				<?if ($arResult['DISPLAY_PROPERTIES']['CML2_ARTICLE']['VALUE'] != ''):?>
				<div class="article">Артикул: <?echo $arResult['DISPLAY_PROPERTIES']['CML2_ARTICLE']['VALUE']?></div>
				<?endif?>
			</div>
			<div class="tab-box" role="tabpanel">
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#c1" aria-controls="c1" role="tab" data-toggle="tab">ОПИСАНИЕ</a></li>
					<li role="presentation no-wrap"><a href="#c2" aria-controls="c2" role="tab" data-toggle="tab">НАЛИЧИЕ В МАГАЗИНАХ</a></li>
					<li role="presentation"><a href="#c3" aria-controls="c3" role="tab" data-toggle="tab" id="go_sizes_table">ТАБЛИЦА РАЗМЕРОВ</a></li>
					<li role="presentation"><a href="#c4" aria-controls="c4" role="tab" data-toggle="tab">ДОСТАВКА</a></li>
				</ul>
				<div class="top-line">
					<?$frame = $this->createFrame()->begin();?>
					<div class="price-box" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
						<span class="hidden" itemprop="priceCurrency">RUB</span>
						<?if(!empty($arResult['NEW_PRICE'])):?>
							<div class="old-price old-price-ab rouble">
								<?=substr($arResult['OLD_PRICE'],0,-5);?>
							</div>
							<?$newPriceValue = substr($arResult['NEW_PRICE'],0,-5);?>
							<div  class="new-price new-price-ab rouble" itemprop="price" content="<?=str_replace(' ', '', $newPriceValue);?>">
								<?=$newPriceValue;?>
							</div>
						<?else:?>
							<?$oldPriceValue = substr($arResult['OLD_PRICE'],0,-5);?>
							<div class="new-price rouble" itemprop="price" content="<?=str_replace(' ', '', $oldPriceValue);?>">
								<?=$oldPriceValue;?>
							</div>
						<?endif;?>
					</div>
					<?$frame->end();?>

					<div class="right-action">
						<div class="action"><i class="fa fa-comments"></i> <a href="#go_review_block" class="get-modal">Отзывы о товаре</a></div>
						<div class="action">
							<div class="action-modal-wrap">
								<i class="fa fa-check-circle"></i> <a href="#" class="get-modal subscribePriceLink" data-item-id="<?=$arResult['ID']?>">Узнать о снижении цены</a>
								<div class="action-modal">
									<?// Грузим сюда аяксом /include/ajax_subscribe_price_popup.php ?>
								</div>
							</div>
						</div>
						<div class="action">
							<div class="action-modal-wrap">
								<i class="fa fa-question-circle"></i>
								<a href="#"
								   class="get-modal subscribeSizeLink"
								   data-color-id="<?=$arResult['PROPERTIES']['GRUPPIROVKA_PO_MODELYAM_SAYT_']['VALUE'];?>"
								   data-item-id="<?=$arResult['ID']?>">Нет вашего размера?</a>
								<div class="action-modal">
									<?// Грузим сюда аяксом /include/ajax_no_size_popup.php ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade in active" id="c1">

						<div class="item-bottom-box">
							<div class="emulate-table full bottom-options">
								<div class="emulate-row">
									<div class="emulate-cell">
										<?/*$color = $arResult['DISPLAY_PROPERTIES']['TSVET_MARKETING']['DISPLAY_VALUE'];?>
										<?if ($color != ''):?>
											<div>Цвет: <span class="color-name"><?=$color?></span></div>
										<?endif*/?>
										<?if(!empty($arResult['OTHER_COLORS'])):?>
											<div class="color-box">
												<?foreach ($arResult['OTHER_COLORS'] as $arColor):?>
													<?$active = ($arColor['CODE'] == $arResult['CODE'])? true : false;?>
													<a href="<?if($active):?>javascript:void(0)<?else: print $arColor['DETAIL_PAGE_URL']; endif?>" <?if($active):?>class="active"<?endif?> title="<?=$arColor['COLOR']['NAME']?>" style="background-image: url('<?=$arColor['COLOR']['FILE_PATH']?><?//=$arColor['IMAGES']['PHOTO'][0]['SMALL']?>')"></a>
												<?endforeach?>
											</div>
										<?endif?>

										<?if (!empty($arResult['OFFERS'])):?>
										<?$frame = $this->createFrame()->begin();?>
										<div>Размер:</div>
										<div class="size-box" id="<? echo $arItemIDs['PROP_DIV'];?>">
											<?
											$offerId = '';
											$i=0;
											foreach ($arResult['OFFERS'] as $key => $arOffer):?>
												<?
												$str_price = 'CATALOG_PRICE_'.$_SESSION['GEO_PRICES']['PRICE_ID'];
												if ($arOffer[$str_price] > 0 && $arResult['OFFERS_AMOUNT'][$arOffer['ID']] > 0):?>
													<?
													$active = ($i == 0)? true : false;
													if($active) $offerId = $arOffer['ID'];
													?>
													<label class="<?=$arOffer['CAN_BUY'] == '1'? '':'lost'?> <?//if($active) print 'active'?>">
														<?=$arOffer['PROPERTIES']['RAZMER']['VALUE']?>
														<input type="radio"
															   value="<?=$arOffer['ID']?>"
															   name="r<?echo $arResult['ID'];?>"
															<?=$arOffer['CAN_BUY'] == '1'? '':'disabled'?>
															<?//if($active) print 'checked'?>
															   class="radio-offer"
															   data-id="<?=$arResult['ID']?>"
															   data-name="<?=$arResult['NAME']?> (<?=$arOffer['PROPERTIES']['RAZMER']['VALUE']?>)"
															>
													</label>
													<?$i++;?>
												<?endif;?>
											<?endforeach?>
										</div>
										<?$frame->end();?>

										<div class="get-size">
											<a href="#go_sizes_table" class="get-modal" data-click="true">Как узнать размер</a> <span>?</span>
										</div>
										<?endif?>
									</div>

									<div class="emulate-cell btn-box">
										<?if ($arResult['PROPERTIES']['OFFERS_AMOUNT']['VALUE'] > 0) {?>
											<a href="#" onmousedown="try { rrApi.addToBasket(<?=$arResult['ID']?>) } catch(e) {}" id='addBucket' class="btn btn-green fa mode1 fa-shopping-cart btn-tobasket" data-product-id="<?=$productId?>" data-product-name="<?=$arResult['NAME']?>" data-product-price="<?=$arResult['CATALOG_PRICE_5']?$arResult['CATALOG_PRICE_5']:$arResult['CATALOG_PRICE_2']?>"  data-type="getMovedModalPanel" data-target="#side-cart"><span>Добавить в корзину</span></a>
											<br/>
											<a href="#" class="btn btn-gray-dark fa mode1 fa-phone fast-order" onmousedown = "try {rrApi.addToBasket('<?=$arResult['ID']?>')} catch(e) {}" data-toggle="modal" data-product-id="<?=$productId?>" data-target="#fastOrderModal_<?=$arResult['ID']?>"><span>Быстрый заказ</span></a>
											<br/>
											<script src="https://yastatic.net/share2/share.js" async="async"></script><br/>
											<div class="ya-share2" data-services="vkontakte,facebook,telegram,viber,twitter,skype,whatsapp,odnoklassniki" data-limit="3" data-copy="hidden"></div>
										<?}else{?>
											<div class="error-txt">К сожалению, данная модель отсутствует на складе.<br/>
												<a href="<?=$arResult['SECTION']['SECTION_PAGE_URL']?>">Посмотрите другие модели</a>
											</div>
										<?}?>
									</div>
								</div>
								<div class="emulate-row">

								</div>
							</div>
						</div>

						<div class="option-table emulate-table full">
							<?$color = $arResult['DISPLAY_PROPERTIES']['TSVET_MARKETING']['DISPLAY_VALUE'];?>
							<?if ($color != ''):?>
								<div class="emulate-row">
									<div class="emulate-cell">Цвет</div>
									<div class="emulate-cell"><?=$color?></div>
								</div>
							<?endif?>
							<?foreach ($arResult['DISPLAY_PROPERTIES'] as $key => $arProp):?>
								<?if ($key != 'TSVET_MARKETING'):?>
								<?if(($key == 'VYSOTA_KABLUKA' && $arProp['VALUE'] == 0)||($key == 'VYSOTA_GOLENISHCHA_PROIZVODSTVO' && $arProp['VALUE'] == 0)) continue;?>
									    <div class="emulate-row">
    										<div class="emulate-cell"><?=$arProp['NAME']?></div>
    										<div class="emulate-cell"><?=\Citfact\Tools::my_mb_ucfirst($arProp['VALUE'])?></div>
    									</div>
								<?endif?>
							<?endforeach?>
						</div>

						<?if(!empty($arResult['DETAIL_TEXT'])):?>
							<div class="detail-text">
								<?if (strlen($arResult['DETAIL_TEXT']) > 400):?>
								<div class="read-more-wrap">
									<div class="read-more-text-wrap">
										<div class="read-more-text" itemprop="description">
											<?=$arResult['DETAIL_TEXT']?>
										</div>
									</div>
									<a class="get-modal read-more">Читать далее</a>
								</div>
								<?else:?>
									<div itemprop="description"><?=$arResult['DETAIL_TEXT']?></div>
								<?endif?>
							</div>
						<?endif?>
					</div>

					<div role="tabpanel" class="tab-pane fade" id="c2">
						<?$frame = $this->createFrame()->begin();?>
						<?if (!empty($arResult['SHOPS'])):?>
							<div class="emulate-table shop-store-wrap full">
							<?foreach ($arResult['SHOPS'] as $arShop):?>
								<?$arStore = $arResult['STORES_AMOUNT'][$arShop['PROPERTY_STORE_ID_VALUE']];
								$address = $arShop['PROPERTY_ADDRESS_VALUE'];
								$graphick = $arShop['PROPERTY_GRAPHICK_VALUE']['TEXT'];
								$phone = $arShop['PROPERTY_PHONE_VALUE'];
								?>
								<?//if (!empty($arStore)):?>
								<div class="shop-store emulate-row">
									<div class="emulate-cell shop-cell valign-top">
										<div class="action-modal-wrap hover-mod">
											<div class="shop-name get-modal"><?=$arShop['NAME']?></div>
											<div class="action-modal position-right detail-size-info">
												<div class="title"><?=$arShop['NAME']?></div>
												<ul>
													<li><i class="fa fa-map-marker"></i> <?=$address?><br><a target="_blank" href="/shops/<?=$arShop['PROPERTY_CITY_CODE']?>/<?=$arShop['CODE']?>/">Посмотреть на карте</a></li>
													<?if ($graphick != ''):?><li><i class="fa fa-clock-o"></i> <?=$graphick?></li><?endif;?>
													<?if ($phone != ''):?><li><i class="fa fa-phone"></i> <?=$phone?></li><?endif;?>
												</ul>
											</div>
										</div>
									</div>
									<div class="emulate-cell valign-top">
                                        <? if (!empty($arStore)): ?>
                                            <?
                                            $sizes = [];
                                            foreach ($arStore as $offer_id) {
                                                $sizes[] = $arResult['SIZES'][$offer_id];
                                            }
                                            $sizes = array_filter($sizes);
                                            sort($sizes, SORT_NUMERIC);
                                            ?>
                                            <? if (!empty($sizes)): ?>
                                                <? foreach ($sizes as $size): ?>
                                                    <span class="size"><?= $size ?></span>
                                                <? endforeach; ?>
                                            <? else: ?>
                                                <span class="size">Есть в наличии</span>
                                            <? endif; ?>
                                        <? else: ?>
                                            <span class="size">Уточняйте наличие в магазине</span>
                                        <? endif; ?>
									</div>
								</div>
								<?//endif?>
							<?endforeach;?>
								</div>
						<?else:?>
                            <div class="no-shops-msg">
                                Вы можете заказать данную модель в нашем интернет-магазине с доставкой до ближайшего пункта самовывоза или курьером до дома.<br>
                                Подробности о доставке — <a href="/help/oplata-i-dostavka" target="_blank">по ссылке</a>.<br>
                                Рассчитать стоимость доставки можно в корзине перед оформлением заказа.<br>
                                При онлайн-оплате — <a href="/events/besplatnaya-dostavka/" target="_blank">бесплатная доставка!</a>
                            </div>
						<?endif;?>
						<?$frame->end();?>
					</div>
					<div role="tabpanel" class="tab-pane fade tab_table_size" id="c3">
						<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array("AREA_FILE_SHOW" => "file","PATH" => "/include/catalog_element_table_size.php"));?>
					</div>
					<div role="tabpanel" class="tab-pane fade tab_delivery" id="c4">
						<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array("AREA_FILE_SHOW" => "file","PATH" => "/include/catalog_element_delivery.php"));?>
					</div>
				</div>
			</div>
		</div>
<!--
        <noindex>
            <? if(isset($arResult['navBar'][0]) && $arResult['navBar'][0]['USE'] == 'Y' && $arResult['navBar'][0]['CURRENT'] != 'current'): ?>
            <a href="<?=$arResult['navBar'][0]['URL']?>?<?=$sort_url?>" class="prevItems">
                Предыдущий товар
            </a>
            <? endif; ?>
            <? if(isset($arResult['navBar'][2]) && $arResult['navBar'][2]['USE'] == 'Y' && $arResult['navBar'][2]['CURRENT'] != 'current'): ?>
                <a href="<?=$arResult['navBar'][2]['URL']?>?<?=$sort_url?>" class="nextItems">
                    Следующий товар
                </a>
            <? endif; ?>
        </noindex>
-->
	</div>
</div>

<div class="container"><div data-retailrocket-markup-block="567292de9872e52a3cbd9a56"  data-product-id="<?=$arResult['ID']?>"></div></div>
<? // Похожие модели
/*
if(!empty($arResult['OTHER_MODELS'])):?>
	<?$frame = $this->createFrame()->begin();?>
	<div class="detail-add-wrap">
		<div class="container">
			<div class="title-add">
				Похожие модели
			</div>
			<div class="add-slider-wrap">
				<ul class="add-slider">
					<? foreach ($arResult['OTHER_MODELS'] as $arModel){ ?>
						<li>
							<a href="<?=$arModel['DETAIL_PAGE_URL']?>">
								<div class="image">
									<?if(!empty($arModel['IMAGES']['PHOTO'][0]['SMALL'])):?>
										<img src="<?=$arModel['IMAGES']['PHOTO'][0]['SMALL']?>" alt="<?=$arModel['NAME']?>">
									<?else:?>
										<img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
									<?endif?>
								</div>
								<div class="price-box">
									<?if ($arModel['NEW_PRICE'] != ''):?>
										<div class="old-price">
											<?=CurrencyFormat($arModel['OLD_PRICE'], 'RUB')?>
										</div>
										<div class="new-price">
											<?=CurrencyFormat($arModel['NEW_PRICE'], 'RUB')?>
										</div>
									<?else:?>
										<div class="new-price">
											<?=CurrencyFormat($arModel['OLD_PRICE'], 'RUB')?>
										</div>
									<?endif;?>
								</div>
							</a>
						</li>
					<? } ?>
				</ul>
			</div>
		</div>
	</div>
	<?$frame->end();?>
<?endif*/?>

<? // Похожие модели
/*$APPLICATION->IncludeComponent(
	"bitrix:sale.recommended.products",
	"catalog",
	Array(
		"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
		"IBLOCK_ID" => $arParams['IBLOCK_ID'],
		"ID" => $arResult["ID"],
		"CODE" => "",
		"MIN_BUYES" => "1",
		"HIDE_NOT_AVAILABLE" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"PRODUCT_SUBSCRIPTION" => "N",
		"SHOW_NAME" => "Y",
		"SHOW_IMAGE" => "Y",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"PAGE_ELEMENT_COUNT" => "12",
		"LINE_ELEMENT_COUNT" => "3",
		"TEMPLATE_THEME" => "blue",
		"DETAIL_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "86400",
		"SHOW_OLD_PRICE" => "N",
		"PRICE_CODE" => $arParams['PRICE_CODE'],
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"CONVERT_CURRENCY" => "N",
		"BASKET_URL" => $arParams['BASKET_URL'],
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"USE_PRODUCT_QUANTITY" => "Y",
		"PROPERTY_CODE_10" => array(0 => "CML2_ARTICLE"),
	)
);*/?>

<? // Вы посмотрели?>
<?$frame = $this->createFrame()->begin();?>
<?$APPLICATION->IncludeComponent(
	"bitrix:catalog.viewed.products",
	"catalog",
	Array(
		"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
		"IBLOCK_ID" => $arParams['IBLOCK_ID'],
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"SECTION_ELEMENT_ID" => "",
		"SECTION_ELEMENT_CODE" => "",
		"DEPTH" => "",
		"DETAIL_URL" => "",
		"SHOW_FROM_SECTION" => "N",
		"HIDE_NOT_AVAILABLE" => "N",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"PRODUCT_SUBSCRIPTION" => "N",
		"SHOW_NAME" => "N",
		"SHOW_IMAGE" => "N",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"PAGE_ELEMENT_COUNT" => "",
		"LINE_ELEMENT_COUNT" => "",
		"TEMPLATE_THEME" => "blue",
		"CACHE_TYPE" => "N",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "N",
		"SHOW_OLD_PRICE" => "Y",
		"PRICE_CODE" => $arParams['PRICE_CODE'],
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"CONVERT_CURRENCY" => "N",
		"BASKET_URL" => $arParams['BASKET_URL'],
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"USE_PRODUCT_QUANTITY" => "N",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"SHOW_PRODUCTS_10" => "Y",
		"PROPERTY_CODE_10" => array(0 => "CML2_ARTICLE"),
	)
);?>
<?$frame->end();?>

<div class="detail-add-wrap" id="go_review_block">
	<div class="container">
		<div class="block-title align-left">
			<h4>Отзывы о товаре</h4>
		</div>
		<div class="reviews-wrap">
			<a href="#" class="get-review btn btn-cell big mode1 fa fa-pencil addProductReviewLink" data-toggle="modal" data-target="#productReviewModal_<?=$arResult['ID']?>" data-item-id="<?=$arResult['ID']?>" data-item-name="<?=urlencode($arResult['NAME'])?>" data-item-link="<?=urlencode($arResult['DETAIL_PAGE_URL'])?>" data-item-src="<?=urlencode($productPhoto)?>">
				<span>Написать отзыв</span>
			</a>
			<?if(!empty($arResult['REVIEWS'])):?>
				<?foreach($arResult['REVIEWS'] as $reviewItem) { ?>
					<div class="item">
						<div class="styled-text-box">
							<div class="title">
								<?=$reviewItem['USERNAME']?> <span class="time-box"><?=$reviewItem['DATE_CREATE']?></span>
								<div class="stars-block">
									<?$class = 'star-el';
									for($i=1; $i<=$maxCountStars; $i++){
										if (($i-1)==$reviewItem['PROPERTY_STARS_VALUE']) {
											$class .= ' not-checked';
										}?>
										<span class="<?=$class;?>"><?=$i;?></span>
									<?}?>
								</div>
							</div>
							<div class="tite-desc">
								<?=$reviewItem['PROPERTY_MESSAGE_VALUE']?>
							</div>
						</div>
					</div>
				<? } ?>
			<?else:?>

			<?endif?>
			<div class="item">
				<div class="styled-text-box">
					<div class="title align-center">Оставьте отзыв о купленном товаре и получите купон на 100 рублей!</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade fastOrderModal" id="fastOrderModal_<?=$arResult['ID']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal"></button>
			<div class="modal-body">
				<div class="fast-order-wrap emulate-table full">
					<?$APPLICATION->IncludeComponent("citfact:form.ajax", "fast_order", Array(
						"IBLOCK_ID" => IBLOCK_FAST_ORDER,
						"SHOW_PROPERTIES" => array(
							"USERNAME" => array(
								"type" => "text",
								"placeholder" => "Введите ваше имя",
								"required" => "Y",
								"class" => 'required'
							),
							"USERPHONE" => array(
								"type" => "text",
								"placeholder" => "Введите ваш телефон",
								"required" => "Y",
								"class" => 'mask-phone required'
							),
							"PRODUCT_NAME" => array(
								"type" => "hidden",
								"placeholder" => "Наименование товара",
								"required" => "Y",
								"value" => $arResult['NAME'],
								'id' => 'input_name_product_'.$arResult['ID']
							),
							"PRODUCT_ID" => array(
								"type" => "hidden",
								"placeholder" => "Id товара",
								"required" => "Y",
								"value" => $arResult['ID'],
								'id' => 'input_id_product_'.$arResult['ID']
							),
							"PRICE_CODES" => array(
								"type" => "hidden",
								"required" => "N",
								"value" => implode(', ', $_SESSION['GEO_PRICES']['TO_PARAMS'])
							),
							"CURRENT_PRICE" => array(
								"type" => "hidden",
								"required" => "N",
								"value" => (!empty($arResult['NEW_PRICE'])? $arResult['NEW_PRICE']:$arResult['OLD_PRICE'])
							),
							"PRICE" => array(
								"type" => "hidden",
								"required" => "N",
								"value" => (!empty($arResult['NEW_PRICE'])? str_replace(" ","",substr($arResult['NEW_PRICE'],0,-5)) : str_replace(" ","",substr($arResult['OLD_PRICE'],0,-5)))
							),
						),
						"EVENT_NAME" => "FAST_ORDER_FORM",
						"SUCCESS_MESSAGE" => "Ваша заявка принята. Мы перезвоним вам в ближайшее время.",
						"ELEMENT_ACTIVE" => "Y",
						"PRODUCT_IMAGE" => $productPhoto
					),
						false
					);?>
				</div>
			</div>
		</div>
	</div>
</div>

<?//global $USER;?>
<div class="modal fade productReviewModal <?/*if(!$USER->IsAuthorized()):?>isauth<?endif*/?>" id="productReviewModal_<?=$arResult['ID']?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<?// Грузим сюда аяксом /include/ajax_product_review_popup.php ?>
		</div>
	</div>
</div>
<?
$pat="";
foreach($arResult["SECTION"]["PATH"] as $path){
	$pat.=$path["NAME"]."/";
}
$pat=substr($pat,0,-1);
?>
<script type="text/javascript">
BX.message({
	ECONOMY_INFO_MESSAGE: '<? echo GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO'); ?>',
	BASIS_PRICE_MESSAGE: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_BASIS_PRICE') ?>',
	TITLE_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR') ?>',
	TITLE_BASKET_PROPS: '<? echo GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS') ?>',
	BASKET_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR') ?>',
	BTN_SEND_PROPS: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS'); ?>',
	BTN_MESSAGE_BASKET_REDIRECT: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT') ?>',
	BTN_MESSAGE_CLOSE: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE'); ?>',
	BTN_MESSAGE_CLOSE_POPUP: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP'); ?>',
	TITLE_SUCCESSFUL: '<? echo GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK'); ?>',
	COMPARE_MESSAGE_OK: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK') ?>',
	COMPARE_UNKNOWN_ERROR: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR') ?>',
	COMPARE_TITLE: '<? echo GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE') ?>',
	BTN_MESSAGE_COMPARE_REDIRECT: '<? echo GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT') ?>',
	SITE_ID: '<? echo SITE_ID; ?>',

	BUY_URL: '<?=$arResult['ADD_URL_TEMPLATE']?>'
});

window.dataLayer = window.dataLayer || [];
<?
$pricePush = 0;
if (!empty($arResult["CATALOG_PRICE_2"])) {
    $pricePush = $arResult["CATALOG_PRICE_2"];
} elseif (!empty($arResult["NEW_PRICE"])) {
    $pricePush = str_replace(" ","",substr($arResult["NEW_PRICE"],0,-5));
} elseif (!empty($arResult["OLD_PRICE"])) {
    $pricePush = str_replace(" ","",substr($arResult["OLD_PRICE"],0,-5));;
}?>
dataLayer.push({
    "event":"detail",
    "ecommerce": {
        "detail": {
            "products": [
                {
                    "id": "<?=$arResult["ID"]?>",
                    "name" : "<?=$arResult["NAME"]?>",
                    "price": <?=$pricePush;?>,
                    "category": "<?=$pat?>"
                }
            ]
        }
    }
});
</script>
