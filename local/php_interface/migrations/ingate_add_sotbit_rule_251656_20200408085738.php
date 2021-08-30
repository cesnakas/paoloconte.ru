<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;
use Sotbit\Seometa\ConditionTable;
use Sotbit\Seometa\SeometaUrlTable;
use Bitrix\Main\Type;
use Ingate\Seo\RedirectTable;

class ingate_add_sotbit_rule_251656_20200408085738 extends Version
{

    protected $description = 'Задача ingate 251656 / Создание правил в модуль sotbit.seometa';

    private $tagIblockId = 0;

    private $errors = [];

    protected $arItems = [
        //кеды по цвету
        0 => [
            //Страница
            'NEW_URL' => '/catalog/kedy-golebye/',  
            // Title	
            'TITLE' => 'Голубые кеды: цены, купить в интернет-магазине Paolo Conte', 
            // Description	
            'DESCRIPTION' => 'Купить голубые кеды в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            // Заголовок h1 
            'H1' => 'Голубые кеды',
            // Анкор тега
            'TAG_ANCHOR' => 'Голубые',
            // Где отображать тег
            'TAG_DESTINATION' => '/catalog/kedy/',
            // Страница из фильтра
            'OLD_URL' => '/catalog/kedy/?set_filter=y&arrFilter_249_2710034573=Y',
            // Раздел для правила модуля sotbit
            'SECTIONS' => 'a:1:{i:0;s:2:"80";}',
            // Фильтр для правила модуля sotbit
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:18;}}}}',
        ],
        1 => [    
            'NEW_URL' => '/catalog/kedy-fioletovye/',
            'TITLE' => 'Фиолетовые кеды: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить фиолетовые кеды в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Фиолетовые кеды',
            'TAG_ANCHOR' => 'Фиолетовые',
            'TAG_DESTINATION' => '/catalog/kedy/',
            'OLD_URL' => '/catalog/kedy/?set_filter=y&arrFilter_249_4248638610=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"80";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:15;}}}}',
        ],
        2 => [
            'NEW_URL' => '/catalog/kedy-korichnevye/',
            'TITLE' => 'Коричневые кеды: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить коричневые кеды в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Коричневые кеды',
            'TAG_ANCHOR' => 'Коричневые',
            'TAG_DESTINATION' => '/catalog/kedy/',
            'OLD_URL' => '/catalog/kedy/?set_filter=y&arrFilter_249_3844537749=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"80";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2;}}}}',
        ],
        3 => [
            'NEW_URL' => '/catalog/kedy-oranzhevye/',
            'TITLE' => 'Оранжевые кеды: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить оранжевые кеды в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Оранжевые кеды',
            'TAG_ANCHOR' => 'Оранжевые',
            'TAG_DESTINATION' => '/catalog/kedy/',
            'OLD_URL' => '/catalog/kedy/?set_filter=y&arrFilter_249_1479901182=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"80";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14;}}}}',
        ],
        4 => [
            'NEW_URL' => '/catalog/kedy-raznotsvetnye/',
            'TITLE' => 'Разноцветные кеды: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить разноцветные кеды в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Разноцветные кеды',
            'TAG_ANCHOR' => 'Разноцветные',
            'TAG_DESTINATION' => '/catalog/kedy/',
            'OLD_URL' => '/catalog/kedy/?set_filter=y&arrFilter_249_4280463101=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"80";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:19;}}}}',
        ],
        //кеды материал 
        5 => [
            'NEW_URL' => '/catalog/kedy-nubuk/',
            'TITLE' => 'Кеды из нубука: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить кеды из нубука в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Кеды из нубука',
            'TAG_ANCHOR' => 'Из нубука',
            'TAG_DESTINATION' => '/catalog/kedy/',
            'OLD_URL' => '/catalog/kedy/?set_filter=y&arrFilter_485_472739346=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"80";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9448;}}}}',
        ],
        6 => [
            'NEW_URL' => '/catalog/kedy-zamsha/',
            'TITLE' => 'Замшевые кеды: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить замшевые кеды в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Замшевые кеды',
            'TAG_ANCHOR' => 'Замшевые',
            'TAG_DESTINATION' => '/catalog/kedy/',
            'OLD_URL' => '/catalog/kedy/?set_filter=y&arrFilter_485_1797938820=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"80";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9449;}}}}',
        ],
        7 => [
            'NEW_URL' => '/catalog/kedy-ekokozha/',
            'TITLE' => 'Кеды из экокожи: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить кеды из экокожи в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Кеды из экокожи',
            'TAG_ANCHOR' => 'Из экокожи',
            'TAG_DESTINATION' => '/catalog/kedy/',
            'OLD_URL' => '/catalog/kedy/?set_filter=y&arrFilter_485_1654395567=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"80";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9445;}}}}',
        ],
        8 => [    
            'NEW_URL' => '/catalog/kedy-tekstil/',
            'TITLE' => 'Текстильные кеды: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить текстильные кеды в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Текстильные кеды',
            'TAG_ANCHOR' => 'Текстильные',
            'TAG_DESTINATION' => '/catalog/kedy/',
            'OLD_URL' => '/catalog/kedy/?set_filter=y&arrFilter_485_2095769591=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"80";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9451;}}}}',
        ],
        // кеды по сезонам
        9 => [
            'NEW_URL' => '/catalog/kedy-demisezonnye/',
            'TITLE' => 'Демисезонные кеды: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить демисезонные кеды в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Демисезонные кеды',
            'TAG_ANCHOR' => 'Демисезонные',
            'TAG_DESTINATION' => '/catalog/kedy/',
            'OLD_URL' => '/catalog/kedy/?set_filter=y&arrFilter_381_1228565287=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"80";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2506;}}}}',
        ],
        10 => [
            'NEW_URL' => '/catalog/kedy-zimnie/',
            'TITLE' => 'Зимние кеды: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить зимние кеды в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Зимние кеды',
            'TAG_ANCHOR' => 'Зимние',
            'TAG_DESTINATION' => '/catalog/kedy/',
            'OLD_URL' => '/catalog/kedy/?set_filter=y&arrFilter_381_700431956=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"80";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}}}',
        ],
        11 => [
            'NEW_URL' => '/catalog/kedy-letnie/',
            'TITLE' => 'Летние кеды: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить летние кеды в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Летние кеды',
            'TAG_ANCHOR' => 'Летние',
            'TAG_DESTINATION' => '/catalog/kedy/',
            'OLD_URL' => '/catalog/kedy/?set_filter=y&arrFilter_381_1589153474=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"80";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2743;}}}}',
        ],
        // Кроссовки по цвету
        12 => [
            'NEW_URL' => '/catalog/krossovki-fioletovye/',
            'TITLE' => 'Фиолетовые кроссовки: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить фиолетовые кроссовки в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Фиолетовые кроссовки',
            'TAG_ANCHOR' => 'Фиолетовые',
            'TAG_DESTINATION' => '/catalog/krossovki/',
            'OLD_URL' => '/catalog/krossovki/?set_filter=y&arrFilter_249_4248638610=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"76";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:15;}}}}',
        ],
        13 => [
            'NEW_URL' => '/catalog/krossovki-korichnevye/',
            'TITLE' => 'Коричневые кроссовки: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить коричневые кроссовки в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Коричневые кроссовки',
            'TAG_ANCHOR' => 'Коричневые',
            'TAG_DESTINATION' => '/catalog/krossovki/',
            'OLD_URL' => '/catalog/krossovki/?set_filter=y&arrFilter_249_3844537749=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"76";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2;}}}}',
        ],
        // кроссовки по материалу
        14 => [
            'NEW_URL' => '/catalog/krossovki-ekokozha/',
            'TITLE' => 'Кроссовки из экокожи: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить кроссовки из экокожи в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Кроссовки из экокожи',
            'TAG_ANCHOR' => 'Из экокожи',
            'TAG_DESTINATION' => '/catalog/krossovki/',
            'OLD_URL' => '/catalog/krossovki/?set_filter=y&arrFilter_485_1654395567=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"76";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9445;}}}}',
        ],
        // Кроссовки по сезонности
        15 => [
            'NEW_URL' => '/catalog/krossovki-demisezonnye/',
            'TITLE' => 'Демисезонные кроссовки: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить демисезонные кроссовки в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Демисезонные кроссовки',
            'TAG_ANCHOR' => 'Демисезонные',
            'TAG_DESTINATION' => '/catalog/krossovki/',
            'OLD_URL' => '/catalog/krossovki/?set_filter=y&arrFilter_381_1228565287=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"76";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2506;}}}}',
        ],
        16 => [
            'NEW_URL' => '/catalog/krossovki-zimnie/',
            'TITLE' => 'Зимние кроссовки: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить зимние кроссовки в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Зимние кроссовки',
            'TAG_ANCHOR' => 'Зимние',
            'TAG_DESTINATION' => '/catalog/krossovki/',
            'OLD_URL' => '/catalog/krossovki/?set_filter=y&arrFilter_381_700431956=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"76";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}}}',
        ],
        17 => [
            'NEW_URL' => '/catalog/krossovki-letnie/',
            'TITLE' => 'Летние кроссовки: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить летние кроссовки в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Летние кроссовки',
            'TAG_ANCHOR' => 'Летние',
            'TAG_DESTINATION' => '/catalog/krossovki/',
            'OLD_URL' => '/catalog/krossovki/?set_filter=y&arrFilter_381_1589153474=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"76";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2743;}}}}',
        ],
        // полуботинки по цвету
        18 => [
            'NEW_URL' => '/catalog/polubotinki-zelenye/',
            'TITLE' => 'Зеленые полуботинки: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить зеленые полуботинки в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Зеленые полуботинки',
            'TAG_ANCHOR' => 'Зеленые',
            'TAG_DESTINATION' => '/catalog/polubotinki/',
            'OLD_URL' => '/catalog/polubotinki/?set_filter=y&arrFilter_249_1818018827=Y',
            'SECTIONS' => 'a:1:{i:0;s:3:"253";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9;}}}}',
        ],
        19 => [
            'NEW_URL' => '/catalog/polubotinki-golubye/',
            'TITLE' => 'Голубые полуботинки: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить голубые полуботинки в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Голубые полуботинки',
            'TAG_ANCHOR' => 'Голубые',
            'TAG_DESTINATION' => '/catalog/polubotinki/',
            'OLD_URL' => '/catalog/polubotinki/?set_filter=y&arrFilter_249_2710034573=Y',
            'SECTIONS' => 'a:1:{i:0;s:3:"253";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:18;}}}}',
        ],
        20 => [
            'NEW_URL' => '/catalog/polubotinki-oranzhevye/',
            'TITLE' => 'Оранжевые полуботинки: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить оранжевые полуботинки в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Оранжевые полуботинки',
            'TAG_ANCHOR' => 'Оранжевые',
            'TAG_DESTINATION' => '/catalog/polubotinki/',
            'OLD_URL' => '/catalog/polubotinki/?set_filter=y&arrFilter_249_1479901182=Y',
            'SECTIONS' => 'a:1:{i:0;s:3:"253";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14;}}}}',
        ],
        // полуботинки по материалу
        21 => [
            'NEW_URL' => '/catalog/polubotinki-lirovannye/',
            'TITLE' => 'Лакированные полуботинки: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить лакированные полуботинки в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Лакированные полуботинки',
            'TAG_ANCHOR' => 'Лакированные',
            'TAG_DESTINATION' => '/catalog/polubotinki/',
            'OLD_URL' => '/catalog/polubotinki/?set_filter=y&arrFilter_485_4081520073=Y',
            'SECTIONS' => 'a:1:{i:0;s:3:"253";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:10217;}}}}',
        ],
        22 => [
            'NEW_URL' => '/catalog/polubotinki-tekstil/',
            'TITLE' => 'Текстильные полуботинки: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить текстильные полуботинки в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Текстильные полуботинки',
            'TAG_ANCHOR' => 'Текстильные',
            'TAG_DESTINATION' => '/catalog/polubotinki/',
            'OLD_URL' => '/catalog/polubotinki/?set_filter=y&arrFilter_485_2095769591=Y',
            'SECTIONS' => 'a:1:{i:0;s:3:"253";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9451;}}}}',
        ],
        // полуботинки по сезону
        23 => [
            'NEW_URL' => '/catalog/polubotinki-letnie/',
            'TITLE' => 'Летние полуботинки: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить летние полуботинки в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Летние полуботинки',
            'TAG_ANCHOR' => 'Летние',
            'TAG_DESTINATION' => '/catalog/polubotinki/',
            'OLD_URL' => '/catalog/polubotinki/?set_filter=y&arrFilter_381_1589153474=Y',
            'SECTIONS' => 'a:1:{i:0;s:3:"253";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2743;}}}}',
        ],
    ];

    public function up()
    {
        $this->checkNeedle();

        $helper = $this->getHelperManager(); 
        $this->tagIblockId = $helper->Iblock()->getIblockId('tags', 'info');
        if ($this->tagIblockId == 0) {
            throw new \Exception('Не найден инфоблок тегов');
        }


        foreach ($this->arItems as $item){
            $this->addSotbitRule($item);
            $this->addTag($helper, $item['TAG_ANCHOR'], $item['TAG_DESTINATION'], $item['NEW_URL']);
        }

        if (!empty($this->errors)){
            throw new \Exception(implode(', ',$this->errors));
        }
    }

    public function down()
    {
        $this->checkNeedle();

        $helper = $this->getHelperManager(); 
        $this->tagIblockId = $helper->Iblock()->getIblockId('tags', 'info');
        if ($this->tagIblockId == 0) {
            throw new \Exception('Не найден инфоблок тегов');
        }

        foreach ($this->arItems as $item) {
            //drop sotbit rules 
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

            //don't drop tags
        }
    }

    public function checkNeedle(){
        if (!Loader::includeModule('sotbit.seometa')){
            throw new \Exception('sotbit module is not icluded');
        }
    } 

    private function addTag($helper, $ancor, $url, $target_url){
        try {
            $helper->Iblock()->addElement($this->tagIblockId, ['NAME' => $ancor], [
                'URL'=> [0 => $url],
                'TARGET_URL' => $target_url
            ]);
        } catch (\Exception $exception) {
            $this->errors[] = $exception->getMessage();
        }
    }

    private function addSotbitRule($item){
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
        }else{
            $this->errors[] = 'Ошибка добавления правила для ' . $item['NEW_URL'];
        }
    }

}
