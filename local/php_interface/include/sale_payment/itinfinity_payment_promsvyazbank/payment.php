<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
include(GetLangFileName(dirname(__FILE__)."/", "/payment.php"));

if (1) {
    $arDate = array(
        'DATE'=>date('Y.m.d H:i:s'),
        'URI'=>$_SERVER['REQUEST_URI'],
        'IP'=>$_SERVER['GEOIP_ADDR'],
        '_REQUEST'=>$_REQUEST,
    );
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/local/var/logs/ps_bank/'.date('Y_m_d').'_payment.log', print_r($arDate, true), FILE_APPEND);
}


CModule::IncludeModule('itinfinity.promsvyazbank');
CITPromsvyazBank::GetPaymentParams( CSalePaySystemAction::GetParamValue("ORDER_ID") );

$amount = CSalePaySystemAction::GetParamValue("SHOULD_PAY"); 
$amount = number_format($amount, 2, ".", "");
$currency = CSalePaySystemAction::GetParamValue("CURRENCY"); 
if(strlen($currency) <= 0)
	$currency = "UAH";

$order = CSalePaySystemAction::GetParamValue("ORDER_ID"); 
$order = CITPromsvyazBank::PSFormatOrderID( $order );

$desc = CITPromsvyazBank::PSOrderDescription();
$m_name = CSalePaySystemAction::GetParamValue("MERCH_NAME"); 
$m_url = CSalePaySystemAction::GetParamValue("MERCH_URL"); 
$merchant = CSalePaySystemAction::GetParamValue("MERCHANT");
$terminal = CSalePaySystemAction::GetParamValue("TERMINAL");
$email = CSalePaySystemAction::GetParamValue("EMAIL"); 
$backref = htmlspecialcharsbx(CSalePaySystemAction::GetParamValue("SHOP_RESULT")); 
$mac = CSalePaySystemAction::GetParamValue("MAC");

if(strlen(CSalePaySystemAction::GetParamValue("IS_TEST")) > 0)
	$server_url = "https://test.3ds.payment.ru/cgi-bin/cgi_link";
else
	$server_url = "https://3ds.payment.ru/cgi-bin/cgi_link";

$trtype = 0;  
$country = ""; 
$merch_gmt = ""; 
$time = ""; 

$nonce = md5( time() );

$key = pack("H*", $mac);   
$time = gmdate("YmdHis", time());
 
$sign = hash_hmac("sha1", 
	(strlen($amount) > 0 ? strlen($amount).$amount : "-").
	(strlen($currency) > 0 ? strlen($currency).$currency : "-").
	(strlen($order) > 0 ? strlen($order).$order : "-").
	(strlen($m_name) > 0 ? strlen($m_name).$m_name : "-").
	(strlen($merchant) > 0 ? strlen($merchant).$merchant : "-").
	(strlen($terminal) > 0 ? strlen($terminal).$terminal : "-").
	(strlen($email) > 0 ? strlen($email).$email : "-").
	(strlen($trtype) > 0 ? strlen($trtype).$trtype : "-").
	(strlen($time) > 0 ? strlen($time).$time : "-").
	(strlen($nonce) > 0 ? strlen($nonce).$nonce : "-").
	(strlen($backref) > 0 ? strlen($backref).$backref : "-")
	, 
	$key
);

?>

<form name="cardform" action="<?=$server_url?>" method="post">
<input type="hidden" name="TRTYPE" VALUE="<?=$trtype?>">
<input type="hidden" name="AMOUNT" value="<?=$amount?>"> 
<input type="hidden" name="CURRENCY" value="<?=$currency?>"> 
<input type="hidden" name="ORDER" value="<?=$order?>">  
<input type="hidden" name="DESC" value="<?=$desc?>"> 
<input type="hidden" name="MERCH_NAME" value="<?=$m_name?>"> 
<input type="hidden" name="MERCH_URL" value="<?=$m_url?>"> 
<input type="hidden" name="MERCHANT" value="<?=$merchant?>"> 
<input type="hidden" name="TERMINAL" value="<?=$terminal?>"> 
<input type="hidden" name="EMAIL" value="<?=$email?>"> 
<input type="hidden" name="LANG" value=""> 
<input type="hidden" name="BACKREF" value="<?=$backref?>"> 
<input type="hidden" name="NONCE" value="<?=$nonce?>">
<input type="hidden" name="P_SIGN" value="<?=$sign?>">
<input type="hidden" name="TIMESTAMP" value="<?=$time?>">
<input type="submit" class="btn btn-green" value="<?=GetMessage("PAY_BUTTON")?>" name="send_button">
</form>