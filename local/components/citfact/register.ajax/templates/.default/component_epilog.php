<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) { die(); }

if ($arResult["USE_CAPTCHA"] == "Y") 
{
    \Bitrix\Main\Page\Asset::getInstance()->addString("<script src='https://www.google.com/recaptcha/api.js'></script>");
}
?>