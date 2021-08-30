<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');

if ($_POST['action'] == 'add') {
	$city_id = $_POST['city_id'];
}
$arShop = array();
// Если мы редактируем магазин, то узнаём ID города этого магазина
if ($_POST['action'] == 'edit' && $_POST['shop_id'] != ''){
	$arShop = \Citfact\Paolo::GetShopsByFilter(array('ID' => $_POST['shop_id']));
	$city_id = $arShop[0]['PROPERTY_CITY_VALUE'];

	$arStores = \Citfact\Paolo::GetStores();
}

$arCity = \Citfact\Paolo::GetCitiesByFilter(array('ID' => $city_id));
//$arPriceTypes = \Citfact\Paolo::GetPriceTypes();


// Обработка POST =================================================================================
//\Citfact\Tools::pre($_POST);
//\Citfact\Tools::pre($_FILES);
if($_POST['action'] != '' && $_POST['shop_name'] != '' && check_bitrix_sessid()) {
	$name = htmlspecialcharsbx($_POST['shop_name']);
	$code = CUtil::translit($_POST['shop_name'], "ru", array("replace_space"=>"-","replace_other"=>"-"));
	$address = htmlspecialcharsbx($_POST['shop_address']);
	$phone = htmlspecialcharsbx($_POST['shop_phone']);
	$graphick = $_POST['shop_graphick'];
	$store_id = htmlspecialcharsbx($_POST['store_id']);
	$coords = htmlspecialcharsbx($_POST['shop_coords']);

	$arProps = array();
	// TODO: Удаление изображений
	if (!empty($_POST['IMAGES'])){
		foreach ($_POST['IMAGES'] as $file_id){
			$arProps['IMAGES'][] = array("VALUE" => $file_id,"DESCRIPTION"=>"");
		}
	}

	$arLoadProductArray = Array(
		"IBLOCK_ID" => IBLOCK_SHOPS,
		"MODIFIED_BY"    => $USER->GetID(),
		"NAME" => $name,
		"CODE" => $code,
		"PROPERTY_VALUES" => array(
			'CITY' => $city_id,
			'ADDRESS' => $address,
			'PHONE' => $phone,
			'GRAPHICK' => Array("VALUE" => Array ("TEXT" => $graphick, "TYPE" => "html")),
			'IMAGES' => $arProps['IMAGES'],
			'COORDS' => $coords,
			'STORE_ID' => $store_id,
		)
	);
	$el = new CIBlockElement;
	if ($_POST['action'] == 'add') {
		$res = $el->Add($arLoadProductArray);
		if ($res == false)
			echo '<span class="error">'.$el->LAST_ERROR.'</span>';
		else{
			$arShop = \Citfact\Paolo::GetShopsByFilter(array('ID' => $res));
			//CIBlockElement::SetPropertyValuesEx($res, false, $arProps);
			$_POST['action'] = 'edit';
		}
	}
	else if($_POST['action'] == 'edit' && $_POST['shop_id'] != ''){
		$res = $el->Update($_POST['shop_id'], $arLoadProductArray);
		if ($res == false)
			echo '<span class="error">'.$el->LAST_ERROR.'</span>';
		else{
			//CIBlockElement::SetPropertyValuesEx($_POST['shop_id'], false, $arProps);
			$arShop = \Citfact\Paolo::GetShopsByFilter(array('ID' => $_POST['shop_id']));
		}
	}
	else{
		echo 'Error: action';
	}
}
// Конец обработки POST ===========================================================================
?>
<?//\Citfact\Tools::pre($arShop);?>
<?//\Citfact\Tools::pre($arStores);?>
<script type="text/javascript">
	BX.WindowManager.Get().SetButtons([BX.CDialog.prototype.btnSave, BX.CDialog.prototype.btnCancel]);
	<?if ($_POST['action'] == 'add'):?>
		BX.WindowManager.Get().SetTitle('Добавить магазин в городе <?=$arCity[0]['NAME']?>');
	<?endif;?>
	<?if ($_POST['action'] == 'edit'):?>
		BX.WindowManager.Get().SetTitle('Редактировать магазин "<?=$arShop[0]['NAME']?>"');
	<?endif;?>

	//var googleMaps = [];
	$(document).ready(function () {
		var mapOptions = {
			zoom: 11,
			minZoom:3,
			center: new google.maps.LatLng(55.6468, 37.581),
			mapTypeControlOptions: {
				mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'map_style']
			}
			//,scrollwheel: false
		};
		var mapid ="popup_map";
		var map = new google.maps.Map(document.getElementById(mapid), mapOptions);
		//googleMaps.push(map);


		// Create the search box and link it to the UI element.
		var input = /** @type {HTMLInputElement} */(
			document.getElementById('pac-input'));
		map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

		var searchBox = new google.maps.places.SearchBox(
			/** @type {HTMLInputElement} */(input));
		// [START region_getplaces]
		// Listen for the event fired when the user selects an item from the
		// pick list. Retrieve the matching places for that item.
		var markers = [];

		$('#pac-input').on('keyup', function () {
			return false;
		});

		google.maps.event.addListener(searchBox, 'places_changed', function() {
			var places = searchBox.getPlaces();

			if (places.length == 0) {
				return;
			}
			for (var i = 0, marker; marker = markers[i]; i++) {
				marker.setMap(null);
			}

			// For each place, get the icon, place name, and location.
			var bounds = new google.maps.LatLngBounds();
			for (var i = 0, place; place = places[i]; i++) {
				var image = {
					url: place.icon,
					size: new google.maps.Size(71, 71),
					origin: new google.maps.Point(0, 0),
					anchor: new google.maps.Point(17, 34),
					scaledSize: new google.maps.Size(25, 25)
				};

				// Create a marker for each place.
				var marker = new google.maps.Marker({
					map: map,
					icon: image,
					title: place.name,
					position: place.geometry.location
				});

				markers.push(marker);
				bounds.extend(place.geometry.location);
			}
			map.fitBounds(bounds);
		});
		// [END region_getplaces]

		var marker = new google.maps.Marker({
			map: map
		});

		<?if ($arShop[0]['PROPERTY_COORDS_VALUE']):?>
			var latlng = new google.maps.LatLng(<?=$arShop[0]['PROPERTY_COORDS_VALUE']?>);
			setMarker(latlng);
			map.setCenter(latlng);
		<?endif?>

		function setMarker(location) {
			marker.setMap(map);
			marker.setPosition(location);
			$('#shop_coords').val(location.lat()+','+location.lng());
		}
		google.maps.event.addListener(map, 'click', function(event) {
			setMarker(event.latLng);
		});

		$('input[name="reset_coords"]').on('click', function () {
			$('#shop_coords').val('');
			marker.setMap(null);
		});
	});
