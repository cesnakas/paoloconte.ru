
  BX.showWait = function (node, msg) {
    var loader = $('.loader');
    if (loader.length <= 0) {
      $('body').append('<div class="lds-css ng-scope loader active" style="display: flex;"><div class="lds-eclipse"><div></div></div></div>');
    }
    loader.addClass('active');
  };


  BX.closeWait = function (node, obMsg) {
    if (window.blockCloseWait) {
      return;
    }
    var loader = $('.loader');
    loader.removeClass('active');
  };




