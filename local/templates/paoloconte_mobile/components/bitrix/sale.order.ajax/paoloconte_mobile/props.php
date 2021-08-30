<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/props_format.php");
?>

<?/*
<div class="section">
<h4><?=GetMessage("SOA_TEMPL_PROP_INFO")?></h4>
	<?
	$bHideProps = true;

	if (is_array($arResult["ORDER_PROP"]["USER_PROFILES"]) && !empty($arResult["ORDER_PROP"]["USER_PROFILES"])):
		if ($arParams["ALLOW_NEW_PROFILE"] == "Y"):
		?>
			<div class="bx_block r1x3">
				<?=GetMessage("SOA_TEMPL_PROP_CHOOSE")?>
			</div>
			<div class="bx_block r3x1">
				<select name="PROFILE_ID" id="ID_PROFILE_ID" onChange="SetContact(this.value)">
					<option value="0"><?=GetMessage("SOA_TEMPL_PROP_NEW_PROFILE")?></option>
					<?
					foreach($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles)
					{
						?>
						<option value="<?= $arUserProfiles["ID"] ?>"<?if ($arUserProfiles["CHECKED"]=="Y") echo " selected";?>><?=$arUserProfiles["NAME"]?></option>
						<?
					}
					?>
				</select>
				<div style="clear: both;"></div>
			</div>
		<?
		else:
		?>
			<div class="bx_block r1x3">
				<?=GetMessage("SOA_TEMPL_EXISTING_PROFILE")?>
			</div>
			<div class="bx_block r3x1">
					<?
					if (count($arResult["ORDER_PROP"]["USER_PROFILES"]) == 1)
					{
						foreach($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles)
						{
							echo "<strong>".$arUserProfiles["NAME"]."</strong>";
							?>
							<input type="hidden" name="PROFILE_ID" id="ID_PROFILE_ID" value="<?=$arUserProfiles["ID"]?>" />
							<?
						}
					}
					else
					{
						?>
						<select name="PROFILE_ID" id="ID_PROFILE_ID" onChange="SetContact(this.value)">
							<?
							foreach($arResult["ORDER_PROP"]["USER_PROFILES"] as $arUserProfiles)
							{
								?>
								<option value="<?= $arUserProfiles["ID"] ?>"<?if ($arUserProfiles["CHECKED"]=="Y") echo " selected";?>><?=$arUserProfiles["NAME"]?></option>
								<?
							}
							?>
						</select>
						<?
					}
					?>
				<div style="clear: both;"></div>
			</div>
		<?
		endif;
	else:
		$bHideProps = false;
	endif;
	?>
</div>

<br/>*/?>
<?$arUser = array();?>
<?if ($USER->IsAuthorized()):?>
<div class="personal-info-box">
	<?$arUser = \Citfact\Tools::getUserInfo($USER->GetID());?>
	<div class="title">
		Персональные данные
	</div>
	<div class="text">
		Ваше имя: <?=$arUser['NAME']?> <?=$arUser['LAST_NAME']?><br>
		Контактный телефон: <?=$arUser['PERSONAL_PHONE']?><br>
		E-mail: <?=$arUser['EMAIL']?>
	</div>

	<a href="/cabinet/personal/">Изменить персональные данные</a>
</div>
<?endif?>

