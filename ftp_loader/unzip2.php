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

$tmpPath = '/ftp_loader/tmp/product_images/';
$realPath = '/ftp_loader/product_images/';

if ($zip->extractTo($_SERVER['DOCUMENT_ROOT'] . $tmpPath)) {
    printSuccess('Распаковали во временную папку');
    if ($res !== TRUE) {
        printError("Невозможно открыть архив: " . $res);
    } else {
        printSuccess('Открыли zip для удаления файлов');
        $arFiles = array();
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            $pattern = "/^([a-zA-Z0-9\\_-]+)/";
            preg_match($pattern, $filename, $matches);
            $arFiles[] = $matches[0];
            $arAllFiles[] = $filename;
        }
        $arFiles = array_unique($arFiles);
        // pre($arFiles, $die=1, $all=false, $hide=false); // tester_id_tuta

        foreach ($arFiles as $test) {
            $pattern = "/" . $test . "\/photo\/(.*)_0004.(.*)/";
            $mainImage = preg_grep($pattern, $arAllFiles);
            if (empty($mainImage)) {
                printError('Нет изображения основного ракурса для ' . $test);
                continue;
            }
            if (!empty($test)) {
                $resRMFiles = Citfact\Paolo::deleteProductAllImage($test);
                if (!empty($resRMFiles['FILES_REMOVE'])) {
                    printSuccess('Файлы удалены для ' . $test);
                }
                if (Citfact\Paolo::recurseCopy($_SERVER['DOCUMENT_ROOT'] . $tmpPath . $test . '/photo/', $_SERVER['DOCUMENT_ROOT'] . $realPath . $test . '/photo/')) {
                    printSuccess('Перенесли файлы из временной папки для ' . $test);
                    if (Citfact\Paolo::setPicturesToElement($test, reset($mainImage))) {
                        printSuccess('Добавили фото основного ракурса в поля "Картинка для анонса" и "Детальная картинка" для ' . $test);
                    } else {
                        printWarning('Не удалось добавить фото основного ракурса в поля "Картинка для анонса" и "Детальная картинка" для ' . $test);
                    }
                } else {
                    printError('Не удалось перенести файлы из временной папки для ' . $test);
                    printError(error_get_last());
                }
                if (!empty($resRMFiles['WARNING'])) {
                    foreach ($resRMFiles['WARNING'] as $warning) {
                        printWarning($warning);
                    }
                }
                if (!empty($resRMFiles['ERROR'])) {
                    foreach ($resRMFiles['ERROR'] as $error) {
                        printError($error);
                    }
                }
            }
        }
    }

    if (Citfact\Paolo::removeDir($_SERVER['DOCUMENT_ROOT'] . $tmpPath)) {
        printSuccess('Удалили временную папку');
    } else {
        printError('Не удалось удалить временную папку');
        printError(error_get_last());
    }
} else {
    printError('Не удалось распаковать!');
}


function printSuccess($var)
{
    print_r('<span style="color: green">SUCCESS: ' . $var . '</span>');
    echo '<br /><br />==============================<br /><br />';
}

function printError($var)
{
    print_r('<span style="color: red">ERROR: ' . $var . '</span>');
    echo '<br /><br />==============================<br /><br />';
}

function printWarning($var)
{
    print_r('<span style="color: #cc8f00">WARNING: ' . $var . '</span>');
    echo '<br /><br />==============================<br /><br />';
}
