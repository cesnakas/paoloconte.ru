<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/composite_first_start_cookie_fix.php');
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

use Bitrix\Main\Context;

$APPLICATION->SetPageProperty("description", "Вакансии сети магазинов модной обуви и аксессуаров Paolo Conte. Работа в офисе и в рознице.");
$APPLICATION->SetTitle("Вакансии");

if ($_SERVER['REQUEST_URI'] != '/vacancy/' . $_SESSION['CITY_CODE'] . '/') {
    $context = Context::getCurrent();

    $schema = $context->getRequest()->isHttps() ? 'https' : 'http';
    $serverName = $context->getServer()->getServerName();
    $cityCode = $_SESSION['CITY_CODE'];

    $url = sprintf('%s://%s/vacancy/%s/', $schema, $serverName, $cityCode);

    LocalRedirect($url, true, '301 Moved permanently');
}
?>

<div class="vacancy-top-box">
    <div class="video-wrap clear-after">
        <a href="#" class="video full"> <img alt="" src="/images/vacancy/paolo-conte-vacancy-full.jpg" width="100%"></a>
        <?/*<a href="#" class="video float-left half"> <img src="http://paoloconte.ru/upload/medialibrary/c64/paolo-conte-vacancy-2.jpg" alt="" width='100%'></a>*/?> <a href="#" class="btn btn-gray-dark get-resume" data-toggle="modal" data-target="#getResumeModal">Отправить резюме сегодня</a>
    </div>
    <div class="vacancy-top-bottom">
        <div class="target-wrap clear-after">
            <div class="item float-left third">
                <div class="title">
                     Создавай!
                </div>
                <div class="text">
                     Мы постоянно запускаем новые проекты, развиваемся и все время двигаемся вперед!
                </div>
                <div class="link">
                     <!-- <a href="#" onclick="scrollTo('.count-1', false); return false;">«Работа в офисе»</a>-->
                </div>
            </div>
            <div class="item float-left third">
                <div class="title">
                     Учись!
                </div>
                <div class="text">
                     Мы не устаем искать новые знания, мы растем и ищем вдохновение у настоящих гуру своего дела!
                </div>
                <div class="link">
                     <!-- <a href="#" onclick="scrollTo('.count-0', false); return false;">«Работа в рознице»</a>-->
                </div>
            </div>
            <div class="item float-left third">
                <div class="title">
                     Развлекайся!
                </div>
                <div class="text">
                     Мы проводим на работе половину своей жизни и, понимая это, делаем ее веселой и зажигательной.
                </div>
                <div class="link">
                    <a href="#" class="showmap" data-toggle="modal" data-target="#getMapModal">«Загляни в Paolo Conte»</a>
                </div>
            </div>
        </div>
        <div class="desc-wrap">
             Не нравится писать, хотите пообщаться лично? Звоните! Телефон отдела персонала - <a href="tel:+7-495-640-84-77">8-495-640-84-77</a> (доб. 241, 242)
        </div>
    </div>
</div>
<div class="vacancy-choose-wrap">
    <div class="city-select">
         Показаны вакансии для города:
        <a href="#" data-toggle="modal" data-target="#cityModalInVacancy">
            <?$APPLICATION->IncludeComponent(
            "articul.geolocation.city_current",
            "vacancy",
            Array(
            )
            );?>
        </a>
    </div>
    <?
    $arFilter = [
        'PROPERTY_CITY' => $_SESSION['CITY_ID'],
        [
            "LOGIC" => "OR",
            ["DATE_ACTIVE_TO" => false],
            [">DATE_ACTIVE_TO" => ConvertTimeStamp(time(), "FULL")]
        ]

    ];

    $APPLICATION->IncludeComponent(
    "citfact:elements.list",
    "vacancy",
    Array(
        "IBLOCK_ID" => 39,
        "FILTER" => $arFilter,
        "FIELDS" => array('DETAIL_TEXT','DETAIL_PAGE_URL'),
        "PROPERTY_CODES" => array('CITY','CITY.NAME','SALARY')
    )
    );?>
