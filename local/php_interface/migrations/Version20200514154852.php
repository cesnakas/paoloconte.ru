<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;
use Ingate\Seo\RedirectTable;
use Sprint\Migration\Exceptions\MigrationException;

class Version20200514154852 extends Version
{

    protected $description = "добавление миграции с 301 редиректом для страницы";

    protected static $rules = [
        0 => [
            'OLD' => 'https://paoloconte.ru/catalog/rubashka/',
            'NEW' => 'https://paoloconte.ru/catalog/rubashki/'
        ]
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
