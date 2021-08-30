<?

use Citfact\CloudLoyalty\DataLoyalty;
use Citfact\CloudLoyalty\Events;

if (!class_exists(CMain)){
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
}
DataLoyalty::getInstance()->deleteCardId();
$exCloudValue = DataLoyalty::getInstance()->getUseCloudScore();
DataLoyalty::getInstance()->setUseCloudScore("N");

if(empty($_POST['PHONE'])){
    echo '<span style="display:none">disable_phone</span>';
    return;
}

$card = str_replace('-', "", $_POST['CARD']);
if ($USER->IsAuthorized()){
    $rsUser = CUser::GetByID(CUser::GetID());
    $arUser = $rsUser->Fetch();
}

//запрос по карте и телефону не найден в CL
if(!empty($card) && !Events::checkUserInCloudloyaltyCardAndPhone($card, $_POST['PHONE']) && !$_POST['ORDERCLICK'] ){
    $textUser = '';
    if(!empty($arUser)){
        $textUser = '<p><font class="errortext">Номер карты можно узнать в личном кабинете</font></p>';
    }
    echo '<div class="order-errors-cont" id="order_errors_cont_personal" style="padding: 0px;" data-ajax-error>
<p><font class="errortext">Номер карты не привязан к телефону '.$_POST['PHONE'].'</font></p>'.$textUser.'</div>';
    return;
}

//в bitrix ecть введеная карта и у пользователя нет карты
if(!empty($card) && Events::checkCardUserInBitrix($card) && empty($arUser['UF_LOYALTY_CARD'])){
    echo '<div class="order-errors-cont" id="order_errors_cont_personal" style="padding: 0px;" data-ajax-error>
<p><font class="errortext">Карта '.$card.' используется другим пользователем, авторизируйтесь  на сайте</font></p></div>';
    return;
}

DataLoyalty::getInstance()->setCardId(str_replace('-','', $_POST['CARD']));
if ($_POST['ENABLE'] == 'true'){
    DataLoyalty::getInstance()->setUseCloudScore("Y");
}
if (DataLoyalty::getInstance()->getUseCloudScore() != $exCloudValue){
    echo DataLoyalty::getInstance()->getUseCloudScore();
    DataLoyalty::getInstance()->deleteOriginalDiscount();
}