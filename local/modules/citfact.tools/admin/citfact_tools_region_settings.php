<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
CModule::IncludeModule("iblock");
IncludeModuleLangFile(__FILE__);

$sModuleId  = 'citfact.tools';
// $IBLOCK_ID = COption::GetOptionString( $sModuleId, 'id_block_trade_filtersorter');

CJSCore::Init(array("jquery", "window"));?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

//\Citfact\Tools::pre($_POST);
if($_REQUEST['Update_tab1'] && check_bitrix_sessid()) {
	$arCities = \Citfact\Paolo::GetCities();
	// Для городов
	foreach ($_POST['pricetypes'] as $region_id => $arRegion){
		$PROP = array();
		foreach ($arRegion as $city_id => $price_type_id){
			$PROP[] = array(
				'CITY_ID' => $city_id,
				'CITY_NAME' => $arCities[$city_id]['NAME'],
				'PRICE_TYPE_ID' => $price_type_id,
				'PRICE_TYPE_ACTION_ID' => $_POST['pricetypes_action'][$region_id][$city_id]
			);
		}
		CIBlockElement::SetPropertyValuesEx($region_id, IBLOCK_REGION_SETTINGS, array('CITIES' => serialize($PROP)));
	}

	// Для региона
	foreach ($_POST['pricetypes_region'] as $region_id => $price_type){
		CIBlockElement::SetPropertyValuesEx($region_id, IBLOCK_REGION_SETTINGS, array('REGION_PRICE' => $price_type));
	}
	foreach ($_POST['pricetypes_action_region'] as $region_id => $price_type_action){
		CIBlockElement::SetPropertyValuesEx($region_id, IBLOCK_REGION_SETTINGS, array('REGION_PRICE_ACTION' => $price_type_action));
	}
}


$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("CITFACT_TOOLS_REGION_SETTINGS_TAB1_HEAD"), "TITLE" => GetMessage("CITFACT_TOOLS_REGION_SETTINGS_TAB1_HEAD")),
	//array("DIV" => "edit2", "TAB" => GetMessage("CITFACT_TOOLS_REGION_SETTINGS_TAB2_HEAD"), "TITLE" => GetMessage("CITFACT_TOOLS_REGION_SETTINGS_TAB2_HEAD")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
?>

<?if (!empty($arErrors)):?>
	<div class="adm-info-message-wrap adm-info-message-red">
		<div class="adm-info-message">
			<div class="adm-info-message-title">Ошибка</div>
			<?foreach($arErrors as $error):?>
				<?=$error?><br />
			<?endforeach?>
			<div class="adm-info-message-icon"></div>
		</div>
	</div>
