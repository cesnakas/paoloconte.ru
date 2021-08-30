<?

$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/..");

$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/include/GGGenerateSitemap.php");
set_time_limit(0);

//подключение модуля поиска
if (CModule::IncludeModule('search')) {
    //В этом массиве будут передаваться данные "прогресса". Он же послужит индикатором окончания исполнения.
    $NS = Array();
    //Задаем максимальную длительность одной итерации равной "бесконечности".
    $sm_max_execution_time = 0;
    //Это максимальное количество ссылок обрабатываемых за один шаг.
    //Установка слишком большого значения приведет к значительным потерям производительности.
    $sm_record_limit = 5000;

    $options = [
        'USE_HTTPS' => 'Y'
    ];

    // PAGES корневые разделы, страницы которых должны попасть в карту сайта
    $customParams = [
        'PAGES' => [
            'index.php',
            'about',
            'catalog',
            'check_loyalty_card',
            'contacts',
            'events',
            'help',
            'promo',
            'shops',
            'socialnye-sety',
            'svoystva-tkaney',
            'vacancy',
        ],
        'PAGE_PARAMS' => [
            'MAIN' => [
                'PRIORITY' => 1,
                'CHANGEFREQ' => 'daily'
            ],
            'CATALOG' => [
                'SECTION' => [
                    'PRIORITY' => 0.9,
                    'CHANGEFREQ' => 'weekly'
                ],
                'SUBSECTION' => [
                    'PRIORITY' => 0.8,
                    'CHANGEFREQ' => 'weekly'
                ],
                'DETAIL' => [
                    'PRIORITY' => 0.7,
                    'CHANGEFREQ' => 'weekly'
                ]
            ],
            'DEFAULT' => [
                'PRIORITY' => 0.5,
                'CHANGEFREQ' => 'monthly'
            ]
        ]
    ];

    do {
        $cSiteMap = new GGSiteMap;
        //Выполняем итерацию создания,
        $NS = $cSiteMap->Create("s1", array($sm_max_execution_time, $sm_record_limit), $NS, $options, $customParams);
        //Пока карта сайта не будет создана.
    } while (is_array($NS));
}

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");