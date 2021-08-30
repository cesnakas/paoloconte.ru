<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;
use Ingate\Seo\CanonicalTable;

Loader::includeModule("sotbit.seometa");

class Version20191002111030 extends Version
{

    protected $description = "Задача ingate 233668 / canonical ";

    protected $canonicals = [
        0 => [
            'URL' => '/novosti/novosti/',
            'CANONICAL' => '/events/'
        ],
        2 => [
            'URL' => '/novosti/news/',
            'CANONICAL' => '/events/'
        ],
        3 => [
            'URL' => '/novosti/',
            'CANONICAL' => '/events/'
        ],
    ];

    protected $domain = 'https://paoloconte.ru';

    public function up()
    {
        $helper = $this->getHelperManager();
        foreach($this->canonicals as $canonical){
            CanonicalTable::add([
                'URL' => $this->domain.$canonical['URL'],
                'CANONICAL' => $this->domain.$canonical['CANONICAL']
            ]);
        }
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        foreach($this->canonicals as $canonical){
            $res = CanonicalTable::getList([
                'select' => ['*'],
                'filter' => [
                    '=URL' => $this->domain.$canonical['URL'],
                    '=CANONICAL' => $this->domain.$canonical['CANONICAL']
                ]
            ]);
            if ($item = $res->fetch()){
                CanonicalTable::delete($item['ID']);
            }
        }
    }

}
