<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

use Citfact\Paolo;

if (!function_exists("showFilePropertyField"))
{
	function showFilePropertyField($name, $property_fields, $values, $max_file_size_show=50000)
	{
		$res = "";

		if (!is_array($values) || empty($values))
			$values = array(
				"n0" => 0,
			);

		if ($property_fields["MULTIPLE"] == "N")
		{
			$res = "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\"></label>";
		}
		else
		{
			$res = '
			<script type="text/javascript">
				function addControl(item)
				{
					var current_name = item.id.split("[")[0],
						current_id = item.id.split("[")[1].replace("[", "").replace("]", ""),
						next_id = parseInt(current_id) + 1;

					var newInput = document.createElement("input");
					newInput.type = "file";
					newInput.name = current_name + "[" + next_id + "]";
					newInput.id = current_name + "[" + next_id + "]";
					newInput.onchange = function() { addControl(this); };

					var br = document.createElement("br");
					var br2 = document.createElement("br");

					BX(item.id).parentNode.appendChild(br);
					BX(item.id).parentNode.appendChild(br2);
					BX(item.id).parentNode.appendChild(newInput);
				}
			</script>
			';

			$res .= "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\"></label>";
			$res .= "<br/><br/>";
			$res .= "<label for=\"\"><input type=\"file\" size=\"".$max_file_size_show."\" value=\"".$property_fields["VALUE"]."\" name=\"".$name."[1]\" id=\"".$name."[1]\" onChange=\"javascript:addControl(this);\"></label>";
		}

		return $res;
	}
}

