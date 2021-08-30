<?php
function lock($name) {
    $lock = sys_get_temp_dir()."/$name.lock";
    echo $lock;
    $aborted = file_exists($lock) ? filemtime($lock) : null;
    $fp = fopen($lock, 'w');

    if (!flock($fp, LOCK_EX|LOCK_NB)) {
        // заблокировать файл не удалось, значит запущена копия скрипта
        return false;
    }
    // получили блокировку файла

    // если файл уже существовал значит предыдущий запуск кто-то прибил извне
    if ($aborted) {
        error_log(sprintf("Запуск скрипта %s был завершен аварийно %s", $name, date('c', $aborted)));
    }

    // снятие блокировки по окончанию работы
    // если этот callback, не будет выполнен, то блокировка
    // все равно будет снята ядром, но файл останется
    register_shutdown_function(function() use ($fp, $lock) {
        flock($fp, LOCK_UN);
        fclose($fp);
        unlink($lock);
    });

    return true;
}