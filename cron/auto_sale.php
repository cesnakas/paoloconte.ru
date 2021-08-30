<?

if (php_sapi_name() != 'cli') {
    exit;
}

$start_time = microtime(true);


//
// BEGIN Bitrix.
//

$dir = __DIR__;
if (strpos($dir, '/cron')) {
    $dir = substr($dir, 0, strpos($dir, '/cron'));
}
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'] = $dir;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

Use \Bitrix\Iblock\PropertyIndex\Manager;

CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');
CModule::IncludeModule('sale');

global $DB;

//
// END Bitrix.
//


ini_set('display_errors', 1);

// Пороговое значение скидки. ПОСТАВИЛ ПОРОГ ЧТОБЫ НЕ СРАБАТЫВАЛ ПЕРЕНОС В РАСПРОДАЖУ. ПОТОКИН 20180212
$threshold_discount = 99;

// Розничная Интернет Акция
$price_sale_id = 5;

// Розничная Интернет
$price_retail_id = 2;

$iblock_type = 'catalog';
$iblock_id = 10;
$iblock_id_offer = 11;

// если да, то вместо обычно кода просто апдейтим у всех элементов фасетный индекс.
// Вообще фасетный индекс апдейтится и так после setelementsection
$isUpdateFacet = false;

$selected_fields = ['ID', 'IBLOCK_ID', 'CODE', 'NAME'];

$statistics = [
    // Товары перенесённые из каталога в раздел "Распродажа".
    0 => 0,
    // Товары из каталога, которым назначен раздел "Распродажа".
    1 => 0,
    // Товары, у которых убран раздел "Распродажа".
    2 => 0,
];


//
// BEGIN Данные раздела Распродажа.
//

$result = CIBlockSection::GetList(
    ['SORT' => 'ASC'],
    [
        'IBLOCK_TYPE' => $iblock_type,
        'CODE' => 'rasprodazha'
    ]
);

if ($sale_section = $result->GetNextElement()) {
    $sale_section = $sale_section->GetFields();
}

if ($sale_section === false) {
    exit;
}

//
// END Данные раздела Распродажа.
//


$result = CIBlockSection::GetList(
    ['SORT' => 'ASC'],
    [
        'IBLOCK_TYPE' => $iblock_type,
        'IBLOCK_ID' => $iblock_id,
    ],
    false,
    ['ID', 'CODE', 'NAME', 'IBLOCK_SECTION_ID']
);

$id_list_sale = [];

$sections_catalog = [];
$sections_sale = [];

$sections = [];

while ($section = $result->Fetch()) {
    $in_sale = false;
    // Цепочка родителей.
    $chain = CIBlockSection::GetNavChain(false, $section['ID'], $selected_fields);
    while ($section_fields = $chain->Fetch()) {
        if ($section_fields['ID'] == $sale_section['ID']) {
            $in_sale = true;
            break;
        }
    }

    if ($in_sale == true) {
        $sections_sale[$section['ID']] = $section;
    } else {
        $sections_catalog[] = $section;
    }
}


//
// BEGIN Разделы имеющие пару, в каталоге и в распродажах.
//

$sections = [];
foreach ($sections_catalog as $section1) {
    foreach ($sections_sale as $section2) {
        if (
            (
                ($section1['IBLOCK_SECTION_ID'] == 54 && $section2['IBLOCK_SECTION_ID'] == 123)
                || ($section1['IBLOCK_SECTION_ID'] == 66 && $section2['IBLOCK_SECTION_ID'] == 124)
            ) && (
                $section1['CODE'] == $section1['CODE'] . '-sale'
                || mb_strtolower($section1['NAME']) == mb_strtolower($section2['NAME'])
            )
        ) {
            $sections[] = [
                'cat' => $section1,
                'sale' => $section2
            ];
        }
    }
}

//
// END Разделы имеющие пару, в каталоге и в распродажах.
//


// Получить все товары.
$result = CIBlockElement::GetList(
    ['SORT' => 'ASC'],
    [
        'IBLOCK_TYPE' => $iblock_type,
        'IBLOCK_ID' => $iblock_id,
        'ID' => 130828,
    ],
    false,
    false,
    $selected_fields
);

