<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
echo ShowError($arResult["ERROR_MESSAGE"]);

$bDelayColumn  = false;
$bDeleteColumn = false;
$bWeightColumn = false;
$bPropsColumn  = false;
$bPriceType    = false;

if ($normalCount > 0):
?>
	<div class="cart-wrap" id="basket_items">
	<?
	$count_items = 0;
	foreach ($arResult["GRID"]["ROWS"] as $k => $arItem){
		if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y"):
			$articul = $arItem['CATALOG']['PROPERTY_184_VALUE'][0];
			$count_items++;
			?>
			<div class="item" id="<?=$arItem["ID"]?>">
				<div class="emulate-table full">
					<div class="image emulate-cell valign-middle">
						<?if($arItem['CATALOG_PHOTO']):?>
							<img src="<?=$arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']?>" alt="<?=$arItem['NAME']?>">
						<?else:?>
							<img src="<?=$arResult['NOPHOTO']?>" alt="Нет фото">
						<?endif;?>
					</div>

					<?/*<div class="count-box emulate-cell">
						<span class="minus"></span>
						<input type="text" value="1">
						<span class="plus"></span>
					</div>*/?>

					<div class="price-box emulate-cell">
						<div id="sum_<?=$arItem["ID"]?>" class="new-price rouble">
							<?=number_format($arItem["PRICE"], 0, '', ' ')?>
						</div>
						<div class="old-price rouble">
							<?=number_format($arItem["FULL_PRICE"], 0, '', ' ')?>
						</div>
					</div>
				</div>

				<div class="emulate-table full">
					<div class="size-cell emulate-cell valign-middle">
						<?foreach ($arItem["PROPS"] as $val):
							echo '<div class="size">'.$val["NAME"].': '.$val["VALUE"].'</div>';
						endforeach;?>

						<?if (is_array($arItem["SKU_DATA"]) && !empty($arItem["SKU_DATA"])):?>
							<a href="#" class="change-size inbasket">Изменить размер</a><br>
							<?
							foreach ($arItem["SKU_DATA"] as $propId => $arProp):
								// if property contains images or values
								$isImgProperty = false;
								if (array_key_exists('VALUES', $arProp) && is_array($arProp["VALUES"]) && !empty($arProp["VALUES"]))
								{
									foreach ($arProp["VALUES"] as $id => $arVal)
									{
										if (isset($arVal["PICT"]) && !empty($arVal["PICT"]) && is_array($arVal["PICT"])
											&& isset($arVal["PICT"]['SRC']) && !empty($arVal["PICT"]['SRC']))
										{
											$isImgProperty = true;
											break;
										}
									}
								}
								$countValues = count($arProp["VALUES"]);
								$full = ($countValues > 5) ? "full" : "";

								if ($isImgProperty): // iblock element relation property?>
								<?
								else:
									?>
									<div class="bx_item_detail_size_small_noadaptive <?=$full?>">
										<div class="bx_size_scroller_container">
											<div class="bx_size" style="display: none;">
												<div class="size-box" id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>"
													 class="sku_prop_list">
													<?
													foreach ($arProp["VALUES"] as $valueId => $arSkuValue):

														// Не показываем предложения, которых нет на складе интернет-магазина (см. result_modifier.php)
														if (!array_key_exists($arSkuValue["NAME"], $arResult['SIZES_AMOUNT'][$arItem['CATALOG']['PROPERTIES']['CML2_LINK']['VALUE']])){
															continue;
														}

														$selected = "";
														foreach ($arItem["PROPS"] as $arItemProp):
															if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
															{
																if ($arItemProp["VALUE"] == $arSkuValue["NAME"])
																	//$selected = "bx_active active";
																	$selected = "active";
															}
														endforeach;
														?>
														<?/*<li style="width:10%;"
																class="sku_prop <?=$selected?>"
																data-value-id="<?=$arSkuValue["NAME"]?>"
																data-element="<?=$arItem["ID"]?>"
																data-property="<?=$arProp["CODE"]?>"
																>
																<a href="javascript:void(0);"><?=$arSkuValue["NAME"]?></a>
															</li>*/?>
														<label class="sku_prop <?=$selected?>"
															   data-value-id="<?=$arSkuValue["NAME"]?>"
															   data-element="<?=$arItem["ID"]?>"
															   data-property="<?=$arProp["CODE"]?>"
															>
															<?=$arSkuValue["NAME"]?>
															<input type="radio" value="" name="r<?=$arItem['ID']?>">
														</label>
													<?
													endforeach;
													?>
												</div>
											</div>
										</div>
									</div>
								<?
								endif;
							endforeach;?>
						<?endif;?>

					</div>
					<a href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delete"])?>" class="del-item emulate-cell align-center">Удалить</a>
				</div>
			</div>
		<?endif;?>
	<? } ?>
	</div>

	<?
	$APPLICATION->IncludeComponent(
		"citfact:loyalty_card.page",
		"mobile",
		Array(
			"COMPONENT_TEMPLATE" => "mobile"
		)
	);
	?>

	<div class="personal-box">
		<?
		if ($arParams["HIDE_COUPON"] != "Y"):
			$couponClass = "";
			if (array_key_exists('VALID_COUPON', $arResult))
			{
				$couponClass = ($arResult["VALID_COUPON"] === true) ? "good" : "bad";
			}
			elseif (array_key_exists('COUPON', $arResult) && !empty($arResult["COUPON"]))
			{
				$couponClass = "good";
			}
			?>
			<div class="line">
				<input type="text" id="coupon" name="COUPON" value="<?=($couponClass == 'good'? $arResult["COUPON"]:'')?>" size="21" class="<?=$couponClass?>" placeholder="Код купона на скидку">
			</div>
		<?else:?>
			&nbsp;
		<?endif;?>
		<div class="line align-center">
			<a href="#" class="recalc" onclick="enterCoupon();window.location.reload();return false;">Пересчитать</a>
		</div>

		<?/*<div class="line">
			<input type="text" placeholder="Выберите промо-код">
		</div>
		<div class="line align-center">
			<a href="#" class="recalc">Пересчитать</a>
		</div>*/?>
	</div>

	<div class="info-box green arrow-top">
		Всего (<?=$count_items?>) <?=\Citfact\Tools::declension($count_items, array('товар', 'товара', 'товаров'), true)?> <?=str_replace(" ", "&nbsp;", $arResult["allSum_FORMATED"])?>
	</div>


	<input type="hidden" id="column_headers" value="<?=CUtil::JSEscape(implode($arHeaders, ","))?>" />
	<input type="hidden" id="offers_props" value="<?=CUtil::JSEscape(implode($arParams["OFFERS_PROPS"], ","))?>" />
	<input type="hidden" id="action_var" value="<?=CUtil::JSEscape($arParams["ACTION_VARIABLE"])?>" />
	<input type="hidden" id="quantity_float" value="<?=$arParams["QUANTITY_FLOAT"]?>" />
	<input type="hidden" id="count_discount_4_all_quantity" value="<?=($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N"?>" />
	<input type="hidden" id="price_vat_show_value" value="<?=($arParams["PRICE_VAT_SHOW_VALUE"] == "Y") ? "Y" : "N"?>" />
	<input type="hidden" id="hide_coupon" value="<?=($arParams["HIDE_COUPON"] == "Y") ? "Y" : "N"?>" />
	<input type="hidden" id="coupon_approved" value="N" />
	<input type="hidden" id="use_prepayment" value="<?=($arParams["USE_PREPAYMENT"] == "Y") ? "Y" : "N"?>" />


