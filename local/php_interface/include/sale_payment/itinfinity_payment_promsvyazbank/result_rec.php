<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

if (1) {
    $arDate = array(
        'DATE'=>date('Y.m.d H:i:s'),
        'URI'=>$_SERVER['REQUEST_URI'],
        'IP'=>$_SERVER['GEOIP_ADDR'],
        '_REQUEST'=>$_REQUEST,
    );
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/local/var/logs/ps_bank/'.date('Y_m_d').'_result_rec.log', print_r($arDate, true), FILE_APPEND);
}


CModule::IncludeModule('sale');
CModule::IncludeModule('itinfinity.promsvyazbank');

$p_amount = $_POST["AMOUNT"];
$p_currency = $_POST["CURRENCY"];
$p_order = $_POST["ORDER"];
$p_mname = $_POST["MERCH_NAME"];
$p_merchant = $_POST["MERCHANT"];
$p_terminal = $_POST["TERMINAL"];
$p_email = $_POST["EMAIL"];
$p_trtype = $_POST["TRTYPE"];
$p_tm = $_POST["TIMESTAMP"];
$p_nonce = $_POST["NONCE"];
$p_back_ref = $_POST["BACKREF"];
$p_res = $_POST["RESULT"];
$p_rc = $_POST["RC"];
$p_rc_text = $_POST["RCTEXT"];
$p_auth_code = $_POST["AUTHCODE"];
$p_rrn = $_POST["RRN"];
$p_int_ref = $_POST["INT_REF"];
$p_sign = $_POST["P_SIGN"];
$org_amount = $_POST["ORG_AMOUNT"];

$arOrder = CSaleOrder::GetByID( IntVal($p_order) );

$paySystemParams = CITPromsvyazBank::GetPaymentParams( $arOrder['ID'] );
CITPromsvyazBank::AddLog( 'Ответ сервера банка', $_REQUEST );


if($p_rc_text == "Approved"){

	switch ($p_res){
		case "0" : $p_result_msg = "Операция успешно завершена"; break;
		case "1" : $p_result_msg = "Запрос идентифицирован как повторный"; break;
		case "2" : $p_result_msg = "Запрос отклонен Банком"; break;
		case "3" : $p_result_msg = "Запрос отклонен Платежным шлюзом"; break;
	}

	$time = date("d.m.Y H:i:s", time());
	$arFields["PS_STATUS"] = "Y";
	$arFields = array(
		"PS_STATUS" => "Y",
		"PS_STATUS_MESSAGE" => $p_rc_text,
		"PS_SUM" => $p_amount,
		"PS_CURRENCY" => $p_currency,
		"PS_STATUS_CODE" => $p_res,
		"PS_STATUS_DESCRIPTION" => $p_result_msg,
		"PS_RESPONSE_DATE" => $time,
		"PS_RRN" => $p_rrn,
		"PS_INT_REF" => $p_int_ref
	);
	CSaleOrder::Update($arOrder["ID"], $arFields);

	CITPromsvyazBank::AddLog("Статус операции: " . $p_result_msg);

	if( $p_res == "0" || $p_res == "1" ){
		switch ( $p_trtype ) {
			case '0':
				CITPromsvyazBank::AddLog("Предавторизация");
				if( CITPromsvyazBank::PaymentAuth( $_POST ) ){
					CITPromsvyazBank::AddLog("Дата создания платёжного документа " . date('Y-m-d') );
					CITPromsvyazBank::SetProperty( 'ITIPSB_RRN', $p_rrn );
					CITPromsvyazBank::SetProperty( 'ITIPSB_INTREF', $p_int_ref );
					CITPromsvyazBank::SetProperty( 'ITIPSB_ORG_AMOUNT', $org_amount );
					
					$ALLOW_DELIVERY = $paySystemParams["ALLOW_DELIVERY"]["VALUE"];
					if( $ALLOW_DELIVERY == "Y") CSaleOrder::DeliverOrder($arOrder["ID"], "Y");

					if($arOrder["PAYED"] != "Y") CSaleOrder::PayOrder($arOrder["ID"], "Y", false, false, 0, array(
						"PAY_VOUCHER_NUM" => $p_rrn, 
						"PAY_VOUCHER_DATE" => CDatabase::FormatDate( date('Y-m-d '), "YYYY-MM-DD H:i:s", CLang::GetDateFormat("FULL", LANG) )
					));
				}else{
					CITPromsvyazBank::AddLog("Угроза безопасности. Подпись службы эквайринга не верна.");
				};
			break;
			case '1':
				CITPromsvyazBank::AddLog("Оплата");
			break;
			case '21':
				CITPromsvyazBank::AddLog("Завершение расчетов");
			break;
			case '22':
				CITPromsvyazBank::AddLog("Отмена");
			break;
		}
	}
	
};
?>