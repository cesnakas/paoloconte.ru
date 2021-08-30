<?php
/**
 * Created by JetBrains PhpStorm.
 * User: maus
 * Date: 02.11.15
 * To change this template use File | Settings | File Templates.
 */

$requiredModules = array(
    'iblock',
    'catalog',
    'sotbit.seometa',
);
foreach ($requiredModules as $module) {
    if (!\Bitrix\Main\Loader::includeModule($module)) {
        die("Требуемый модуль {$module} не найден!");
    }
}

$libDir = realpath(__DIR__ . '/classes');

$Directory = new RecursiveDirectoryIterator($libDir);
$Iterator = new RecursiveIteratorIterator($Directory);
$Regex = new RegexIterator($Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$classMap = array();
foreach ($Regex as $k => $v) {
    // приводим к юниксовым путям, для единообразия
    $relPath = str_replace('\\', '/', str_replace(__DIR__, '', $k));
    // тут ещё и убираем расширение
    $nsPath = substr(str_replace('\\', '/', str_replace($libDir, '', $k)), 0, -4);
    $nsPath = 'Trinet\Seometa\Batchop' . str_replace('/', '\\', $nsPath);
    $classMap[$nsPath] = $relPath;
}

if ( !class_exists('XLSXWriter') ) {
    $classMap['XLSXWriter'] = '/vendor/xlsxwriter.class.php';
}

require_once $libDir . '/Constants.php';
$classMap[Trinet\Seometa\Batchop\Constants::MODULE_CODE] = '/install/index.php';

CModule::AddAutoloadClasses(Trinet\Seometa\Batchop\Constants::MODULE_ID, $classMap);