<?// БЛОК ОТКЛЮЧЕН ?>
<?if (false):?>
<div id="basket_items_list">
	<div class="bx_ordercart_order_table_container">
		<table class="full" id="basket_items">
			<tr>
				<th>
					Фото товара
				</th>
				<th>
					Характеристики
				</th>
				<th>
					<?/*Количество*/?>
				</th>
				<th>
					Цена
				</th>
				<th>
					Скидка
				</th>
				<th>
					Стоимость
				</th>
				<th>
				</th>
			</tr>
			<?
			foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):
				if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y"):
					$articul = $arItem['CATALOG']['PROPERTY_184_VALUE'][0];

					//favoriteIcon
					if(!empty($arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']))
						$favoritePhoto = $arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL'];
					else
						$favoritePhoto = $arResult['NOPHOTO'];
					?>
					<tr id="<?=$arItem["ID"]?>">
						<td>
							<div class="image">
								<span class="to-favorite" data-image="<?=$favoritePhoto?>" data-product-id="<?=$arItem['PRODUCT_ID']?>">
									<i class="fa fa-heart"></i>
									<i class="fa active fa-heart-o"></i>
								</span>

								<?if($arItem['CATALOG_PHOTO']):?>
									<img src="<?=$arItem['CATALOG_PHOTO']['PHOTO'][0]['SMALL']?>" alt="<?=$arItem['NAME']?>">
								<?else:?>
									<img src="<?=$arResult['NOPHOTO']?>" alt="нет фото">
								<?endif;?>
							</div>
						</td>
						<td>
							<?/*<div class="name">Туфли</div>*/?>
							<div class="index"><?=$articul?></div>
							<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?><a href="<?=$arItem["DETAIL_PAGE_URL"] ?>"><?endif;?>
								<span id="bx_name_<?=$arItem['ID']?>"><?=$arItem["NAME"]?></span>
							<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>

							<?
								foreach ($arItem["PROPS"] as $val):
									/*if (is_array($arItem["SKU_DATA"]))
									{
										$bSkip = false;
										foreach ($arItem["SKU_DATA"] as $propId => $arProp)
										{
											if ($arProp["CODE"] == $val["CODE"])
											{
												$bSkip = true;
												break;
											}
										}
										if ($bSkip)
											continue;
									}*/
									//echo $val["NAME"].":&nbsp;<span>".$val["VALUE"]."<span><br/>";
									echo '<div class="size">'.$val["NAME"].': '.$val["VALUE"].'</div>';
								endforeach;
							?>

							<?if (is_array($arItem["SKU_DATA"]) && !empty($arItem["SKU_DATA"])):?>
							<a href="#" class="change-size inbasket">Изменить размер</a><br>
							<?
								foreach ($arItem["SKU_DATA"] as $propId => $arProp):
									// if property contains images or values
									$isImgProperty = false;
									if (array_key_exists('VALUES', $arProp) && is_array($arProp["VALUES"]) && !empty($arProp["VALUES"]))
									{
										foreach ($arProp["VALUES"] as $id => $arVal)
										{
											if (isset($arVal["PICT"]) && !empty($arVal["PICT"]) && is_array($arVal["PICT"])
												&& isset($arVal["PICT"]['SRC']) && !empty($arVal["PICT"]['SRC']))
											{
												$isImgProperty = true;
												break;
											}
										}
									}
									$countValues = count($arProp["VALUES"]);
									$full = ($countValues > 5) ? "full" : "";

									if ($isImgProperty): // iblock element relation property
										?>
										<?/*<div class="bx_item_detail_scu_small_noadaptive <?=$full?>">

													<span class="bx_item_section_name_gray">
														<?=$arProp["NAME"]?>:
													</span>

											<div class="bx_scu_scroller_container">

												<div class="bx_scu">
													<ul id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>"
														style="width: 200%; margin-left:0%;"
														class="sku_prop_list"
														>
														<?
														foreach ($arProp["VALUES"] as $valueId => $arSkuValue):

															$selected = "";
															foreach ($arItem["PROPS"] as $arItemProp):
																if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
																{
																	if ($arItemProp["VALUE"] == $arSkuValue["NAME"] || $arItemProp["VALUE"] == $arSkuValue["XML_ID"])
																		$selected = "bx_active";
																}
															endforeach;
															?>
															<li style="width:10%;"
																class="sku_prop <?=$selected?>"
																data-value-id="<?=$arSkuValue["XML_ID"]?>"
																data-element="<?=$arItem["ID"]?>"
																data-property="<?=$arProp["CODE"]?>"
																>
																<a href="javascript:void(0);">
																	<span style="background-image:url(<?=$arSkuValue["PICT"]["SRC"]?>)"></span>
																</a>
															</li>
														<?
														endforeach;
														?>
													</ul>
												</div>

												<div class="bx_slide_left" onclick="leftScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>, <?=$countValues?>);"></div>
												<div class="bx_slide_right" onclick="rightScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>, <?=$countValues?>);"></div>
											</div>

										</div>*/?>
									<?
									else:
										?>
										<div class="bx_item_detail_size_small_noadaptive <?=$full?>">
											<div class="bx_size_scroller_container">
												<div class="bx_size" style="display: none;">
													<div class="size-box" id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>"
														style="width: 200%; margin-left:0%;"
														class="sku_prop_list"
														>
														<?
														foreach ($arProp["VALUES"] as $valueId => $arSkuValue):

															// Не показываем предложения, которых нет на складе интернет-магазина (см. result_modifier.php)
															if (!array_key_exists($arSkuValue["NAME"], $arResult['SIZES_AMOUNT'][$arItem['CATALOG']['PROPERTIES']['CML2_LINK']['VALUE']])){
																continue;
															}

															$selected = "";
															foreach ($arItem["PROPS"] as $arItemProp):
																if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
																{
																	if ($arItemProp["VALUE"] == $arSkuValue["NAME"])
																		//$selected = "bx_active active";
																		$selected = "active";
																}
															endforeach;
															?>
															<?/*<li style="width:10%;"
																class="sku_prop <?=$selected?>"
																data-value-id="<?=$arSkuValue["NAME"]?>"
																data-element="<?=$arItem["ID"]?>"
																data-property="<?=$arProp["CODE"]?>"
																>
																<a href="javascript:void(0);"><?=$arSkuValue["NAME"]?></a>
															</li>*/?>
															<label class="sku_prop <?=$selected?>"
																   data-value-id="<?=$arSkuValue["NAME"]?>"
																   data-element="<?=$arItem["ID"]?>"
																   data-property="<?=$arProp["CODE"]?>"
															>
																<?=$arSkuValue["NAME"]?>
																<input type="radio" value="" name="r<?=$arItem['ID']?>">
															</label>
														<?
														endforeach;
														?>
													</div>
												</div>
											</div>
										</div>
									<?
									endif;
								endforeach;?>
							<?endif;?>

							<?/*<div><a href="#" class="to-favorite-list">В список желаний</a></div>*/?>
						</td>
						<td>
							<?/*
							<?
							$ratio = isset($arItem["MEASURE_RATIO"]) ? $arItem["MEASURE_RATIO"] : 0;
							$max = isset($arItem["AVAILABLE_QUANTITY"]) ? "max=\"".$arItem["AVAILABLE_QUANTITY"]."\"" : "";
							$useFloatQuantity = ($arParams["QUANTITY_FLOAT"] == "Y") ? true : false;
							$useFloatQuantityJS = ($useFloatQuantity ? "true" : "false");

							if (!isset($arItem["MEASURE_RATIO"]))
							{
								$arItem["MEASURE_RATIO"] = 1;
							}
							?>
							<div class="count-box">
								<?if (floatval($arItem["MEASURE_RATIO"]) != 0):?>
								<span class="minus" onclick="setQuantity(<?=$arItem["ID"]?>, <?=$arItem["MEASURE_RATIO"]?>, 'down', <?=$useFloatQuantityJS?>);">
									<i class="fa fa-minus"></i>
								</span>
								<?endif?>

								<input
									type="text"
									size="3"
									id="QUANTITY_INPUT_<?=$arItem["ID"]?>"
									name="QUANTITY_INPUT_<?=$arItem["ID"]?>"
									size="2"
									maxlength="18"
									min="0"
									<?=$max?>
									step="<?=$ratio?>"
									style="max-width: 50px"
									value="<?=$arItem["QUANTITY"]?>"
									onchange="updateQuantity('QUANTITY_INPUT_<?=$arItem["ID"]?>', '<?=$arItem["ID"]?>', <?=$ratio?>, <?=$useFloatQuantityJS?>)"
								>

								<?if (floatval($arItem["MEASURE_RATIO"]) != 0):?>
								<span class="plus" onclick="setQuantity(<?=$arItem["ID"]?>, <?=$arItem["MEASURE_RATIO"]?>, 'up', <?=$useFloatQuantityJS?>);">
									<i class="fa fa-plus"></i>
								</span>
								<?endif?>
								<input type="hidden" id="QUANTITY_<?=$arItem['ID']?>" name="QUANTITY_<?=$arItem['ID']?>" value="<?=$arItem["QUANTITY"]?>" />
							</div>*/?>
						</td>
						<td>
							<div class="price-box">
								<?if (floatval($arItem["DISCOUNT_PRICE_PERCENT"]) > 0):?>
									<div class="old-price rouble">
										<?=number_format($arItem["FULL_PRICE"], 0, '', ' ')?>
									</div>
								<?endif;?>
								<div class="new-price rouble">
									<?=number_format($arItem["PRICE"], 0, '', ' ')?>
								</div>
							</div>
						</td>
						<td>
							<?=$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"]?>
						</td>
						<td>
							<div id="sum_<?=$arItem["ID"]?>"><?=$arItem['SUM']?></div>
						</td>
						<td>
							<a href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delete"])?>" class="del-elem close-small"></a>
						</td>
					</tr>
				<?
				endif;
			endforeach;
			?>
		</table>

		<div class="end-cart-box emulate-table full">
			<div class="emulate-cell valign-middle">
				Выберите код купона для получения скидки и нажмите кнопку “Пересчитать”:
				<div class="promo">

					<?/*<select>
						<option>Выберите промо-код</option>
						<option>Выберите промо-код</option>
						<option>Выберите промо-код</option>
					</select>*/?>

					<?
					if ($arParams["HIDE_COUPON"] != "Y"):
						$couponClass = "";
						if (array_key_exists('VALID_COUPON', $arResult))
						{
							$couponClass = ($arResult["VALID_COUPON"] === true) ? "good" : "bad";
						}
						elseif (array_key_exists('COUPON', $arResult) && !empty($arResult["COUPON"]))
						{
							$couponClass = "good";
						}
						?>
						<input type="text" id="coupon" name="COUPON" value="<?=($couponClass == 'good'? $arResult["COUPON"]:'')?>" size="21" class="<?=$couponClass?>" style="width: 200px; height: 35px;">
					<?else:?>
						&nbsp;
					<?endif;?>

					<a href="#" class="btn btn-gray-dark mode2 small icon-arrow-right" onclick="enterCoupon(); window.location.reload(); overlay.show(); return false;">Пересчитать</a>
				</div>
			</div>
			<div class="emulate-cell align-right">
				<?if (floatval($arResult["DISCOUNT_PRICE_ALL"]) > 0):?>
					Цена без скидки: <span id="PRICE_WITHOUT_DISCOUNT"><?=$arResult["PRICE_WITHOUT_DISCOUNT"]?></span></br>
				<?endif;?>

				Цена со скидкой: <span class="fwb" id="allSum_FORMATED"><?=str_replace(" ", "&nbsp;", $arResult["allSum_FORMATED"])?></span> *

				<?if (floatval($arResult["DISCOUNT_PRICE_ALL"]) > 0):?>
					<div class="discount">
						<span id="DISCOUNT_PRICE_ALL_FORMATED">
							Ваша экономия: <?=$arResult['DISCOUNT_PRICE_ALL_FORMATED']?>
					   </span>
					</div>
				<?endif?>

				<div class="desc">
					* Без учета доставки
				</div>
			</div>
		</div>
		<?// =========================================================================================================?>

	<?
	foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):
		$arHeader["name"] = (isset($arHeader["name"]) ? (string)$arHeader["name"] : '');
		if ($arHeader["name"] == '')
			$arHeader["name"] = GetMessage("SALE_".$arHeader["id"]);
		$arHeaders[] = $arHeader["id"];
	endforeach;?>

		<?/*<table id="basket_items">
			<thead>
				<tr>
					<td class="margin"></td>
					<?
					foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):
						$arHeader["name"] = (isset($arHeader["name"]) ? (string)$arHeader["name"] : '');
						if ($arHeader["name"] == '')
							$arHeader["name"] = GetMessage("SALE_".$arHeader["id"]);
						$arHeaders[] = $arHeader["id"];

						// remember which values should be shown not in the separate columns, but inside other columns
						if (in_array($arHeader["id"], array("TYPE")))
						{
							$bPriceType = true;
							continue;
						}
						elseif ($arHeader["id"] == "PROPS")
						{
							$bPropsColumn = true;
							continue;
						}
						elseif ($arHeader["id"] == "DELAY")
						{
							$bDelayColumn = true;
							continue;
						}
						elseif ($arHeader["id"] == "DELETE")
						{
							$bDeleteColumn = true;
							continue;
						}
						elseif ($arHeader["id"] == "WEIGHT")
						{
							$bWeightColumn = true;
						}

						if ($arHeader["id"] == "NAME"):
						?>
							<td class="item" colspan="2" id="col_<?=$arHeader["id"];?>">
						<?
						elseif ($arHeader["id"] == "PRICE"):
						?>
							<td class="price" id="col_<?=$arHeader["id"];?>">
						<?
						else:
						?>
							<td class="custom" id="col_<?=$arHeader["id"];?>">
						<?
						endif;
						?>
							<?=$arHeader["name"]; ?>
							</td>
					<?
					endforeach;

					if ($bDeleteColumn || $bDelayColumn):
					?>
						<td class="custom"></td>
					<?
					endif;
					?>
						<td class="margin"></td>
				</tr>
			</thead>

			<tbody>
				<?
				foreach ($arResult["GRID"]["ROWS"] as $k => $arItem):

					if ($arItem["DELAY"] == "N" && $arItem["CAN_BUY"] == "Y"):
				?>
					<tr id="<?=$arItem["ID"]?>">
						<td class="margin"></td>
						<?
						foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):

							if (in_array($arHeader["id"], array("PROPS", "DELAY", "DELETE", "TYPE"))) // some values are not shown in the columns in this template
								continue;

							if ($arHeader["id"] == "NAME"):
							?>
								<td class="itemphoto">
									<div class="bx_ordercart_photo_container">
										<?
										if (strlen($arItem["PREVIEW_PICTURE_SRC"]) > 0):
											$url = $arItem["PREVIEW_PICTURE_SRC"];
										elseif (strlen($arItem["DETAIL_PICTURE_SRC"]) > 0):
											$url = $arItem["DETAIL_PICTURE_SRC"];
										else:
											$url = $templateFolder."/images/no_photo.png";
										endif;
										?>

										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?><a href="<?=$arItem["DETAIL_PAGE_URL"] ?>"><?endif;?>
											<div class="bx_ordercart_photo" style="background-image:url('<?=$url?>')"></div>
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>
									</div>
									<?
									if (!empty($arItem["BRAND"])):
									?>
									<div class="bx_ordercart_brand">
										<img alt="" src="<?=$arItem["BRAND"]?>" />
									</div>
									<?
									endif;
									?>
								</td>
								<td class="item">
									<h2 class="bx_ordercart_itemtitle">
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?><a href="<?=$arItem["DETAIL_PAGE_URL"] ?>"><?endif;?>
											<?=$arItem["NAME"]?>
										<?if (strlen($arItem["DETAIL_PAGE_URL"]) > 0):?></a><?endif;?>
									</h2>
									<div class="bx_ordercart_itemart">
										<?
										if ($bPropsColumn):
											foreach ($arItem["PROPS"] as $val):

												if (is_array($arItem["SKU_DATA"]))
												{
													$bSkip = false;
													foreach ($arItem["SKU_DATA"] as $propId => $arProp)
													{
														if ($arProp["CODE"] == $val["CODE"])
														{
															$bSkip = true;
															break;
														}
													}
													if ($bSkip)
														continue;
												}

												echo $val["NAME"].":&nbsp;<span>".$val["VALUE"]."<span><br/>";
											endforeach;
										endif;
										?>
									</div>
									<?
									if (is_array($arItem["SKU_DATA"]) && !empty($arItem["SKU_DATA"])):
										foreach ($arItem["SKU_DATA"] as $propId => $arProp):

											// if property contains images or values
											$isImgProperty = false;
											if (array_key_exists('VALUES', $arProp) && is_array($arProp["VALUES"]) && !empty($arProp["VALUES"]))
											{
												foreach ($arProp["VALUES"] as $id => $arVal)
												{
													if (isset($arVal["PICT"]) && !empty($arVal["PICT"]) && is_array($arVal["PICT"])
														&& isset($arVal["PICT"]['SRC']) && !empty($arVal["PICT"]['SRC']))
													{
														$isImgProperty = true;
														break;
													}
												}
											}
											$countValues = count($arProp["VALUES"]);
											$full = ($countValues > 5) ? "full" : "";

											if ($isImgProperty): // iblock element relation property
											?>
												<div class="bx_item_detail_scu_small_noadaptive <?=$full?>">

													<span class="bx_item_section_name_gray">
														<?=$arProp["NAME"]?>:
													</span>

													<div class="bx_scu_scroller_container">

														<div class="bx_scu">
															<ul id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>"
																style="width: 200%; margin-left:0%;"
																class="sku_prop_list"
																>
																<?
																foreach ($arProp["VALUES"] as $valueId => $arSkuValue):

																	$selected = "";
																	foreach ($arItem["PROPS"] as $arItemProp):
																		if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
																		{
																			if ($arItemProp["VALUE"] == $arSkuValue["NAME"] || $arItemProp["VALUE"] == $arSkuValue["XML_ID"])
																				$selected = "bx_active";
																		}
																	endforeach;
																?>
																	<li style="width:10%;"
																		class="sku_prop <?=$selected?>"
																		data-value-id="<?=$arSkuValue["XML_ID"]?>"
																		data-element="<?=$arItem["ID"]?>"
																		data-property="<?=$arProp["CODE"]?>"
																		>
																		<a href="javascript:void(0);">
																			<span style="background-image:url(<?=$arSkuValue["PICT"]["SRC"]?>)"></span>
																		</a>
																	</li>
																<?
																endforeach;
																?>
															</ul>
														</div>

														<div class="bx_slide_left" onclick="leftScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>, <?=$countValues?>);"></div>
														<div class="bx_slide_right" onclick="rightScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>, <?=$countValues?>);"></div>
													</div>

												</div>
											<?
											else:
											?>
												<div class="bx_item_detail_size_small_noadaptive <?=$full?>">

													<span class="bx_item_section_name_gray">
														<?=$arProp["NAME"]?>:
													</span>

													<div class="bx_size_scroller_container">
														<div class="bx_size">
															<ul id="prop_<?=$arProp["CODE"]?>_<?=$arItem["ID"]?>"
																style="width: 200%; margin-left:0%;"
																class="sku_prop_list"
																>
																<?
																foreach ($arProp["VALUES"] as $valueId => $arSkuValue):

																	$selected = "";
																	foreach ($arItem["PROPS"] as $arItemProp):
																		if ($arItemProp["CODE"] == $arItem["SKU_DATA"][$propId]["CODE"])
																		{
																			if ($arItemProp["VALUE"] == $arSkuValue["NAME"])
																				$selected = "bx_active";
																		}
																	endforeach;
																?>
																	<li style="width:10%;"
																		class="sku_prop <?=$selected?>"
																		data-value-id="<?=$arSkuValue["NAME"]?>"
																		data-element="<?=$arItem["ID"]?>"
																		data-property="<?=$arProp["CODE"]?>"
																		>
																		<a href="javascript:void(0);"><?=$arSkuValue["NAME"]?></a>
																	</li>
																<?
																endforeach;
																?>
															</ul>
														</div>
														<div class="bx_slide_left" onclick="leftScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>, <?=$countValues?>);"></div>
														<div class="bx_slide_right" onclick="rightScroll('<?=$arProp["CODE"]?>', <?=$arItem["ID"]?>, <?=$countValues?>);"></div>
													</div>

												</div>
											<?
											endif;
										endforeach;
									endif;
									?>
								</td>
							<?
							elseif ($arHeader["id"] == "QUANTITY"):
							?>
								<td class="custom">
									<span><?=$arHeader["name"]; ?>:</span>
									<div class="centered">
										<table cellspacing="0" cellpadding="0" class="counter">
											<tr>
												<td>
													<?
													$ratio = isset($arItem["MEASURE_RATIO"]) ? $arItem["MEASURE_RATIO"] : 0;
													$max = isset($arItem["AVAILABLE_QUANTITY"]) ? "max=\"".$arItem["AVAILABLE_QUANTITY"]."\"" : "";
													$useFloatQuantity = ($arParams["QUANTITY_FLOAT"] == "Y") ? true : false;
													$useFloatQuantityJS = ($useFloatQuantity ? "true" : "false");
													?>
													<input
														type="text"
														size="3"
														id="QUANTITY_INPUT_<?=$arItem["ID"]?>"
														name="QUANTITY_INPUT_<?=$arItem["ID"]?>"
														size="2"
														maxlength="18"
														min="0"
														<?=$max?>
														step="<?=$ratio?>"
														style="max-width: 50px"
														value="<?=$arItem["QUANTITY"]?>"
														onchange="updateQuantity('QUANTITY_INPUT_<?=$arItem["ID"]?>', '<?=$arItem["ID"]?>', <?=$ratio?>, <?=$useFloatQuantityJS?>)"
													>
												</td>
												<?
												if (!isset($arItem["MEASURE_RATIO"]))
												{
													$arItem["MEASURE_RATIO"] = 1;
												}

												if (
													floatval($arItem["MEASURE_RATIO"]) != 0
												):
												?>
													<td id="basket_quantity_control">
														<div class="basket_quantity_control">
															<a href="javascript:void(0);" class="plus" onclick="setQuantity(<?=$arItem["ID"]?>, <?=$arItem["MEASURE_RATIO"]?>, 'up', <?=$useFloatQuantityJS?>);"></a>
															<a href="javascript:void(0);" class="minus" onclick="setQuantity(<?=$arItem["ID"]?>, <?=$arItem["MEASURE_RATIO"]?>, 'down', <?=$useFloatQuantityJS?>);"></a>
														</div>
													</td>
												<?
												endif;
												if (isset($arItem["MEASURE_TEXT"]))
												{
													?>
														<td style="text-align: left"><?=$arItem["MEASURE_TEXT"]?></td>
													<?
												}
												?>
											</tr>
										</table>
									</div>
									<input type="hidden" id="QUANTITY_<?=$arItem['ID']?>" name="QUANTITY_<?=$arItem['ID']?>" value="<?=$arItem["QUANTITY"]?>" />
								</td>
							<?
							elseif ($arHeader["id"] == "PRICE"):
							?>
								<td class="price">
										<div class="current_price" id="current_price_<?=$arItem["ID"]?>">
											<?=$arItem["PRICE_FORMATED"]?>
										</div>
										<div class="old_price" id="old_price_<?=$arItem["ID"]?>">
											<?if (floatval($arItem["DISCOUNT_PRICE_PERCENT"]) > 0):?>
												<?=$arItem["FULL_PRICE_FORMATED"]?>
											<?endif;?>
										</div>

									<?if ($bPriceType && strlen($arItem["NOTES"]) > 0):?>
										<div class="type_price"><?=GetMessage("SALE_TYPE")?></div>
										<div class="type_price_value"><?=$arItem["NOTES"]?></div>
									<?endif;?>
								</td>
							<?
							elseif ($arHeader["id"] == "DISCOUNT"):
							?>
								<td class="custom">
									<span><?=$arHeader["name"]; ?>:</span>
									<div id="discount_value_<?=$arItem["ID"]?>"><?=$arItem["DISCOUNT_PRICE_PERCENT_FORMATED"]?></div>
								</td>
							<?
							elseif ($arHeader["id"] == "WEIGHT"):
							?>
								<td class="custom">
									<span><?=$arHeader["name"]; ?>:</span>
									<?=$arItem["WEIGHT_FORMATED"]?>
								</td>
							<?
							else:
							?>
								<td class="custom">
									<span><?=$arHeader["name"]; ?>:</span>
									<?
									if ($arHeader["id"] == "SUM"):
									?>
										<div id="sum_<?=$arItem["ID"]?>">
									<?
									endif;

									echo $arItem[$arHeader["id"]];

									if ($arHeader["id"] == "SUM"):
									?>
										</div>
									<?
									endif;
									?>
								</td>
							<?
							endif;
						endforeach;

						if ($bDelayColumn || $bDeleteColumn):
						?>
							<td class="control">
								<?
								if ($bDeleteColumn):
								?>
									<a href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delete"])?>"><?=GetMessage("SALE_DELETE")?></a><br />
								<?
								endif;
								if ($bDelayColumn):
								?>
									<a href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delay"])?>"><?=GetMessage("SALE_DELAY")?></a>
								<?
								endif;
								?>
							</td>
						<?
						endif;
						?>
							<td class="margin"></td>
					</tr>
					<?
					endif;
				endforeach;
				?>
			</tbody>
		</table>*/?>
	</div>
	<input type="hidden" id="column_headers" value="<?=CUtil::JSEscape(implode($arHeaders, ","))?>" />
	<input type="hidden" id="offers_props" value="<?=CUtil::JSEscape(implode($arParams["OFFERS_PROPS"], ","))?>" />
	<input type="hidden" id="action_var" value="<?=CUtil::JSEscape($arParams["ACTION_VARIABLE"])?>" />
	<input type="hidden" id="quantity_float" value="<?=$arParams["QUANTITY_FLOAT"]?>" />
	<input type="hidden" id="count_discount_4_all_quantity" value="<?=($arParams["COUNT_DISCOUNT_4_ALL_QUANTITY"] == "Y") ? "Y" : "N"?>" />
	<input type="hidden" id="price_vat_show_value" value="<?=($arParams["PRICE_VAT_SHOW_VALUE"] == "Y") ? "Y" : "N"?>" />
	<input type="hidden" id="hide_coupon" value="<?=($arParams["HIDE_COUPON"] == "Y") ? "Y" : "N"?>" />
	<input type="hidden" id="coupon_approved" value="N" />
	<input type="hidden" id="use_prepayment" value="<?=($arParams["USE_PREPAYMENT"] == "Y") ? "Y" : "N"?>" />

	<?/*<div class="bx_ordercart_order_pay">

		<div class="bx_ordercart_order_pay_left">
			<div class="bx_ordercart_coupon">
				<?
				if ($arParams["HIDE_COUPON"] != "Y"):

					$couponClass = "";
					if (array_key_exists('VALID_COUPON', $arResult))
					{
						$couponClass = ($arResult["VALID_COUPON"] === true) ? "good" : "bad";
					}
					elseif (array_key_exists('COUPON', $arResult) && !empty($arResult["COUPON"]))
					{
						$couponClass = "good";
					}

				?>
					<span><?=GetMessage("STB_COUPON_PROMT")?></span>
					<input type="text" id="coupon" name="COUPON" value="<?=$arResult["COUPON"]?>" onchange="enterCoupon();" size="21" class="<?=$couponClass?>">
				<?else:?>
					&nbsp;
				<?endif;?>
			</div>
		</div>

		<div class="bx_ordercart_order_pay_right">
			<table class="bx_ordercart_order_sum">
				<?if ($bWeightColumn):?>
					<tr>
						<td class="custom_t1"><?=GetMessage("SALE_TOTAL_WEIGHT")?></td>
						<td class="custom_t2" id="allWeight_FORMATED"><?=$arResult["allWeight_FORMATED"]?></td>
					</tr>
				<?endif;?>
				<?if ($arParams["PRICE_VAT_SHOW_VALUE"] == "Y"):?>
					<tr>
						<td><?echo GetMessage('SALE_VAT_EXCLUDED')?></td>
						<td id="allSum_wVAT_FORMATED"><?=$arResult["allSum_wVAT_FORMATED"]?></td>
					</tr>
					<tr>
						<td><?echo GetMessage('SALE_VAT_INCLUDED')?></td>
						<td id="allVATSum_FORMATED"><?=$arResult["allVATSum_FORMATED"]?></td>
					</tr>
				<?endif;?>

					<tr>
						<td class="fwb"><?=GetMessage("SALE_TOTAL")?></td>
						<td class="fwb" id="allSum_FORMATED"><?=str_replace(" ", "&nbsp;", $arResult["allSum_FORMATED"])?></td>
					</tr>
					<tr>
						<td class="custom_t1"></td>
						<td class="custom_t2" style="text-decoration:line-through; color:#828282;" id="PRICE_WITHOUT_DISCOUNT">
							<?if (floatval($arResult["DISCOUNT_PRICE_ALL"]) > 0):?>
								<?=$arResult["PRICE_WITHOUT_DISCOUNT"]?>
							<?endif;?>
						</td>
					</tr>

			</table>
			<div style="clear:both;"></div>
		</div>
		<div style="clear:both;"></div>

		<div class="bx_ordercart_order_pay_center">

			<?if ($arParams["USE_PREPAYMENT"] == "Y" && strlen($arResult["PREPAY_BUTTON"]) > 0):?>
				<?=$arResult["PREPAY_BUTTON"]?>
				<span><?=GetMessage("SALE_OR")?></span>
			<?endif;?>

			<a href="javascript:void(0)" onclick="checkOut();" class="checkout"><?=GetMessage("SALE_ORDER")?></a>
		</div>
	</div>*/?>
</div>
<?endif?>
<?
else:
?>
<div id="basket_items_list">
	<table>
		<tbody>
			<tr>
				<td colspan="<?=$numCells?>" style="text-align:center">
					<div class=""><?=GetMessage("SALE_NO_ITEMS");?></div>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?
endif;
?>