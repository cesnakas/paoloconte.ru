<?php


namespace Fact;

define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/local/logs/exchange.log");

class UpdateStorage
{
    private static $exchangeArray = array();
    private static $enable = false;
    private static $updatedProds = array();

    public static function isUpdated($arProd)
    {

        $result = false;

        if (defined('NEW_EXCHANGE')&& NEW_EXCHANGE){
            self::$enable = true;
        }

        if (self::$enable) {
            $Prod = State::getInstance()->getByKey($arProd['id_parent_prod']);
            if (array_key_exists($arProd['CATALOG_GROUP_ID'], $Prod)) {
                $result = true;
            } else {
                $params = array(
                    'KEY'   => $arProd['id_parent_prod'],
                    'VALUE' => $arProd
                );
                State::getInstance()->update($params);
            }
        }

        return $result;
    }

    /**
     * Save info
     */

    public static function save($arMark)
    {
        self::$exchangeArray[$arMark['NAME']] = $arMark['VAL'];

        AddMessage2Log($arMark, 'exchange');

    }


    /**
     * STOP EXCHANGE
     */

    public static function stopExchange()
    {
        echo 'EXCHANGE STOPPED';
        self::save(
            array(
                'NAME' => 'end',
                'VAL'  => time()
            )
        );
        die;
    }

}
