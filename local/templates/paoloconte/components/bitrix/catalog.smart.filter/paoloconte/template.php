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
$this->SetViewTarget("smart_filter");
?>
<div class="bx_filter header-m c-filter" data-menu="filter">
    <div class="header-m__inner">
        <div class="header-m__title">
            Фильтры
        </div>
        <div class="header-m__close" data-menu-close >
            <div class="plus plus--cross"></div>
        </div>
        
        <div class="header-m__items">
            <form name="<?echo $arResult["FILTER_NAME"]."_form"?>"
                  action="<?echo $arResult["FORM_ACTION"]?>"
                  method="get"
                  class="c-filter__form"
                  role="tablist"
                  aria-multiselectable="true">
              <div class="c-filter__items">
                  <?foreach($arResult["HIDDEN"] as $arItem):?>
                    <input type="hidden" name="<?echo $arItem["CONTROL_NAME"]?>" id="<?echo $arItem["CONTROL_ID"]?>" value="<?echo $arItem["HTML_VALUE"]?>" />
                  <?endforeach;?>

                  <?foreach($arResult["ITEMS"] as $key=>$arItem):
                      if(empty($arItem["VALUES"]) || isset($arItem["PRICE"])) continue;
                      if($arItem["DISPLAY_TYPE"] == "A" && (!$arItem["VALUES"]["MIN"]["VALUE"] || !$arItem["VALUES"]["MAX"]["VALUE"] || $arItem["VALUES"]["MIN"]["VALUE"] == $arItem["VALUES"]["MAX"]["VALUE"])) continue;
                      $active = ($arItem["DISPLAY_EXPANDED"]== "Y")? true : false;
                      ?>

                    <div class="c-filter__item bx_filter_parameters_box" data-toggle-wrap>
                      <div class="c-filter__title" role="tab" id="heading-f<?=$arItem["ID"]?>" data-toggle-btn>
                          <?=$arItem["NAME"]?>
                        <div class="plus" data-toggle-btn></div>
                      </div>
                      <span class="bx_filter_container_modef"></span>
                      <div class="c-filter__list hide" role="tabpanel" data-toggle-list>
                          <?
                          switch ($arItem["DISPLAY_TYPE"]){
                              case "G": //CHECKBOXES_WITH_PICTURES
                              case "H": //CHECKBOXES_WITH_PICTURES_AND_LABELS
                                  ?>
                                  <? foreach ($arItem["VALUES"] as $val => $ar):?>
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

                                  if ($arItem['CODE'] == 'TSVET_DLYA_FILTRA' && empty($ar['VALUE'])) {
                                      continue;
                                  }
                                  ?>
                                <label  class="c-filter__color <?=$class?>"
                                        data-role="label_<?=$ar["CONTROL_ID"]?>"
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

                                  <?
                                  break;
                              default: //CHECKBOXES
                                  ?>
                                  <?foreach($arItem["VALUES"] as $val => $ar):?>
                                  <?if ($arItem["CODE"] == 'RAZMER') {?>
                                  <div class="c-filter__size">
                                    <input type="checkbox"
                                           id="<?=$ar["CONTROL_ID"]?>"
                                           name="<?=$ar["CONTROL_NAME"]?>"
                                           value="<?=$ar["HTML_VALUE"]?>"
                                        <?=$ar["CHECKED"]? 'checked="checked"': ''?>
                                           onclick="smartFilter.click(this)">
                                    <label for="<?=$ar["CONTROL_ID"]?>"><?=$ar["VALUE"]?></label>
                                  </div>
                                  <?} else {?>
                                  <div class="b-checkbox">
                                    <label class="b-checkbox__label"
                                           data-role="label_<?=$ar["CONTROL_ID"]?>"
                                        <?=$ar["DISABLED"] ? 'disabled': '' ?>>
                                      <input type="checkbox"
                                             id="<?=$ar["CONTROL_ID"]?>"
                                             name="<?=$ar["CONTROL_NAME"]?>"
                                             value="<?=$ar["HTML_VALUE"]?>"
                                          <?=$ar["CHECKED"]? 'checked="checked"': ''?>
                                             onclick="smartFilter.click(this)"
                                             class="b-checkbox__input">
                                      <span class="b-checkbox__box">
                                                        <span class="b-checkbox__line b-checkbox__line--short"></span>
                                                        <span class="b-checkbox__line b-checkbox__line--long"></span>
                                                    </span>
                                      <span class="b-checkbox__text"><?=$ar["VALUE"] == 'Y'? 'Да' : $ar["VALUE"];?></span>
                                    </label>
                                  </div>
                                  <?}?>
                              <?endforeach;?>
                              <?
                          }
                          ?>
                      </div>
                    </div>
                  <?endforeach?>



                <div class="bx_filter_popup_result <?=$arParams["POPUP_POSITION"]?>" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) print 'style="display:none"';?> style="display: inline-block;">
                    <?=GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
                  <span class="arrow"></span>
                  <a href="<?=$arResult["FILTER_URL"]?>"><?=GetMessage("CT_BCSF_FILTER_SHOW")?></a>
                </div>
              </div>
                <div class="c-filter__btns">


                    <input id="setFilter" class="hidden" type="submit" id="set_filter" name="set_filter" value="y" />
                    <input id="resetFilter" class="hidden" type="submit" id="del_filter" name="del_filter" value="y" />

                    <button type="button" class="send-form btn btn--transparent">Применить</button>
                    <label for="resetFilter" class="c-filter__clear">
                        <span>Сбросить фильтр</span>
                    </label>
                    <div class="selected-fitler">
                        <? foreach ($arResult["ITEMS"] as $key => $arItem) {
                            foreach ($arItem["VALUES"] as $item) {
                                if ($item["CHECKED"] == "1") {
                                    if ($item["VALUE"] == "Y") {
                                        $item["VALUE"] = "Да";
                                    }
                                    switch ($arItem["TYPE"]) {
                                        case "NUMBER" :
                                            ?><a class="filter__item-del" href="javascript:void(0);"
                                                 data-id="<?= $arItem["CODE"] . $item["UF_XML_ID"] ?>"><span
                                                class="plus"></span>
                                            <?
                                            echo $arItem["VALUE"]["FROM"] . " - " . $arItem["VALUE"]["TO"]; ?>
                                            </a><?
                                            break;
                                        case "DEFAULT" :
                                            ?><a class="filter__item-del" href="javascript:void(0);"
                                                 data-id="<?= $item["VALUE"] ?>"><span
                                                class="plus"></span><?= $item["VALUE"]; ?>
                                            </a><?
                                            break;
                                        default :
                                            ?><a class="filter__item-del" href="javascript:void(0);"
                                                 data-id="<?= $item["CONTROL_ID"] ?>"><span class="plus"></span>
                                            <?
                                            echo $item["VALUE"];
                                            ?></a><?
                                            break;
                                    }
                                }
                            }
                        } ?>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="header-m__bottom">
            <label for="setFilter" class="btn btn--black setFilter"><span>Показать</span></label>
            <label for="resetFilter" class="c-filter__clear">
                <span>Сбросить фильтр</span>
                <span class="btn btn--transparent">Сбросить</span>
            </label>
        </div>
    </div>
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

						<?/*<div class="bx_filter_popup_result <?=$arParams["POPUP_POSITION"]?>" id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) echo 'style="display:none"';?> style="display: inline-block;">
							<?echo GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
							<span class="arrow"></span>
							<a href="<?echo $arResult["FILTER_URL"]?>"><?echo GetMessage("CT_BCSF_FILTER_SHOW")?></a>
						</div>*/?>
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
<?$this->EndViewTarget();?>