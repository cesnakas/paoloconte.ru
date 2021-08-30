<?php

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

include_once('ftp.php');

$ftpConfig = array(
    'SERVER' => 'ftp2.paoloconte.ru',
    'PORT' => 21,
    'LOGIN' => 'web',
    'PASSWORD' => 'HXowm%2Dk',
    'TIMEOUT' => 30,
);

$myFtp = new helpers\ftp($ftpConfig);
if ($myFtp->connect()) {

    print "FTP соединение с " . $ftpConfig['SERVER'] . " успешно установлено\n" . '<br />';

    $fileFrom = './1cbitrix/upload.zip';
    $fileTo = $DOCUMENT_ROOT . '/ftp_loader/upload.zip';
    //$result = $myFtp->download($fileFrom, $fileTo);

    // Открываем локальный архив и смотрим дату его создания
    $date_change_local = 0;
    if (file_exists($fileTo)) {
        $date_change_local = filectime($fileTo);
        //echo "Файл $fileTo в последний раз был изменен: " . date("F d Y H:i:s.", $date_change_local)."<br/>";
    }

    //  получение времени модификации файла
    $date_change_remote = ftp_mdtm($myFtp->ftp, $fileFrom);
    if ($buff != -1) {
        // дата последней модификации somefile.txt : March 26 2003 14:16:41.
        //echo "Дата последней модификации $file : " . date("F d Y H:i:s.", $date_change_remote).'<br/>';

        // Если локальная дата меньше удаленной, качаем файл и распаковываем
        if (true || $date_change_local < $date_change_remote) {
            echo "Качаем файл $fileFrom !" . '<br />';
            if ($myFtp->download($fileFrom, $fileTo)) {
                $zip = new ZipArchive;
                if ($zip->open($fileTo) !== TRUE) {
                    echo "Невозможно открыть архив.\n" . '<br />';
                }
                if ($zip->extractTo($_SERVER['DOCUMENT_ROOT'] . '/ftp_loader/product_images/')) {
                    echo 'Распаковали!' . '<br />';
                } else {
                    echo "Не удалось распаковать!\n" . '<br />';
                }
            } else {
                echo "Не удалось скачать файл!\n" . '<br />';
            }
        } else {
            echo "Не качаем!\n" . '<br />';
        }
    }
} else {
    print '<b>ОШИБКА: FTP соединение с ' . $ftpConfig['SERVER'] . ' не удалось установить</b><br><hr><br>';
}
