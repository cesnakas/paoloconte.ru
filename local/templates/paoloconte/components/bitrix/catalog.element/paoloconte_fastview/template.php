
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
$name = (!empty($arResult['PROPERTIES']['NAIMENOVANIE_MARKETING']['VALUE']))
    ? $arResult['PROPERTIES']['NAIMENOVANIE_MARKETING']['VALUE']
    : $arResult["NAME"];

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

$setRRPrice = 0;
if (!empty($arResult['NEW_PRICE'])) {
    $setRRPrice = str_replace(" ", "", $arResult['NEW_PRICE']);
} elseif (!empty($arResult['OLD_PRICE'])) {
    $setRRPrice = str_replace(" ", "", $arResult['OLD_PRICE']);
}
$setRRPrice = preg_replace("/\D*/", "", $setRRPrice);
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
            "price": <?=$setRRPrice ?>,
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
            "oldPrice": <?=preg_replace("/\D*/", "", $arResult['OLD_PRICE'])?>,
            <?}else{?>
           "oldPrice": 0,
            <?}?>
            <?if(!empty($arResult['OTHER_COLORS'][$arResult['ID']]['COLOR']['NAME'])){?>
            "color": "<?=$arResult['OTHER_COLORS'][$arResult['ID']]['COLOR']['NAME']?>",
            <?}?>
        })
        rrApi.view(<?=$arResult['ID']?>);
    });
