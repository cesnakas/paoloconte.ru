<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<script>
  BX.message({
    TEMPLATE_PATH: '<? echo $this->__folder ?>',
    COMPONENT_PATH: '<? echo $this->__component->__path ?>',
    arParams_subscribe_size: <?=CUtil::PhpToJSObject($arParams)?>
  });
</script>

<div class="title-1">
    Нет вашего размера?
</div>
<div class="modal-text">
    Просто укажите желаемый цвет и размер.<br>
    Мы поищем эту модель в других местах и дадим знать о ее появлении! <br>
    <br>
    Заметьте, модель популярна, поэтому некоторых размеров уже может не быть.
</div>
<form action="#" class="form">
    <?= bitrix_sessid_post() ?>
    <input type="text" name="yarobot" value="" class="hide">

    <? foreach ($arResult['SHOW_PROPERTIES'] as $arProp): ?>
        <? if ($arProp['PARAMS_TYPE'] == 'text'): ?>
            <? if ($arProp['CODE'] != 'EMAIL'): ?>
                <div class="form__item">
                    <input type="text"
                           name="<?= $arProp['CODE'] ?>"
                           class="<?= $arProp['REQUIRED'] == 'Y' ? 'required' : '' ?> <?= $arProp['CODE'] == 'PHONE_NUMBER' ? 'mask-phone phone-size' : '' ?> <?= $arProp['CODE'] == 'EMAIL' ? 'email email-size' : '' ?> <?= $arProp['CODE'] == 'PRICE' ? 'mask-price' : '' ?>"
                           placeholder="<?= $arProp['PLACEHOLDER'] ?>"/>
                </div>
            <? endif; ?>
        <? elseif ($arProp['PARAMS_TYPE'] == 'textarea'): ?>
            <div class="form__item">
                <textarea name="<?= $arProp['CODE'] ?>" cols="30" rows="10"
                          class="<?= $arProp['REQUIRED'] == 'Y' ? 'required' : '' ?>"
                          placeholder="<?= $arProp['PLACEHOLDER'] ?>"></textarea>
            </div>
        <? elseif ($arProp['PARAMS_TYPE'] == 'hidden'): ?>
        <input type="hidden" name="<?= $arProp['CODE'] ?>" class="<?= $arProp['REQUIRED'] == 'Y' ? 'required' : '' ?>"
               value="<?= $arProp['VALUE']; ?>"/>
            <div class="modal-inner">

                <? elseif ($arProp['PARAMS_TYPE'] == 'radio'): ?>
                    <div class="modal-s">
                        <div class="modal-s__title"><?= $arProp['NAME'] ?></div>

                        <div class="modal-s__inner">
                            <?
                            CModule::IncludeModule('catalog');
                            $id_tov = $arParams['SHOW_PROPERTIES']['TOVAR_ID']['value'];
                            $sizes = array();
                            $res2 = CCatalogSKU::getOffersList(
                                $id_tov,
                                0,
                                array(),
                                array(),
                                array('CODE' => array('RAZMER'))
                            );
                            $tempOffers = [];
                            if (!empty($res2[$id_tov])){
                                foreach ($res2[$id_tov] as $offerTemp){
                                    $tempOffers[$offerTemp['PROPERTIES']['RAZMER']['VALUE']] = $offerTemp;
                                }
                                ksort($tempOffers);
                                $res2[$id_tov] = $tempOffers;
                            }
                            foreach ($res2[$id_tov] as $el_of => $offer) {
                                $sizes[] = $offer['PROPERTIES']['RAZMER']['VALUE']; ?>
                                <input type="radio" value="<?= $offer['PROPERTIES']['RAZMER']['VALUE'] ?>"
                                       id="<?= $offer['ID']; ?>" name="SIZE" class="checkbox-offer">
                                <label id="<?= $offer['ID']; ?>" for="<?= $offer['ID']; ?>">
                                    <?= $offer['PROPERTIES']['RAZMER']['VALUE'] ?>
                                </label>
                            <? } ?>
                        </div>
                    </div>
                <? elseif ($arProp['PARAMS_TYPE'] == 'color'): ?>
                <div class="modal-color">
                    <div class="modal-color__title"><?= $arProp['NAME'] ?></div>

                    <div class="modal-color__inner">
                        <? foreach ($arResult['OTHER_COLORS'] as $arColor): ?>
                            <input type="radio" value="<?= $arColor['COLOR']['NAME'] ?>"
                                   id="<?= $arColor['COLOR']['NAME']; ?>" name="COLOR" class="colorbox-offer">
                            <label for="<?= $arColor['COLOR']['NAME']; ?>"
                                   style="background-image: url('<?= $arColor['COLOR']['FILE_PATH']; ?>');"></label>
                        <? endforeach ?>
                    </div>
                </div>
            </div>
        <? elseif ($arProp['PARAMS_TYPE'] == 'checkbox'):; ?>
            <script>
              $(document).ready(function () {
                $('div.line > #a2').click();
              });
            </script>
        <? endif; ?>
    <? endforeach ?>

    <div class="modal-pp">
        <a href="#" id="subscribe_size_submit" class="btn btn--black">Отправить заявку</a>
        <div class="modal-pp__text">
            <? $APPLICATION->IncludeFile(
                SITE_DIR . "/include/oferta_application.php",
                Array(),
                Array("MODE" => "text")
            ); ?>
        </div>
    </div>

    <div class="errors_cont"></div>
    <div class="success_cont"></div>