<?endif?>

	<style>
		.hidden{ display: none; }
		.region_settings_table{ width: 100%; min-width: 900px; border-spacing: 0;}
		.region_settings_table td{ padding: 0; margin: 0;
			border-top: 1px solid #E5EDEF;
			border-bottom: 1px solid #E5EDEF;
		}
		.region_settings_table thead td{
			background-color: #AEBBC0;
			background-image: -webkit-linear-gradient(top, #B9C7CD, #AAB6B8);
			background-image: -moz-linear-gradient(top, #b9c7cd, #aab6b8);
			background-image: -ms-linear-gradient(top, #b9c7cd, #aab6b8);
			background-image: -o-linear-gradient(top, #b9c7cd, #aab6b8);
			background-image: linear-gradient(top, #b9c7cd, #aab6b8);
			border: 1px solid;
			border-color: #D9E0E4 #89979D #919B9D #EBEEF0;
			font-weight: bold;
			text-shadow: 0 1px rgba(255, 255, 255, 0.7);
			height: 21px;
			vertical-align: middle;
			padding: 6px 0 4px 10px;
		}
		.region_settings_table tbody tr:hover td{background-color: #E0E9EC;}
		.region_block_td{width: 300px;}
		.buttons_td{width: 100px;}

		.region_block{
			display: table-cell;
			vertical-align: middle;
			width: 230px;
			height: 40px;
			padding: 10px 5px;
		}
		.region_name{
			display: inline-block;
			margin: 0 0 5px 0;
			font-size: 14px;
			font-weight: bold;
			text-shadow: 0 1px rgba(255, 255, 255, 0.7);
		}

		.add-city-button{/*display: none;*/}
		.region_settings_table tr:hover .add-city-button{display: inline-block; vertical-align: middle;}

		.cities_selected_block{
			width: 100%;
		}
		.city_selected {
			margin: 5px 0 20px 0;
			/*border-bottom: 1px solid #DDD;*/
		}
		.city_selected_name {
			display: inline-block;
			width: 160px;
			font-size: 14px;
			font-weight: bold;
			text-shadow: 0 1px rgba(255, 255, 255, 0.7);
		}

		.cities_list_cont{
			display: none;
			padding: 5px;
			line-height: 20px;
		}
		.dashed{border-bottom: 1px dashed; cursor: pointer;}
		.cities_list_cont .city_name{
			margin-right: 10px;
			color: #3C6DB3;
		}

		.shop_selected { margin: 10px 0 10px 0; }
		.add_shop_link{ margin: 0 0 0 10px; color: #3C6DB3; }
		.shop_name{ color: #3C6DB3; }
		.shop_address{ margin:0 0 0 15px; color: #777; }

		form[name="shop_edit_form"] label{display: block;}
		form[name="shop_edit_form"] textarea{resize: none;}

		.controls {
			margin-top: 16px;
			border: 1px solid transparent;
			border-radius: 2px 0 0 2px;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			height: 32px;
			outline: none;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
		}

		#pac-input {
			background-color: #fff;
			font-family: Roboto;
			font-size: 15px;
			font-weight: 300;
			margin-left: 12px;
			padding: 0 11px 0 13px;
			text-overflow: ellipsis;
			width: 400px;
		}

		#pac-input:focus {
			border-color: #4d90fe;
		}
	</style>

	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>

	<script>
		/**
		 * CAjax object
		 */
		var CAjax = {
			/**
			 *  Ajax handler
			 *  @url - ajax request url
			 *  @reauest - request data
			 *  @action - action
			 *  @container - result container
			 */
			send: function(url, action, request, container, callback) {
				$.ajax({
					url: url,
					data: {
						action: action,
						request: request
					},
					success: function(data) {
						var json = JSON.parse(data);
						//console.log(result);
						if (typeof callback === "function") {
							callback(json);
						}
					}
				});
			}
		};

		$(document).ready(function () {
			$('.add-city-button').on('click', function(){
				var button = $(this);
				var request = button.data('region-name');
				var cont = $(this).parent().parent().find('.cities_list_cont');
				CAjax.send(
					'/bitrix/admin/citfact_tools_ajax_actions.php',
					button.data('ajax-action'),
					request,
					cont,
					function (json) {
						var cities = json.result.cities;
						var region = json.result.region;

						var appendix = '';
						cont.html('');
						for(prop in cities) if (cities.hasOwnProperty(prop)) {
							appendix += '<span class="city_name dashed" data-region-id="' + region[0]['ID'] + '" data-city-id="' + cities[prop]['ID'] + '">' + cities[prop]['NAME'] + '</span>';
						}
						cont.append(appendix);

						cont.slideDown();
					}
				);
				return false;
			});

			$('body').on('click', '.city_name', function(){
				var link = $(this);

				var cityId = link.data('city-id');
				var regionId = link.data('region-id');

				var options = $('select[name="pricetypes"]').html();
				var select = '<select name="pricetypes['+regionId+']['+cityId+']">' + options + '</select>';
				var select_action = '<select name="pricetypes_action['+regionId+']['+cityId+']">' + options + '</select>';
				var appendix = '<div class="city_selected"><span class="city_selected_name">' + link.html() + '</span>' + select + ' ' + select_action + '</div>';
				link.parent().prev('div').append(appendix);
				link.hide();

				if (link.parent().find('span:visible').length == 0) link.parent().hide();
			});

			$('body').on('click', '.delete-selected-city', function(){
				$(this).parent().remove();
			});

			// Модалка добавления/изменения магазина
			$('body').on('click', '.add_shop_link, .shop_name', function(){
				var link = $(this);
				var action = link.data('action');
				var city_id = link.data('city-id');
				var shop_id = link.data('shop-id');
				var title = 'Добавить магазин';
				var shop_popup = new BX.CDialog({
					'title': title,
					'width': 960,
					'height': 800,
					'content_url': '/local/modules/citfact.tools/admin/popup_shop_edit.php', // TODO: путь сделать универсальным
					'content_post': 'action=' + action + '&city_id=' + city_id + '&shop_id=' + shop_id,
					//this.JSParamsToPHP(arProp, 'PROP')+'&'+this.SESS,
					'draggable': true,
					'resizable': true
					//'buttons': [BX.CDialog.btnSave, BX.CDialog.btnCancel]
				});
				shop_popup.Show();
			});
		});
	</script>

<?$tabControl->Begin();?>

<?$tabControl->BeginNextTab();?>
	<tr><td>
		<?$arPriceTypes = \Citfact\Paolo::GetPriceTypes();
		//\Citfact\Tools::pre($arPriceTypes);
		// TODO: Перенести формирование опций селекта в функцию
		?>
		<div class="hidden">
			<select name="pricetypes" id="">
			<?if (!empty($arPriceTypes))?>
				<option value="">Не выбран</option>
				<?foreach ($arPriceTypes as $arPriceType):?>
					<option value="<?=$arPriceType['ID']?>"><?=$arPriceType['NAME']?></option>
				<?endforeach?>
			</select>
		</div>
		<form method="post" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data" name="post_form" id="post_form">
			<input class="adm-btn-save" type="submit" name="Update_tab1" value="<?=GetMessage("CITFACT_TOOLS_REGION_SETTINGS_INPUTNAME"); ?>" title="<?=GetMessage("CITFACT_TOOLS_REGION_SETTINGS_INPUTNAME"); ?>">
			<br/><br/>
			<?echo bitrix_sessid_post();?>
			<?
			$arRegions = \Citfact\Paolo::GetRegionSettings();
			$arShops = \Citfact\Paolo::GetShops();
			//\Citfact\Tools::pre($arRegions);
			//\Citfact\Tools::pre($arShops);
			?>
			<?if (!empty($arRegions)):?>
				<table class="region_settings_table">
					<thead><td colspan="2">Регион</td><td>Города</td></thead>
					<tbody>
						<?foreach ($arRegions as $arRegion):?>
							<tr>
								<td class="region_block_td">
									<div class="region_block">
										<span class="region_name"><?=$arRegion['NAME']?></span><br/>
										Розничная цена: <br/>
										<select name="pricetypes_region[<?=$arRegion['ID']?>]" id="">
											<?if (!empty($arPriceTypes))?>
												<option value="">Не выбран</option>
											<?foreach ($arPriceTypes as $arPriceType):?>
 												<option value="<?=$arPriceType['ID']?>" <?=($arPriceType['ID'] == $arRegion['PROPERTY_REGION_PRICE_VALUE']? 'selected':'')?>><?=$arPriceType['NAME']?></option>
											<?endforeach;?>
										</select>
										Акционная цена: <br/>
										<select name="pricetypes_action_region[<?=$arRegion['ID']?>]" id="">
											<?if (!empty($arPriceTypes))?>
												<option value="">Не выбран</option>
											<?foreach ($arPriceTypes as $arPriceType):?>
												<option value="<?=$arPriceType['ID']?>" <?=($arPriceType['ID'] == $arRegion['PROPERTY_REGION_PRICE_ACTION_VALUE']? 'selected':'')?>><?=$arPriceType['NAME']?></option>
											<?endforeach;?>
										</select>
									</div>
								</td>
								<td class="buttons_td">
									<span class="add-city-button adm-filter-add-button" onclick="this.blur();" hidefocus="true" title="Добавить город" data-ajax-action="region-show-cities-list" data-region-name="<?=$arRegion['NAME']?>"></span>
								</td>
								<td>
									<div class="cities_selected_block">
										<?
										$arCities = unserialize($arRegion['~PROPERTY_CITIES_VALUE']['TEXT']);
										//\Citfact\Tools::pre($arCities);
										?>
										<?
										// Блок города
										if (!empty($arCities))
										foreach ($arCities as $arCity):?>
										<div class="city_selected">
											<span class="city_selected_name"><?=$arCity['CITY_NAME']?></span><!--
											--><select name="pricetypes[<?=$arRegion['ID']?>][<?=$arCity['CITY_ID']?>]" id="" title="Тип цены для города">
												<?if (!empty($arPriceTypes))?>
													<option value="">Не выбран</option>
												<?foreach ($arPriceTypes as $arPriceType):?>
													<option value="<?=$arPriceType['ID']?>" <?=($arPriceType['ID'] == $arCity['PRICE_TYPE_ID']? 'selected':'')?>><?=$arPriceType['NAME']?></option>
												<?endforeach;?>
											</select>

											<select name="pricetypes_action[<?=$arRegion['ID']?>][<?=$arCity['CITY_ID']?>]" id="" title="Акционный тип цены для города">
												<?if (!empty($arPriceTypes))?>
													<option value="">Не выбран</option>
												<?foreach ($arPriceTypes as $arPriceType):?>
													<option value="<?=$arPriceType['ID']?>" <?=($arPriceType['ID'] == $arCity['PRICE_TYPE_ACTION_ID']? 'selected':'')?>><?=$arPriceType['NAME']?></option>
												<?endforeach;?>
											</select>

											<span class="minus adm-filter-item-delete delete-selected-city" onclick="this.blur();" hidefocus="true" title="Удалить город"></span>
											<span class="add_shop_link dashed" data-action="add" data-city-id="<?=$arCity['CITY_ID']?>">Добавить магазин</span>

											<?// Магазины в городе?>
											<?
											if (!empty($arShops[$arCity['CITY_ID']]))
											foreach ($arShops[$arCity['CITY_ID']] as $arShop):?>
												<div class="shop_selected"><span class="shop_name dashed" data-action="edit" data-shop-id="<?=$arShop['ID']?>"><?=$arShop['NAME']?></span><span class="shop_address"><?=$arShop['PROPERTY_ADDRESS_VALUE']?></span></div>
											<?endforeach?>
										</div>
										<?endforeach;?>
									</div>
									<div class="cities_list_cont"></div>
								</td>
							</tr>
						<?endforeach?>
					</tbody>
				</table>
			<?endif?>

			<br/><br/>
			<input class="adm-btn-save" type="submit" name="Update_tab1" value="<?=GetMessage("CITFACT_TOOLS_REGION_SETTINGS_INPUTNAME"); ?>" title="<?=GetMessage("CITFACT_TOOLS_REGION_SETTINGS_INPUTNAME"); ?>">
		</form>

		<div class="hidden"><?$APPLICATION->IncludeComponent("bitrix:main.file.input", "drag_n_drop",
				array(
					"INPUT_NAME"=>"IMAGES",
					"MULTIPLE"=>"Y",
					"MODULE_ID"=>"iblock",
					"MAX_FILE_SIZE"=>"",
					"ALLOW_UPLOAD"=>"I",
					"ALLOW_UPLOAD_EXT"=>"",
					"INPUT_VALUE" => $_POST['IMAGES'],
				),
				false
			);?>
		</div>

	</td></tr>
<?/*
<?$tabControl->BeginNextTab();?>
	<tr><td>
		<form method="post" action="<?echo $APPLICATION->GetCurPage()?>" enctype="multipart/form-data" name="post_form2" id="post_form2">
			<?echo bitrix_sessid_post();?>
			Форма второго таба
			<br/><br/>
			<input class="adm-btn" type="submit" name="Update_tab2" value="<?=GetMessage("CITFACT_TOOLS_REGION_SETTINGS_INPUTNAME"); ?>" title="<?=GetMessage("CITFACT_TOOLS_REGION_SETTINGS_INPUTNAME"); ?>">
		</form>
	</td></tr>
*/?>
<?$tabControl->Buttons();?>
<?$tabControl->End();?>

<?echo BeginNote();?>
	<span class="required">*</span> <?echo GetMessage("CITFACT_TOOLS_REGION_SETTINGS_REQUIRED_FIELDS")?>
<?echo EndNote();?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>