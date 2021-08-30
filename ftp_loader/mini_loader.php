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

$fileTo = $_SERVER['DOCUMENT_ROOT'].'/ftp_loader/uploader.zip';
$zip = new ZipArchive;
			if ($zip->open($fileTo) !== TRUE) {
				echo "Невозможно открыть архив.\n";
			}
			if ($zip->extractTo($_SERVER['DOCUMENT_ROOT'].'/ftp_loader/product_images/')){
				echo 'Распаковали!';
			}
			else{
				echo "Не удалось распаковать!\n";
			}
