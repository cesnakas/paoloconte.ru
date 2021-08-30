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

$fileTo = $DOCUMENT_ROOT.'/ftp_loader/upload.zip';
//$fileTo = $DOCUMENT_ROOT.'/ftp_loader/koshelki.zip';

echo $fileTo."\n";

// unzip upload.zip -d product_images_new

$zip = new ZipArchive;
$res = $zip->open($fileTo);
if ($res !== TRUE) {
	echo "Невозможно открыть архив: " . $res . "\n";
}
if ($zip->extractTo($_SERVER['DOCUMENT_ROOT'].'/ftp_loader/product_images/')){
	echo 'Распаковали!';
}
else{
	echo "Не удалось распаковать!\n";
}
