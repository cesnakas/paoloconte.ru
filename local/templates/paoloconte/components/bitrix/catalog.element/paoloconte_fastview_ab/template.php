
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

//_c($arResult);

CJSCore::Init();

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
<script type="text/javascript">
	rrApiOnReady.push(function() {
		try{ rrApi.view(<?=$arResult['ID']?>); } catch(e) {}
	})
</script>
<button type="button" class="close" data-dismiss="modal"></button>
<div class="modal-body">
	<div class="detail-wrap clear-after" id="<? echo $arItemIDs['ID']; ?>">
		<div class="detail-item-image-wrap">
			<div class="detail-item-image-color-item">
				<span class="to-favorite label-icon" data-image="<?=$productPhoto?>" data-product-id="<?=$productIdToFav?>" data-text="Добавить в список желаний" data-toggle="modal">
					<i class="fa fa-heart"></i>
					<i class="fa active fa-heart-o"></i>
					<div class="label-icon-text">Добавить в список желаний</div>
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
									<div class="catalog_image_360_fastview" data-images="<?=$images360?>"></div>
								</div>
							</li>
						<?endif?>
						<?if(!empty($arResult['CATALOG_IMG']['PHOTO'])):?>
							<?foreach($arResult['CATALOG_IMG']['PHOTO'] as $photo) { ?>
								<li class="emulate-table full">
									<div class="emulate-cell valign-bottom image">
										<img src="<?=$photo['BIG']?>" alt="<?=$arResult['NAME']?>" class="silder_image elevate-zoom" data-zoom-image="<?=$photo['BIGGEST']?>">
									</div>
								</li>
							<? } ?>
						<?else:?>
							<li class="emulate-table full">
								<div class="emulate-cell valign-bottom image">
									<img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
								</div>
							</li>
						<?endif?>
					</ul>
				</div>

				<?$countPhotos = count($arResult['CATALOG_IMG']['PHOTO'])+count($arResult['CATALOG_IMG']['360']);?>
				<?if((!empty($arResult['CATALOG_IMG']['PHOTO']) || !empty($arResult['CATALOG_IMG']['360'])) && $countPhotos > 1):?>
					<div class="detail-item-small-wrap">
						<ul class="detail-item-small alt">
							<?if(!empty($arResult['CATALOG_IMG']['360'])):?>
								<li class="image">
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

		<div class="detail-item-text-wrap">
			<div class="item-title main-title pts-bold">
				<h1><a href="<?=$arResult['DETAIL_PAGE_URL']?>"><?=$arResult['NAME']?></a></h1>
				<div class="rating_stars">
					<?for($i=1; $i<=5; $i++):?>
						<?$checked = ($i==$arResult['RATING'])? 'checked' : '';?>
						<input type="radio" title="<?=$i?>" value="<?=$i?>" class="star" disabled="disabled" <?=$checked?>/>
					<?endfor?>
				</div>
			</div>


			<div class="tab-box" role="tabpanel">
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active"><a href="#c1" aria-controls="c1" role="tab" data-toggle="tab">ОПИСАНИЕ</a></li>
					<li role="presentation"><a href="#c2" aria-controls="c2" role="tab" data-toggle="tab">НАЛИЧИЕ В МАГАЗИНЕ</a></li>
					<li role="presentation"><a href="#c3" aria-controls="c3" role="tab" data-toggle="tab" id="go_sizes_table_link">ТАБЛИЦА РАЗМЕРОВ</a></li>
					<li role="presentation"><a href="#c4" aria-controls="c4" role="tab" data-toggle="tab">ДОСТАВКА</a></li>
				</ul>

				<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade in active" id="c1">
						<div class="top-line">
							<div class="price-box">
								<?if(!empty($arResult['NEW_PRICE'])):?>
									<div class="old-price old-price-ab rouble">
										<?=substr($arResult['OLD_PRICE'],0,-5);?>
									</div>
									<div class="new-price new-price-ab rouble">
										<?=substr($arResult['NEW_PRICE'],0,-5);?>
									</div>
								<?else:?>
									<div class="new-price rouble">
										<?=substr($arResult['OLD_PRICE'],0,-5);?>
									</div>
								<?endif;?>
							</div>
							<div class="right-action">
								<div class="action fast-view">
									<div class="action-modal-wrap">
										<i class="fa fa-comments"></i> <a href="#" class="get-modal">Отзыв о товаре</a>
										<div class="action-modal">
												<div class="modal-content">
													<button type="button" class="close" data-dismiss="modal"></button>
													<?
													global $USER;
													if(!$USER->IsAuthorized()):
														?>
														<div class="modal-body">
															<div class="modal-title">
																Оставьте отзыв и получите купон на скидку
															</div>
															<?$APPLICATION->IncludeComponent(
																"citfact:authorize.ajax",
																"popup",
																Array(
																	"REDIRECT_TO" => $arParams['BACK_URL'],
																	"FORM_ID" => 'reviews'
																)
															);?>
														</div>
														<div class="modal-body bg">
															<div class="line clear-after">
																<div class="float-left">
																	<a href="/forgotpassword/">
																		Забыли пароль?
																	</a>
																</div>
																<a href="/register/" class="float-right link">Регистрация</a>
															</div>
														</div>
													<?else:?>
														<div class="modal-body">
															<div class="modal-title">
																Оставьте отзыв и получите купон на скидку
															</div>
															<?$APPLICATION->IncludeComponent("citfact:form.ajax", "product_review_fastview", Array(
																"IBLOCK_ID" => IBLOCK_PRODUCT_REVIEW,
																"SHOW_PROPERTIES" => array(
																	"USER_ID" => array(
																		"type" => "hidden",
																		"required" => "Y",
																		"value" => $USER->GetID(),
																	),
																	"USER_NAME" => array(
																		"type" => "hidden",
																		"required" => "Y",
																		"value" => $USER->GetFullName(),
																	),
																	"USER_EMAIL" => array(
																		"type" => "hidden",
																		"required" => "Y",
																		"value" => $USER->GetEmail(),
																	),
																	"PRODUCT_ID" => array(
																		"type" => "hidden",
																		"class" => '',
																		"value" => $arResult['ID'],
																	),
																	"PRODUCT_IMAGE" => array(
																		"type" => "img",
																		"value" => (!empty($arResult['CATALOG_IMG']['PHOTO'][0]))?$arResult['CATALOG_IMG']['PHOTO'][0]['BIG']:$arResult['NOPHOTO'],
																	),
																	"PRODUCT_NAME" => array(
																		"type" => "ready",
																		"value" => $arResult['NAME'],
																	),
																	"PRODUCT_LINK" => array(
																		"type" => "hidden",
																		"value" => $arResult['DETAIL_PAGE_URL'],
																	),
																	"MESSAGE" => array(
																		"type" => "textarea",
																		"class" => 'required message_type',
																		"placeholder" => 'Опишите ваши впечатления от данной модели. Нам важно знать ваше мнение',
																		"error" => 'Слишком короткий отзыв. Хотелось бы подробностей.',
																	),
																	"STARS" => array(
																		"type" => "stars",
																		"class" => 'star',
																		"value" => 5,
																	),
																	"SUBSCRIBE" => array(
																		"type" => "checkbox",
																		"value" => 'ДА',
																	),
																),
																"EVENT_NAME" => "PRODUCT_REVIEW_FORM",
																"SUCCESS_MESSAGE" => "Спасибо, ваш отзыв принят и появится на сайте после проверки модератором. После этого вы получите на почту письмо с кодом купона.",
																"ELEMENT_ACTIVE" => "N",
																"USER_NAME" => $USER->GetFullName(),
																"USER_EMAIL" => $USER->GetEmail(),
															),
																false
															);?>
														</div>
													<?endif;?>
												</div>
										</div>
									</div>
								</div>

								<div class="action fast-view">
									<div class="action-modal-wrap">
										<i class="fa fa-check-circle"></i> <a href="#" class="get-modal">Узнать о снижении цены</a>

										<div class="action-modal">
											<?$user_email = $USER->GetEmail();?>
											<?$APPLICATION->IncludeComponent("citfact:form.ajax", "subscribe_price_fastview", Array(
												"IBLOCK_ID" => 36,
												"SHOW_PROPERTIES" => array(
													"TOVAR_ID" => array(
														"type" => "hidden",
														"required" => "Y",
														"value" => $arResult['ID']
													),
													"CITY_ID" => array(
														"type" => "hidden",
														"required" => "Y",
														"value" => $_SESSION['CITY_ID']
													),
													"USER_ID" => array(
														"type" => "hidden",
														"value" => $USER->GetID()
													),
													"EMAIL" => array(
														"type" => "text",
														"placeholder" => "Введите ваш email",
														"required" => "Y",
														"value" => ($user_email!=''? $user_email:''),
													),
													"PRICE" => array(
														"type" => "text",
														"placeholder" => "Введите цену",
														"required" => "Y",
														"value" => '',
													),
													"SUBSCRIBE" => array(
														"type" => "checkbox",
														"value" => 'Y',
													),
												),
												"EVENT_NAME" => "SUBSCRIBE_PRICE_FORM",
												"SUCCESS_MESSAGE" => "Ваша заявка на подписку принята.",
												"ELEMENT_ACTIVE" => "Y",
												"CHECK_EQUAL_PROPS" => array('EMAIL', 'TOVAR_ID')
											),
												false
											);?>
										</div>
									</div>
								</div>
							</div>
						</div>



						<div class="item-bottom-box fast-view">
							<div class="emulate-table full bottom-options">
								<div class="emulate-row">
									<div class="emulate-cell">
										<?$color = $arResult['DISPLAY_PROPERTIES']['TSVET_MARKETING']['DISPLAY_VALUE'];?>
										<?if ($color != ''):?>
											<div>Цвет: <span class="color-name"><?=$color?></span></div>
										<?endif?>
										<?if(!empty($arResult['OTHER_COLORS'])):?>
											<div class="color-box">
												<?foreach ($arResult['OTHER_COLORS'] as $arColor):?>
													<?$active = ($arColor['CODE'] == $arResult['CODE'])? true : false;?>
													<a href="<?if($active):?>javascript:void(0)<?else: print $arColor['DETAIL_PAGE_URL']; endif?>" <?if($active):?>class="active"<?endif?> title="<?=$arColor['COLOR']['NAME']?>" style="background-image: url('<?=$arColor['COLOR']['FILE_PATH']?><?//=$arColor['IMAGES']['PHOTO'][0]['SMALL']?>')"></a>
												<?endforeach?>
											</div>
										<?endif?>

										<?if (!empty($arResult['OFFERS'])):?>
											<div>Размер:</div>
											<div class="size-box" id="<? echo $arItemIDs['PROP_DIV'];?>">
												<?
												$offerId = '';
												$i=0;
												foreach ($arResult['OFFERS'] as $key => $arOffer):?>
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
													<?$i++; endforeach?>
											</div>
										<?endif?>

										<?/*<div class="get-size">
											<a href="#go_sizes_table" class="get-modal" data-click="true">Как узнать размер</a> <span>?</span>
										</div>*/?>

									</div>

									<div class="emulate-cell btn-box">
										<a href="#" class="btn btn-green fa mode1 fa-shopping-cart btn-tobasket <?if($arParams['RELOAD_PAGE_2_BASKET'] == 'Y'){?>reload_page_2_basket<?}?>" onmousedown="try { rrApi.addToBasket(<?=$arResult['ID']?>) } catch(e) {}" data-product-id="<?=$productId?>" <?if($arParams['RELOAD_PAGE_2_BASKET'] !== 'Y'){?>data-type="getMovedModalPanel" data-target="#side-cart"<?}?> data-product-name="<?=$arResult['NAME']?>" data-product-price="<?=$arResult['CATALOG_PRICE_5']?$arResult['CATALOG_PRICE_5']:$arResult['CATALOG_PRICE_2']?>" ><span>Добавить в корзину</span></a>
										<br/>
										<a href="#" onmousedown="try { rrApi.addToBasket(<?=$arResult['ID']?>) } catch(e) {}" class="btn btn-gray-dark fa mode1 fa-phone fast-order" data-product-id="<?=$productId?>" data-toggle="modal" data-target="#fastOrderModal_<?=$arResult['ID']?>"><span>Быстрый заказ</span></a>
										<br/>
										<div class="social-box yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareQuickServices="vkontakte,facebook,twitter"></div>
										<script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>
									</div>

								</div>
								<div class="emulate-row">

								</div>
							</div>
						</div>

						<div class="option-table emulate-table full">
							<div class="option-table emulate-table full">
								<? if (!empty($arResult["DETAIL_TEXT"])) { ?>
									<div class="emulate-row">
										<div class="emulate-cell">Описание</div>
										<div class="emulate-cell"><?=$arResult["DETAIL_TEXT"]?></div>
									</div>
								<? } ?>
								<?/*<div class="emulate-row">
									<div class="emulate-cell">
										Рейтинг
									</div>
									<div class="emulate-cell">
										<div class="rating_stars">
											<?for($i=1; $i<=5; $i++):?>
												<?$checked = ($i==$arResult['RATING'])? 'checked' : '';?>
												<input type="radio" title="<?=$i?>" value="<?=$i?>" class="star" disabled="disabled" <?=$checked?>/>
											<?endfor?>
										</div>
									</div>
								</div>*/?>
								<?foreach ($arResult['DISPLAY_PROPERTIES'] as $key => $arProp):?>
									<?if ($key != 'TSVET_MARKETING'):?>
										<?if(($key == 'VYSOTA_KABLUKA' && $arProp['VALUE'] == 0)||($key == 'VYSOTA_GOLENISHCHA_PROIZVODSTVO' && $arProp['VALUE'] == 0)||($key == 'KOLODKA')) continue;?>
									<div class="emulate-row">
										<div class="emulate-cell"><?=$arProp['NAME']?></div>
										<div class="emulate-cell"><?=$arProp['VALUE']?></div>
									</div>
									<?endif?>
								<?endforeach?>
							</div>
						</div>

					</div>

					<div role="tabpanel" class="tab-pane fade" id="c2">
						<?if (!empty($arResult['SHOPS'])):?>
							<div class="emulate-table shop-store-wrap full">
								<?foreach ($arResult['SHOPS'] as $arShop):?>
									<?$arStore = $arResult['STORES_AMOUNT'][$arShop['PROPERTY_STORE_ID_VALUE']];
									$address = $arShop['PROPERTY_ADDRESS_VALUE'];
									$graphick = $arShop['PROPERTY_GRAPHICK_VALUE']['TEXT'];
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
														<li><i class="fa fa-clock-o"></i> <?=$graphick?></li>
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
								"class" => 'TEST required'
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


