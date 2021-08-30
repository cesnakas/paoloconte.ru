<?php

namespace Sprint\Migration;

use \Bitrix\Main\Config\Option;

class last_export_time_ka_20200705154200 extends Version
{

    protected $description = "Установка опции для новой 1С";

    public function up()
    {
        $helper = $this->getHelperManager();

        Option::set('sale', 'ka_last_export_time_committed_/1c_exchange/1c_exc', 1593475200);
        Option::set('sale', 'last_export_time_committed_/1c_exchange/1c_exc', Option::get('sale', 'last_export_time_committed_/1c_exchange/1c_exchan'));
    }

    public function down()
    {
        $helper = $this->getHelperManager();
    }

}
