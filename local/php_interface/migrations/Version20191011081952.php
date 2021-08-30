<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;
use Sotbit\Seometa\ConditionTable;
use Sotbit\Seometa\SeometaUrlTable;
use Bitrix\Main\Type;
use Ingate\Seo\RedirectTable;

Loader::includeModule("sotbit.seometa");


class Version20191011081952 extends Version
{

    protected $description = "Задача ingate 234448 / Создание правил в модуль sotbit.seometa";

    protected $arItems = [
        0 => [
           'NEW_URL' => '/catalog/botinki_m_zima/',
           'OLD_URL' => '/catalog/botinki_m/?set_filter=y&arrFilter_381_700431956=Y',
           'TITLE' => 'Зимние ботинки в Москве: купить мужские ботинки на зиму, цены',
           'DESCRIPTION' => 'Зимние мужские ботинки в официальном интернет-магазине Paolo Conte в Москве. Купите мужские ботинки на зиму по выгодной цене! Бесплатная доставка по России с примеркой. Широкий размерный ряд.',
           'H1' => 'Зимние мужские ботинки',
           'SECTIONS' => 'a:1:{i:0;s:3:"109";}',
           'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}}}',
        ],
        1 => [
            'NEW_URL' => '/catalog/botinki_m_zima_chernie/',
            'OLD_URL' => '/catalog/botinki_m/?set_filter=y&arrFilter_249_2250319025=Y&arrFilter_381_700431956=Y',
            'TITLE' => 'Зимние черные мужские ботинки в Москве: купить черные ботинки на зиму, цены',
            'DESCRIPTION' => 'Купите мужские зимние ботинки черного цвета в официальном интернет-магазине Paolo Conte. Мужские черные ботинки на зиму по выгодной цене в Москве! Бесплатная доставка по России с примеркой.',
            'H1' => 'Зимние черные мужские ботинки',
            'SECTIONS' => 'a:1:{i:0;s:3:"109";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:1;}}}}',
        ],
        2 => [
            'NEW_URL' => '/catalog/botinki_m_zima_meh/',
            'OLD_URL' => '/catalog/botinki_m/?set_filter=y&arrFilter_486_566291088=Y&arrFilter_381_700431956=Y',
            'TITLE' => 'Зимние мужские ботинки с мехом в Москве: купить ботинки с мехом, цены',
            'DESCRIPTION' => 'Зимние мужские ботинки с натуральным мехом в Москве по выгодной цене. Бесплатная доставка по России с примеркой от интернет-магазина Paolo Conte.',
            'H1' => 'Зимние мужские ботинки с мехом',
            'SECTIONS' => 'a:1:{i:0;s:3:"109";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}i:3;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:486";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:10639;}}}}',
        ],
        3 => [
            'NEW_URL' => '/catalog/botinki-zima/',
            'OLD_URL' => '/catalog/botinki/?set_filter=y&arrFilter_381_700431956=Y',
            'TITLE' => 'Зимние ботинки в Москве: купить женские ботинки на зиму, цены',
            'DESCRIPTION' => 'Зимние женские ботинки в Москве от интернет-магазина Paolo Conte. Ботинки на зиму по выгодной цене! Бесплатная доставка по России с примеркой.',
            'H1' => 'Зимние женские ботинки',
            'SECTIONS' => 'a:1:{i:0;s:2:"79";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}}}',
        ],
        4 => [
            'NEW_URL' => '/catalog/obuv_1-zima-kozha/',
            'OLD_URL' => '/catalog/obuv_1/?set_filter=y&arrFilter_485_362488377=Y&arrFilter_381_700431956=Y',
            'TITLE' => 'Зимняя мужская кожаная обувь в Москве: купить обувь из натуральной кожи, цены',
            'DESCRIPTION' => 'Зимняя мужская кожаная обувь в официальном интернет-магазине Paolo Conte. Купите мужскую обувь из натуральной кожи на зиму по выгодной цене в Москве! Бесплатная доставка по России с примеркой. Широкий размерный ряд.',
            'H1' => 'Зимняя мужская кожаная обувь',
            'SECTIONS' => 'a:1:{i:0;s:2:"66";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}i:2;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9444;}}}}',
        ],
        5 => [
            'NEW_URL' => '/catalog/obuv_1-zima-meh/',
            'OLD_URL' => '/catalog/obuv_1/?set_filter=y&arrFilter_486_566291088=Y&arrFilter_381_700431956=Y',
            'TITLE' => 'Зимняя мужская обувь на меху в Москве: купить обувь на натуральном меху, цены',
            'DESCRIPTION' => 'Купите зимнюю мужскую обувь на меху в официальном интернет-магазине Paolo Conte! Мужская обувь на натуральном меху по выгодной цене в Москве! Бесплатная доставка по России с примеркой.',
            'H1' => 'Зимняя мужская обувь на меху',
            'SECTIONS' => 'a:1:{i:0;s:2:"66";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}i:2;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:486";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:10639;}}}}',
        ],
        6 => [
            'NEW_URL' => '/catalog/obuv-zima/',
            'OLD_URL' => '/catalog/obuv/?set_filter=y&arrFilter_381_700431956=Y',
            'TITLE' => 'Зимняя женская обувь в Москве: купить обувь на зиму, цены',
            'DESCRIPTION' => 'Купите зимнюю женскую обувь в официальном интернет-магазине Paolo Conte! Женская обувь на зиму по выгодной цене в Москве! Бесплатная доставка по России с примеркой.',
            'H1' => 'Зимняя женская обувь',
            'SECTIONS' => 'a:1:{i:0;s:2:"54";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}}}',
        ],
        7 => [
            'NEW_URL' => '/catalog/obuv-zima-kozha/',
            'OLD_URL' => '/catalog/obuv/?set_filter=y&arrFilter_485_362488377=Y&arrFilter_381_700431956=Y',
            'TITLE' => 'Зимняя женская обувь из кожи в Москве: купить кожаную обувь, цены',
            'DESCRIPTION' => 'Кожаная женская обувь в официальном интернет-магазине Paolo Conte. Купите женскую обувь из натуральной кожи на зиму по выгодной цене в Москве! Бесплатная доставка по России с примеркой.',
            'H1' => 'Зимняя женская обувь из кожи',
            'SECTIONS' => 'a:1:{i:0;s:2:"54";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9444;}}}}',
        ],
        8 => [
            'NEW_URL' => '/catalog/sapogi-zima/',
            'OLD_URL' => '/catalog/sapogi/?set_filter=y&arrFilter_381_700431956=Y',
            'TITLE' => 'Зимние женские сапоги в Москве: купить сапоги на зиму, цены',
            'DESCRIPTION' => 'Купите женские сапоги в официальном интернет-магазине Paolo Conte! Женские сапоги на зиму по выгодной цене в Москве! Бесплатная доставка по России с примеркой.',
            'H1' => 'Зимние женские сапоги',
            'SECTIONS' => 'a:1:{i:0;s:2:"55";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:1:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}}}',
        ],
        9 => [
            'NEW_URL' => '/catalog/sapogi-zima-chernye/',
            'OLD_URL' => '/catalog/sapogi/?set_filter=y&arrFilter_249_2250319025=Y&arrFilter_381_700431956=Y',
            'TITLE' => 'Зимние черные женские сапоги в Москве: купить черные сапоги на зиму, цены',
            'DESCRIPTION' => 'Зимние черные женские сапоги в официальном интернет-магазине Paolo Conte в Москве. Купите черные сапоги на зиму по выгодной цене! Бесплатная доставка с примеркой по России. Широкий размерный ряд.',
            'H1' => 'Зимние черные женские сапоги',
            'SECTIONS' => 'a:1:{i:0;s:2:"55";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}i:1;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:249";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:1;}}}}',
        ],
        10 => [
            'NEW_URL' => '/catalog/sapogi-zima-kozha/',
            'OLD_URL' => '/catalog/sapogi/?set_filter=y&arrFilter_485_362488377=Y&arrFilter_381_700431956=Y',
            'TITLE' => 'Зимние женские сапоги из кожи в Москве: купить кожаные сапоги, цены',
            'DESCRIPTION' => 'Зимние женские сапоги из натуральной кожи в официальном интернет-магазине Paolo Conte. Купите кожаные сапоги на зиму по выгодной цене в Москве! Бесплатная доставка по России с примеркой.',
            'H1' => 'Зимние женские сапоги из кожи',
            'SECTIONS' => 'a:1:{i:0;s:2:"55";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}i:2;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:485";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:9444;}}}}',
        ],
        11 => [
            'NEW_URL' => '/catalog/sapogi-zima-meh/',
            'OLD_URL' => '/catalog/sapogi/?set_filter=y&arrFilter_486_566291088=Y&arrFilter_381_700431956=Y',
            'TITLE' => 'Зимние женские сапоги на меху в Москве: купить сапоги с натуральным мехом, цены',
            'DESCRIPTION' => 'Зимние женские сапоги с натуральным мехом в официальном интернет-магазине Paolo Conte. Купите сапоги на зиму с мехом по выгодной цене в Москве! Бесплатная доставка по России с примеркой.',
            'H1' => 'Зимние женские сапоги на меху',
            'SECTIONS' => 'a:1:{i:0;s:2:"55";}',
            'RULE' => 'a:3:{s:8:"CLASS_ID";s:9:"CondGroup";s:4:"DATA";a:2:{s:3:"All";s:3:"AND";s:4:"True";s:4:"True";}s:8:"CHILDREN";a:2:{i:0;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:381";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:2742;}}i:2;a:2:{s:8:"CLASS_ID";s:17:"CondIBProp:10:486";s:4:"DATA";a:2:{s:5:"logic";s:5:"Equal";s:5:"value";i:10639;}}}}',
        ]
    ];

    public function up()
    {
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
    }

    public function down()
    {
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