</form>
<style>
    .checkbox-offer,
    .colorbox-offer {
        display: none;
    }

    label.size,
    label.color-box {
        font-size: 13px;
        width: 27px;
        height: 27px;
        border-radius: 27px;
        text-align: center;
        line-height: 27px;
        display: inline-block;
        cursor: pointer;
        margin-right: 2px;
    }

    label.color-box {
        border: 1px solid #e1e1e1;
    }

    label.color-box:hover,
    .colorbox-offer:checked + label {
        border-width: 5px;
    }

    label.size:hover,
    .checkbox-offer:checked + label {
        background: #e8e8e8;
    }
</style>
<script>
  $(document).ready(function () {
    var template_path = BX.message('TEMPLATE_PATH');
    var component_path = BX.message('COMPONENT_PATH');
    var arParams_subscribe_size = BX.message('arParams_subscribe_size');

    $('#subscribe_size_submit').click(function () {
      var form = $(this).parents('form');
      var data_send = {formdata: form.serialize(), params: arParams_subscribe_size, sessid: $('input#sessid').val()};
      var result_cont = form.find('.success_cont');
      var errors_cont = form.find('.errors_cont');
      var color_send = $('input.colorbox-offer:checked').val();
      var label_id = $('input.checkbox-offer:checked').val();
      var size_send = parseInt($('label[for=' + label_id + ']').text().trim());
      var url_send = location.href;
      var name_send = $('h1[itemprop="name"]').text();
      if (validate_ajax_form(form)) {
        $.ajax({
          type: "POST",
          url: component_path + '/ajax.php?COLOR=' + color_send + '&SIZE=' + label_id + '&URL=' + url_send + '&NAME_TOVAR=' + name_send,
          data: data_send,
          success: function (data) {
            var json = JSON.parse(data);
            result_cont.html('');
            errors_cont.html('');
            if (json.errors.length > 0) {
              for (var key in json.errors) {
                errors_cont.append('<p class="">' + json.errors[key] + '</p>');
              }
              errors_cont.hide().fadeIn();
            } else {
              for (var key in json.result) {
                result_cont.append('<p class="">' + json.result[key] + '</p>');
              }
              ////var email = GetQueryStringParams(data_send.formdata, 'EMAIL');
              ////(window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function () {
              ////  rrApi.setEmail(email);
              ////});
              result_cont.hide().fadeIn();
              form[0].reset();
              //window.location.reload();
            }
          }
        });
      } else {
        // прокручиваем к первой ошибке
        //var pos = form.find('input.error:first').offset();
        //$('html,body').animate({scrollTop:pos.top-70},500);
      }
      return false;
    });


    function validate_ajax_form(form) {
      flag = true;
      //var email_s = $('input.email-size').val();
      var phone_s = $('input.phone-size').val();
      if (phone_s == '') {
        form.find('input.required, textarea.required').each(function () {
          //console.log($(this).find('input').val());
          var input = $(this);
          var val = input.val();
          //var email_val = $('.email').val();
          var phone_val = $('.mask-phone').val();
          var placeholder = input.attr('placeholder');

          if ((phone_val == '') || val == placeholder || val.length < 3
            // если телефон, то не меньше 6 символов
            || (input.hasClass('phone') && val.length < 6)) {
            $(this).addClass('error');
            $(this).parent().find('.label-alert').show();
            flag = false;
          } else {
            var re = /^[0-9- ]*$/;
            var re_email = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if (input.hasClass('phone') && !re.test(val)) {
              $(this).addClass('error');
              flag = false;
            } else if (input.hasClass('email') && !re_email.test(val)) {
              $(this).addClass('error');
              flag = false;
            } else {
              $(this).removeClass('error');
              $(this).parent().find('.tip-error').hide();
              $(this).parent().find('.tip-error-phone').hide();
            }
          }
        });
      } else {
        form.find('textarea.required').each(function () {
          //console.log($(this).find('input').val());
          var input = $(this);
          var val = input.val();
          //var email_val = $('.email').val();
          var phone_val = $('.mask-phone').val();
          var placeholder = input.attr('placeholder');

          if ((phone_val == '') || val == placeholder || val.length < 3
            // если телефон, то не меньше 6 символов
            || (input.hasClass('phone') && val.length < 6)) {
            $(this).addClass('error');
            $(this).parent().find('.label-alert').show();
            flag = false;
          } else {
            var re = /^[0-9- ]*$/;
            var re_email = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if (input.hasClass('phone') && !re.test(val)) {
              $(this).addClass('error');
              flag = false;
            } else if (input.hasClass('email') && !re_email.test(val)) {
              $(this).addClass('error');
              flag = false;
            } else {
              $(this).removeClass('error');
              $(this).parent().find('.tip-error').hide();
              $(this).parent().find('.tip-error-phone').hide();
            }
          }
        });
      }


      //console.log(flag);
      return flag;
    }

    if (typeof GetQueryStringParams != 'function')
      function GetQueryStringParams(sPageURL, sParam) {
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++) {
          var sParameterName = sURLVariables[i].split('=');
          if (sParameterName[0] == sParam) {
            return sParameterName[1];
          }
        }
      }

  }); // end document ready
</script>
