<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

$psTitle = GetMessage("SPCP_DTITLE");
$psDescription = GetMessage("SPCP_DDESCR");
$arPSCorrespondence = array(
		"MERCH_NAME" => array(
				"NAME" => GetMessage("MERCH_NAME"),
				"DESCR" => GetMessage("MERCH_NAME_DESCR"),
				"VALUE" => $_SERVER["HTTP_HOST"],
				"TYPE" => ""
			),
		"MERCH_URL" => array(
				"NAME" => GetMessage("MERCH_URL"),
				"DESCR" => GetMessage("MERCH_URL_DESCR"),
				"VALUE" => "http://".$_SERVER["HTTP_HOST"],
				"TYPE" => ""
			),
		"MERCHANT" => array(
				"NAME" => GetMessage("MERCHANT"),
				"DESCR" => GetMessage("MERCHANT_DESCR"),
				"VALUE" => "790367686219999",
				"TYPE" => ""
			),
		"TERMINAL" => array(
				"NAME" => GetMessage("TERMINAL"),
				"DESCR" => GetMessage("TERMINAL_DESCR"),
				"VALUE" => "79036768",
				"TYPE" => ""
			),
		"MAC" => array(
				"NAME" => GetMessage("MAC"),
				"DESCR" => GetMessage("MAC_DESCR"),
				"VALUE" => "C50E41160302E0F5D6D59F1AA3925C45",
				"TYPE" => ""
			),
		"IS_TEST" => array(
				"NAME" => GetMessage("IS_TEST"),
				"DESCR" => GetMessage("IS_TEST_DESCR"),
				"VALUE" => "Y",
				"TYPE" => ""
			),
		"SHOP_RESULT" => array(
				"NAME" => GetMessage("SHOP_RESULT"),
				"DESCR" => GetMessage("SHOP_RESULT_DESCR"),
				"VALUE" =>"http://".$_SERVER["HTTP_HOST"]."/pay/",
				"TYPE" => ""
			),
		"ORDER_ID" => array(
				"NAME" => GetMessage("ORDER_ID"),
				"DESCR" => GetMessage("ORDER_ID_DESCR"),
				"VALUE" => "ID",
				"TYPE" => "ORDER"
			),
		"ORDER_DESC" => array(
				"NAME" => GetMessage("ORDER_DESC"),
				"DESCR" => GetMessage("ORDER_DESC_DESCR"),
				"VALUE" => GetMessage("ORDER_DESC_VAL"),
				"TYPE" => ""
			),
		"SHOULD_PAY" => array(
				"NAME" => GetMessage("SHOULD_PAY"),
				"DESCR" => GetMessage("SHOULD_PAY_DESCR"),
				"VALUE" => "SHOULD_PAY",
				"TYPE" => "ORDER"
			),
		"CURRENCY" => array(
				"NAME" => GetMessage("CURRENCY"),
				"DESCR" => GetMessage("CURRENCY_DESCR"),
				"VALUE" => "CURRENCY",
				"TYPE" => "ORDER"
			),
		"EMAIL" => array(
				"NAME" => GetMessage("EMAIL"),
				"DESCR" => GetMessage("EMAIL_DESCR"),
				"VALUE" => COption::GetOptionString("sale", "order_email"),
				"TYPE" => ""
			),
		"ALLOW_DELIVERY" => array(
				"NAME" => GetMessage("ALLOW_DELIVERY"),
				"DESCR" => GetMessage("ALLOW_DELIVERY_DESCR"),
				"VALUE" => "Y",
				"TYPE" => ""
			),
		"PAY_OK" => array(
				"NAME" => GetMessage("PAY_OK"),
				"DESCR" => GetMessage("PAY_OK_DESCR"),
				"VALUE" => GetMessage("PAYMENT_OK"),
				"TYPE" => ""
			),
		"PAY_ERROR" => array(
				"NAME" => GetMessage("PAY_ERROR"),
				"DESCR" => GetMessage("PAY_ERROR_DESCR"),
				"VALUE" => GetMessage("ERROR_FROM_SERVER"),
				"TYPE" => ""
			),
		"PAY_DEBUG" => array(
				"NAME" => GetMessage("PAY_DEBUG"),
				"DESCR" => GetMessage("PAY_DEBUG_DESCR"),
				"VALUE" => "N",
				"TYPE" => ""
			),
	);                                     
?>