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

<script>
	app.setPageTitle({"title" : "<?=CUtil::JSEscape(htmlspecialcharsback($arResult["NAME"]))?>"});
</script>

<div class="detail-wrap clear-after" id="<? echo $arItemIDs['ID']; ?>">
	<div class="title-r align-center">
		<h1><?
			echo (
			isset($arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != ''
				? $arResult["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"]
				: $arResult["NAME"]
			); ?></h1>
	</div>

	<div class="detail-item-big-wrap">
		<ul class="detail-item-big">
			<?if(!empty($arResult['CATALOG_IMG']['PHOTO'])):?>
				<?foreach($arResult['CATALOG_IMG']['PHOTO'] as $photo) { ?>
					<li class="emulate-table full">
						<div class="emulate-cell valign-middle image image-cont">
							<a class="fancybox-detail" rel="gallery_1" href="<?=$photo['BIGGEST']?>" onclick="return false;">
								<img src="<?=$photo['BIG']?>" alt="<?=$arResult['NAME']?>" class="silder_image">
							</a>
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

	<div class="price-box">
		<?if(!empty($arResult['NEW_PRICE'])):?>
			<div class="old-price rouble">
				<?=substr($arResult['OLD_PRICE'],0,-5);?>
			</div>
			<div class="new-price rouble">
				<?=substr($arResult['NEW_PRICE'],0,-5);?>
			</div>
		<?else:?>
			<div class="new-price rouble">
				<?=substr($arResult['OLD_PRICE'],0,-5);?>
			</div>
		<?endif;?>
	</div>

	<div class="align-center">
		<div class="action green">
			<a href="#" data-toggle="modal" data-target="#side-low-price">Узнать о снижении цены</a>
		</div>
	</div>

	<div class="bottom-options">

		<?if(!empty($arResult['OTHER_COLORS'])):?>
			<div class="">
				Выберите цвет:
			</div>
			<div class="">
				<?/*<div class="color-box">
					<?for($i=1;$i<4;$i++) { ?>
						<a href="#" style="background-image: url('images/content/detail-item-big-1.png')"></a>
					<? } ?>
				</div>*/?>
					<div class="color-box">
						<?foreach ($arResult['OTHER_COLORS'] as $arColor):?>
							<?$active = ($arColor['CODE'] == $arResult['CODE'])? true : false;?>
							<a href="<?if($active):?>javascript:void(0)<?else: print "/paoloconte_app".$arColor['DETAIL_PAGE_URL']; endif?>" <?if($active):?>class="active"<?endif?> title="<?=$arColor['COLOR']['NAME']?>" style="background-image: url('<?=$arColor['COLOR']['FILE_PATH']?><?//=$arColor['IMAGES']['PHOTO'][0]['SMALL']?>')"></a>
						<?endforeach?>
					</div>
			</div>
		<?endif?>

		<?if (!empty($arResult['OFFERS'])):?>
		<div class="get-size">Размер:</div>
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
		<?endif?>

		<div class="btn-box">

			<?if ($arResult['PROPERTIES']['OFFERS_AMOUNT']['VALUE'] > 0) {?>
				<a href="#" class="btn btn-gold full btn-tobasket" data-product-id="<?=$productId?>"><span>Добавить в корзину</span></a>
				<div class="info-tobasket">Товар добавлен в корзину</div>
				<a href="#" class="btn btn-gray-dark full fast-order" data-toggle="modal" data-target="#side-fast-order"><span>Быстрый заказ</span></a>
			<br/>
			<?}else{?>
				<div class="error-txt">К сожалению, данный товар отсутствует на складе.<br/>
					<a href="<?=$arResult['SECTION']['SECTION_PAGE_URL']?>">Посмотреть другие товары</a>
				</div>
			<?}?>

			<?/*<a href="#" class="btn btn-gray-dark full">Быстрый заказ</a>
			<a href="#" class="btn btn-gold full"><span>Добавить в корзину</span></a>*/?>

			<div class="align-center">
            	<span class="to-favorite" data-toggle="modal" data-target="#side-to-favorite" data-product-id="<?=$productIdToFav?>" data-image="<?=$productPhoto?>">
            	        <i class="fa fa-heart"></i>
            	        <i class="fa active fa-heart-o"></i>
            	        <span class="text">Добавить в список желаний</span>
            	</span>
			</div>
		</div>
	</div>

	<div class="panel-group rotate styled-detail-property" id="" role="tablist" aria-multiselectable="true">
		<div class="panel">
			<div class="panel-heading pts-bold" role="tab" id="heading-f1">
				<a class="collapsed clear-after" data-toggle="collapse" href="#collapse-f1" aria-expanded="true" aria-controls="collapse-f1">
					<span class="head-text">Описание и характеристики</span>
					<i class="fa fa-caret-down"></i>
				</a>
			</div>

			<div id="collapse-f1" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-f1">
				<div class="panel-body">

					<div class="emulate-table full">
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
									<div class="emulate-cell"><?=$arProp['VALUE']?></div>
								</div>
							<?endif?>
						<?endforeach?>
					</div>
				</div>
			</div>

		</div>
		<div class="panel">
			<div class="panel-heading pts-bold" role="tab" id="heading-f2">
				<a class="collapsed clear-after" data-toggle="collapse" href="#collapse-f2" aria-expanded="true" aria-controls="collapse-f2">
					<span class="head-text">Наличие в магазинах</span>
					<i class="fa fa-caret-down"></i>
				</a>
			</div>

			<div id="collapse-f2" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-f2">
				<div class="panel-body">
					<?if (!empty($arResult['SHOPS'])):?>
						<div class="emulate-table shop-store-wrap full">
							<?foreach ($arResult['SHOPS'] as $arShop):?>
								<?$arStore = $arResult['STORES_AMOUNT'][$arShop['PROPERTY_STORE_ID_VALUE']];
								$address = $arShop['PROPERTY_ADDRESS_VALUE'];
								$graphick = $arShop['PROPERTY_GRAPHICK_VALUE']['TEXT'];
								$phone = $arShop['PROPERTY_PHONE_VALUE'];
								?>
								<div class="shop-store emulate-row">
									<div class="emulate-cell shop-cell valign-top">
										<div class="action-modal-wrap hover-mod">
											<div class="shop-name get-modal">
												<div><?=$arShop['NAME']?></div>
												<ul>
													<li><i class="fa fa-map-marker"></i> <?=$address?></li>
													<?if ($graphick != ''):?><li><i class="fa fa-clock-o"></i> <?=$graphick?></li><?endif;?>
													<?if ($phone != ''):?><li><i class="fa fa-phone"></i> <?=$phone?></li><?endif;?>
												</ul>
											</div>
										</div>
									</div>
									<div class="emulate-cell valign-top">
										<?if (!empty($arStore)):?>
											<?if ($hasOffers):?>
												<?foreach ($arStore as $offer_id):?>
													<span class="size"><?=$arResult['SIZES'][$offer_id]?></span>
												<?endforeach?>
											<?else:?>
												<span class="size">Есть в наличии</span>
											<?endif;?>
										<?else:?>
											<span class="size">Уточняйте наличие в магазине</span>
										<?endif;?>
									</div>
								</div>
							<?endforeach;?>
						</div>
					<?else:?>
						<div class="no-shops-msg">К сожалению, в вашем городе нет фирменных магазинов Paolo Conte.</div>
					<?endif;?>
				</div>
			</div>

		</div>
		<div class="panel">
			<div class="panel-heading pts-bold" role="tab" id="heading-f3">
				<a class="collapsed clear-after" data-toggle="collapse" href="#collapse-f3" aria-expanded="true" aria-controls="collapse-f3">
					<span class="head-text">Доставка</span>
					<i class="fa fa-caret-down"></i>
				</a>
			</div>
			<div id="collapse-f3" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-f3">
				<div class="panel-body">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array("AREA_FILE_SHOW" => "file","PATH" => "/include/catalog_element_delivery.php"));?>
				</div>
			</div>
		</div>
		<div class="panel">
			<div class="panel-heading pts-bold" role="tab" id="heading-f4">
				<a class="collapsed clear-after" data-toggle="collapse" href="#collapse-f4" aria-expanded="true" aria-controls="collapse-f4">
					<span class="head-text">Размерная сетка</span>
					<i class="fa fa-caret-down"></i>
				</a>
			</div>
			<div id="collapse-f4" class="panel-collapse collapse tab_table_size" role="tabpanel" aria-labelledby="heading-f4">
				<div class="panel-body">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",Array("AREA_FILE_SHOW" => "file","PATH" => "/include/catalog_element_table_size_mobile.php"));?>
				</div>
			</div>
		</div>
	</div>

	<?if (!empty($arResult['OTHER_MODELS'])):?>
	<div class="detail-add-box  align-center">
		<?/*<div class="btn-box">
			<a href="#" class="btn btn-white full">Похожие модели</a>
		</div>*/?>
		<div class="title-r align-center">
			Похожие модели
		</div>

		<div class="catalog-wrap-mobile clear-after">
			<? foreach ($arResult['OTHER_MODELS'] as $arModel){ ?>
				<div class="item-wrap">
					<div class="item catalog-item">
						<div class="item-body">
							<a href="/paoloconte_app<?=$arModel['DETAIL_PAGE_URL']?>">
								<div class="image">
									<?if(!empty($arModel['IMAGES']['PHOTO'][0]['SMALL'])):?>
										<img src="<?=$arModel['IMAGES']['PHOTO'][0]['SMALL']?>" alt="<?=$arModel['NAME']?>">
									<?else:?>
										<img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
									<?endif?>
								</div>
							</a>
							<div class="price-box">
								<div class="old-price">
									<?=CurrencyFormat($arModel['OLD_PRICE'], 'RUB')?>
								</div>
								<div class="new-price">
									<?=CurrencyFormat($arModel['NEW_PRICE'], 'RUB')?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<? } ?>
		</div>
	</div>
	<?endif?>

	<? // Вы посмотрели
	$APPLICATION->IncludeComponent(
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
			"PAGE_ELEMENT_COUNT" => "6",
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

</div>

<div class="reviews-wrap">
	<div class="title-r align-center">
		Отзывы о товаре
	</div>

	<?if(!empty($arResult['REVIEWS'])):?>
		<?foreach($arResult['REVIEWS'] as $reviewItem) { ?>
			<div class="item">
				<div class="styled-text-box">
					<div class="title">
						<?=$reviewItem['USERNAME']?> <span class="time-box"><?=$reviewItem['DATE_CREATE']?></span>
					</div>
					<div class="rating">
						<?for($i=1; $i<=5; $i++):?>
							<i class="fa <?=$i<=$reviewItem['PROPERTY_STARS_VALUE']? 'fa-star':'fa-star-o'?>"></i>
						<?endfor;?>
					</div>
					<div class="tite-desc">
						<?=$reviewItem['PROPERTY_MESSAGE_VALUE']?>
					</div>
				</div>
			</div>
		<? } ?>
	<?else:?>
		<div class="item">
			<div class="styled-text-box">
				<div class="title align-center">
					У данного товара еще нет отзывов, но вы можете быть первым!
				</div>
			</div>
		</div>
	<?endif?>

	<a href="#" class="get-review btn btn-gray-dark full big"  data-toggle="modal" data-target="#side-get-review">
		<span>Оставить отзыв</span>
	</a>
</div>

<?$this->SetViewTarget("detail_add_review_block");?>
<div id="side-get-review" class="modal fade side-get-review app-styles-modal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 99999;">
	<div class="modal-dialog">
		<div class="modal-content">
	<div class="moved-panel-wrap">
		<div class="moved-panel-head app_modal_head">
			<h6>
				<?global $USER;?>
				<?if($USER->IsAuthorized()):?>
					Оставить отзыв
				<?else:?>
				Авторизация
				<?endif;?>
			</h6>
			<button type="button" class="close" data-dismiss="modal"></button>
		</div>
		<div class="moved-panel-body">
			<?global $USER;?>
			<?if($USER->IsAuthorized()):?>
			<div class="aside-box">
				Вы авторизованы как:
				<div class="gold"><?=$USER->GetFullName()?></div>

				<div class="review-form">
					<?$APPLICATION->IncludeComponent("citfact:form.ajax", "product_review", Array(
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
							"PRODUCT_NAME" => array(
								"type" => "hidden",
								"value" => $arResult['NAME'],
							),
							"MESSAGE" => array(
								"type" => "textarea",
								"class" => 'required',
								"placeholder" => 'Ваш отзыв от товаре',
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
						"SUCCESS_MESSAGE" => "Спасибо, ваш отзыв принят и появится на сайте после проверки модератором.",
						"ELEMENT_ACTIVE" => "N",
						"USER_NAME" => $USER->GetFullName(),
						"USER_EMAIL" => $USER->GetEmail(),
					),
						false
					);?>
				</div>

				<?/*<div class="review-form">
					<div class="message">
						<span class="round-label true"><i class="fa fa-check"></i></span>
						Ваш отзыв успешно отправлен. Он будет размещен сразу после модерации.
					</div>

					<div class="btn-box">
						<a href="#" class="btn btn-gray-dark full">Вернуться к покупкам</a>
					</div>

				</div>*/?>
			</div>
			<?else:?>
				<?$APPLICATION->IncludeComponent(
					"citfact:authorize.ajax",
					"app",
					Array(
						"REDIRECT_TO" => '',
						"FORM_ID" => 'popup'
					)
				);?>
			<?endif;?>
		</div>
	</div>
		</div>
	</div>
</div>
<?// Закомменчено, ибо не работает с несколькими областями подряд?>
<?//$this->EndViewTarget();?>

<?$this->SetViewTarget("detail_fast_order_block");?>
<div id="side-fast-order" class="modal fade side-low-price app-styles-modal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 99999;">
	<div class="modal-dialog">
		<div class="modal-content">
	<div class="moved-panel-wrap">
		<div class="moved-panel-head app_modal_head">
			<h6>Быстрый заказ</h6>
			<button type="button" class="close" data-dismiss="modal"></button>
		</div>
		<div class="moved-panel-body">
			<div class="aside-box fast-order-wrap">
				<?$APPLICATION->IncludeComponent("citfact:form.ajax", "fast_order_mobile", Array(
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
					),
					"EVENT_NAME" => "FAST_ORDER_FORM",
					"SUCCESS_MESSAGE" => "Ваш заказ принят. Мы перезвоним вам в ближайшее время.",
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
</div>

<?$this->SetViewTarget("detail_subscribe_price_block");?>
<div id="side-low-price" class="modal fade side-low-price app-styles-modal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 99999;">
	<div class="modal-dialog">
		<div class="modal-content">
	<div class="moved-panel-wrap">
		<div class="moved-panel-head app_modal_head">
			<h6>Узнать о снижении цены</h6>
			<button type="button" class="close" data-dismiss="modal"></button>
		</div>
		<div class="moved-panel-body">
			<div class="aside-box">
				<?$APPLICATION->IncludeComponent("citfact:form.ajax", "mobile_subscribe_price", Array(
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
							"value" => '',
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
</div>
<?$this->EndViewTarget();?>


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
</script>