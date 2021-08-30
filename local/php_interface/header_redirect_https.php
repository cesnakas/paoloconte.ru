<?$secured = ($_SERVER["HTTP_SCHEME"] == 'https') ? true : false;
$curUri = $_SERVER['REQUEST_URI'];
if (
    (!$secured) &&
    (strpos($curUri, "1c_exchange.php") === false)
) {
    $redirectUrl = "https://".$_SERVER["SERVER_NAME"].$curUri;
    //header( 'Location: ' . $redirectUrl, true, 301 ); // этот код НЕ срабатывает, а следующие две строчки работают
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirectUrl);
}?>