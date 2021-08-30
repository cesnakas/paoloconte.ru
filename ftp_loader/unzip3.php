<?

$dir = __DIR__;
if (strpos($dir, '/ftp_loader')) {
    $dir = substr($dir, 0, strpos($dir, '/ftp_loader'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;
if (!$USER->isAdmin()) {
    die;
}

set_time_limit(30000);

$fileTo = $DOCUMENT_ROOT . '/ftp_loader/upload.zip';
//$fileTo = $DOCUMENT_ROOT.'/ftp_loader/koshelki.zip';
echo '<br />==============================<br /><br />';
echo $fileTo . "\n";
echo '<br /><br />==============================<br /><br />';
// unzip upload.zip -d product_images_new

$zip = new ZipArchive;
$res = $zip->open($fileTo);
if ($res !== TRUE) {
    echo "Невозможно открыть архив: " . $res . "\n";
    echo '<br /><br />==============================<br /><br />';
} else {
    echo "Открыли zip";
    echo '<br /><br />==============================<br /><br />';
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $filename = $zip->getNameIndex($i);
        $CATALOG_IMG_PHOTO = str_replace("/", "\/", CATALOG_IMG_PHOTO);
        $pattern = "/([a-z0-9\\_-]+)" . $CATALOG_IMG_PHOTO . '([a-z0-9\\_-]+)\..+/';
        preg_match($pattern, $filename, $matches);

        if (!empty($matches[1]) && !empty($matches[2])) {
            $resRMFiles = Citfact\Paolo::deleteProductImage($matches[1], $matches[2]);
            if (!empty($resRMFiles['FILES_REMOVE'])) {
                echo "Удалили файлы для " . $filename;
                echo '<br /><br />==============================<br /><br />';
            } elseif (!empty($resRMFiles['ERROR'])) {
                foreach ($resRMFiles['ERROR'] as $err) {
                    echo $err;
                    echo '<br /><br />==============================<br /><br />';
                }
            }
        }
    }
}

echo '<br /><br />==============================<br /><br />';
