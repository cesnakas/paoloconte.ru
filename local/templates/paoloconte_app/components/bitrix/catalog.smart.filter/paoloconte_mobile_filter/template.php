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

$templateData = array(
	'TEMPLATE_THEME' => $this->GetFolder().'/themes/'.$arParams['TEMPLATE_THEME'].'/colors.css',
	'TEMPLATE_CLASS' => 'bx_'.$arParams['TEMPLATE_THEME']
);
?>
<div class="bx_filter filter-wrap">
	<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter">
		<?foreach($arResult["HIDDEN"] as $arItem):?>
			<input type="hidden" name="<?echo $arItem["CONTROL_NAME"]?>" id="<?echo $arItem["CONTROL_ID"]?>" value="<?echo $arItem["HTML_VALUE"]?>" />
		<?endforeach;?>
		<div class="panel-group styled-accordion" id="" role="tablist" aria-multiselectable="true">
			<?foreach($arResult["ITEMS"] as $key=>$arItem):
				if(empty($arItem["VALUES"]) || isset($arItem["PRICE"])) continue;
				if($arItem["DISPLAY_TYPE"] == "A" && (!$arItem["VALUES"]["MIN"]["VALUE"] || !$arItem["VALUES"]["MAX"]["VALUE"] || $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"])) continue;
				$active = ($arItem["DISPLAY_EXPANDED"]== "Y")? true : false;
				?>
				<div class="panel bx_filter_parameters_box">
					<div class="panel-heading pts-bold" role="tab" id="heading-f<?=$arItem["ID"]?>">
						<a class="<?if(!$active) print'collapsed'?>" data-toggle="collapse" href="#collapse-f<?=$arItem["ID"]?>" aria-expanded="true" aria-controls="collapse-f<?=$arItem["ID"]?>">
							<?=$arItem["NAME"]?>
							<i class="fa plus fa-plus"></i>
							<i class="fa minus fa-minus"></i>
						</a>
					</div>
					<span class="bx_filter_container_modef"></span>
					<div id="collapse-f<?=$arItem["ID"]?>" class="panel-collapse collapse <?if($active) print'in'?>" role="tabpanel" aria-labelledby="heading-f<?=$arItem["ID"]?>">
						<div class="panel-body">
							<?
							switch ($arItem["DISPLAY_TYPE"]){
								case "G": //CHECKBOXES_WITH_PICTURES
								case "H": //CHECKBOXES_WITH_PICTURES_AND_LABELS
									?>
									<div class="radio-box mode-inline mode-colored">
										<?foreach ($arItem["VALUES"] as $val => $ar):?>
											<?
											$class = "";
											if ($ar["CHECKED"])
												$class.= " active";
											if ($ar["DISABLED"])
												$class.= " disabled";

											if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"]))
												$image = $ar["FILE"]["SRC"];
											else
												$image = '/images/icons/multicolor.jpg';
											?>

											<label
												data-role="label_<?=$ar["CONTROL_ID"]?>"
												class="<?=$class?>"
												style="background-image:url('<?=$image?>');"
												onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'active');"
												title="<?=$ar['VALUE']?>"
												>
												<input
													type="checkbox"
													name="<?=$ar["CONTROL_NAME"]?>"
													id="<?=$ar["CONTROL_ID"]?>"
													value="<?=$ar["HTML_VALUE"]?>"
													<?=$ar["CHECKED"]? 'checked="checked"': ''?>
													style="display: none"
													>
											</label>
										<?endforeach?>
									</div>
									<?
									break;
								default: //CHECKBOXES
									?>
									<div class="check-box">
										<?foreach($arItem["VALUES"] as $val => $ar):?>
											<div class="line <?=($arItem["CODE"] == 'RAZMER'? 'infilter':'')?>">
												<input
													id="<?=$ar["CONTROL_ID"]?>"
													type="checkbox"
													name="<?=$ar["CONTROL_NAME"]?>"
													value="<?=$ar["HTML_VALUE"]?>"
													<?=$ar["CHECKED"]? 'checked="checked"': ''?>
													onclick="smartFilter.click(this)"
													>
												<label
													data-role="label_<?=$ar["CONTROL_ID"]?>"
													for="<?=$ar["CONTROL_ID"]?>"
													<?=$ar["DISABLED"] ? 'disabled': '' ?>
													>
													<?=$ar["VALUE"] == 'Y'? 'Да' : $ar["VALUE"];?>
												</label>
											</div>
										<?endforeach;?>
									</div>
									<?
							}
							?>
						</div>
					</div>
				</div>
			<?endforeach?>
		</div>
		<div class="bx_filter_popup_result <?=$arParams["POPUP_POSITION"]?>" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) print 'style="display:none"';?> style="display: inline-block;">
			<?=GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
			<span class="arrow"></span>
			<a href="<?=$arResult["FILTER_URL"]?>"><?=GetMessage("CT_BCSF_FILTER_SHOW")?></a>
		</div>
		<div class="btn-box align-center">
			<input id="setFilter" class="hidden" type="submit" id="set_filter" name="set_filter" value="Показать" />
			<input id="resetFilter" class="hidden" type="submit" id="del_filter" name="del_filter" value="Сбросить фильтр>" />

			<label for="setFilter" class="btn btn-gray-dark full big fa mode1 fa-eye setFilter"><span>Показать</span></label>
			<label for="resetFilter" class="refresh resetFilter">Сбросить фильтр</label>
		</div>
	</form>
