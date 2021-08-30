<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');
?>
<button type="button" class="close" data-dismiss="modal"></button>
<?if(!$USER->IsAuthorized()):?>
	<div class="modal-body modal-new modal-new--auth">
		<div class="title-1">
            Войдите на сайт
			<?/*Оставьте отзыв и получите купон на скидку*/?>
		</div>
		<?$APPLICATION->IncludeComponent(
			"citfact:authorize.ajax",
			"popup",
			Array(
				"REDIRECT_TO" => $_REQUEST['BACK_URL'],
				"FORM_ID" => 'reviews'
			)
		);?>
	</div>
	<div class="modal-body bg">
		<div class="line clear-after">
			<div class="float-left">
				<a href="/forgotpassword/">
					Забыли пароль?
				</a>
			</div>
			<a href="/register/" class="float-right link">Регистрация</a>
		</div>
	</div>
<?else:?>
	<?global $USER;?>
	<div class="modal-body modal-new">
		<div class="title-1">
			Оставьте отзыв и получите купон на скидку
		</div>
		<?$APPLICATION->IncludeComponent("citfact:form.ajax", "product_review", Array(
			"IBLOCK_ID" => IBLOCK_PRODUCT_REVIEW,
			"SHOW_PROPERTIES" => array(
				"USER_ID" => array(
					"type" => "hidden",
					"required" => "Y",
					"value" => $USER->GetID(),
				),
				"USER_NAME" => array(
					"type" => "hidden",
					"required" => "Y",
					"value" => $USER->GetFullName(),
				),
				"USER_EMAIL" => array(
					"type" => "hidden",
					"required" => "Y",
					"value" => $USER->GetEmail(),
				),
				"PRODUCT_ID" => array(
					"type" => "hidden",
					"class" => '',
					"value" => htmlspecialchars($_GET['ELEMENT_ID']),
				),
				"PRODUCT_NAME" => array(
					"type" => "ready",
					"value" => htmlspecialchars(urldecode($_GET['ELEMENT_NAME'])),
				),
				"PRODUCT_IMAGE" => array(
					"type" => "img",
					"value" => htmlspecialchars($_GET['ELEMENT_IMAGE']),
				),
				"PRODUCT_LINK" => array(
					"type" => "hidden",
					"value" => htmlspecialchars(urldecode($_GET['ELEMENT_LINK'])),
				),
				"MESSAGE" => array(
					"type" => "textarea",
					"class" => 'required message_type',
					"placeholder" => 'Опишите ваши впечатления от данной модели. Нам важно знать ваше мнение',
					"error" => 'Слишком короткий отзыв. Хотелось бы подробностей.',
				),
				"STARS" => array(
					"type" => "stars",
					"class" => 'star',
					"value" => 5,
				),
				"SUBSCRIBE" => array(
					"type" => "checkbox",
					"value" => 'ДА',
				),
			),
			"EVENT_NAME" => "PRODUCT_REVIEW_FORM",
			"SUCCESS_MESSAGE" => "Спасибо, ваш отзыв принят и появится на сайте после проверки модератором. После этого вы получите на почту письмо с кодом купона.",
			"ELEMENT_ACTIVE" => "N",
			"USER_NAME" => $USER->GetFullName(),
			"USER_EMAIL" => $USER->GetEmail(),
		),
			false
		);?>
	</div>
<?endif;?>

<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>
