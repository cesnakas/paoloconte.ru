<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}
//$GLOBALS['APPLICATION']->AddHeadScript($templateFolder . '/script.js');
//$GLOBALS['APPLICATION']->SetAdditionalCSS($templateFolder . '/style.css');
?>
<div class="container">
<?$APPLICATION->IncludeComponent(
	"citfact:authorize.ajax",
	"mobile",
	Array(
		"REDIRECT_TO" => '',
		"FORM_ID" => 'need_auth'
	)
);?></div>