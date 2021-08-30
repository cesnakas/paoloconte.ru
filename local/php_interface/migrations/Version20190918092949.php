<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;
use Sotbit\Seometa\ConditionTable;
use Sotbit\Seometa\SeometaUrlTable;
use Bitrix\Main\Type;
use Ingate\Seo\RedirectTable;

Loader::includeModule("sotbit.seometa");


class Version20190918092949 extends Version
{

    protected $description = "Задача ingate 230660 / Создание правил в модуль sotbit.seometa";

    protected $arItems = [
        0 => [
           'NEW_URL' => '/catalog/bosonozhki-bez-kabluka/',
           'OLD_URL' => '/catalog/bosonozhki/?set_filter=y&arrFilter_526_2497722713=Y',
           'TITLE' => 'Купить босоножки без каблука в Москве: цены в интернет-магазине Paolo Conte',
           'DESCRIPTION' => 'Босоножки без каблука в официальном интернет-магазине Paolo Conte в Москве. Купите босоножки без каблука по выгодной цене! Бесплатная доставка с примеркой по России.',
           'H1' => 'Босоножки без каблука',
           'SECTIONS' => 'a:1:{i:0;s:2:"77";}',
           'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:2;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14715;}}}}',
           /*
           "товары категории: Босоножки
           признак отбора товаров:
           наличие каблука - без каблука"
           */
        ],
        1 => [
            'NEW_URL' => '/catalog/bosonozhki-na-kabluke/',
            'OLD_URL' => '/catalog/bosonozhki/?set_filter=y&arrFilter_526_3823569359=Y',
            'TITLE' => 'Купить босоножки на каблуке в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Босоножки на каблуке в интернет-магазине Paolo Conte в Москве. Купите босоножки на каблуке или шпильке с бесплатной доставкой по России! Широкий размерный ряд.',
            'H1' => 'Босоножки на каблуке',
            'SECTIONS' => 'a:1:{i:0;s:2:"77";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14714;}}}}',
            /*"товары категории: Босоножки
            признак отбора товаров:
            наличие каблука - на каблуке"
            */
        ],
        // 2 => [
        //      /* Данный пункт пропускаем, т.к. в фильтре нет свойства "Тип носка" */
        //     'NEW_URL' => '/catalog/bosonozhki-s-zakrytym-nosom/',
        //     'OLD_URL' => '/catalog/bosonozhki/?arrFilter_525_2105777260=Y&set_filter=y',
        //     'TITLE' => 'Купить босоножки с закрытым носом в Москве: цены в интернет-магазине Paolo Conte',
        //     'DESCRIPTION' => 'Босоножки с закрытым носом в интернет-магазине Paolo Conte в Москве. Купите закрытые босоножки с бесплатной доставкой и примеркой по России! Широкий размерный ряд обуви.',
        //     'H1' => 'Босоножки с закрытым носом',
        //     'SECTIONS' => 'a:1:{i:0;s:2:"77";}',
        //     'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:525";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14713;}}}}',
        //     /*
        //     "товары категории: Босоножки
        //     признак отбора товаров:
        //     тип носка - закрытый"
        //     */
        // ],
        3 => [
            'NEW_URL' => '/catalog/botforty-khaki/',
            'OLD_URL' => '/catalog/botforty/?arrFilter_249_2654653878=Y&set_filter=y',
            'TITLE' => 'Купить ботфорты цвета хаки в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Ботфорты цвета хаки в официальном интернет-магазине Paolo Conte в Москве. Купите ботфорты цвета хаки с бесплатной доставкой по России! Широкий размерный ряд.',
            'H1' => 'Ботфорты цвета хаки',
            'SECTIONS' => 'a:1:{i:0;s:3:"108";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:16;}}}}',
            /*
            "товары категории: Ботфорты
            признак отбора товаров:
            цвет - хаки"
            */
        ],
        4 => [
            'NEW_URL' => '/catalog/botilony-krasnye-na-kabluke/',
            'OLD_URL' => '/catalog/botilony/?arrFilter_249_1239777880=Y&arrFilter_526_3823569359=Y&set_filter=y',
            'TITLE' => 'Купить бордовые и красные ботильоны на каблуке в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Бордовые и красные ботильоны на каблуке в интернет-магазине Paolo Conte в Москве. Купите бордовые ботильоны на каблуке или шпильке с бесплатной доставкой по России! Широкий размерный ряд.',
            'H1' => 'Бордовые и красные ботильоны на каблуке',
            'SECTIONS' => 'a:1:{i:0;s:2:"82";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:8;}}i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14714;}}}}',
            /*
            "товары категории: Ботильоны
            признак отбора товаров:
            наличие каблука - на каблуке
            цвет - красный"
            */
        ],
        5 => [
            'NEW_URL' => '/catalog/botilony-na-kabluke/',
            'OLD_URL' => '/catalog/botilony/?arrFilter_526_3823569359=Y&set_filter=y',
            'TITLE' => 'Купить ботильоны на каблуке в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Ботильоны на каблуке в официальном интернет-магазине Paolo Conte в Москве. Купите ботильоны на каблуке или шпильке по выгодной цене! Бесплатная доставка с примеркой по России.',
            'H1' => 'Ботильоны на каблуке',
            'SECTIONS' => 'a:1:{i:0;s:2:"82";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14714;}}}}',
            /*
            "товары категории: Ботильоны
            признак отбора товаров:
            наличие каблука - на каблуке"
            */
        ],
        6 => [
            'NEW_URL' => '/catalog/botilony-zima/',
            'OLD_URL' => '/catalog/botilony/?arrFilter_381_700431956=Y&set_filter=y',
            'TITLE' => 'Купить зимние ботильоны в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Зимние ботильоны в официальном интернет-магазине Paolo Conte в Москве. Купите ботильоны на зиму по выгодной цене! Бесплатная доставка с примеркой по России.',
            'H1' => 'Зимние ботильоны',
            'SECTIONS' => 'a:1:{i:0;s:2:"82";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}}}',
            /*
            "товары категории: Ботильоны
            признак отбора товаров:
            сезонность - зима"
            */
        ],
        7 => [
            'NEW_URL' => '/catalog/botinki-kozha/',
            'OLD_URL' => '/catalog/botinki/?arrFilter_485_362488377=Y&set_filter=y',
            'TITLE' => 'Купить кожаные ботинки в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Кожаные ботинки в официальном интернет-магазине Paolo Conte в Москве. Купите ботинки из натуральной кожи по выгодной цене! Бесплатная доставка с примеркой по России.',
            'H1' => 'Кожаные ботинки',
            'SECTIONS' => 'a:1:{i:0;s:2:"79";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9444;}}}}',
            /*
            "товары категории: Ботинки
            признак отбора товаров:
            материал - натуральная кожа"
            */
        ],
        8 => [
            'NEW_URL' => '/catalog/polubotinki-na-kabluke/',
            'OLD_URL' => '/catalog/polubotinki/?arrFilter_526_3823569359=Y&set_filter=y',
            'TITLE' => 'Купить полуботинки на каблуке в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Полуботинки на каблуке в официальном интернет-магазине Paolo Conte в Москве. Купите женские полуботинки на каблуке с бесплатной доставкой по России! Широкий размерный ряд.',
            'H1' => 'Полуботинки на каблуке',
            'SECTIONS' => 'a:1:{i:0;s:3:"253";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14714;}}}}',
            /*
            "товары категории: Полуботинки женские
            признак отбора товаров:
            наличие каблука - на каблуке"
            */
        ],
        9 => [
            'NEW_URL' => '/catalog/sapogi-demisezonnye-na-kabluke/',
            'OLD_URL' => '/catalog/sapogi/?arrFilter_381_1228565287=Y&arrFilter_526_3823569359=Y&set_filter=y',
            'TITLE' => 'Купить сапоги демисезонные на каблуке в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Демисезонные сапоги на каблуке в интернет-магазине Paolo Conte в Москве. Купите сапоги на каблуке с бесплатной доставкой и примеркой по России! Широкий размерный ряд.',
            'H1' => 'Сапоги демисезонные на каблуке',
            'SECTIONS' => 'a:1:{i:0;s:2:"55";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2506;}}i:2;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14714;}}}}',
            /*
            "товары категории: Сапоги
            признак отбора товаров:
            наличие каблука - на каблуке
            сезонность - весна-осень"
            */
        ],
        10 => [
            'NEW_URL' => '/catalog/sapogi-na-kabluke/',
            'OLD_URL' => '/catalog/sapogi/?arrFilter_526_3823569359=Y&set_filter=y',
            'TITLE' => 'Купить сапоги на каблуке в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Сапоги на каблуке в официальном интернет-магазине Paolo Conte в Москве. Купите сапоги на шпильке или каблуке по выгодной цене! Бесплатная доставка с примеркой по России.',
            'H1' => 'Сапоги на каблуке',
            'SECTIONS' => 'a:1:{i:0;s:2:"55";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14714;}}}}',
            /*
            "товары категории: Сапоги
            признак отбора товаров:
            наличие каблука - на каблуке"
            */
        ],
        11 => [
            'NEW_URL' => '/catalog/sapogi-osen/',
            'OLD_URL' => '/catalog/sapogi/?arrFilter_381_1228565287=Y&set_filter=y',
            'TITLE' => 'Купить осенние сапоги в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Осенние сапоги в официальном интернет-магазине Paolo Conte в Москве. Купите сапоги на осень по выгодной цене! Бесплатная доставка с примеркой по России.',
            'H1' => 'Осенние сапоги',
            'SECTIONS' => 'a:1:{i:0;s:2:"55";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2506;}}}}',
            /*
            "товары категории: Сапоги
            признак отбора товаров:
            сезонность - весна-осень"
            */
        ],
        12 => [
            'NEW_URL' => '/catalog/sapogi-zamshevye-na-kabluke/',
            'OLD_URL' => '/catalog/sapogi/?arrFilter_485_1797938820=Y&arrFilter_526_3823569359=Y&set_filter=y',
            'TITLE' => 'Купить замшевые сапоги на каблуке в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Замшевые сапоги на каблуке в официальном интернет-магазине Paolo Conte в Москве. Купите сапоги из замши по выгодной цене! Бесплатная доставка с примеркой по России.',
            'H1' => 'Замшевые сапоги на каблуке',
            'SECTIONS' => 'a:1:{i:0;s:2:"55";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9449;}}i:2;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14714;}}}}',
            /*
            "товары категории: Сапоги
            признак отбора товаров:
            наличие каблука - на каблуке
            материал - замша"
            */
        ],
        13 => [
            'NEW_URL' => '/catalog/sapogi-zima/',
            'OLD_URL' => '/catalog/sapogi/?arrFilter_381_700431956=Y&set_filter=y',
            'TITLE' => 'Купить зимние сапоги в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Зимние сапоги в официальном интернет-магазине Paolo Conte в Москве. Купите сапоги на зиму с бесплатной доставкой и примеркой по России! Широкий размерный ряд.',
            'H1' => 'Зимние сапоги',
            'SECTIONS' => 'a:1:{i:0;s:2:"55";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:2;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}}}',
            /*
            "товары категории: Сапоги
            признак отбора товаров:
            сезонность - зима"
            */
        ],
        14 => [
            'NEW_URL' => '/catalog/tufli-lakovye/',
            'OLD_URL' => '/catalog/tufli/?arrFilter_485_4081520073=Y&set_filter=y',
            'TITLE' => 'Купить лаковые туфли в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Лаковые туфли в официальном интернет-магазине Paolo Conte в Москве. Купите лаковые женские туфли с бесплатной доставкой и примеркой по России! Широкий размерный ряд.',
            'H1' => 'Лаковые туфли',
            'SECTIONS' => 'a:1:{i:0;s:2:"57";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:10217;}}}}',
            /*
            "товары категории: Туфли женские
            признак отбора товаров:
            материал - лаковая кожа"
            */
        ],
        15 => [
            'NEW_URL' => '/catalog/tufli-na-kabluke/',
            'OLD_URL' => '/catalog/tufli/?arrFilter_526_3823569359=Y&set_filter=y',
            'TITLE' => 'Купить туфли на каблуке в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Туфли на каблуке в официальном интернет-магазине Paolo Conte в Москве. Купите туфли на каблуке или шпильке с бесплатной доставкой и примеркой по России! Широкий размерный ряд.',
            'H1' => 'Туфли на каблуке',
            'SECTIONS' => 'a:1:{i:0;s:2:"57";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14714;}}}}',
            /*
            "товары категории: Туфли женские
            признак отбора товаров:
            наличие каблука - на каблуке"
            */
        ],
        16 => [
            'NEW_URL' => '/catalog/tufli-bezhevye-lakovye/',
            'OLD_URL' => '/catalog/tufli/?arrFilter_249_2106341805=Y&arrFilter_485_4081520073=Y&set_filter=y',
            'TITLE' => 'Купить лаковые бежевые туфли в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Лаковые бежевые туфли в интернет-магазине Paolo Conte в Москве. Купите лаковые туфли бежевого цвета с бесплатной доставкой и примеркой по России! Широкий размерный ряд обуви.',
            'H1' => 'Лаковые бежевые туфли',
            'SECTIONS' => 'a:1:{i:0;s:2:"57";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:2;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:5;}}i:3;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:10217;}}}}',
            /*
            "товары категории: Туфли женские
            признак отбора товаров:
            материал - лаковая кожа
            цвет - бежевые"
            */
        ],
        17 => [
            'NEW_URL' => '/catalog/tufli-bezhevye-na-kabluke/',
            'OLD_URL' => '/catalog/tufli/?arrFilter_249_2106341805=Y&arrFilter_526_3823569359=Y&set_filter=y',
            'TITLE' => 'Купить бежевые туфли на каблуке в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Бежевые туфли на каблуке в интернет-магазине Paolo Conte в Москве. Купите бежевые туфли на шпильке или каблуке по выгодной цене! Бесплатная доставка с примеркой по России.',
            'H1' => 'Бежевые туфли на каблуке',
            'SECTIONS' => 'a:1:{i:0;s:2:"57";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:5;}}i:2;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14714;}}}}',
            /*
            "товары категории: Туфли женские
            признак отбора товаров:
            наличие каблука - на каблуке
            цвет - бежевый"
            */
        ],
        18 => [
            'NEW_URL' => '/catalog/tufli-chernye-na-kabluke/',
            'OLD_URL' => '/catalog/tufli/?arrFilter_249_2250319025=Y&arrFilter_526_3823569359=Y&set_filter=y',
            'TITLE' => 'Купить черные туфли на каблуке в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Черные туфли на каблуке в официальном интернет-магазине Paolo Conte в Москве. Купите черные туфли на шпильке или каблуке по выгодной цене! Бесплатная доставка с примеркой по России.',
            'H1' => 'Черные туфли на каблуке',
            'SECTIONS' => 'a:1:{i:0;s:2:"57";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:1;}}i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14714;}}}}',
            /*
            "товары категории: Туфли женские
            признак отбора товаров:
            наличие каблука - на каблуке
            цвет - черный"
            */
        ],
        19 => [
            'NEW_URL' => '/catalog/tufli-kozha-na-kabluke/',
            'OLD_URL' => '/catalog/tufli/?arrFilter_485_362488377=Y&arrFilter_526_3823569359=Y&set_filter=y',
            'TITLE' => 'Купить кожаные туфли на каблуке в Москве: цены в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Кожаные туфли на каблуке в интернет-магазине Paolo Conte в Москве. Купите туфли из натуральной кожи на каблуке с бесплатной доставкой и примеркой по России! Широкий размерный ряд обуви.',
            'H1' => 'Кожаные туфли на каблуке',
            'SECTIONS' => 'a:1:{i:0;s:2:"57";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:2:"OR";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14714;}}i:2;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9444;}}}}',
            /*
            "товары категории: Туфли женские
            признак отбора товаров:
            наличие каблука - на каблуке
            материал - натуральная кожа"
            */
        ],
        // 20 => [
        //     'NEW_URL' => '/catalog/tufli-m/',
        //     'OLD_URL' => '',
        //     'TITLE' => 'Купить мужские туфли в Москве: цены в интернет-магазине Paolo Conte',
        //     'DESCRIPTION' => 'Мужские туфли в официальном интернет-магазине Paolo Conte в Москве. Купите мужские туфли по выгодной цене! Бесплатная доставка с примеркой по России.',
        //     'H1' => 'Мужские туфли',
        //     'SECTIONS' => '',
        //     'RULE' => '',
        //     /*
        //     https://paoloconte.ru
        //     товары категории: Туфли мужские
        //     */
        // ],
    ];

