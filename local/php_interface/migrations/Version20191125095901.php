<?php

namespace Sprint\Migration;
use Bitrix\Main\Loader;

class Version20191125095901 extends Version
{

    protected $description = "Задача 64490/ Изменение название полей SDEK ";
    const STATUS_SHEEPLA = 'STATUS_SHEEPLA';
    const CTN_SHEEPLA = 'CTN_SHEEPLA';

    private function updateOrderPropsSHEEPLA($status, $ctn){
        Loader::includeModule('sale');

        $db_props = \CSaleOrderProps::GetList(
            array("SORT" => "ASC"),
            array(
                "CODE" => array(self::STATUS_SHEEPLA, self::CTN_SHEEPLA),
            ),
            false,
            false,
            array()
        );
        while($props = $db_props->Fetch()){
            switch ($props['CODE']){
                case self::STATUS_SHEEPLA:
                    \CSaleOrderProps::Update($props['ID'], array('NAME' => $status));
                    break;
                case self::CTN_SHEEPLA:
                    \CSaleOrderProps::Update($props['ID'], array('NAME' => $ctn));
                    break;
            }
        }
    }

    public function up()
    {
        $helper = $this->getHelperManager();
        $this->updateOrderPropsSHEEPLA('Статус доставки СДЕК', 'Номер отправления СДЕК');
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $this->updateOrderPropsSHEEPLA('Статус Sheepla', 'DispatchNumber (CTN) Sheepla');

    }

}
