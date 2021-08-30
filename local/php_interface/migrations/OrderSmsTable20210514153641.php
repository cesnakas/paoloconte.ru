<?php

namespace Sprint\Migration;


use Bitrix\Main\Application;
use Citfact\Tools;

class OrderSmsTable20210514153641 extends Version
{

    protected $description = "Добавление таблицы для хранения смс";

    public function up()
    {

        try {
            \Citfact\Entity\Sms\OrderSmsTable::getEntity()->createDbTable();
            print_r(\Citfact\Entity\Sms\OrderSmsTable::getTableName() . ' created.');
        } catch (Exception $e) {
            print_r($e->getMessage());
        }

    }

    public function down()
    {
        if (Tools::isDev()) {
            $connection = Application::getConnection();
            $connection->dropTable(\Citfact\Entity\Sms\OrderSmsTable::getTableName());
        }
    }

}
