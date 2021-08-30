<?php

namespace Sprint\Migration;

use CIBlockElement;
use Sprint\Migration\Exceptions\HelperException;
use Bitrix\Main\Loader;
use Sotbit\Seometa\ConditionTable;
use Sotbit\Seometa\SeometaUrlTable;
use Bitrix\Main\Type;
use Ingate\Seo\RedirectTable;

class Delete_pages20200518162818 extends Version
{

    protected $description = "Миграция для удаления страниц";
    protected $pages = [
        '/catalog/botforty-nubuk/',
        '/catalog/botforty-velur/',
        '/catalog/botilony-zheltye/',
        '/catalog/baletki-krasnye/',
        '/catalog/kedy-nubuk/',
        '/catalog/kedy-oranzhevye/',
        '/catalog/kedy-raznotsvetnye/',
        '/catalog/kedy-zelenye/',
        '/catalog/krossovki-demisezonnye/',
        '/catalog/krossovki-nubuk/',
        '/catalog/lofery_1-sinie/',
        '/catalog/polubotinki-na-kabluke/',
        '/catalog/botinki-belye/',
        '/catalog/botinki-bezhevye/',
        '/catalog/botinki-chernye/',
        '/catalog/botinki-ekokozha/',
        '/catalog/botinki-fioletovye/',
        '/catalog/botinki-haki/',
        '/catalog/botinki-korichnevye/',
        '/catalog/botinki-krasnye/',
        '/catalog/botinki-lakirovannye/',
        '/catalog/botinki-nubuk/',
        '/catalog/botinki-rozovye/',
        '/catalog/botinki-serye/',
        '/catalog/botinki-sinie/',
        '/catalog/botinki-tekstil/',
        '/catalog/botinki-zamsha/',
        '/catalog/botinki-zelenye/',
        '/catalog/botinki-zheltye/',
        '/catalog/baletki-zelenye/',
        '/catalog/baletki-iz-ekokozhi/',
        '/catalog/mokasiny-nubuk/',
        '/catalog/mokasiny-zamsha/',
        '/catalog/sandalii-oranzhevye/',
        '/catalog/sandalii-sinie/',
        '/catalog/tufli-raznocvetnye/',
        '/catalog/tufli-velyur/',
    ];
    public function up()
    {
        $this->checkNeedle();

        $helper = $this->getHelperManager();
        $this->tagIblockId = $helper->Iblock()->getIblockId('tags', 'info');

        if ($this->tagIblockId == 0) {
            throw new \Exception('Не найден инфоблок тегов');
        }

        foreach ($this->pages as $page){
            $this->delete($page);
        }

        if (!empty($this->errors)){
            throw new \Exception(implode(', ',$this->errors));
        }
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        //please ...)
    }

    protected function delete($url){
        $res = SeometaUrlTable::getList(array(
            'select' => array('*'),
            'filter' => array('=NEW_URL' => $url),
            'order'  => array('ID'),
            'limit'  => 1
        ));
        if ($page = $res->fetch()){
            SeometaUrlTable::deleteByConditionId($page['CONDITION_ID']);
//            ConditionTable::delete($page['CONDITION_ID']);
        }

        $res_redirect = RedirectTable::getList([
            'select' => array('*'),
            'filter' => array('=NEW' => $url),
            'order'  => array('ID'),
            'limit'  => 1
        ]);
        if ($redirect_target = $res_redirect->fetch()){
            RedirectTable::delete($redirect_target['ID']);
        }
    }

    public function checkNeedle(){
        if (!Loader::includeModule('sotbit.seometa')){
            throw new \Exception('sotbit module is not icluded');
        }
    }
}