</script>
<button type="button" class="close" data-dismiss="modal"></button>
<div class="modal-body">

	<div class="product product--modal" id="<? echo $arItemIDs['ID']; ?>">
        <div class="product__inner">
            <div class="product-slider">

                <div class="tag">
                    <?if ($arResult['PROPERTIES']['HIT']['VALUE'] != ''):?><span class="tag__item tag__item--hit">HIT</span><?endif?>
                    <?if ($arResult['PROPERTIES']['NEW']['VALUE'] != ''):?><span class="tag__item tag__item--new">NEW</span><?endif?>
                    <?if (false && $arResult['PROPERTIES']['SALE']['VALUE'] != ''):?><span class="tag__item tag__item--sell"><?=$arResult['PROPERTIES']['SALE']['VALUE']?>%</span><?endif?>
                    <?if ($arResult['SALE_PERCENT'] > 0):?><span class="tag__item tag__item--sell"><?=$arResult['SALE_PERCENT']?>%</span><?endif?>
                </div>

                <div class="product-slider-small">
                    <div class="product-slider__arrow product-slider__arrow--prev" data-detail-preview-prev></div>
                    <div class="swiper-container" data-detail-preview>
                        <div class="swiper-wrapper">
                            <?$countPhotos = count($arResult['CATALOG_IMG']['PHOTO']) + count($arResult['CATALOG_IMG']['360']);?>
                            <?if((!empty($arResult['CATALOG_IMG']['PHOTO']) || !empty($arResult['CATALOG_IMG']['360'])) && $countPhotos > 1):?>
                                <?if(!empty($arResult['CATALOG_IMG']['360'])):?>
                                    <div class="swiper-slide" data-detail-preview-items>
                                        <img src="/local/templates/paoloconte/images/3d.png" alt="<?=$name?>">
                                    </div>
                                <?endif?>
                                <?$i = 0;
                                foreach($arResult['CATALOG_IMG']['PHOTO'] as $photo) {
                                    $i++;
                                    ?>
                                    <div class="swiper-slide" data-detail-preview-items <?= ($i == 1) ? 'data-src-first' : ''?>>
                                        <img src="<?=$photo['SMALL']?>" alt="<?=$name?>">
                                    </div>
                                <? } ?>
                            <?endif?>
                        </div>
                    </div>
                    <div class="product-slider__arrow product-slider__arrow--next" data-detail-preview-next></div>
                </div>

                <div class="product-slider-big swiper-container" data-detail-main>
                    <div class="swiper-wrapper">
                        <?if(!empty($arResult['CATALOG_IMG']['360'])):?>
                            <?
                            $images360 = '';
                            foreach($arResult['CATALOG_IMG']['360'] as $photo) {
                                $images360 .= $photo['BIG'].',';
                            }
                            $images360 = substr($images360,0,-1);
                            ?>
                            <div class="swiper-slide">
                                <div class="catalog_image_360_fastview" data-images="<?=$images360?>"></div>
                            </div>
                        <?endif?>
                        <?if(!empty($arResult['CATALOG_IMG']['PHOTO'])):?>
                            <?foreach($arResult['CATALOG_IMG']['PHOTO'] as $photo) { ?>
                                <div class="swiper-slide">
                                    <img src="<?=$photo['BIG']?>" alt="<?=$name?>" class="silder_image elevate-zoom" data-zoom-image="<?=$photo['BIGGEST']?>">
                                </div>
                            <? } ?>
                        <?else:?>
                            <div class="swiper-slide">
                                <img src="<?=$arResult['NOPHOTO']?>" alt="Нет фото">
                            </div>
                        <?endif?>
                    </div>
                </div>
            </div>

            <div class="product-info">
                <h1><a href="<?=$arResult['DETAIL_PAGE_URL']?>"><?=$name?></a></h1>

                <div class="rating_stars">
                    <?for($i=1; $i<=5; $i++):?>
                        <?$checked = ($i==$arResult['RATING'])? 'checked' : '';?>
                        <input type="radio" title="<?=$i?>" value="<?=$i?>" class="star" disabled="disabled" <?=$checked?>/>
                    <?endfor?>
                </div>

                <div class="product-price">
                    <?if(!empty($arResult['NEW_PRICE'])):?>
                        <div class="product-price__top">
                            <div class="product-price__old rouble">
                                <?=substr($arResult['OLD_PRICE'],0,-5);?>
                            </div>
                        </div>
                        <div class="product-price__bottom">
                            <div class="product-price__current rouble">
                                <?=substr($arResult['NEW_PRICE'],0,-5);?>
                            </div>
                    <?else:?>
                        <div class="product-price__bottom">
                            <div class="product-price__current rouble">
                                <?=substr($arResult['OLD_PRICE'],0,-5);?>
                            </div>
                    <?endif;?>
                            <div class="action-modal-wrap">
                                <a href="#" class="get-modal">Узнать о снижении цены</a>

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

                        </div> <?/* 'product-price__bottom' end */?>
                </div>



                <?$color = $arResult['DISPLAY_PROPERTIES']['TSVET_MARKETING']['DISPLAY_VALUE'];?>
                <?/*if ($color != ''):?>
                    <div>Цвет: <span class="color-name"><?=$color?></span></div>
                <?endif*/?>
                <?if(!empty($arResult['OTHER_COLORS'])):?>
                    <div class="product__color">
                        <?foreach ($arResult['OTHER_COLORS'] as $arColor):?>
                            <?$active = ($arColor['CODE'] == $arResult['CODE'])? true : false;?>
                            <a href="<?if($active):?>javascript:void(0)<?else: print $arColor['DETAIL_PAGE_URL']; endif?>" <?if($active):?>class="active"<?endif?> title="<?=$arColor['COLOR']['NAME']?>" style="background-image: url('<?=$arColor['COLOR']['FILE_PATH']?><?//=$arColor['IMAGES']['PHOTO'][0]['SMALL']?>')"></a>
                        <?endforeach?>
                    </div>
                <?endif?>

                <?if (!empty($arResult['OFFERS'])):?>
                    <div class="product-size">
                        <div class="product-size__top">
                            <div class="product-size__title">Размер:</div>
                        </div>
                        <div class="product-size__inner" id="<? echo $arItemIDs['PROP_DIV'];?>">
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
                                           data-name="<?=$name?> (<?=$arOffer['PROPERTIES']['RAZMER']['VALUE']?>)"
                                    >
                                </label>
                                <?$i++; endforeach?>
                        </div>
                    </div>
                <?endif?>

                    <?/*<div class="action-modal-wrap">
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
                </div>*/?>

                    <?/*<div class="option-table emulate-table full">
                        <div class="option-table emulate-table full">
                            <? if (!empty($arResult["DETAIL_TEXT"])) { ?>
                                <div class="emulate-row">
                                    <div class="emulate-cell">Описание</div>
                                    <div class="emulate-cell"><?=$arResult["DETAIL_TEXT"]?></div>
                                </div>
                            <? } ?>
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
                    </div>*/?>

                <div class="product__btns">
                    <a href="#"
                       class="btn btn--black btn-tobasket <?if($arParams['RELOAD_PAGE_2_BASKET'] == 'Y'){?>reload_page_2_basket<?}?>"
                       onmousedown="try { rrApi.addToBasket(<?=$arResult['ID']?>) } catch(e) {}"
                       data-product-id="<?=$productId?>" <?if($arParams['RELOAD_PAGE_2_BASKET'] !== 'Y'){?>
                        data-type="getMovedModalPanel"
                        data-target="#side-cart"<?}?>
                       data-product-name="<?=$name?>"
                       data-product-price="<?=$arResult['CATALOG_PRICE_5']?$arResult['CATALOG_PRICE_5']:$arResult['CATALOG_PRICE_2']?>" >
                        <span>В корзину</span>
                    </a>
                    <a href="#"
                       onmousedown="try { rrApi.addToBasket(<?=$arResult['ID']?>) } catch(e) {}"
                       class="btn btn--transparent fast-order"
                       data-product-id="<?=$productId?>"
                       data-toggle="modal"
                       data-target="#fastOrderModal_<?=$arResult['ID']?>">
                        <span>Быстрый заказ</span>
                    </a>
                </div>

                <div class="product__characteristics">
                    <?$color = $arResult['DISPLAY_PROPERTIES']['TSVET_MARKETING']['DISPLAY_VALUE'];?>
                    <?if ($color != ''):?>
                        <div>
                            <span>Цвет:</span>
                            <span><?=$color?></span>
                        </div>
                    <?endif?>
                    <?foreach ($arResult['DISPLAY_PROPERTIES'] as $key => $arProp):?>
                        <?if ($key != 'TSVET_MARKETING'):?>
                            <?if(($key == 'VYSOTA_KABLUKA' && $arProp['VALUE'] == 0)||($key == 'VYSOTA_GOLENISHCHA_PROIZVODSTVO' && $arProp['VALUE'] == 0)) continue;?>
                            <div>
                                <span><?=$arProp['NAME']?>:</span>
                                <span><?=\Citfact\Tools::my_mb_ucfirst($arProp['VALUE'])?></span>
                            </div>
                            <? if ($key == "MATERIAL_VERKHA_MARKETING" && !empty($arProp['VALUE'])) { ?>
                                <meta itemprop="material" content="<?= \Citfact\Tools::my_mb_ucfirst($arProp['VALUE']); ?>">
                            <? } ?>
                        <?endif?>
                    <?endforeach?>
                </div>



                <div class="product-links">
                    <span class="product-links__link label-icon to-favorite-new"
                          data-image="<?=$productPhoto?>"
                          data-product-id="<?=$productIdToFav?>"
                          data-text="Добавить в список желаний"
                          data-toggle="modal">
                        <svg class='i-icon'>
                            <use xlink:href='#heart'/>
                        </svg>
                    </span>
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
                <div class="title-1">
                    Быстрый заказ
                </div>
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
								"value" => $name,
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
		var products_favorite = $(document).find(".to-favorite-new");
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
								$(document).find('.to-favorite-new[data-product-id="' + result.products[i].ID + '"]')
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
								$(document).find('.to-favorite-new[data-product-id="' + result.products[i].ID + '"]')
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

			$('.to-favorite-new').data('product-id', $(this).val());
			checkFavorite();
		});

		//добавление и удаление из избранного ADD_FAVORITE DEL_FAVORITE
		$(document).on("click", ".to-favorite-new", function () {
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
    <script>
        window.dataLayer.push({
            'event': 'fireRemarketingTag',
            'google_tag_params': {
                'ecomm_prodid': '<?=$arResult['PROPERTIES']['CML2_ARTICLE']['VALUE']?>',
                'ecomm_pagetype': 'product',
                'ecomm_totalvalue': <?=$pricePush?>,
            }
        });
    </script>