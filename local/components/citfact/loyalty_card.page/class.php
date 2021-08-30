<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

//use Bitrix\Sale\DiscountCouponsManager;

class CBitrixLoyaltyCardComponent extends CBitrixComponent
{

    //DiscountCouponsManager::clear();
    // Месяц нужно получить на русском с маленькой буквы,
    // поэтому делаем так
    private function getRusDateTime($datetime) {
        if (empty($datetime)) {
            return $datetime;
        }

        $month = \Citfact\Paolo::getRusMonth(ConvertDateTime($datetime, "F", "ru"));

        return ConvertDateTime($datetime, "DD ", "ru").$month." в ".ConvertDateTime($datetime, "HH:MI", "ru");
    }


    private function getParameters(){
        $this->arParams["ONLY_CHECK"] = ($this->arParams["ONLY_CHECK"] == "Y" ? true : false);
    }


    public function doAll() {
        global $APPLICATION;
        $this->getParameters();

        if (!empty($_REQUEST["BARCODE"]) && empty($_REQUEST["barcode"])) {
            $_REQUEST["barcode"] = $_REQUEST["BARCODE"];
        }

        // Капча
        if ($this->arParams["ONLY_CHECK"]) {
            include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
            $cpt = new CCaptcha();
            $captchaPass = COption::GetOptionString("main", "captcha_password", "");
            if(strlen($captchaPass) <= 0)
            {
                $captchaPass = randString(10);
                COption::SetOptionString("main", "captcha_password", $captchaPass);
            }
            $cpt->SetCodeCrypt($captchaPass);
            $arResult["CAPTCHA_CODE_CRYPT"] = htmlspecialchars($cpt->GetCodeCrypt());
        }

        if ($this->arParams["ONLY_CHECK"] && !empty($_REQUEST["barcode"]) && !$APPLICATION->CaptchaCheckCode($_GET["captcha_word"], $_GET["captcha_code"])) {
            $arResult["BARCODE"] = $_REQUEST["barcode"];
            $arResult["ERRORS"]["TYPE"] = "INCORRECT_CAPTCHA";
            return $arResult;
        }

        if (!$this->arParams["ONLY_CHECK"]) {
            global $USER;
            $rsUser = CUser::GetByID($USER->GetID());
            $arUser = $rsUser->Fetch();

            $arResult["ACTIVE"] = false;

            // У пользователя активирована скидка
            if ($arUser["UF_USE_LOYALTY_CARD"]) {
                $arResult["BARCODE"] = $arUser["UF_LOYALTY_CARD"];
                $arResult["DISCOUNT"] = $arUser["UF_CARD_DISCOUNT"];
                $arResult["LAST_UPDATE"] = $this->getRusDateTime($arUser["UF_LOYALTY_CARD_DATE"]);
                $arResult["INFO_TEXT"] = $arUser["UF_LOYALTY_CARD_INFO"];
                $arResult["ACTIVE"] = true;
            // Карта привязана, но не активирована
            } elseif (!empty($arUser["UF_LOYALTY_CARD"]) && !$arUser["UF_USE_LOYALTY_CARD"]) {
                $arResult["BARCODE"] = $arUser["UF_LOYALTY_CARD"];
                $arResult["DISCOUNT"] = $arUser["UF_CARD_DISCOUNT"];
                $arResult["LAST_UPDATE"] = $this->getRusDateTime($arUser["UF_LOYALTY_CARD_DATE"]);
                $arResult["INFO_TEXT"] = $arUser["UF_LOYALTY_CARD_INFO"];
            }
        }

        // Если ввели карту, нужно ее проверить
        $barcode = trim($_REQUEST["barcode"]);
        $barcode = str_replace(" ", "", $barcode);
        if (!empty($barcode)) {
            // К пользователю уже привязана другая карта
            if (isset($arResult["BARCODE"]) && $arResult["BARCODE"] != $barcode) {
                $arResult["ERRORS"]["TYPE"] = "CARD_ALREADY_EXISTS";
            } else {
                $arResult["BARCODE"] = $barcode;

                try {
                    $loyaltyCardInfo = \Citfact\Paolo::GetLoyaltyCardsInfo($arResult["BARCODE"]);

                    if (!empty($loyaltyCardInfo)) {
                        // Если карта активирована, необходимо ее проверить и обновить скидку, если необходимо
                        $arResult["DISCOUNT"] = intval($loyaltyCardInfo["DISCOUNT_PERCENT"]);
                        $arResult["INFO_TEXT"] = $loyaltyCardInfo["INFO_TEXT"];

                        if (!$this->arParams["ONLY_CHECK"]) {
                            if ($arResult["ACTIVE"]) {
                                if ($arResult["DISCOUNT"] > 0) {
                                    \Citfact\Paolo::CreateCoupon($arResult["BARCODE"], $arResult["DISCOUNT"], $arUser["ID"]);
                                }
                            }
                            $datetime = new DateTime();
                            $arResult["LAST_UPDATE"] = $datetime->format('d.m.Y H:i:s');
                            $USER->Update(
                                $USER->GetID(),
                                array(
                                    "UF_LOYALTY_CARD" => $arResult["BARCODE"],
                                    "UF_CARD_DISCOUNT" => $arResult["DISCOUNT"],
                                    "UF_LOYALTY_CARD_DATE" => $arResult["LAST_UPDATE"],
                                    "UF_LOYALTY_CARD_INFO" => $arResult["INFO_TEXT"]
                                )
                            );
                            $arResult["LAST_UPDATE"] = $this->getRusDateTime($arResult["LAST_UPDATE"]);
                        }
                    } else {
                        $arResult["ERRORS"]["TYPE"] = "BARCODE_DOESNT_EXIST";
                    }
                } catch (Exception $e) {
                    if ($e instanceof \Citfact\IncorrectLoyaltyCardException) {
                        $arResult["ERRORS"]["TYPE"] = "INCORRECT_BARCODE";
                    } elseif ($e instanceof \Citfact\SaleDiscountCreateCouponException) {
                        $arResult["ERRORS"]["TYPE"] = "UNKNOWN_ERROR";
                    } else {
                        $arResult["ERRORS"]["TYPE"] = "UNKNOWN_ERROR";
                        //throw $e;
                    }
                }
            }
        }

        // Создаем почтовое событие, если пользователь ввел карту
        if (!$this->arParams["ONLY_CHECK"]) {
            if (!$arResult["ACTIVE"] && empty($arResult["ERRORS"]) && !empty($arResult["BARCODE"]) && empty($arUser["UF_LOYALTY_CARD"])) {
                $arEventFields = array(
                    "LOYALTY_CARD_NUMBER"   => $arResult["BARCODE"],
                    "LINK"                  => "/bitrix/admin/user_edit.php?lang=ru&ID=".$arUser["ID"]
                );
                CEvent::Send("LOYALTY_CARD_ADD", "s1", $arEventFields);
            }
        }

        return $arResult;
    }
}