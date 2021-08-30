<script>
  document.addEventListener('DOMContentLoaded', function () {
    var cancelOrderBtn = document.getElementById('sale-adm-status-cancel-dialog-btn');

    if (cancelOrderBtn) {
      cancelOrderBtn.addEventListener('click', function (event) {
        var cancelOrderBtn = event.target;

        (function (i, s, o, g, r, a, m) {
          i['GoogleAnalyticsObject'] = r;
          i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
          }, i[r].l = 1 * new Date();
          a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
          a.async = 1;
          a.src = g;
          m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-10107368-1', 'auto',);
        ga('require', 'ec');

        var re = /(["'])(.+?)\1/g;
        var res = cancelOrderBtn.onclick.toString().match(re);
        var orderId = res[0].replace(/'/g, '');

        ga('ec:setAction', 'refund', {
          'id': orderId
        });

        ga('send', 'event', 'Ecommerce', 'Refund', {'nonInteraction': 1});
      });
    }
  });
</script>