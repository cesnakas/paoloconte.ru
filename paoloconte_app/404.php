<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404","Y");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Страница не найдена");?>

	<div class="error-404-wrap">
		<div class="top-text align-center">
			К сожалению, запрашиваемой вами страницы не существует, но внутри сайта есть много интересного.<br>Попробуйте вернуться на главную и убедиться в этом или воспользуйтесь поиском, если ищете что-то конкретное.
		</div>

		<div class="btn-box align-center">
			<a href="/" class="btn btn-gray-dark mode2 icon-arrow-right">Вернуться на главную</a>
		</div>
	</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>