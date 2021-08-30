<?php

namespace Sprint\Migration;


class IngateFillTags20200130104537 extends Version
{

    protected $description = "Задача ingate 246230 / Первичное наполнение ИБ с тегами";

    protected $tags = [
        [
            'NAME' => 'Босоножки без каблука',
            'URL' => '/catalog/bosonozhki/',
            'TARGET_URL' => '/catalog/bosonozhki-bez-kabluka/'
        ],
        [
            'NAME' => 'Черные женские босоножки',
            'URL' => '/catalog/bosonozhki/',
            'TARGET_URL' => '/catalog/bosonozhki-chernye/',
        ],
        [
            'NAME' => 'Босоножки из натуральной кожи',
            'URL' => '/catalog/bosonozhki/',
            'TARGET_URL' => '/catalog/bosonozhki-kozha/',
        ],
        [
            'NAME' => 'Босоножки на каблуке',
            'URL' => '/catalog/bosonozhki/',
            'TARGET_URL' => '/catalog/bosonozhki-na-kabluke/',
        ],
        [
            'NAME' => 'Ботфорты цвета хаки',
            'URL' => '/catalog/botforty/',
            'TARGET_URL' => '/catalog/botforty-khaki/',
        ],
        [
            'NAME' => 'Бежевые ботильоны',
            'URL' => '/catalog/botilony/',
            'TARGET_URL' => '/catalog/botilony-bezhevye/',
        ],
        [
            'NAME' => 'Ботильоны из натуральной кожи',
            'URL' => '/catalog/botilony/',
            'TARGET_URL' => '/catalog/botilony-kozha/',
        ],
        [
            'NAME' => 'Ботильоны на каблуке',
            'URL' => '/catalog/botilony/',
            'TARGET_URL' => '/catalog/botilony-na-kabluke/',
        ],
        [
            'NAME' => 'Зимние ботильоны',
            'URL' => '/catalog/botilony/',
            'TARGET_URL' => '/catalog/botilony-zima/',
        ],
        [
            'NAME' => 'Кожаные ботинки',
            'URL' => '/catalog/botinki/',
            'TARGET_URL' => '/catalog/botinki-kozha/',
        ],
        [
            'NAME' => 'Зимние женские ботинки',
            'URL' => '/catalog/botinki/',
            'TARGET_URL' => '/catalog/botinki-zima/',
        ],
        [
            'NAME' => 'Зимние мужские ботинки',
            'URL' => '/catalog/botinki_m/',
            'TARGET_URL' => '/catalog/botinki_m_zima/',
        ],
        [
            'NAME' => 'Черные мужские ботинки',
            'URL' => '/catalog/botinki_m_zima/',
            'TARGET_URL' => '/catalog/botinki_m_zima_chernie/',
        ],
        [
            'NAME' => 'Мужские ботинки с мехом',
            'URL' => '/catalog/botinki_m_zima/',
            'TARGET_URL' => '/catalog/botinki_m_zima_meh/',
        ],
        [
            'NAME' => 'Зимняя обувь из кожи',
            'URL' => '/catalog/obuv-zima/',
            'TARGET_URL' => '/catalog/obuv-zima-kozha/',
        ],
        [
            'NAME' => 'Коричневые полуботинки',
            'URL' => '/catalog/polubotinki/',
            'TARGET_URL' => '/catalog/polubotinki-korichnevye/',
        ],
        [
            'NAME' => 'Полуботинки на каблуке',
            'URL' => '/catalog/polubotinki/',
            'TARGET_URL' => '/catalog/polubotinki-na-kabluke/',
        ],
        [
            'NAME' => 'Черные сапоги',
            'URL' => '/catalog/sapogi/',
            'TARGET_URL' => '/catalog/sapogi-chernye/',
        ],
        [
            'NAME' => 'Коричневые сапоги',
            'URL' => '/catalog/sapogi/',
            'TARGET_URL' => '/catalog/sapogi-korichnevye/',
        ],
        [
            'NAME' => 'Из натуральной кожи',
            'URL' => '/catalog/sapogi/',
            'TARGET_URL' => '/catalog/sapogi-kozha/',
        ],
        [
            'NAME' => 'Сапоги на каблуке',
            'URL' => '/catalog/sapogi/',
            'TARGET_URL' => '/catalog/sapogi-na-kabluke/',
        ],
        [
            'NAME' => 'Осенние сапоги',
            'URL' => '/catalog/sapogi/',
            'TARGET_URL' => '/catalog/sapogi-osen/',
        ],
        [
            'NAME' => 'Осенние на каблуке',
            'URL' => '/catalog/sapogi-osen/',
            'TARGET_URL' => '/catalog/sapogi-demisezonnye-na-kabluke/',
        ],
        [
            'NAME' => 'Серые сапоги',
            'URL' => '/catalog/sapogi/',
            'TARGET_URL' => '/catalog/sapogi-serye/',
        ],
        [
            'NAME' => 'Замшевые на каблуке',
            'URL' => '/catalog/sapogi/',
            'TARGET_URL' => '/catalog/sapogi-zamshevye-na-kabluke/',
        ],
        [
            'NAME' => 'Зимние сапоги',
            'URL' => '/catalog/sapogi/',
            'TARGET_URL' => '/catalog/sapogi-zima/',
        ],
        [
            'NAME' => 'Зимние черные сапоги',
            'URL' => '/catalog/sapogi-zima/',
            'TARGET_URL' => '/catalog/sapogi-zima-chernye/',
        ],
        [
            'NAME' => 'Зимние сапоги из кожи',
            'URL' => '/catalog/sapogi-zima/',
            'TARGET_URL' => '/catalog/sapogi-zima-kozha/',
        ],
        [
            'NAME' => 'Зимние сапоги на меху',
            'URL' => '/catalog/sapogi-zima/',
            'TARGET_URL' => '/catalog/sapogi-zima-meh/',
        ],
        [
            'NAME' => 'Бежевые туфли',
            'URL' => '/catalog/tufli/',
            'TARGET_URL' => '/catalog/tufli-bezhevye/',
        ],
        [
            'NAME' => 'Черные туфли',
            'URL' => '/catalog/tufli/',
            'TARGET_URL' => '/catalog/tufli-chernye/',
        ],
        [
            'NAME' => 'Туфли из натуральной кожи',
            'URL' => '/catalog/tufli/',
            'TARGET_URL' => '/catalog/tufli-kozha/',
        ],
        [
            'NAME' => 'Лаковые туфли',
            'URL' => '/catalog/tufli/',
            'TARGET_URL' => '/catalog/tufli-lakovye/',
        ],
        [
            'NAME' => 'На каблуке',
            'URL' => '/catalog/tufli/',
            'TARGET_URL' => '/catalog/tufli-na-kabluke/',
        ],
        [
            'NAME' => 'Кожаные на каблуке',
            'URL' => '/catalog/tufli-kozha/',
            'TARGET_URL' => '/catalog/tufli-kozha-na-kabluke/',
        ],
        [
            'NAME' => 'Лаковые бежевые туфли',
            'URL' => '/catalog/tufli-lakovye/',
            'TARGET_URL' => '/catalog/tufli-bezhevye-lakovye/',
        ],
        [
            'NAME' => 'Кожаные на каблуке',
            'URL' => '/catalog/tufli-na-kabluke/',
            'TARGET_URL' => '/catalog/tufli-kozha-na-kabluke/',
        ],
        [
            'NAME' => 'Черные на каблуке',
            'URL' => '/catalog/tufli-na-kabluke/',
            'TARGET_URL' => '/catalog/tufli-chernye-na-kabluke/',
        ],
        [
            'NAME' => 'Бежевые на каблуке',
            'URL' => '/catalog/tufli-na-kabluke/',
            'TARGET_URL' => '/catalog/tufli-bezhevye-na-kabluke/',
        ],
    ];

    public function up()
    {
        $helper = $this->getHelperManager();

        $iblock_id = $helper->Iblock()->getIblockId('tags', 'info');

        if ($iblock_id == 0) {
            throw new \Exception('Не найден инфоблок тегов');
        }

        $errors = [];
        foreach($this->tags as $tag){
            try {
                $fields = ['NAME' => $tag['NAME']];
                $props = [
                    'URL'=> [0 => $tag['URL']],
                    'TARGET_URL' => $tag['TARGET_URL']
                ];

                $helper->Iblock()->addElement($iblock_id, $fields, $props);
            } catch (\Exception $exception) {
                $errors[] = $exception->getMessage();
            }
        }

        if (!empty($errors)){
            throw new \Exception(implode(', ',$errors));
        }

    }

    public function down()
    {
        //your code ...
    }

}