</script>

<form enctype="multipart/form-data" action="" method="post" name="shop_edit_form">
	<?echo bitrix_sessid_post();?>
	<input type="hidden" name="action" value="<?=htmlspecialcharsbx($_POST['action'])?>"/>
	<input type="hidden" name="city_id" value="<?=$arCity[0]['ID']?>"/>
	<input type="hidden" name="shop_id" value="<?=$arShop[0]['ID']?>"/>
	<label>Название магазина<br><input type="text" name="shop_name" value="<?=$arShop[0]['NAME']?>"/></label>
	<label>Адрес магазина<br><input type="text" name="shop_address" value="<?=$arShop[0]['PROPERTY_ADDRESS_VALUE']?>"/></label>
	<label>Телефон магазина<br><input type="text" name="shop_phone" value="<?=$arShop[0]['PROPERTY_PHONE_VALUE']?>"/></label>
	График работы:<br/>
	<textarea name="shop_graphick" cols="30" rows="10"><?=$arShop[0]['~PROPERTY_GRAPHICK_VALUE']['TEXT']?></textarea>
	<br/>

	Склад на сайте:<br/>
	<select name="store_id" id="">
		<option value="">Не выбран</option>
		<?foreach ($arStores as $arStore):?>
			<option value="<?=$arStore['ID']?>" <?=($arStore['ID'] == $arShop[0]['PROPERTY_STORE_ID_VALUE']? 'selected':'')?>><?=$arStore['TITLE']?></option>
		<?endforeach?>
	</select>
	<br/>

	<?if (!empty($arShop[0]['PROPERTY_IMAGES_VALUE']))
		foreach($arShop[0]['PROPERTY_IMAGES_VALUE'] as $file_id):?>
			<?
			$file = CFile::ResizeImageGet($file_id, array('width'=>100, 'height'=>100), BX_RESIZE_IMAGE_EXACT, true);
			$img = '<img src="'.$file['src'].'" width="100" alt="" title="" />';?>
			<div style="display: inline-block;"><?/*<input type="text" name="IMAGES[]" value="<?=$file_id?>">*/?><?=$img?></div>
	<?endforeach?>
	<br/>
	<?$APPLICATION->IncludeComponent("bitrix:main.file.input", "drag_n_drop",
		array(
			"INPUT_NAME"=>"IMAGES",
			"MULTIPLE"=>"Y",
			"MODULE_ID"=>"iblock",
			"MAX_FILE_SIZE"=>"",
			"ALLOW_UPLOAD"=>"I",
			"ALLOW_UPLOAD_EXT"=>"",
			//"INPUT_VALUE" => $_POST['IMAGES'],
		),
		false
	);?>
	<br/>
	<br/>
	<input type="text" name="shop_coords" id="shop_coords" readonly style="width: 250px;"/> <input type="button" name="reset_coords" value="Очистить координаты"/>

	<?/*<select name="pricetype">
		<?if (!empty($arPriceTypes))?>
			<option value="">Не выбран</option>
		<?foreach ($arPriceTypes as $arPriceType):?>
			<option value="<?=$arPriceType['ID']?>" <?=($arPriceType['ID'] == $arCity['PROPERTY_PRICE_TYPE']? 'selected':'')?>><?=$arPriceType['NAME']?></option>
		<?endforeach;?>
	</select>*/?>
</form>
	<?// Карта снаружи формы, чтобы не срабатывал битриксовый submit?>
	<input id="pac-input" class="controls" type="text" placeholder="Найти место">
	<div id="popup_map" style="width: 100%; height:400px;"></div>
<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>
