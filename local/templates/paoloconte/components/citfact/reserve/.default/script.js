$(function () {

  var shopStoreWrap = $(document).find('.shop-store-wrap');
  var formReserveContainer = $('#reserveForm');
  var formReserveSize = formReserveContainer.find('#form-reserve-size');
  var formReserveShopName = formReserveContainer.find('#form-reserve-shop-name');
  var msgContainer = $('#resultModalInReserve');
  var msgTitle = msgContainer.find('#title-in-reserve');
  var msgText = msgContainer.find('#msg-in-reserve');
  var title = '';
  var msg = '';

  // смена активности кнопок в зависимости от выбранного размера/магазина
  shopStoreWrap.find('.size').on('click', function () {
    var shopId = $(this).data('shop-id');
    var reserveBtn = shopStoreWrap.find('.btn-reserve[data-shop-id="' + shopId + '"]');

    shopStoreWrap.find('.size').not(this).removeClass('active');
    $(this).addClass('active');

    shopStoreWrap.find('.btn-reserve').not(reserveBtn).removeClass('active');
    reserveBtn.addClass('active');
  });

  shopStoreWrap.find('.choose-shop').on('click', function () {
    $(this).parent().find('.size').trigger('click');
  });

  // проверка остатков
  shopStoreWrap.find('.btn-reserve').on('click', function (e) {
    e.preventDefault();

    if (!$(this).hasClass('active')) {
      title = 'Не выбран размер';
      msg = 'Чтобы отложить товар, пожалуйста, выберите требуемый размер.';

      if (shopStoreWrap.find('.size').hasClass('without-size')) {
        title = 'Не выбран магазин';
        msg = 'Чтобы отложить товар, пожалуйста, выберите магазин.';
      }

      msgTitle.html(title);
      msgText.html(msg);
      msgContainer.modal('show');

      return false;
    }

    var storeId = $(this).data('store-id');
    var size = shopStoreWrap.find('.size.active').data('size');
    var artNum = $(this).data('artnum');
    var shopName = $(this).data('shop-name');

    if (size) {
      formReserveSize.html(size);
    }
    formReserveShopName.html(shopName);

    $.ajax({
      type: "POST",
      url: ajaxPath + 'check_ostatki.php',
      data: {storeId: storeId, artNum: artNum, size: size},
      timeout: 15000,
      async: true,
      beforeSend: function () {
        overlay.show();
      },
      complete: function () {
        overlay.hide();
      },
      success: function (data) {
        if (data.length > 0) {
          var res = JSON.parse(data);
          if (res.success === true) {
            $('#reserveForm').modal('show');
          } else {
            msgTitle.html(res.error.title);
            msgText.html(res.error.msg);
            msgContainer.modal('show');
          }
        } else {
          title = 'Не удалось проверить наличие';
          msg = 'К сожалению, мы не смогли получить информацию о наличии выбранного товара в магазине. Пожалуйста, попробуйте позже.';
          msgTitle.html(title);
          msgText.html(msg);
          msgContainer.modal('show');
        }
      },
      error: function (jqXHR, textStatus) {
        title = 'Не удалось проверить наличие';
        msg = 'К сожалению, мы не смогли получить информацию о наличии выбранного товара в магазине. Пожалуйста, попробуйте позже.';
        msgTitle.html(title);
        msgText.html(msg);
        msgContainer.modal('show');
      }
    });
  });

  // форма резервирования товара
  formReserveContainer.find('#form_reserve_submit').on('click', function (e) {
    e.preventDefault();
    var btnReserveActive = shopStoreWrap.find('.btn-reserve.active');
    var artNum = btnReserveActive.data('artnum');
    var storeId = btnReserveActive.data('store-id');
    var shopId = btnReserveActive.data('shop-id');
    var shopName = btnReserveActive.data('shop-name');
    var productName = formReserveContainer.find('#reserve-product_name').html();
    var size = shopStoreWrap.find('.size.active').data('size');
    var form = formReserveContainer.find('#form_reserve');
    var sessid = formReserveContainer.find('input#sessid').val();

    if (validateform(form)) {
      $.ajax({
        type: "POST",
        url: ajaxPath + 'reserve.php',
        data: {
          storeId: storeId,
          shopId: shopId,
          shopName: shopName,
          artNum: artNum,
          productName: productName,
          size: size,
          formdata: form.serialize(),
          sessid: sessid
        },
        timeout: 15000,
        async: true,
        beforeSend: function () {
          $('#reserveForm').modal('hide');
          overlay.show();
        },
        complete: function () {
          overlay.hide();
          form[0].reset();
        },
        success: function (data) {
          if (data.length > 0) {
            var res = JSON.parse(data);
            if (res.success === true) {
              var date = new Date(),
                strDate = ('0' + (date.getDate() + 1)).slice(-2) + '.' +
                  ('0' + (date.getMonth() + 1)).slice(-2) + '.' +
                  date.getFullYear() + ' ' +
                  ('0' + date.getHours()).slice(-2) + ':' +
                  ('0' + date.getMinutes()).slice(-2);

              if (size) {
                size = "в размере " + size;
              } else {
                size = '';
              }

              title = 'Спасибо! Мы отложим товар для Вас!';
              msg = productName + size;
              msg += '<br><br>Товар будет ждать Вас <strong>до ' + strDate + '</strong> в магазине по адресу:'
              msg += shopStoreWrap.find('.detail-size-info[data-shop-id="' + shopId + '"]').html();
              msgTitle.html(title);
              msgText.html(msg);
              msgContainer.modal('show');
            } else {
              msgTitle.html(res.error.title);
              msgText.html(res.error.msg);
              msgContainer.modal('show');
            }
          } else {
            title = 'Не удалось отложить товар';
            msg = 'К сожалению, мы не смогли выполнить резериврование выбранного товара в магазине. Пожалуйста, попробуйте позже.';
            msgTitle.html(title);
            msgText.html(msg);
            msgContainer.modal('show');
          }
        },
        error: function (jqXHR, textStatus) {
          title = 'Не удалось отложить товар';
          msg = 'К сожалению, мы не смогли выполнить резериврование выбранного товара в магазине. Пожалуйста, попробуйте позже.';
          msgTitle.html(title);
          msgText.html(msg);
          msgContainer.modal('show');
        }
      });
    }
  });

  function validateform(form) {
    flag = true;
    form.find('input.required, textarea.required').each(function () {
      var input = $(this);
      var val = input.val();
      var placeholder = input.attr('placeholder');

      if (val == '' || val == placeholder || val.length < 3 || (input.hasClass('phone') && val.length < 6)) {
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

    return flag;
  }
  
  
});

function getAndShowCityModalInReserve(el){
  BX.ajax({
    url: ajaxPath + 'modalreserve.php',
    method: 'POST',
    dataType: 'html',
    timeout: 60,
    async: true,
    processData: false,
    scriptsRunFirst: false,
    emulateOnload: true,
    cache: false,
    onsuccess: function (data) {
      if ($('body').append(data).find('#cityModalInReserve')){
        $(el).replaceWith("<a href=\"javascript:void(0);\" data-toggle=\"modal\" data-target=\"#cityModalInReserve\">Выбрать другой город</a>");
        $('#cityModalInReserve').modal('show');
      }
    }
  });
}

BX.ready(function(){
  var getModalLink = document.querySelector('.js-get-inreserve-modal-and-show');
  if (getModalLink){
    BX.bind(getModalLink, 'click', function(){
      getAndShowCityModalInReserve(this)
    });
  }
});