if (!function_exists("PrintPropsForm"))
{
	function PrintPropsForm($arSource = array(), $locationTemplate = ".default", $arUser = array(), $arError = array())
	{
		global $USER;
		$isAuthorized = $USER->IsAuthorized();

		if (!empty($arSource))
		{
			?>
				<div>
					<?
					foreach ($arSource as $arProperties)
					{
						if ($arProperties['CODE'] == 'ADDRESS'
							|| $arProperties['CODE'] == 'LOCATION'
						){
							continue;
						}

						if(CSaleLocation::isLocationProMigrated())
						{
							$propertyAttributes = array(
								'type' => $arProperties["TYPE"],
								'valueSource' => $arProperties['SOURCE'] == 'DEFAULT' ? 'default' : 'form'
							);

							if(intval($arProperties['IS_ALTERNATE_LOCATION_FOR']))
								$propertyAttributes['isAltLocationFor'] = intval($arProperties['IS_ALTERNATE_LOCATION_FOR']);

							if(intval($arProperties['CAN_HAVE_ALTERNATE_LOCATION']))
								$propertyAttributes['altLocationPropId'] = intval($arProperties['CAN_HAVE_ALTERNATE_LOCATION']);

							if($arProperties['IS_ZIP'] == 'Y')
								$propertyAttributes['isZip'] = true;
						}
						?>


						<?
						if ($arProperties["TYPE"] == "CHECKBOX")
						{
							?>
                        <div data-property-id-row="<?=intval(intval($arProperties["ID"]))?>" class="<?=($arProperties['CODE'] == 'ZIP' or  $arProperties['CODE'] == 'CUP' or $arProperties)? 'hidden':''?>">
							<input type="hidden" name="<?=$arProperties["FIELD_NAME"]?>" value=""
							data-prop-code="<?=$arProperties['CODE']?>">

							<div class="bx_block r1x3 pt8">
								<div class="form__label">
                                    <?=$arProperties["NAME"]?>
                                    <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                        <span class="bx_sof_req">*</span>
                                    <?endif;?>
								</div>
							</div>

							<div class="bx_block r1x3 pt8">
								<input type="checkbox" name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" value="Y"<?if ($arProperties["CHECKED"]=="Y") echo " checked";?>>

								<?
								if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
								?>
								<div class="bx_description">
									<?=$arProperties["DESCRIPTION"]?>
								</div>
								<?
								endif;
								?>
							</div>

							<div style="clear: both;"></div>
							<?
						}
						elseif ($arProperties["TYPE"] == "TEXT")
						{

                             $value = $arProperties['VALUE'];
                            if ($isAuthorized === true && $arProperties['CODE'] == 'FIO') {
                                $value = $arUser["LAST_NAME"].' '.$arUser['NAME'].' '.$arUser["SECOND_NAME"];
                            }
                            if ($isAuthorized === true && $arProperties['CODE'] == 'EMAIL') {
                                $value = $arUser["EMAIL"];
                            }
                            if ($isAuthorized === true && $arProperties['CODE'] == 'PHONE'){
                                $value = $arUser["PERSONAL_PHONE"];
                                if($value=='') {
                                    $value = $arProperties['VALUE'];
                                }

                            }
                            if ($isAuthorized === true && $arProperties['CODE'] == 'LOYALTY_CARD')
                                $value = Paolo::getLoyaltycardCoupon();
                            if ($isAuthorized === true && $arProperties['CODE'] == 'USER_LOYALTY_CARD')
                                $value = Paolo::getUserLoyaltycard();
                            $class = '';
                            if ($arProperties['CODE'] == 'PHONE'){
                                $class = 'mask-phone';
                                $dataAttribute = 'data-phone';
                            }
                            if ($arProperties['CODE'] == 'LOYALTY_CARD'){
                                $class = 'mask-card';
                                $dataAttribute = 'data-card';
                                if($arUser['UF_LOYALTY_CARD']){
                                    $value = $arUser['UF_LOYALTY_CARD'];
                                    $class = 'mask-card-all';
                                }

                            }
                            if ($arProperties['CODE'] == 'CUP'){
                                $value = $_SESSION['CATALOG_USER_COUPONS'][0];
                            }
                            if(isset($arError['PRINT'][$arProperties['ID']])) {
                                $class .= ' input-error';
                            }
                            if ($isAuthorized === true && $arProperties['CODE'] == 'NAME') {
                                $value = $arUser['NAME'];
                            }
						    if ($isAuthorized === true && $arProperties['CODE'] == 'SECOND_NAME') {
                                $value = $arUser["SECOND_NAME"];
                            }
						    if ($isAuthorized === true && $arProperties['CODE'] == 'SURNAME') {
                                $value = $arUser["LAST_NAME"];
                            }

						    if (in_array($arProperties['CODE'], ['STREET', 'HOUSE', 'FLAT'])) {
                                continue;
                            }

                            $class .= ' property-' . $arProperties['CODE'];
						    $hidden = isHideProp($arProperties, $isAuthorized);
							?>

                            <div data-property-id-row="<?=intval(intval($arProperties["ID"]))?>"
                            class="form__item <?=(in_array($arProperties['CODE'], ['ZIP', 'CUP', 'LOYALTY_CARD']) or $hidden)? 'hidden':''?>"
							data-prop-code="<?=$arProperties['CODE']?>">

							<div class="bx_block r3x1 <?=($hidden)?"hidden":""?>" <?=$dataAttribute?>>
							    <label for="<?=$arProperties["FIELD_NAME"]?>" class="form__label"><?=$arProperties["PLACEHOLDER"]?>
							    <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
									<span class="bx_sof_req">*</span>
								<?endif;?>
							    </label>
                                <input
                                        type="text"
                                        maxlength="250"
                                        size="<?=$arProperties["SIZE1"]?>"
                                        value="<?=$value?>"
                                        name="<?=$arProperties["FIELD_NAME"]?>"
                                        id="<?=$arProperties["FIELD_NAME"]?>"
                                        class="<?=$class;?>"

                                        <?if (strlen(trim($arProperties["DESCRIPTION"])) > 0):?>
									        title="<?=$arProperties["DESCRIPTION"]?>"
								        <?endif;?>
                                />
                            <?
                            if(isset($arError['PRINT'][$arProperties['ID']])) { ?>
                                <div class="order-errors-cont show" style="padding: 0px;">
                                    <p><font class="errortext">
                                    <?php
                                    if ($arProperties['CODE'] == 'EMAIL') {
                                        echo '* Введите корректный e-mail';
                                    } else {
                                        echo $arError['MESSAGES'][$arError['PRINT'][$arProperties['ID']]];
                                    }
                                    ?>
                                    </font></p>
                                </div>
                            <? } ?>
							</div>
							<?
						}
						elseif ($arProperties["TYPE"] == "SELECT")
						{
							?>
							<div data-property-id-row="<?=intval(intval($arProperties["ID"]))?>"
							class="<?=($arProperties['CODE'] == 'ZIP' or  $arProperties['CODE'] == 'CUP' or $arProperties)? 'hidden':''?>"
							data-prop-code="<?=$arProperties['CODE']?>">
							<br/>
							<div class="bx_block r1x3 pt8">
								<div class="form__label"><?=$arProperties["NAME"]?></div>
								<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
									<span class="bx_sof_req">*</span>
								<?endif;?>
							</div>

							<div class="bx_block r3x1">
								<select name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
									<?
									foreach($arProperties["VARIANTS"] as $arVariants):
									?>
										<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
									<?
									endforeach;
									?>
								</select>

								<?
								if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
								?>
								<div class="bx_description">
									<?=$arProperties["DESCRIPTION"]?>
								</div>
								<?
								endif;
								?>
							</div>
							<div style="clear: both;"></div>
							<?
						}
						elseif ($arProperties["TYPE"] == "MULTISELECT")
						{
							?>
							<div data-property-id-row="<?=intval(intval($arProperties["ID"]))?>" class="<?=($arProperties['CODE'] == 'ZIP' or  $arProperties['CODE'] == 'CUP' or $arProperties)? 'hidden':''?>"
							data-prop-code="<?=$arProperties['CODE']?>">
							<br/>
							<div class="bx_block r1x3 pt8">
								<?=$arProperties["NAME"]?>
								<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
									<span class="bx_sof_req">*</span>
								<?endif;?>
							</div>

							<div class="bx_block r3x1">
								<select multiple name="<?=$arProperties["FIELD_NAME"]?>" id="<?=$arProperties["FIELD_NAME"]?>" size="<?=$arProperties["SIZE1"]?>">
									<?
									foreach($arProperties["VARIANTS"] as $arVariants):
									?>
										<option value="<?=$arVariants["VALUE"]?>"<?if ($arVariants["SELECTED"] == "Y") echo " selected";?>><?=$arVariants["NAME"]?></option>
									<?
									endforeach;
									?>
								</select>

								<?
								if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
								?>
								<div class="bx_description">
									<?=$arProperties["DESCRIPTION"]?>
								</div>
								<?
								endif;
								?>
							</div>
							<div style="clear: both;"></div>
							<?
						}
						elseif ($arProperties["TYPE"] == "TEXTAREA")
						{
							$rows = ($arProperties["SIZE2"] > 10) ? 4 : $arProperties["SIZE2"];
							?>
							<div data-property-id-row="<?=intval(intval($arProperties["ID"]))?>" class="<?=($arProperties['CODE'] == 'ZIP' or  $arProperties['CODE'] == 'CUP' or $arProperties)? 'hidden':''?>"
							data-prop-code="<?=$arProperties['CODE']?>">
							<br/>
							<div class="bx_block r1x3 pt8">
								<div class="form__label">
                                    <?=$arProperties["NAME"]?>
                                    <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                        <span class="bx_sof_req">*</span>
                                    <?endif;?>
								</div>
							</div>

							<div class="bx_block r3x1">
								<textarea
                                        rows="<?=$rows?>"
                                        cols="<?=$arProperties["SIZE1"]?>"
                                        name="<?=$arProperties["FIELD_NAME"]?>"
                                        id="<?=$arProperties["FIELD_NAME"]?>"
                                >
                                    <?=$arProperties["VALUE"]?>
                                </textarea>

								<?
								if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
								?>
								<div class="bx_description">
									<?=$arProperties["DESCRIPTION"]?>
								</div>
								<?
								endif;
								?>
							</div>
							<div style="clear: both;"></div>
							<?
						}
						elseif ($arProperties["TYPE"] == "LOCATION")
						{
							?>
							<div data-property-id-row="<?=intval(intval($arProperties["ID"]))?>" class="<?=($arProperties['CODE'] == 'ZIP' or  $arProperties['CODE'] == 'CUP' or $arProperties)? 'hidden':''?>"
							data-prop-code="<?=$arProperties['CODE']?>">
							<div class="bx_block r1x3 pt8">
								<?=$arProperties["NAME"]?>
								<?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
									<span class="bx_sof_req">*</span>
								<?endif;?>
							</div>

							<div class="bx_block r3x1">

								<?
								$value = 0;
								if (is_array($arProperties["VARIANTS"]) && count($arProperties["VARIANTS"]) > 0)
								{
									foreach ($arProperties["VARIANTS"] as $arVariant)
									{
										if ($arVariant["SELECTED"] == "Y")
										{
											$value = $arVariant["ID"];
											break;
										}
									}
								}

								// here we can get '' or 'popup'
								// map them, if needed
								if(CSaleLocation::isLocationProMigrated())
								{
									$locationTemplate = $locationTemplate == 'popup' ? 'search' : 'steps';
									$locationTemplate = $_REQUEST['PERMANENT_MODE_STEPS'] == 1 ? 'steps' : $locationTemplate; // force to "steps"
								}
								?>

								<?if($locationTemplate == 'steps'):?>
									<input type="hidden" id="LOCATION_ALT_PROP_DISPLAY_MANUAL[<?=intval($arProperties["ID"])?>]" name="LOCATION_ALT_PROP_DISPLAY_MANUAL[<?=intval($arProperties["ID"])?>]" value="<?=($_REQUEST['LOCATION_ALT_PROP_DISPLAY_MANUAL'][intval($arProperties["ID"])] ? '1' : '0')?>" />
								<?endif?>

								<?CSaleLocation::proxySaleAjaxLocationsComponent(array(
									"AJAX_CALL" => "N",
									"COUNTRY_INPUT_NAME" => "COUNTRY",
									"REGION_INPUT_NAME" => "REGION",
									"CITY_INPUT_NAME" => $arProperties["FIELD_NAME"],
									"CITY_OUT_LOCATION" => "Y",
									"LOCATION_VALUE" => $value,
									"ORDER_PROPS_ID" => $arProperties["ID"],
									"ONCITYCHANGE" => ($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitForm()" : "",
									"SIZE1" => $arProperties["SIZE1"],
								),
								array(
									"ID" => $value,
									"CODE" => "",
									"SHOW_DEFAULT_LOCATIONS" => "Y",

									// function called on each location change caused by user or by program
									// it may be replaced with global component dispatch mechanism coming soon
									"JS_CALLBACK" => "submitFormProxy", //($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitFormProxy" : "",

									// function window.BX.locationsDeferred['X'] will be created and lately called on each form re-draw.
									// it may be removed when sale.order.ajax will use real ajax form posting with BX.ProcessHTML() and other stuff instead of just simple iframe transfer
									"JS_CONTROL_DEFERRED_INIT" => intval($arProperties["ID"]),

									// an instance of this control will be placed to window.BX.locationSelectors['X'] and lately will be available from everywhere
									// it may be replaced with global component dispatch mechanism coming soon
									"JS_CONTROL_GLOBAL_ID" => intval($arProperties["ID"]),

									"DISABLE_KEYBOARD_INPUT" => 'N',
									"PRECACHE_LAST_LEVEL" => "Y",
								),
								$locationTemplate,
								true,
								'location-block-wrapper'
								)?>

								<?
								if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
								?>
								<div class="bx_description">
									<?=$arProperties["DESCRIPTION"]?>
								</div>
								<?
								endif;
								?>

							</div>
							<div style="clear: both;"></div>
							<?
						}
						elseif ($arProperties["TYPE"] == "RADIO")
						{
							?>
							<div data-property-id-row="<?=intval(intval($arProperties["ID"]))?>" class="<?=($arProperties['CODE'] == 'ZIP' or  $arProperties['CODE'] == 'CUP' or $arProperties)? 'hidden':''?>"
							data-prop-code="<?=$arProperties['CODE']?>">
							<div class="bx_block r1x3 pt8">
							    <div class="form__label">
                                    <?=$arProperties["NAME"]?>
                                    <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                        <span class="bx_sof_req">*</span>
                                    <?endif;?>
                                </div>
							</div>

							<div class="bx_block r3x1">
								<?
								if (is_array($arProperties["VARIANTS"]))
								{
									foreach($arProperties["VARIANTS"] as $arVariants):
									?>
										<input
											type="radio"
											name="<?=$arProperties["FIELD_NAME"]?>"
											id="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>"
											value="<?=$arVariants["VALUE"]?>" <?if($arVariants["CHECKED"] == "Y") echo " checked";?> />

										<label for="<?=$arProperties["FIELD_NAME"]?>_<?=$arVariants["VALUE"]?>"><?=$arVariants["NAME"]?></label></br>
									<?
									endforeach;
								}
								?>

								<?
								if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
								?>
								<div class="bx_description">
									<?=$arProperties["DESCRIPTION"]?>
								</div>
								<?
								endif;
								?>
							</div>
							<div style="clear: both;"></div>
							<?
						}
						elseif ($arProperties["TYPE"] == "FILE")
						{
							?>
							<div data-property-id-row="<?=intval(intval($arProperties["ID"]))?>" class="<?=($arProperties['CODE'] == 'ZIP' or  $arProperties['CODE'] == 'CUP' or $arProperties)? 'hidden':''?>"
							data-prop-code="<?=$arProperties['CODE']?>">
							<br/>
							<div class="bx_block r1x3 pt8">
							    <div class="form__label">
                                    <?=$arProperties["NAME"]?>
                                    <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                        <span class="bx_sof_req">*</span>
                                    <?endif;?>
                                </div>
							</div>

							<div class="bx_block r3x1">
								<?=showFilePropertyField("ORDER_PROP_".$arProperties["ID"], $arProperties, $arProperties["VALUE"], $arProperties["SIZE1"])?>

								<?
								if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
								?>
								<div class="bx_description">
									<?=$arProperties["DESCRIPTION"]?>
								</div>
								<?
								endif;
								?>
							</div>

							<div style="clear: both;"></div><br/>
							<?
						}
						?>
						</div>

						<?if(CSaleLocation::isLocationProEnabled()):?>
							<script>

								(window.top.BX || BX).saleOrderAjax.addPropertyDesc(<?=CUtil::PhpToJSObject(array(
									'id' => intval($arProperties["ID"]),
									'attributes' => $propertyAttributes
								))?>);

							</script>
						<?endif?>

						<?
					}
					?>
				</div>
			<?
		}
	}
}


if (!function_exists("PrintLocationAndAddress"))
{
	function PrintLocationAndAddress($arSource = array(), $locationTemplate = ".default", $saved_location, $saved_address = '', $arError = array(), $checkedDelivery = false, $checkedPaySystem = false)
	{
		if (!empty($arSource))
		{
			?>
			<div>
				<?
				foreach ($arSource as $arProperties)
				{
					if ($arProperties['CODE'] != 'ADDRESS'
						&& $arProperties['CODE'] != 'LOCATION'
					){
						continue;
					}

					$haveDRelation = false;
					$havePRelation = false;
					$dCheck = false;
					$pCheck = false;
					if (!empty($arProperties['RELATION'])) {
					    foreach ($arProperties['RELATION'] as $relation) {
					        if ($relation['ENTITY_TYPE'] == 'D') {
					            $haveDRelation = true;
					            if ($relation['ENTITY_ID'] == $checkedDelivery) {
					                $dCheck = true;
					            }
					        }
					        if ($relation['ENTITY_TYPE'] == 'P') {
					            $havePRelation = true;
					            if ($relation['ENTITY_ID'] == $checkedPaySystem) {
					                $pCheck = true;
					            }
					        }
					    }
					}
					if (($haveDRelation && !$dCheck) || ($havePRelation && !$pCheck)) {
					    continue;
					}

					if(CSaleLocation::isLocationProMigrated())
					{
						$propertyAttributes = array(
							'type' => $arProperties["TYPE"],
							'valueSource' => $arProperties['SOURCE'] == 'DEFAULT' ? 'default' : 'form'
						);

						if(intval($arProperties['IS_ALTERNATE_LOCATION_FOR']))
							$propertyAttributes['isAltLocationFor'] = intval($arProperties['IS_ALTERNATE_LOCATION_FOR']);

						if(intval($arProperties['CAN_HAVE_ALTERNATE_LOCATION']))
							$propertyAttributes['altLocationPropId'] = intval($arProperties['CAN_HAVE_ALTERNATE_LOCATION']);

						if($arProperties['IS_ZIP'] == 'Y')
							$propertyAttributes['isZip'] = true;
					}
					?>
					<div data-property-id-row="<?=intval(intval($arProperties["ID"]))?>"
							data-prop-code="<?=$arProperties['CODE']?>"
							 class="form__item">

						<?
						if ($arProperties["TYPE"] == "TEXTAREA")
						{
                            $arErrorBorderColor = '';
                            if($arError['PRINT'][$arProperties['ID']] != '') {
                                $arErrorBorderColor = 'border-color: red';
                            }
                            if($arError['RELATED'][$arProperties['ID']] != '') {
                                $arErrorBorderColor = 'border-color: red';
                            }
							$rows = ($arProperties["SIZE2"] > 10) ? 4 : $arProperties["SIZE2"];
							?>
							<br/>

							<div class="form__item bx_block r3x1">
							    <label for="<?=$arProperties["FIELD_NAME"]?>" class="form__label">Ваш адрес</label>
								<textarea placeholder="<?=$arProperties["PLACEHOLDER"]?>"
								          rows="<?=$rows?>"
                                          cols="<?=$arProperties["SIZE1"]?>"
                                          name="<?=$arProperties["FIELD_NAME"]?>"
                                          id="<?=$arProperties["FIELD_NAME"]?>"
                                          style="<?=$arErrorBorderColor;?>"
                                          <?if (strlen(trim($arProperties["DESCRIPTION"])) > 0):?>
									      title="<?=$arProperties["DESCRIPTION"]?>"
								          <?endif;?>
                                ><?=($saved_address!=''? $saved_address:$arProperties["VALUE"])?></textarea>
                                <textarea name="OLD_ADDRESS" class="hidden"><?=($saved_address!=''? $saved_address:$arProperties["VALUE"])?></textarea>
								<input name="PICKUP_ADDRESS" id="PICKUP_ADDRESS" type="hidden" class="hidden" value="<?=htmlspecialcharsEx($_REQUEST['PICKUP_ADDRESS']);?>" />
							</div>
							<div style="clear: both;"></div>
						<?
						}
						elseif ($arProperties["TYPE"] == "LOCATION")
						{
							?>
							<div class="bx_block r1x3 pt8">
								<div class="form__label">
                                    <?=$arProperties["NAME"]?>
                                    <?if ($arProperties["REQUIED_FORMATED"]=="Y"):?>
                                        <span class="bx_sof_req">*</span>
                                    <?endif;?>
								</div>
								<?
								$value = 0;
								if (is_array($arProperties["VARIANTS"]) && count($arProperties["VARIANTS"]) > 0)
								{
									foreach ($arProperties["VARIANTS"] as $arVariant)
									{
										if ($arVariant["SELECTED"] == "Y")
										{
											$value = $arVariant["ID"];
											break;
										}
									}
								}

								/*if ($saved_location != '')
									$value = $saved_location;*/

								// here we can get '' or 'popup'
								// map them, if needed
								if(CSaleLocation::isLocationProMigrated())
								{
									$locationTemplate = $locationTemplate == 'popup' ? 'search' : 'steps';
									$locationTemplate = $_REQUEST['PERMANENT_MODE_STEPS'] == 1 ? 'steps' : $locationTemplate; // force to "steps"
								}
								?>

								<?if($locationTemplate == 'steps'):?>
									<input type="hidden" id="LOCATION_ALT_PROP_DISPLAY_MANUAL[<?=intval($arProperties["ID"])?>]" name="LOCATION_ALT_PROP_DISPLAY_MANUAL[<?=intval($arProperties["ID"])?>]" value="<?=($_REQUEST['LOCATION_ALT_PROP_DISPLAY_MANUAL'][intval($arProperties["ID"])] ? '1' : '0')?>" />
								<?endif?>

								<?CSaleLocation::proxySaleAjaxLocationsComponent(array(
									"AJAX_CALL" => "N",
									"COUNTRY_INPUT_NAME" => "COUNTRY",
									"REGION_INPUT_NAME" => "REGION",
									"CITY_INPUT_NAME" => $arProperties["FIELD_NAME"],
									"CITY_OUT_LOCATION" => "Y",
									"LOCATION_VALUE" => $value,
									"ORDER_PROPS_ID" => $arProperties["ID"],
									"ONCITYCHANGE" => ($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitForm()" : "",
									"SIZE1" => $arProperties["SIZE1"],
								),
									array(
										"ID" => ($saved_location != ''? $saved_location:$value),
										"CODE" => "",
										"SHOW_DEFAULT_LOCATIONS" => "Y",

										// function called on each location change caused by user or by program
										// it may be replaced with global component dispatch mechanism coming soon
										"JS_CALLBACK" => "submitFormProxy", //($arProperties["IS_LOCATION"] == "Y" || $arProperties["IS_LOCATION4TAX"] == "Y") ? "submitFormProxy" : "",

										// function window.BX.locationsDeferred['X'] will be created and lately called on each form re-draw.
										// it may be removed when sale.order.ajax will use real ajax form posting with BX.ProcessHTML() and other stuff instead of just simple iframe transfer
										"JS_CONTROL_DEFERRED_INIT" => intval($arProperties["ID"]),

										// an instance of this control will be placed to window.BX.locationSelectors['X'] and lately will be available from everywhere
										// it may be replaced with global component dispatch mechanism coming soon
										"JS_CONTROL_GLOBAL_ID" => intval($arProperties["ID"]),

										"DISABLE_KEYBOARD_INPUT" => 'N',
										"PRECACHE_LAST_LEVEL" => "Y",
									),
									$locationTemplate,
									true,
									'location-block-wrapper'
								)?>
								<?
								if (strlen(trim($arProperties["DESCRIPTION"])) > 0):
									?>
									<div class="bx_description">
										<?=$arProperties["DESCRIPTION"]?>
									</div>
								<?
								endif;
								?>
							</div>
						<?
						}
						?>
					</div>

				<?if(CSaleLocation::isLocationProEnabled()):?>
					<script>

						(window.top.BX || BX).saleOrderAjax.addPropertyDesc(<?=CUtil::PhpToJSObject(array(
									'id' => intval($arProperties["ID"]),
									'attributes' => $propertyAttributes
								))?>);

					</script>
				<?endif?>

				<?
				}
				?>
			</div>
		<?
		}
	}
}
?>