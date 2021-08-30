<?php

namespace Citfact;

/**
 * Class Lock
 * @package Citfact
 */
class Lock {
    const PRODUCT_AVAILABILITY = 'product_availability';
    const ACTIVATE_AFTER_EXCHANGE= 'activate_after_exchange';
    const ACTIVATE_OFFERS = 'activate_offers';
    const ACTIVATE_TRADE_OFFERS = 'active_trade_offers_store_amount_and_price';

    /**
     * @var string
     */
    private static $dir = '/local/var/lockfiles/';

    /**
     * @var null
     */
    protected $key = null;  //user given value
    /**
     * @var bool|null|resource
     */
    protected $file = null;  //resource to lock
    /**
     * @var bool
     */
    protected $own = false; //have we locked resource

    /**
     * Lock constructor.
     * @param $key
     */
    function __construct($key) {
        $this->key = $key;
        //create a new resource or get exisitng with same key
        $this->file = fopen($_SERVER['DOCUMENT_ROOT'] . static::$dir . "$key.lockfile", 'w+');
    }


    /**
     *
     */
    function __destruct() {
        if ($this->own == true) {
            $this->unlock();
        }
    }

    /**
     * @return bool
     */
    function lock() {
        if (!flock($this->file, LOCK_EX | LOCK_NB)) { //failed
            return false;
        }
        ftruncate($this->file, 0); // truncate file
        //write something to just help debugging
        fwrite($this->file, "Locked\n");
        fflush($this->file);

        $this->own = true;

        return true; // success
    }


    /**
     * @return bool
     */
    function unlock() {
        if ($this->own == true) {
            if (!flock($this->file, LOCK_UN)) { //failed
                return false;
            }
            ftruncate($this->file, 0); // truncate file
            //write something to just help debugging
            fwrite($this->file, "Unlocked\n");
            fflush($this->file);
            $this->own = false;
        }

        return true; // success
    }
}