<?session_start();
define('SITE_MAINTEMPLATE_PATH', '/local/templates/paoloconte');
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width,initial-scale=0.64">
		<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico"/>

		<?$APPLICATION->SetTitle("Промо");
		$APPLICATION->ShowCSS(true, true);
		$APPLICATION->ShowHeadStrings();
		$APPLICATION->ShowHeadScripts();
		$APPLICATION->SetAdditionalCSS(SITE_MAINTEMPLATE_PATH . "/template_styles.css");
		$APPLICATION->AddHeadScript(SITE_MAINTEMPLATE_PATH . "/scripts.js");

		$APPLICATION->IncludeComponent("articul.geolocation.detect_ip", "", array("IBLOCK_CODE" => "city"));
		?>

		<!--[if lt IE 9]>
		<script src="vendor/html5shiv/dist/html5shiv.js"></script>
		<![endif]-->
</head>

<body>
<?$APPLICATION->ShowPanel();?>
<div class="full-page-wrap">

	<div class="left-part">
		<a href="/" class="logo" style="background-image: url('<?=SITE_TEMPLATE_PATH?>/images/background/logo-big.png')"></a>

		<?$APPLICATION->IncludeComponent(
			"citfact:elements.list",
			"menu_promo",
			Array(
				"IBLOCK_ID" => 22,
				"PROPERTY_CODES" => array('LINK'),
			)
		);?>
		<div class="bottom-wrap align-center">
			<div class="social">
				<a href="#" title=""><i class="fa fa-twitter"></i></a>
				<a href="#" title=""><i class="fa fa-facebook"></i></a>
				<a href="#" title=""><i class="fa fa-vk"></i></a>
				<a href="#" title=""><i class="fa fa-instagram"></i></a>
			</div>
			<div class="text">
				+7 (800) 333 70 77
			</div>
		</div>
	</div>