</div>

<?/*ТУТ ЕСЛИ НАДО ЛЕЖИТ СТАНДАРТНЫЙ ШАБЛОН ФИЛЬТРА, ОН ПО УСЛОВИЮ ОТКЛЮЧЕН*/?>
<?if(false):?>
<div class="bx_filter <?=$templateData["TEMPLATE_CLASS"]?> <?if ($arParams["FILTER_VIEW_MODE"] == "horizontal"):?>bx_horizontal<?endif?>">
	<div class="bx_filter_section">
		<div class="bx_filter_title"><?echo GetMessage("CT_BCSF_FILTER_TITLE")?></div>
		<form name="<?echo $arResult["FILTER_NAME"]."_form"?>" action="<?echo $arResult["FORM_ACTION"]?>" method="get" class="smartfilter">
			<?foreach($arResult["HIDDEN"] as $arItem):?>
			<input type="hidden" name="<?echo $arItem["CONTROL_NAME"]?>" id="<?echo $arItem["CONTROL_ID"]?>" value="<?echo $arItem["HTML_VALUE"]?>" />
			<?endforeach;
			//prices
			foreach($arResult["ITEMS"] as $key=>$arItem)
			{
				$key = $arItem["ENCODED_ID"];
				if(isset($arItem["PRICE"])):
					if (!$arItem["VALUES"]["MIN"]["VALUE"] || !$arItem["VALUES"]["MAX"]["VALUE"] || $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"])
						continue;
					?>
					<div class="bx_filter_parameters_box active">
						<span class="bx_filter_container_modef"></span>
						<div class="bx_filter_parameters_box_title" onclick="smartFilter.hideFilterProps(this)"><?=$arItem["NAME"]?></div>
						<div class="bx_filter_block">
							<div class="bx_filter_parameters_box_container">
								<div class="bx_filter_parameters_box_container_block">
									<div class="bx_filter_input_container">
										<input
											class="min-price"
											type="text"
											name="<?echo $arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>"
											id="<?echo $arItem["VALUES"]["MIN"]["CONTROL_ID"]?>"
											value="<?echo $arItem["VALUES"]["MIN"]["HTML_VALUE"]?>"
											size="5"
											onkeyup="smartFilter.keyup(this)"
										/>
									</div>
								</div>
								<div class="bx_filter_parameters_box_container_block">
									<div class="bx_filter_input_container">
										<input
											class="max-price"
											type="text"
											name="<?echo $arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>"
											id="<?echo $arItem["VALUES"]["MAX"]["CONTROL_ID"]?>"
											value="<?echo $arItem["VALUES"]["MAX"]["HTML_VALUE"]?>"
											size="5"
											onkeyup="smartFilter.keyup(this)"
										/>
									</div>
								</div>
								<div style="clear: both;"></div>

								<div class="bx_ui_slider_track" id="drag_track_<?=$key?>">
									<?
									$price1 = $arItem["VALUES"]["MIN"]["VALUE"];
									$price2 = $arItem["VALUES"]["MIN"]["VALUE"] + round(($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"])/4);
									$price3 = $arItem["VALUES"]["MIN"]["VALUE"] + round(($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"])/2);
									$price4 = $arItem["VALUES"]["MIN"]["VALUE"] + round((($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"])*3)/4);
									$price5 = $arItem["VALUES"]["MAX"]["VALUE"];
									?>
									<div class="bx_ui_slider_part p1"><span><?=$price1?></span></div>
									<div class="bx_ui_slider_part p2"><span><?=$price2?></span></div>
									<div class="bx_ui_slider_part p3"><span><?=$price3?></span></div>
									<div class="bx_ui_slider_part p4"><span><?=$price4?></span></div>
									<div class="bx_ui_slider_part p5"><span><?=$price5?></span></div>

									<div class="bx_ui_slider_pricebar_VD" style="left: 0;right: 0;" id="colorUnavailableActive_<?=$key?>"></div>
									<div class="bx_ui_slider_pricebar_VN" style="left: 0;right: 0;" id="colorAvailableInactive_<?=$key?>"></div>
									<div class="bx_ui_slider_pricebar_V"  style="left: 0;right: 0;" id="colorAvailableActive_<?=$key?>"></div>
									<div class="bx_ui_slider_range" id="drag_tracker_<?=$key?>"  style="left: 0%; right: 0%;">
										<a class="bx_ui_slider_handle left"  style="left:0;" href="javascript:void(0)" id="left_slider_<?=$key?>"></a>
										<a class="bx_ui_slider_handle right" style="right:0;" href="javascript:void(0)" id="right_slider_<?=$key?>"></a>
									</div>
								</div>
								<div style="opacity: 0;height: 1px;"></div>
							</div>
						</div>
					</div>
					<?
					$presicion = 2;
					if (Bitrix\Main\Loader::includeModule("currency"))
					{
						$res = CCurrencyLang::GetFormatDescription($arItem["VALUES"]["MIN"]["CURRENCY"]);
						$presicion = $res['DECIMALS'];
					}
					$arJsParams = array(
						"leftSlider" => 'left_slider_'.$key,
						"rightSlider" => 'right_slider_'.$key,
						"tracker" => "drag_tracker_".$key,
						"trackerWrap" => "drag_track_".$key,
						"minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
						"maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
						"minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
						"maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
						"curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
						"curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
						"fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"] ,
						"fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
						"precision" => $presicion,
						"colorUnavailableActive" => 'colorUnavailableActive_'.$key,
						"colorAvailableActive" => 'colorAvailableActive_'.$key,
						"colorAvailableInactive" => 'colorAvailableInactive_'.$key,
					);
					?>
					<script type="text/javascript">
						BX.ready(function(){
							window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>);
						});
					</script>
				<?endif;
			}

			//not prices
			foreach($arResult["ITEMS"] as $key=>$arItem)
			{
				if(empty($arItem["VALUES"]) || isset($arItem["PRICE"])) continue;

				if ($arItem["DISPLAY_TYPE"] == "A" && (!$arItem["VALUES"]["MIN"]["VALUE"] || !$arItem["VALUES"]["MAX"]["VALUE"] || $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"])) continue;
				?>
				<div class="bx_filter_parameters_box <?if ($arItem["DISPLAY_EXPANDED"]== "Y"):?>active<?endif?>">
					<span class="bx_filter_container_modef"></span>
					<div class="bx_filter_parameters_box_title" onclick="smartFilter.hideFilterProps(this)"><?=$arItem["NAME"]?></div>
					<div class="bx_filter_block">
						<div class="bx_filter_parameters_box_container">
						<?
						$arCur = current($arItem["VALUES"]);
						switch ($arItem["DISPLAY_TYPE"])
						{
							case "G"://CHECKBOXES_WITH_PICTURES
								?>
								<?foreach ($arItem["VALUES"] as $val => $ar):?>
									<input
										style="display: none"
										type="checkbox"
										name="<?=$ar["CONTROL_NAME"]?>"
										id="<?=$ar["CONTROL_ID"]?>"
										value="<?=$ar["HTML_VALUE"]?>"
										<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
									/>
									<?
									$class = "";
									if ($ar["CHECKED"])
										$class.= " active";
									if ($ar["DISABLED"])
										$class.= " disabled";
									?>
									<label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx_filter_param_label dib<?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'active');">
										<span class="bx_filter_param_btn bx_color_sl">
											<?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
											<span class="bx_filter_btn_color_icon" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
											<?endif?>
										</span>
									</label>
								<?endforeach?>
								<?
								break;
							case "H"://CHECKBOXES_WITH_PICTURES_AND_LABELS
								?>
								<?foreach ($arItem["VALUES"] as $val => $ar):?>
									<input
										style="display: none"
										type="checkbox"
										name="<?=$ar["CONTROL_NAME"]?>"
										id="<?=$ar["CONTROL_ID"]?>"
										value="<?=$ar["HTML_VALUE"]?>"
										<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
									/>
									<?
									$class = "";
									if ($ar["CHECKED"])
										$class.= " active";
									if ($ar["DISABLED"])
										$class.= " disabled";
									?>
									<label for="<?=$ar["CONTROL_ID"]?>" data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx_filter_param_label<?=$class?>" onclick="smartFilter.keyup(BX('<?=CUtil::JSEscape($ar["CONTROL_ID"])?>')); BX.toggleClass(this, 'active');">
										<span class="bx_filter_param_btn bx_color_sl">
											<?if (isset($ar["FILE"]) && !empty($ar["FILE"]["SRC"])):?>
												<span class="bx_filter_btn_color_icon" style="background-image:url('<?=$ar["FILE"]["SRC"]?>');"></span>
											<?endif?>
										</span>
										<span class="bx_filter_param_text">
											<?=$ar["VALUE"]?>
										</span>
									</label>
								<?endforeach?>
								<?
								break;
							default://CHECKBOXES
								?>
								<?foreach($arItem["VALUES"] as $val => $ar):?>
									<label data-role="label_<?=$ar["CONTROL_ID"]?>" class="bx_filter_param_label <? echo $ar["DISABLED"] ? 'disabled': '' ?>" for="<? echo $ar["CONTROL_ID"] ?>">
										<span class="bx_filter_input_checkbox">
											<input
												type="checkbox"
												value="<? echo $ar["HTML_VALUE"] ?>"
												name="<? echo $ar["CONTROL_NAME"] ?>"
												id="<? echo $ar["CONTROL_ID"] ?>"
												<? echo $ar["CHECKED"]? 'checked="checked"': '' ?>
												onclick="smartFilter.click(this)"
											/>
											<span class="bx_filter_param_text"><? echo $ar["VALUE"]; ?></span>
										</span>
									</label>
								<?endforeach;?>
						<?
						}
						?>
						</div>
						<div class="clb"></div>
					</div>
				</div>
			<?
			}
			?>
			<div class="clb"></div>
			<div class="bx_filter_button_box active">
				<div class="bx_filter_block">
					<div class="bx_filter_parameters_box_container">
						<input class="bx_filter_search_button" type="submit" id="set_filter" name="set_filter" value="<?=GetMessage("CT_BCSF_SET_FILTER")?>" />
						<input class="bx_filter_search_reset" type="submit" id="del_filter" name="del_filter" value="<?=GetMessage("CT_BCSF_DEL_FILTER")?>" />

						<div class="bx_filter_popup_result <?=$arParams["POPUP_POSITION"]?>" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"';?> style="display: inline-block;">
							<?echo GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
							<span class="arrow"></span>
							<a href="<?echo $arResult["FILTER_URL"]?>"><?echo GetMessage("CT_BCSF_FILTER_SHOW")?></a>
						</div>
					</div>
				</div>
			</div>
		</form>
		<div style="clear: both;"></div>
	</div>
</div>
<?endif?>

<script>
	var smartFilter = new JCSmartFilter('<?echo CUtil::JSEscape($arResult["FORM_ACTION"])?>', '<?=CUtil::JSEscape($arParams["FILTER_VIEW_MODE"])?>');
</script>