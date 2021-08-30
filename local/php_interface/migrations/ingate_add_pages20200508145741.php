<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;
use Sotbit\Seometa\ConditionTable;
use Sotbit\Seometa\SeometaUrlTable;
use Bitrix\Main\Type;
use Ingate\Seo\RedirectTable;

class ingate_add_pages20200508145741 extends Version
{

    protected $description = "Задача ingate 254346 / Миграция страниц";

    private $tagIblockId = 0;

    private $errors = [];

    protected $arItems = [
        0 => [
            //Страница
            'NEW_URL' => '/catalog/sabo-zelenye/',
            // Title
            'TITLE' => 'Зеленые сабо: цены, купить в интернет-магазине Paolo Conte',
            // Description
            'DESCRIPTION' => 'Купить зеленые сабо в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            // Заголовок h1
            'H1' => 'Зеленые сабо',
            // Анкор тега
            'TAG_ANCHOR' => 'Зеленые',
            // Где отображать тег
            'TAG_DESTINATION' => '/catalog/sabo/',
            // Страница из фильтра
            'OLD_URL' => '/catalog/sabo/?set_filter=y&arrFilter_249_1818018827=Y',
            // Раздел для правила модуля sotbit
            'SECTIONS' => 'a:1:{i:0;s:2:"84";}',
            // Фильтр для правила модуля sotbit
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9;}}}}',
        ],
        1 => [
            'NEW_URL' => '/catalog/sabo-fioletovye/',
            'TITLE' => 'Фиолетовые сабо: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить фиолетовые сабо в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Фиолетовые сабо',
            'TAG_ANCHOR' => 'Фиолетовые',
            'TAG_DESTINATION' => '/catalog/sabo/',
            'OLD_URL' => '/catalog/sabo/?set_filter=y&arrFilter_249_4248638610=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"84";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:15;}}}}',
        ],
        2 => [
            'NEW_URL' => '/catalog/sabo-krasnye/',
            'TITLE' => 'Красные сабо: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить красные сабо в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Красные сабо',
            'TAG_ANCHOR' => 'Красные',
            'TAG_DESTINATION' => '/catalog/sabo/',
            'OLD_URL' => '/catalog/sabo/?set_filter=y&arrFilter_249_1239777880=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"84";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:8;}}}}',
        ],
        3 => [
            'NEW_URL' => '/catalog/sabo-oranzhevye/',
            'TITLE' => 'Оранжевые сабо: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить оранжевые сабо в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Оранжевые сабо',
            'TAG_ANCHOR' => 'Оранжевые',
            'TAG_DESTINATION' => '/catalog/sabo/',
            'OLD_URL' => '/catalog/sabo/?set_filter=y&arrFilter_249_1479901182=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"84";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14;}}}}',
        ],
        4 => [
            'NEW_URL' => '/catalog/sabo-zheltye/',
            'TITLE' => 'Желтые сабо: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить желтые сабо в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Желтые сабо',
            'TAG_ANCHOR' => 'Желтые',
            'TAG_DESTINATION' => '/catalog/sabo/',
            'OLD_URL' => '/catalog/sabo/?set_filter=y&arrFilter_249_1479901182=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"84";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:13;}}}}',
        ],
        5 => [
            'NEW_URL' => '/catalog/sabo-ekokozha/',
            'TITLE' => 'Сабо из экокожи: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить сабо из экокожи в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Сабо из экокожи',
            'TAG_ANCHOR' => 'Из экокожи',
            'TAG_DESTINATION' => '/catalog/sabo/',
            'OLD_URL' => '/catalog/sabo/?set_filter=y&arrFilter_485_1654395567=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"84";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9445;}}}}',
        ],
        6 => [
            'NEW_URL' => '/catalog/sabo-tekstil/',
            'TITLE' => 'Текстильные сабо: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить текстильные сабо в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Текстильные сабо',
            'TAG_ANCHOR' => 'Текстильные',
            'TAG_DESTINATION' => '/catalog/sabo/',
            'OLD_URL' => '/catalog/sabo/?set_filter=y&arrFilter_485_2095769591=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"84";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9451;}}}}',
        ],
        7 => [
            'NEW_URL' => '/catalog/sabo-na-kabluke/',
            'TITLE' => 'Сабо на каблуке: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить сабо на каблуке в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Сабо на каблуке',
            'TAG_ANCHOR' => 'На каблуке',
            'TAG_DESTINATION' => '/catalog/sabo/',
            'OLD_URL' => '/catalog/sabo/?set_filter=y&arrFilter_526_3823569359=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"84";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14714;}}}}',
        ],
        8 => [
            'NEW_URL' => '/catalog/sabo-bez-kabluka/',
            'TITLE' => 'Сабо без каблука: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить сабо без каблука в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Сабо без каблука',
            'TAG_ANCHOR' => 'Без каблука',
            'TAG_DESTINATION' => '/catalog/sabo/',
            'OLD_URL' => '/catalog/sabo/?set_filter=y&arrFilter_526_2497722713=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"84";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:526";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14715;}}}}',
        ],
        9 => [
            'NEW_URL' => '/catalog/sandalii-golubye/',
            'TITLE' => 'Голубые сандалии: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить голубые сандалии в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Голубые сандалии',
            'TAG_ANCHOR' => 'Голубые',
            'TAG_DESTINATION' => '/catalog/sandalii/',
            'OLD_URL' => '/catalog/sandalii/?set_filter=y&arrFilter_249_2710034573=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"81";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:18;}}}}',
        ],
        10 => [
            'NEW_URL' => '/catalog/sandalii-oranzhevye/',
            'TITLE' => 'Оранжевые сандалии: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить оранжевые сандалии в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Оранжевые сандалии',
            'TAG_ANCHOR' => 'Оранжевые',
            'TAG_DESTINATION' => '/catalog/sandalii/',
            'OLD_URL' => '/catalog/sandalii/?set_filter=y&arrFilter_249_1479901182=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"81";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:14;}}}}',
        ],
        11 => [
            'NEW_URL' => '/catalog/sandalii-zheltye/',
            'TITLE' => 'Желтые сандалии: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить желтые сандалии в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Желтые сандалии',
            'TAG_ANCHOR' => 'Желтые',
            'TAG_DESTINATION' => '/catalog/sandalii/',
            'OLD_URL' => '/catalog/sandalii/?set_filter=y&arrFilter_249_993175258=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"81";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:13;}}}}',
        ],
        12 => [
            'NEW_URL' => '/catalog/sandalii-lakirovannye/',
            'TITLE' => 'Лакированные сандалии: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить лакированные сандалии в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Лакированные сандалии',
            'TAG_ANCHOR' => 'Лакированные',
            'TAG_DESTINATION' => '/catalog/sandalii/',
            'OLD_URL' => '/catalog/sandalii/?set_filter=y&arrFilter_485_4081520073=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"81";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:10217;}}}}',
        ],
        13 => [
            'NEW_URL' => '/catalog/sandalii-zamshevye/',
            'TITLE' => 'Замшевые сандалии: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить замшевые сандалии в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Замшевые сандалии',
            'TAG_ANCHOR' => 'Замшевые',
            'TAG_DESTINATION' => '/catalog/sandalii/',
            'OLD_URL' => '/catalog/sandalii/?set_filter=y&arrFilter_485_1797938820=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"81";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9449;}}}}',
        ],
        14 => [
            'NEW_URL' => '/catalog/sandalii-ekokozha/',
            'TITLE' => 'Сандалии из экокожи: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить сандалии из экокожи в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Сандалии из экокожи',
            'TAG_ANCHOR' => 'Из экокожи',
            'TAG_DESTINATION' => '/catalog/sandalii/',
            'OLD_URL' => '/catalog/sandalii/?set_filter=y&arrFilter_485_1654395567=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"81";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9445;}}}}',
        ],
        15 => [
            'NEW_URL' => '/catalog/sandalii-tekstil/',
            'TITLE' => 'Текстильные сандалии: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить текстильные сандалии в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Текстильные сандалии',
            'TAG_ANCHOR' => 'Текстильные',
            'TAG_DESTINATION' => '/catalog/sandalii/',
            'OLD_URL' => '/catalog/sandalii/?set_filter=y&arrFilter_485_2095769591=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"81";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9451;}}}}',
        ],
        16 => [
            'NEW_URL' => '/catalog/tufli-fuksiya/',
            'TITLE' => 'Туфли цвета фуксия: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить туфли цвета фуксия в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Туфли цвета фуксия',
            'TAG_ANCHOR' => 'Фуксия',
            'TAG_DESTINATION' => '/catalog/tufli/',
            'OLD_URL' => '/catalog/tufli/?set_filter=y&arrFilter_249_1512752529=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"57";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:20;}}}}',
        ],
        17 => [
            'NEW_URL' => '/catalog/tufli-nubuk/',
            'TITLE' => 'Туфли из нубука: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить туфли из нубука в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Туфли из нубука',
            'TAG_ANCHOR' => 'Из нубука',
            'TAG_DESTINATION' => '/catalog/tufli/',
            'OLD_URL' => '/catalog/tufli/?set_filter=y&arrFilter_485_472739346=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"57";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9448;}}}}',
        ],
        18 => [
            'NEW_URL' => '/catalog/tufli-tekstil/',
            'TITLE' => 'Текстильные туфли: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить текстильные туфли в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Текстильные туфли',
            'TAG_ANCHOR' => 'Текстильные',
            'TAG_DESTINATION' => '/catalog/tufli/',
            'OLD_URL' => '/catalog/tufli/?set_filter=y&arrFilter_485_2095769591=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"57";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9451;}}}}',
        ],
        19 => [
            'NEW_URL' => '/catalog/tufli-letnie/',
            'TITLE' => 'Летние туфли: цены, купить в интернет-магазине Paolo Conte',
            'DESCRIPTION' => 'Купить летние туфли в интернет-магазине Paolo Conte по выгодной цене. Обширный каталог женской обуви в наличии.',
            'H1' => 'Летние туфли',
            'TAG_ANCHOR' => 'Летние',
            'TAG_DESTINATION' => '/catalog/tufli/',
            'OLD_URL' => '/catalog/tufli/?set_filter=y&arrFilter_381_1589153474=Y',
            'SECTIONS' => 'a:1:{i:0;s:2:"57";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2743;}}}}',
        ]
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