<script>
	//проверка на избранное CHECK_FAVORITE
	function checkFavorite(){
		var products_ids = {};
		var products_favorite = $(document).find(".to-favorite");
		products_favorite.each(function(i){
			var prod_id = $(this).data('product-id');
			if(prod_id !='' && prod_id !=undefined)
				products_ids[i]={'ID':prod_id};
		});
		if(Object.keys(products_ids).length >0){
			var obj = {
				TYPE: "CHECK_FAVORITE",
				products_id: products_ids
			};
			$.post("/include/ajax_handler.php", obj)
				.done(function(outData){
					var result = JSON.parse(outData);
					if (result.status) {
						for (i in result.products) {
							if(result.products[i].FAVORITE == 'Y') {
								$(document).find('.to-favorite[data-product-id="' + result.products[i].ID + '"]')
									.attr('data-favorite-id', result.products[i].BASKET_ID)
									.addClass("add-in-favorite")
									.find("i").each(function () {
										if ($(this).hasClass("fa-heart-o")) {
											$(this).addClass('active').fadeOut(300);
										}
										else {
											$(this).removeClass('active').fadeIn(300);
										}
									});
							}
							else{
								$(document).find('.to-favorite[data-product-id="' + result.products[i].ID + '"]')
									.attr('data-favorite-id', result.products[i].BASKET_ID)
									.removeClass("add-in-favorite")
									.find("i").each(function () {
										if ($(this).hasClass("fa-heart")) {
											$(this).addClass('active').fadeOut(300);
										}
										else {
											$(this).removeClass('active').fadeIn(300);
										}
									});
							}
						}
					}
				});
		}
	}

	$(document).ready(function () {

		//изменение id товара в зависимости от выбраного размера
		$(document).on('change', 'input.radio-offer', function() {
			var productId = $(this).data('id');
			var productName = $(this).data('name');
			//console.log(productId+' '+productName);
			$(document).find('#input_id_product_'+productId).val(productId);
			$(document).find('#input_name_product_'+productId).val(productName);
			$('.btn-tobasket').data('product-id', $(this).val());

			$('.to-favorite').data('product-id', $(this).val());
			checkFavorite();
		});

		//добавление и удаление из избранного ADD_FAVORITE DEL_FAVORITE
		$(document).on("click", ".to-favorite", function () {
			var that = this;
			var image = $(this).data('image');
			if($(this).hasClass('add-in-favorite')){
				var product_id = $(this).attr('data-favorite-id');
				var obj = {
					TYPE: "DEL_FAVORITE",
					product_id: product_id
				};
			}
			else{
				var product_id = $(this).data('product-id');
				var obj = {
					TYPE: "ADD_FAVORITE",
					product_id: product_id
				};
			}
			if (product_id != '') {
				overlay.show();
				$.post("/include/ajax_handler.php", obj)
					.done(function (outData) {
						var result = JSON.parse(outData);
						if (result.status) {
							BX.onCustomEvent('OnBasketChange');
							checkFavorite();
							if (!$(that).hasClass("add-in-favorite")) {
								$('#toFavoriteModal').find('img').attr('src', image);
								$('#toFavoriteModal').modal('show');
							}
						}
						else {
							console.log("ОШИБКА! - " + result.error);
						}
					})
					.complete(function () {
						overlay.hide();
					});
			}
		});

		//проверка на избранное CHECK_FAVORITE
		checkFavorite();
	});
</script>
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
<?
$pricePush = 0;
if (!empty($arResult["CATALOG_PRICE_2"])) {
    $pricePush = $arResult["CATALOG_PRICE_2"];
} elseif (!empty($arResult["NEW_PRICE"])) {
    $pricePush = str_replace(" ","",substr($arResult["NEW_PRICE"],0,-5));
} elseif (!empty($arResult["OLD_PRICE"])) {
    $pricePush = str_replace(" ","",substr($arResult["OLD_PRICE"],0,-5));;
}?>
window.dataLayer = window.dataLayer || [];
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