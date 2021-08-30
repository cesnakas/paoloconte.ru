<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Контакты сети магазинов обуви и аксессуаров Paolo Conte: адрес, телефон, реквизиты.");
$APPLICATION->SetTitle("Адрес компании Паоло Конте");
?>
<script src="//api-maps.yandex.ru/2.1/?apikey=<?=\COption::GetOptionString("main", "map_yandex_keys")?>&lang=ru_RU" type="text/javascript"></script>
<script>
	var myMap;
	// Дождёмся загрузки API и готовности DOM.
	ymaps.ready(init);
	function init () {
		myMap = new ymaps.Map('map', {
			// При инициализации карты обязательно нужно указать
			// её центр и коэффициент масштабирования.
			center: [55.68584471, 37.44175263], // Москва
			zoom: 11
		});

		// Создаем геообъект с типом геометрии "Точка".
		myGeoObject = new ymaps.GeoObject({
			// Описание геометрии.
			geometry: {
				type: "Point",
				coordinates: [55.68584471, 37.44175263]
			},
			// Свойства.
			properties: {
				// Контент метки.
				iconContent: 'Paolo Conte',
				hintContent: 'Офис компании'
			}
		}, {
			// Опции.
			// Иконка метки будет растягиваться под размер ее содержимого.
			preset: 'islands#blackStretchyIcon'
		});

		myMap.geoObjects.add(myGeoObject);
	}
</script>
<?$APPLICATION->IncludeComponent(
	"citfact:page.static",
	"",
	Array(
		"IBLOCK_TYPE" => "info",
		"IBLOCK_ID" => 21,
		"USE_CODE" => "Y",
		"PAGE_ID" => ""
	)
);?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>