<div class="bx_section personal-box <?=($USER->IsAuthorized() === true? 'hidden':'')?>">
	<div class="title">
		Персональные данные
	</div>

	<?/*<h4>
		<?=GetMessage("SOA_TEMPL_BUYER_INFO")?>
		<?
		if (array_key_exists('ERROR', $arResult) && is_array($arResult['ERROR']) && !empty($arResult['ERROR']))
		{
			$bHideProps = false;
		}

		if ($bHideProps && $_POST["showProps"] != "Y"):
		?>
			<a href="#" class="slide" onclick="fGetBuyerProps(this); return false;">
				<?=GetMessage('SOA_TEMPL_BUYER_SHOW');?>
			</a>
		<?
		elseif (($bHideProps && $_POST["showProps"] == "Y")):
		?>
			<a href="#" class="slide" onclick="fGetBuyerProps(this); return false;">
				<?=GetMessage('SOA_TEMPL_BUYER_HIDE');?>
			</a>
		<?
		endif;
		?>
		<input type="hidden" name="showProps" id="showProps" value="<?=($_POST["showProps"] == 'Y' ? 'Y' : 'N')?>" />
	</h4>*/?>
	<div id="sale_order_props" <?=($bHideProps && $_POST["showProps"] != "Y")?"style='display:none;'":''?>>
        <?if(!empty($arResult['ERROR_KEY']['USER_PROPS_N'])) {?>
            <div class="order-errors-cont" id="order_errors_cont_personal" style="padding: 0px;">
                <?foreach($arResult['ERROR_KEY']['USER_PROPS_N'] as $key=>$v) echo ShowError($arResult["ERROR"][$v]);?>
            </div>
        <?}
		// USER_PROPS_N - не входят в профиль
		// USER_PROPS_Y - входят в профиль
		PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_N"], $arParams["TEMPLATE_LOCATION"], $arUser, $arResult['ERROR_KEY']);
		PrintPropsForm($arResult["ORDER_PROP"]["USER_PROPS_Y"], $arParams["TEMPLATE_LOCATION"], $arUser, $arResult['ERROR_KEY']);
		?>
	</div>
</div>


<div class="personal-box">
	<div class="box-title">
		Адрес доставки
	</div>
    <?if(!empty($arResult['ERROR_KEY']['USER_PROPS_Y'])) {?>
        <div class="order-errors-cont" id="order_errors_cont_delivery" style="padding: 0px;">
            <?foreach($arResult['ERROR_KEY']['USER_PROPS_Y'] as $key=>$v)
                echo ShowError($arResult["ERROR"][$v]);?>
        </div>
    <?}?>
	<?// Если нашли сохраненные адреса, показываем селект для выбора, адреса достаются в result_modifier.php?>
	<?
	$has_addresses = false;
	if (!empty($arResult['ADDRESSES'])){
		$has_addresses = true;
	}
	$saved_location = '';
	$saved_address = '';
	?>

	<?if ($has_addresses === true):?>
		<?
		if ($_POST['ADDRESS_MY'] == 'new'){
			$saved_location = '';
			$saved_address = '';
		}
		else{
			$selected_key = $_POST['ADDRESS_MY'];
			$saved_location = $arResult['ADDRESSES'][$selected_key]['LOCATION_ID'];
			$saved_address = $arResult['ADDRESSES'][$selected_key]['ADDRESS'];
		}
		?>

		<div class="end-cart-box addresses no-pad">
			<select name="ADDRESS_MY">
				<?foreach($arResult['ADDRESSES'] as $key => $arAddress):?>
					<?$isSelected = false;
					if ( $_POST['ADDRESS_MY'] == $key || ($_POST['ADDRESS_MY'] == '' && $arAddress['SELECTED'] == 1) ) {
						$isSelected = true;
					}
					?>
					<option value="<?=$key?>" <?=$isSelected === true? 'selected':''?>><?=$arAddress['LOCATION_NAME']?>, <?=$arAddress['ADDRESS']?></option>
				<?endforeach?>
				<option value="new" <?=$_POST['ADDRESS_MY'] == 'new'? 'selected':''?>>Другой адрес</option>
			</select>

			<?foreach($arResult['ADDRESSES'] as $key => $arAddress):?><div id="address_<?=$key?>" class="hidden" data-location-id="<?=$arAddress['LOCATION_ID']?>"><?=$arAddress['ADDRESS']?></div><?endforeach?>
		</div>
	<?endif?>

	<?// Отдельно выводим местоположение и адрес?>
	<div class="my-addresses <?if ($has_addresses === true && $_POST['ADDRESS_MY'] != 'new'):?>hidden<?endif;?>" style="margin: 20px 0 0 0;">
		<?$geo_location = \Citfact\Paolo::GetBitrixLocation($_SESSION['CITY_ID']);
		if (/*$_SESSION['goLocations'] != 'Y' &&*/$_POST['DELIVERY_ID'] == '' && $saved_location == '' && $geo_location != '' && $geo_location != 0){
			$saved_location = $geo_location;
			/*$_SESSION['goLocations'] = 'Y';*/
		}
		else{
		}
		?>
		<?PrintLocationAndAddress($arResult["ORDER_PROP"]["USER_PROPS_Y"], $arParams["TEMPLATE_LOCATION"], $saved_location, $saved_address);?>
	</div>

	<?/*$APPLICATION->IncludeComponent(
		"bitrix:sale.location.selector.steps",
		"",
		Array(
			"COMPONENT_TEMPLATE" => ".default",
			"ID" => "980",
			"CODE" => "",
			"INPUT_NAME" => "LOCATION",
			"PROVIDE_LINK_BY" => "id",
			"JSCONTROL_GLOBAL_ID" => "",
			"JS_CALLBACK" => "",
			"SEARCH_BY_PRIMARY" => "N",
			"FILTER_BY_SITE" => "Y",
			"SHOW_DEFAULT_LOCATIONS" => "Y",
			//"DISABLE_KEYBOARD_INPUT" => 'Y',
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "36000000",
			"FILTER_SITE_ID" => "s1",
			// function window.BX.locationsDeferred['X'] will be created and lately called on each form re-draw.
			// it may be removed when sale.order.ajax will use real ajax form posting with BX.ProcessHTML() and other stuff instead of just simple iframe transfer
			"JS_CONTROL_DEFERRED_INIT" => 100500,

			// an instance of this control will be placed to window.BX.locationSelectors['X'] and lately will be available from everywhere
			// it may be replaced with global component dispatch mechanism coming soon
			"JS_CONTROL_GLOBAL_ID" => 100500,
		),
		false
	);*/?>