</div>
<div class="vacancy-end-wrap detail-add-wrap no-shadow" style="background-image: url('/local/templates/paoloconte/images/background/vacancy-bg.jpg');">
    <div class="title align-center">
         Не нашли подходящую вам вакансию?<br>
         Заполните анкету.
    </div>
    <div class="btn-box align-center">
        <a href="#" class="btn btn--black" data-toggle="modal" data-target="#getResumeModal">Заполнить анкету</a>
    </div>
    <div class="social-box align-center">
         Следите за новыми вакансиями в соц.сетях:
        <div class="social">
             <?/*<a href="#" class="twitter"><i class="fa fa-twitter"></i></a>*/?> <a href="https://www.facebook.com/Paolo.Conte.Shoes" class="facebook"><i class="fa fa-facebook"></i></a> <a href="https://vk.com/paolo.conte.shop" class="vk"><i class="fa fa-vk"></i></a> <a href="https://instagram.com/paolo.conte.shop/" class="instagram"><i class="fa fa-instagram"></i></a>
        </div>
    </div>
</div>
<?// Анкета во всплывающем окне?>
<div class="modal fade getResumeModal" id="getResumeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
            <div class="modal-body modal-new">
                 <?$APPLICATION->IncludeComponent(
                    "citfact:form.ajax",
                    "vacancy_simple",
                    Array(
                        "IBLOCK_ID" => 40,
                        "SHOW_PROPERTIES" => array(
                            'VACANCY'=>array(
                                'type'=>'text',
                                'placeholder'=>'Вакансия *',
                                'required'=>'Y'
                                ),
                            'FIO'=>array(
                                'type'=>'text',
                                'placeholder'=>'ФИО *',
                                'required'=>'Y'
                                ),
                            'PHONE'=>array(
                                'type'=>'text',
                                'placeholder'=>'Телефон *',
                                'required'=>'Y'
                                ),
                            'EMAIL'=>array(
                                'type'=>'text',
                                'placeholder'=>'E-mail *',
                                'required'=>'Y'
                                ),
                            'BIRTHDATE'=>array(
                                'type'=>'text',
                                'placeholder'=>'Дата рождения *',
                                'required'=>'Y'
                                ),
                            'COUNTRY'=>array(
                                'type'=>'text',
                                'placeholder'=>'Гражданство *',
                                'required'=>'Y'
                                ),
                            'ADDRESS'=>array(
                                'type'=>'text',
                                'placeholder'=>'Город проживания *',
                                'required'=>'Y'
                                ),
                            'SALARY'=>array(
                                'type'=>'text',
                                'placeholder'=>'Ожидания по зарплате *',
                                'required'=>'Y'
                                ),
                            'REF_FROM'=>array(
                                'type'=>'text',
                                'placeholder'=>'Откуда узнали о вакансии? *',
                                'required'=>'Y'
                                ),
                            'FILE_REZUME'=>array(
                                'type'=>'file',
                                'required'=>'Y'),
                                ),
                        "EVENT_NAME" => "VACANCY_SIMPLE_FORM",
                        "SUCCESS_MESSAGE" => "Ваша анкета принята. Мы свяжемся с вами в ближайшее время.",
                        "ELEMENT_ACTIVE" => "Y",
                        "AJAX_FILES_PATH" => "/include/ajax_fileupload/files/",
                        "FILE_PROPERTY_CODE" => "FILE_REZUME",
                        "ATTACH_FILES" => "Y"
                    )
                );?>
            </div>
        </div>
    </div>
</div>
<?// Карта во всплывающем окне?>
<div class="modal fade getMapModal" id="getMapModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <button type="button" class="close" data-dismiss="modal"></button>
        <div class="modal-content">
            <div class="modal-body">
                 <?$APPLICATION->IncludeComponent(
                    "citfact:shops.page",
                    "vacancy_map",
                    Array(
                        "DIRECTORY_CODE" => 'shops'
                    )
                );?>
            </div>
        </div>
    </div>
</div>

<? require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');