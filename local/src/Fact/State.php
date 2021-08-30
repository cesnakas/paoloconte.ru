<?php


namespace Fact;


class State
{
    private static $instance;  // экземпляр объекта
    private $state;


    private function __construct()
    {

        if (!empty($_SESSION['STATE_EXCHANGE'])) {
            $this->initFromArray($_SESSION['STATE_EXCHANGE']);
        } else {
            $this->time = time();
            $this->state = array();
        }
    }

    private function __clone()
    { /* ... @return Singleton */
    }  // Защищаем от создания через клонирование

    private function __wakeup()
    { /* ... @return Singleton */
    }  // Защищаем от создания через unserialize

    public static function getInstance()
    {    // Возвращает единственный экземпляр класса. @return Singleton
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get()
    {
        return $this->state;
    }

    /**
     * init
     */

    public function initFromArray($array)
    {
        $this->time = $_SESSION['STATE_EXCHANGE_TIME'];
        $this->state = $array;
    }

    public function save()
    {
        $_SESSION['STATE_EXCHANGE_TIME'] = $this->time;
        $_SESSION['STATE_EXCHANGE'] = $this->state;
    }

    /**
     * update state
     */

    public function update($param)
    {
        $this->state[$param['KEY']][$param['VALUE']['CATALOG_GROUP_ID']] = $param['VALUE'];
        $this->save();
    }

    /**
     * get by key
     */

    public function getByKey($key)
    {
        if (array_key_exists($key, $this->state)) {
            return $this->state[$key];
        }
        return array();
    }

    /**
     * dump
     */

    public function dump($die = false)
    {
        dump($this->state, $die);
    }

    /**
     * get microtime
     */

    public function getMicroTime()
    {
        $this->time;
    }

    /**
     * purge
     */

    public function purge()
    {
        unset ($_SESSION['STATE_EXCHANGE']);
        unset ($_SESSION['STATE_EXCHANGE_TIME']);
    }
}