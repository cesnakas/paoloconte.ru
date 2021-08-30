<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

global $USER;

$APPLICATION->IncludeComponent("citfact:form.ajax", "subscribe_price", Array(
	"IBLOCK_ID" => 36,
	"SHOW_PROPERTIES" => array(
		"TOVAR_ID" => array(
			"type" => "hidden",
			"required" => "Y",
			"value" => htmlspecialchars($_GET['ELEMENT_ID'])
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
);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>