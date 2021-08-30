<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

IncludeModuleLangFile(__FILE__);

/**
 * ����������� ����� ��������
 */
require($_SERVER['DOCUMENT_ROOT'] . "/local/modules/sberbank.ecom/config.php");

$psTitle = $mess["partner_name"];
$psDescription = GetMessage('RBS_PAYMENT_PAY_FROM', array('#BANK#' => $mess["partner_name"])); //'������ ����� ' . $mess["partner_name"];
$user_name_name = GetMessage('RBS_PAYMENT_LOGIN'); //"�����";
$password_name = GetMessage('RBS_PAYMENT_PASSWORD'); //"������";
$two_stage_name = GetMessage('RBS_PAYMENT_STAGING'); //"����������� �������";
$two_stage_descr = GetMessage('RBS_PAYMENT_STAGING_DESCR'); //"���� �������� 'Y', ����� ������������� ������������� ������. ��� ������ �������� ����� ������������� ������������� ������.";
$test_mode_name = GetMessage('RBS_PAYMENT_TEST_MODE'); //"�������� �����";
$test_mode_descr = GetMessage('RBS_PAYMENT_TEST_MODE_DESCR'); //"���� �������� 'Y', ������ ����� �������� � �������� ������. ��� ������ �������� ����� ����������� ����� ������.";
$logging_name = GetMessage('RBS_PAYMENT_LOGGING'); //"�����������";
$logging_descr = GetMessage('RBS_PAYMENT_LOGGING_DESCR'); //"���� �������� 'Y', ������ ����� ���������� ���� ������ � ����. ��� ������ �������� ����������� ����������� �� �����.";
$order_number_name = GetMessage('RBS_PAYMENT_ACCOUNT_NUMBER'); //"���������� ������������� ������ � ��������";
$amount_name = GetMessage('RBS_PAYMENT_ORDER_SUM'); //"����� ������";
$shipment_name = GetMessage('RBS_PAYMENT_SHIPMENT_NAME'); //"��������� ��������";
$shipment_descr = GetMessage('RBS_PAYMENT_SHIPMENT_DESCR'); //"���� �������� 'Y', �� ����� �������� ������ ����� ������������� ��������� �������� ������.";
$shipment_set_payed = GetMessage('RBS_PAYMENT_SET_PAYED'); //"������������� �� � ����� ��������";
$shipment_set_payed_descr = GetMessage('RBS_PAYMENT_SET_PAYED_DESCR'); //"������������� �� � ����� ��������";
$ckeck_name = GetMessage('RBS_PAYMENT_CHECK'); //"������������� �� � ����� ��������";
$check_description = GetMessage('RBS_PAYMENT_CHECK_DESCR'); //"������������� �� � ����� ��������";

$arPSCorrespondence = array(
	"USER_NAME" => array(
		"NAME" => $user_name_name
	),
	"PASSWORD" => array(
		"NAME" => $password_name
	),
	"TWO_STAGE" => array(
		"NAME" => $two_stage_name, 
		"DESCR" => $two_stage_descr
	),
	"TEST_MODE" => array(
		"NAME" => $test_mode_name, 
		"DESCR" => $test_mode_descr, 
		"VALUE" => "Y"
	),
	"LOGGING" => array(
		"NAME" => $logging_name, 
		"DESCR" => $logging_descr
	),
	"ORDER_NUMBER" => array(
		"NAME" => $order_number_name,
		"VALUE" => "ID", 
		"TYPE" => "ORDER"
	),
	"AMOUNT" => array(
		"NAME" => $amount_name,
		"VALUE" => "SHOULD_PAY", 
		"TYPE" => "ORDER"
	),
	"SHIPMENT_ENABLE" => array(
		"NAME" => $shipment_name,
		"DESCR" => $shipment_descr, 
		"TYPE" => "VALUE"
	),
//    "RBS_SET_PAYED" => array(
//        "NAME" => $shipment_set_payed,
//        "DESCR" => $shipment_set_payed_descr,
//        "TYPE" => "VALUE",
//        "VALUE" => "Y",
//    ),
//    "CHECK" => array(
//        "NAME" => $ckeck_name,
//        "DESCR" => $check_description,
//        "TYPE" => "VALUE",
//        "VALUE" => "Y",
//    ),
);