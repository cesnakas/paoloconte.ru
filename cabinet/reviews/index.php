<?define('NEED_AUTH', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Личный кабинет - отзывы");
?>
<div class="cabinet-wrap">
	<?$APPLICATION->IncludeComponent("citfact:user.reviews.product.list", "paoloconte", Array(
		"PAGER_TEMPLATE" => "paolo_modern",
	));?>
</div>
<br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>