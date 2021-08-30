if (!BX) {
  alert("Error: Notify Bar requires $APPLICATION->ShowHeadScripts() in <head> section");
}

if (BX) BX.ready(function () {
  function notifyBar() {
    var closeBtnId = "notifybar_btn_close";
    var cookieDisableName = "DISABLE_NOTIFY_BAR";

    var Cookies = {
      set: function (name, value, days) {
        if (days) {
          var date = new Date();
          date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
          var expires = "; expires=" + date.toGMTString();
        } else {
          var expires = "";
        }

        document.cookie = name + "=" + value + expires + "; path=/";
      },

      get: function (name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');

        for (var i = 0; i < ca.length; i++) {
          var c = ca[i];
          while (c.charAt(0) == ' ') {
            c = c.substring(1, c.length);
          }

          if (c.indexOf(nameEQ) == 0) {
            return c.substring(nameEQ.length, c.length);
          }
        }

        return null;
      },

      erase: function (name) {
        Cookies.set(name, "", -1);
      }
    };

    var el_closeBtn = document.getElementById(closeBtnId);

    BX.bind(el_closeBtn, "click", function () {
      Cookies.set(cookieDisableName, "Y", 1);
      var el_notifyBarBlock = el_closeBtn.parentNode;
      el_notifyBarBlock.style.display = "none";
    });
  }

  if (typeof window.frameCacheVars !== 'undefined' && typeof BX !== 'undefined') {
    BX.addCustomEvent('onFrameDataReceived', function () {
      notifyBar();
    });
  }

  notifyBar();
});
