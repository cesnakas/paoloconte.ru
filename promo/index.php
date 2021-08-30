<?
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<div class="right-part">
	<div class="promo-slider">
		<?$APPLICATION->IncludeComponent(
			"citfact:elements.list",
			"slider_promo",
			Array(
				"IBLOCK_ID" => 23,
				"PROPERTY_CODES" => array('LINK', 'IMAGE', 'PODPIS'),
			)
		);?>
	</div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>