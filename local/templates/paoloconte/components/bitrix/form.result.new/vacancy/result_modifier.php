<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!empty($_SESSION['LIGHT']))
{
	unset($arResult['arQuestions']['file_resume']);
	unset($arResult['arAnswers']['file_resume']);
	unset($arResult['QUESTIONS']['file_resume']);
}

$arResult['QUESTIONS']['vacancy']['VALUE'] = $_SESSION['VACANCY'];

?>