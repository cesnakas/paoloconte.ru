<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
$APPLICATION->SetTitle("Войдите на сайт");
//$GLOBALS['APPLICATION']->AddHeadScript($templateFolder . '/script.js');
//$GLOBALS['APPLICATION']->SetAdditionalCSS($templateFolder . '/style.css');
?>

<?$APPLICATION->IncludeComponent(
	"citfact:authorize.ajax",
	"",
	Array(
		"REDIRECT_TO" => '',
		"FORM_ID" => 'need_auth'
	)
);?>