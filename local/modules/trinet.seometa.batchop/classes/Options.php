<?php

namespace Trinet\Seometa\Batchop;


use Bitrix\Main\Config\Option;

/**
 * Class Options
 * @package Trinet\Seometa\Batchop
 *
 * @property int $iblockId
 *
 * @property int $colTitle
 * @property int $colDescription
 * @property int $colHeader
 * @property int $colCrumb
 * @property int $colChpuUrl
 * @property int $colRealUrl
 * @property int $colSectionUrl
 * @property int $colPropertyName
 * @property int $colPropertyValue
 *
 * @property array $propertyMap
 *
 * @property bool $addChpu
 *
 */
class Options
{
	const COLUMNS = [
        'colTitle'=>1,
        'colDescription'=>0,
        'colHeader'=>1,
        'colCrumb'=>0,
        'colChpuUrl'=>0,
        'colRealUrl'=>0,
        'colSectionUrl'=>0,
        'colPropertyName'=>0,
        'colPropertyValue'=>0,
    ];
	
    private static $instance = null;
	
	private $data = ['iblockId' => 1, 'addChpu' => false, 'propertyMap' => '' ];
	
    private $stringTypes = ['propertyMap',];
    private $boolTypes = ['addChpu',];

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Options();
        }

        return self::$instance;
    }

    private function __construct()
    {
        foreach ( self::COLUMNS as $name => $default ) {
	        $this->data[$name] = $default;
        }
	    foreach ( $this->data as $prop => $default) {
            $this->{$prop} = Option::get( Constants::MODULE_ID, $prop, $default);

        }
    }

    private function __clone()
    {
        // Конструктор копирования нам не нужен
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        trigger_error(
            'Undefined property via __get(): ' . $name,
            E_USER_NOTICE
        );

        return null;
    }

    private function __set($name, $value)
    {
        if (array_key_exists($name, $this->data)) {
            if ( $name === 'propertyMap' ) {
                // @TODO костыль, но лучшего способа пока не вижу
                $this->data[$name] = $this->transformPropertyMap($value);
            }elseif ( in_array($name, $this->stringTypes) ) {
                $this->data[$name] = (string)$value;
            } elseif ( in_array($name, $this->boolTypes) ) {
                $this->data[$name] = ($value === "Y");
            } else {
                $this->data[$name] = (int)$value;
            }

        }
    }

    public function withChpu()
    {
        return $this->addChpu;
    }

    public function getFilterType()
    {
        return Option::get("sotbit.seometa", "FILTER_TYPE", "bitrix_chpu");
    }

    public function isValid()
    {
        $ok = ( $this->iblockId >0 );
        if ( $ok ) {
            $requiredColumns = array_filter(self::COLUMNS);
            foreach ( array_keys($requiredColumns ) as $prop) {
                if ( ! $this->$prop ) {
                    $ok = false;
                    break;
                }
            }
        }
        if ( $ok ) {
            /*
             * допустимые варианты:
             * 1) указан столбец colRealUrl
             * 2) указаны столбцы "Url раздела", "Свойство", "Значение"
             */
            $ok = ( $this->colRealUrl > 0
                || ( $this->colSectionUrl >0 && $this->colPropertyName >0 && $this->colPropertyValue > 0 )
            );
        }

        if ( $ok ) {
            $ok = ( $this->addChpu && $this->colChpuUrl >0 ) || !$this->addChpu;
        }

        return $ok;
    }

    /**
     *
     * @return array
     */
    private function transformPropertyMap( $serialized )
    {
        $map = [];
        if ( strlen($serialized) ) {
            $tmp  = unserialize($serialized);
            foreach (  $tmp as $row ) {
                $parts = explode(':', $row, 2);
                $parts[0] = (int) $parts[0];
                $parts[1] = trim( $parts[1] );
                if ( $parts[0] > 0 && $parts[1] !== '' ) {
                    $map[ $parts[1] ] = $parts[0];
                }
            }
        }
        return $map;
    }
}