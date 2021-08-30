<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$arComponentDescription = array(
	"NAME" => basename(__DIR__)." (определение города по IP)",
	"DESCRIPTION" => 'Определяет город посетителя по IP. Устанавливает в "$_SESSION" и "$_COOKIES" значения TARIFF_AREA, CIFY_ID, CITY_CODE, CITY_NAME',
	"ICON" => "/images/articul.gif",
	"SORT" => 1,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => basename(dirname(__DIR__)),
	),
   "AREA_BUTTONS" => array(
      array(
         "URL" => "javascript: alert('Click button!');",
         "SRC" => "/images/button.gif",
         "TITLE" => "Button"
      ),
   ),
   "COMPLEX" => "N"
);
?>