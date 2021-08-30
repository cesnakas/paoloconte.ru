<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Лист ожиданий");
?>
<div class="container">
	<div class="top-text">В листе ожиданий интернет-магазин Paolo Conte размещает информацию об обуви и аксессуарах, которые Вы хотите приобрести по более низкой цене, чем существующая. Отправив товар в лист ожиданий, Вы можете быть уверены, что не пропустите этот момент. Сайт сообщит Вам о снижении его цены до указанного уровня.</div>
	<div class="cabinet-wrap">
		<?$APPLICATION->IncludeComponent(
			"citfact:elements.list",
			"subscribe_price_app",
			Array(
				"IBLOCK_ID" => 36,
				"PROPERTY_CODES" => array('TOVAR_ID', 'USER_ID', 'EMAIL', 'PRICE', 'SENDED',
					'TOVAR_ID.PROPERTY_CML2_LINK',
				),
				"FILTER" => array('PROPERTY_USER_ID' => $USER->GetID(), '!PROPERTY_SENDED' => 'Y'),
			)
		);?>
	</div>
</div>

<script>
	app.setPageTitle({"title" : "Лист ожиданий"});
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>