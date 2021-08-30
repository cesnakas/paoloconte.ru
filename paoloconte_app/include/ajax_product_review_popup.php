<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
Loader::includeModule('citfact.tools');
?>
<button type="button" class="close" data-dismiss="modal"></button>
<?if(!$USER->IsAuthorized()):?>
	<div class="modal-body">
		<div class="modal-title">
			Оставить отзыв
		</div>
		<?$APPLICATION->IncludeComponent(
			"citfact:authorize.ajax",
			"popup",
			Array(
				"REDIRECT_TO" => '',
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
	<div class="modal-body">
		<div class="modal-title">
			Оставить отзыв
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
					"type" => "hidden",
					"value" => htmlspecialchars(urldecode($_GET['ELEMENT_NAME'])),
				),
				"PRODUCT_LINK" => array(
					"type" => "hidden",
					"value" => htmlspecialchars(urldecode($_GET['ELEMENT_LINK'])),
				),
				"MESSAGE" => array(
					"type" => "textarea",
					"class" => 'required',
					"placeholder" => 'Ваш отзыв от товаре',
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
			"SUCCESS_MESSAGE" => "Спасибо, ваш отзыв принят и появится на сайте после проверки модератором.",
			"ELEMENT_ACTIVE" => "N",
			"USER_NAME" => $USER->GetFullName(),
			"USER_EMAIL" => $USER->GetEmail(),
		),
			false
		);?>
	</div>
<?endif;?>

<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>