while ($item = $result->Fetch()) {
    if ($isUpdateFacet) {
        Manager::updateElementIndex($item['IBLOCK_ID'], $item['ID']);
        continue;
    }

    // Получить группы в которых состоит товар.
    $records = CIBlockElement::GetElementGroups($item['ID'], false, $selected_fields);
    $groups = [];
    while ($group = $records->Fetch()) {
        $groups[$group['ID']] = $group;
    }

    $resOffer = CCatalogSKU::getOffersList($item['ID'], $item['IBLOCK_ID'], array('ACTIVE' => 'Y'), $selected_fields, array());
    $firstOffer = false;
    if (!empty($resOffer[$item['ID']])) {
        $firstOffer = array_shift($resOffer[$item['ID']]);
    }

    // Розничная Интернет
    $res = CPrice::GetList(
        [],
        [
            'PRODUCT_ID' => ($firstOffer) ? $firstOffer['ID'] : $item['ID'],
            'CATALOG_GROUP_ID' => $price_retail_id
        ]
    );
    $price_retail = $res->Fetch();
    $price_retail['PRICE'] = floatval($price_retail['PRICE']);

    // Розничная Интернет Акция
    $res = CPrice::GetList(
        [],
        [
            'PRODUCT_ID' => ($firstOffer) ? $firstOffer['ID'] : $item['ID'],
            'CATALOG_GROUP_ID' => $price_sale_id
        ]
    );
    $price_sale = $res->Fetch();
    $price_sale['PRICE'] = floatval($price_sale['PRICE']);

    $discount = 0;
    if ($price_sale['PRICE'] > 0) {
        $amount_in_percent = $price_retail['PRICE'] / 100;
        $discount = round(($price_retail['PRICE'] - $price_sale['PRICE']) / $amount_in_percent);
    }

    if ($price_sale['PRICE'] > 0) {
        // Если скидка более ## процентов, то показывать только в распродаже.
        if ($discount >= $threshold_discount) {
            $update = false;
            $id_list = [];
            foreach ($groups as $group) {
                foreach ($sections as $section) {
                    if ($section['cat']['ID'] == $group['ID']) {
                        $id_list[$section['sale']['ID']] = $section['sale']['ID'];
                        $update = true;
                    } else if ($section['sale']['ID'] == $group['ID']) {
                        $id_list[$group['ID']] = $group['ID'];
                    }
                }
            }

            if ($update == true && count($id_list) > 0) {
                CIBlockElement::SetElementSection($item['ID'], $id_list);
                Manager::updateElementIndex($item['IBLOCK_ID'], $item['ID']);

                $statistics[0]++;
            }
        } else { // Если скидка менее 40%, то показать в каталоге и распродаже.
            $id_list = [];
            foreach ($groups as $group) {
                $id_list[$group['ID']] = $group['ID'];
                foreach ($sections as $section) {
                    if ($section['cat']['ID'] == $group['ID'] || $section['sale']['ID'] == $group['ID']) {
                        $id_list[$section['sale']['ID']] = $section['sale']['ID'];
                        $id_list[$section['cat']['ID']] = $section['cat']['ID'];
                    }
                }
            }

            if (count($groups) != count($id_list) && count($id_list) > 0) {
                CIBlockElement::SetElementSection($item['ID'], $id_list);
                Manager::updateElementIndex($item['IBLOCK_ID'], $item['ID']);

                $statistics[1]++;
            }
        }
    } else { // Если убрали цену "Розничная Интернет Акция", то необходимо вернуть товар каталог.
        $update = false;
        $id_list = [];
        foreach ($groups as $group) {
            if (array_key_exists($group['ID'], $sections_sale) == true) {
                // Найти соответственную группу из каталога.
                foreach ($sections as $section) {
                    if ($section['sale']['ID'] == $group['ID']) {
                        if (array_key_exists('cat', $section) == true) {
                            $id_list[$section['cat']['ID']] = $section['cat']['ID'];
                            $update = true;
                        }
                        break;
                    }
                }
            } else {
                // А этот участок нужен, чтобы сохранить группы, к которым был привязан товар помимо распродажи.
                $id_list[$group['ID']] = $group['ID'];
            }
        }

        if ($update == true && count($id_list) > 0) {
            CIBlockElement::SetElementSection($item['ID'], $id_list);
            Manager::updateElementIndex($item['IBLOCK_ID'], $item['ID']);

            $statistics[2]++;
        }
    }
}

$time_end = microtime(true);
$time = $time_end - $start_time;

if ($time < 0) {
    $time = round(($time_end - $start_time), 3);
} else {
    $time = round($time, 3);
}


$output = [];
$output[] = '---------- СТАТИСТИКА ----------';
$output[] = 'Товары перенесённые из каталога в раздел "Распродажа": ' . $statistics[0];
$output[] = 'Товары из каталога, которым назначен раздел "Распродажа": ' . $statistics[1];
$output[] = 'Товары, у которых убран раздел "Распродажа": ' . $statistics[2];

$output[] = 'Время выполнения: ' . $time . ' сек.';
$output[] = 'Пиковая нагрузка на память: ' . memory_get_peak_usage(true) . ' байт';

echo implode("\n", $output) . "\n\n\n";
