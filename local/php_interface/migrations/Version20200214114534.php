<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;
use Ingate\Seo\RedirectTable;
use Sprint\Migration\Exceptions\MigrationException;

class Version20200214114534 extends Version
{

    protected $description = "Задача ingate 247481 / добавление миграции с 301 редиректами для модуля ingate.seo";

    protected static $rules = [
        0 => [
            'OLD' => 'https://paoloconte.ru/catalog/botforty-3/',
            'NEW' => 'https://paoloconte.ru/catalog/botforty-sale/'
        ],
        2 => [
            'OLD' => 'https://paoloconte.ru/catalog/botilony-3/',
            'NEW' => 'https://paoloconte.ru/catalog/botilony-sale/'
        ],
        3 => [
            'OLD' => 'https://paoloconte.ru/catalog/botinki-3/',
            'NEW' => 'https://paoloconte.ru/catalog/botinki-sale/'
        ],
        4 => [
            'OLD' => 'https://paoloconte.ru/catalog/botinki-4/',
            'NEW' => 'https://paoloconte.ru/catalog/botinki-2/'
        ],
        5 => [
            'OLD' => 'https://paoloconte.ru/catalog/sapogi-3/',
            'NEW' => 'https://paoloconte.ru/catalog/sapogi-sale/'
        ],
        6 => [
            'OLD' => 'https://paoloconte.ru/catalog/ukhod-za-obuvyu_2/',
            'NEW' => 'https://paoloconte.ru/catalog/ukhod-za-obuvyu1/'
        ],
    ];

    public function up()
    {
        if (!Loader::includeModule('ingate.seo')){
            Throw new MigrationException('ошибка подключения модуля ingate.seo');
        }

        foreach (self::$rules as $rule){
            $result = RedirectTable::add([
                'NEW' => $rule['NEW'],
                'OLD' => $rule['OLD'],
                'ACTIVE' => 'Y',
                'STATUS' => '301'
            ]);
            if (!$result->isSuccess())
            {
                $errors[] = 'Правило для '.$rule['OLD'].' не создано';
            }
        }

        if (!empty($errors)){
            Throw new MigrationException(implode(', ',$errors));
        }

    }

    public function down()
    {
        if (!Loader::includeModule('ingate.seo')){
            Throw new MigrationException('ошибка подключения модуля ingate.seo');
        }

        foreach(self::$rules as $rule){

            $item = RedirectTable::getList([
                'filter' => [
                    '=NEW' => $rule['NEW'],
                    '=OLD' => $rule['OLD'],
                ],
                'select' => ['ID']
            ]);

            if ($elId = $item->fetch()['ID']){
                RedirectTable::delete($elId);
            }
        }
    }

}