</div>


<script type="text/javascript">
	function fGetBuyerProps(el)
	{
		var show = '<?=GetMessageJS('SOA_TEMPL_BUYER_SHOW')?>';
		var hide = '<?=GetMessageJS('SOA_TEMPL_BUYER_HIDE')?>';
		var status = BX('sale_order_props').style.display;
		var startVal = 0;
		var startHeight = 0;
		var endVal = 0;
		var endHeight = 0;
		var pFormCont = BX('sale_order_props');
		pFormCont.style.display = "block";
		pFormCont.style.overflow = "hidden";
		pFormCont.style.height = 0;
		var display = "";

		if (status == 'none')
		{
			el.text = '<?=GetMessageJS('SOA_TEMPL_BUYER_HIDE');?>';

			startVal = 0;
			startHeight = 0;
			endVal = 100;
			endHeight = pFormCont.scrollHeight;
			display = 'block';
			BX('showProps').value = "Y";
			el.innerHTML = hide;
		}
		else
		{
			el.text = '<?=GetMessageJS('SOA_TEMPL_BUYER_SHOW');?>';

			startVal = 100;
			startHeight = pFormCont.scrollHeight;
			endVal = 0;
			endHeight = 0;
			display = 'none';
			BX('showProps').value = "N";
			pFormCont.style.height = startHeight+'px';
			el.innerHTML = show;
		}

		(new BX.easing({
			duration : 700,
			start : { opacity : startVal, height : startHeight},
			finish : { opacity: endVal, height : endHeight},
			transition : BX.easing.makeEaseOut(BX.easing.transitions.quart),
			step : function(state){
				pFormCont.style.height = state.height + "px";
				pFormCont.style.opacity = state.opacity / 100;
			},
			complete : function(){
					BX('sale_order_props').style.display = display;
					BX('sale_order_props').style.height = '';

					pFormCont.style.overflow = "visible";
			}
		})).animate();
	}
</script>
<?if(!empty($arResult['ERROR_KEY']['PICKUP'])) {?>
    <div class="order-errors-cont" id="order_errors_cont_address">
        <?echo ShowError($arResult["ERROR"][$arResult['ERROR_KEY']['PICKUP']]);?>
    </div>
<?}?>
<?if(!CSaleLocation::isLocationProEnabled()):?>
	<div style="display:none;">

		<?$APPLICATION->IncludeComponent(
			"bitrix:sale.ajax.locations",
			$arParams["TEMPLATE_LOCATION"],
			array(
				"AJAX_CALL" => "N",
				"COUNTRY_INPUT_NAME" => "COUNTRY_tmp",
				"REGION_INPUT_NAME" => "REGION_tmp",
				"CITY_INPUT_NAME" => "tmp",
				"CITY_OUT_LOCATION" => "Y",
				"LOCATION_VALUE" => "",
				"ONCITYCHANGE" => "submitForm()",
			),
			null,
			array('HIDE_ICONS' => 'Y')
		);?>

	</div>
<?endif?>