    public function up()
    {
        $helper = $this->getHelperManager();

        foreach ($this->arItems as $item){
            $arFields = [
                'NAME' => $item['H1'],
                'ACTIVE' => 'Y',
                'SEARCH' => 'Y',
                'SORT' => 100,
                'DATE_CHANGE' => new Type\DateTime( date( 'Y-m-d H:i:s' ), 'Y-m-d H:i:s' ),
                'SITES' => serialize(Array('s1')),
                'TYPE_OF_CONDITION' => '',
                'FILTER_TYPE' => 'bitrix_not_chpu',
                'TYPE_OF_INFOBLOCK' => 'catalog',
                'INFOBLOCK' => '10',
                'SECTIONS' => $item['SECTIONS'],
                'RULE' => $item['RULE'],
                'META' => serialize(Array(
                    'ELEMENT_TITLE' => $item['TITLE'],
                    'ELEMENT_KEYWORDS' => '',
                    "ELEMENT_DESCRIPTION" => $item['DESCRIPTION'],
                    "ELEMENT_PAGE_TITLE" => $item['H1'],
                    "ELEMENT_BREADCRUMB_TITLE" => '',
                    'TEMPLATE_NEW_URL' => '',
                    "ELEMENT_BOTTOM_DESC" => '',
                    "ELEMENT_ADD_DESC" => '',
                    "ELEMENT_TOP_DESC_TYPE" => '',
                    "ELEMENT_BOTTOM_DESC_TYPE" => '',
                    "ELEMENT_ADD_DESC_TYPE" => '',
                )),
                'NO_INDEX' => 'N',
                'STRONG' => 'Y',
                'PRIORITY' => '0.5',
                'CHANGEFREQ' => 'monthly',
                'CATEGORY_ID' => '0',
                'TAG' => '',
                'CONDITION_TAG' => '',
                'STRICT_RELINKING' => 'N',
            ];

            $result = ConditionTable::add($arFields);

            if ($result->isSuccess()){
                $ID = $result->getId();
                $arUrlFields = [
                    'ACTIVE' => 'Y',
                    "NAME" => $item['H1'],
                    "REAL_URL" => $item['OLD_URL'],
                    "NEW_URL" => $item['NEW_URL'],
                    "CONDITION_ID" => $ID,
                    "DATE_CHANGE" => new Type\DateTime( date( 'Y-m-d H:i:s' ), 'Y-m-d H:i:s' ),
                ];
                $res_url = SeometaUrlTable::add($arUrlFields);

                $res_redirect = RedirectTable::add([
                    'ACTIVE' => 'Y',
                    'OLD' => $item['OLD_URL'],
                    'NEW' => $item['NEW_URL']
                ]);

                unset($res_url, $res_redirect);
            }

            unset($result);
        }

        //your code ...
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        foreach ($this->arItems as $item) {
            $res = ConditionTable::getList(array(
                'select' => array('*'),
                'filter' => array('=NAME' => $item['H1'], '=RULE' => $item['RULE']),
                'order'  => array('ID'),
                'limit'  => 1
            ));
            if($target = $res->fetch()){
                SeometaUrlTable::deleteByConditionId($target['ID']);
                ConditionTable::delete($target['ID']);
            }

            $res_redirect = RedirectTable::getList([
                'select' => array('*'),
                'filter' => array('=NEW' => $item['NEW_URL'], '=OLD' => $item['OLD_URL']),
                'order'  => array('ID'),
                'limit'  => 1
            ]);
            if ($redirect_target = $res_redirect->fetch()){
                RedirectTable::delete($redirect_target['ID']);
            }
        }
    }

}
