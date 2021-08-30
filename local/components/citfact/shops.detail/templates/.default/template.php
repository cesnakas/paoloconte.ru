<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arSelect = Array("ID");
$arFilter = Array("IBLOCK_ID" => 39, "PROPERTY_CITY" => $arResult["SHOP"]["CITY_ID"], "ACTIVE" => "Y", "ACTIVE_DATE" => "Y");
$res = CIBlockElement::GetList(Array(), $arFilter);
$els = 0;

while ($ob = $res->GetNext()) $els++;
?>
<script>
    BX.message({
        SHOP_TO_MAP: <? echo CUtil::PhpToJSObject($arResult['SHOP']) ?>,
        SHOPS_TO_MAP: <? echo CUtil::PhpToJSObject($arResult['SHOPS']) ?>,
        CITY_CURRENT: <? echo CUtil::PhpToJSObject($arResult['CITY_CURRENT']) ?>
    });
</script>

<div itemscope itemtype="http://schema.org/ShoeStore">
    <span class="hidden" itemprop="name"><?= $arResult['SHOP']['NAME'] ?></span>

	<?if($arResult['SHOP']['COORDS'] != ''):?>
        <div id="shop-detail-map" class="shops-map"></div>
    <?endif?>



    <div class="shops">
        <div class="aside aside--shops">

            <div class="aside__sidebar">
                <?if ($arResult['SHOP']['ADDRESS'] != ''):?>
                    <div class="shops-info">
                        <div>Адрес</div>
                        <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                            <span itemprop="streetAddress"><?=$arResult['SHOP']['ADDRESS']?></span>
                            <div class="hidden" itemprop="addressLocality"><?=$arResult['SHOP']['CITY_NAME']?></div>
                        </div>
                    </div>
                <?endif?>
                <?if ($arResult['SHOP']['GRAPHICK'] != ''):?>
                    <div class="shops-info">
                        <div>Часы работы:</div>
                        <div><?= $arResult['SHOP']['GRAPHICK']?></div>
                    </div>
                <?endif?>
                <?if ($arResult['SHOP']['PHONE'] != ''):?>
                    <div class="shops-info">
                        <div>Телефон:</div>
                        <div>
                            <a href="tel:<?=$arResult['SHOP']['PHONE']?>" itemprop="telephone"><?=$arResult['SHOP']['PHONE']?></a>
                        </div>
                    </div>
                <?endif?>
                <?if ($arResult['SHOP']['SITE'] != ''):?>
                    <div class="shops-info">
                        <div>Сайт:</div>
                        <div>
                            <span>
                                <a target="_blank" 
                                   rel="nofollow"
                                   href="<?=$arResult['SHOP']['SITE']?>">
                                    <?=$arResult['SHOP']['SITE']?>
                                </a>
                            </span>
                        </div>
                    </div>
                <?endif?>

                <a href="/shops/<?=$arResult['BACK_URL']?>" class="btn btn--transparent">
                    <span>Назад к магазинам</span>
                </a>
            </div>

            <div class="aside__main">
                <?/*if (!empty($arResult['SHOP']['IMAGES'])):?> TODO раскомментить и убрать верстку
                    <div class="shops-slider">
                        <div class="shops-slider__top">
                            <?foreach($arResult['SHOP']['IMAGES'] as $file_id):?>
                                <?$file = CFile::ResizeImageGet($file_id, array('width'=>1120, 'height'=>600), BX_RESIZE_IMAGE_EXACT, true);
                                $img = '<img src="'.$file['src'].'" alt="" title="" />';?>
                                <img src="<?=$img?>" alt="">
                            <? endforeach ?>
                        </div>

                        <div class="shops-slider__bottom">
                            <?foreach($arResult['SHOP']['IMAGES'] as $file_id):?>
                                <div class="shops-slider__slide">
                                    <?$file = CFile::ResizeImageGet($file_id, array('width'=>275, 'height'=>172), BX_RESIZE_IMAGE_EXACT, true);
                                    $img = '<img src="'.$file['src'].'" alt="" title="" />';?>
                                    <img src="<?=$img ?>" alt="">
                                </div>
                            <? endforeach ?>
                        </div>
                    </div>
                <?endif*/?>

                <?if (!empty($arResult['SHOP']['IMAGES'])):?>
                <div class="shops-slider">
                    <div class="shops-slider__top">
                        <?foreach($arResult['SHOP']['IMAGES'] as $file_id):?>
                            <?$file = CFile::ResizeImageGet($file_id, array('width'=>1120, 'height'=>600), BX_RESIZE_IMAGE_EXACT, true);
                            $img = '<img src="'.$file['src'].'" alt="" title="" />';?>
                            <?=$img?>
                        <? endforeach ?>
                    </div>
                    <div class="shops-slider__bottom">
                        <?foreach($arResult['SHOP']['IMAGES'] as $file_id):?>
                            <div class="shops-slider__slide">
                                <?$file = CFile::ResizeImageGet($file_id, array('width'=>275, 'height'=>172), BX_RESIZE_IMAGE_EXACT, true);
                                $img = '<img src="'.$file['src'].'" alt="" title="" />';?>
                                <?=$img ?>
                            </div>
                        <? endforeach ?>
                    </div>
    
                    <?if ($els>0):?>
                        <div class="shops__vacancy">
                            <a href="/vacancy/?city=<?=$arResult["SHOP"]["CITY_ID"]?>"><span class="vacansy-block-city-name">Вакансии в городе: +<?=$els?></span></a>
                            <a href="/vacancy/?city=<?=$arResult["SHOP"]["CITY_ID"]?>" class="vacansy-block-sent-res btn btn-gold small mode2 icon-arrow-right">Подробнее</a>
                        </div>
                    <?endif;?>
                </div>
                <?endif?>
                
                
                <div class="shops-feedback">
                    <div class="shops-feedback__top">
                        <div class="shops-feedback__placeholder"></div>
                        <h2>Отзывы о магазине</h2>
                        <div class="shops-feedback__btn">
                            <a href="#" class="btn btn--transparent" data-toggle="modal" data-target="#reviewShopsModal">
                                <span>Написать отзыв</span>
                            </a>
                        </div>
                    </div>
                    <?=(!empty($arResult['REVIEWS'])? '':'У этого магазина еще нет отзывов, но вы можете быть первым!')?>
    
                    <? foreach ($arResult['REVIEWS'] as $arReview):?>
                        <div class="shops-feedback__item">
                            <div class="shops-feedback__text">
                                <?=$arReview['PROPERTY_REVIEW_TEXT_VALUE']['TEXT']?>
                            </div>
                            <div class="shops-feedback__info">
                                <?$date = FormatDateFromDB($arReview['DATE_CREATE'], 'DD MMMM YYYY');?>
                                <?=$arReview['PROPERTY_USERNAME_VALUE']?>, <span class="time-box"><?=$date?></span>
                            </div>
                        </div>
                    <?endforeach?>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="modal fade reviewShopsModal" id="reviewShopsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-new">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"></button>
            <div class="modal-body">
                <div class="title-1">
                    Оставить отзыв о магазине
                </div>
                <?$APPLICATION->IncludeComponent(
                    "citfact:form.ajax",
                    "review_shops",
                    Array(
                        "IBLOCK_ID" => 35,
                        "SHOW_PROPERTIES" => array(
                            'USERNAME' => array('type' => 'text', 'placeholder' => 'Введите ваше имя', 'required'=>'Y'),
                            'USERPHONE' => array('type' => 'text', 'placeholder' => 'Введите ваш телефон', 'required'=>'Y'),
                            'REVIEW_TEXT' => array('type' => 'textarea', 'placeholder' => 'Введите текст отзыва', 'required'=>'Y'),
                            'SHOP_ID' => array('type' => 'hidden', 'required'=>'Y', 'value' => $arResult['SHOP']['ID']),
                            'CITY_NAME' => array('type' => 'hidden', 'required'=>'Y', 'value' => $arResult['SHOP']['CITY_NAME']),
                            'SHOP_NAME' => array('type' => 'hidden', 'required'=>'Y', 'value' => $arResult['SHOP']['NAME']),
                            'SHOP_ADDRESS' => array('type' => 'hidden', 'required'=>'Y', 'value' => $arResult['SHOP']['ADDRESS']),
                        ),
                        //"EVENT_MESSAGE_ID" => Array("32"), // тут id шаблона, а не события
                        "EVENT_NAME" => 'REVIEW_SHOPS_FORM',
                        "SUCCESS_MESSAGE" => 'Спасибо за ваш отзыв о магазине! Мы покажем его на сайте после проверки модератором.',
                        "ELEMENT_ACTIVE" => 'N'
                    )
                );?>
            </div>
        </div>
    </div>
</div>