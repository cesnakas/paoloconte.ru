<?define('NEED_AUTH', true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вход на сайт");
?>
<script>
    app.setPageTitle({"title" : "Вход на сайт"});
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>