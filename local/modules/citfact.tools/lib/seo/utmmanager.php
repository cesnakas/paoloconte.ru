<?php

namespace Citfact\Seo;

class UtmManager
{
    protected static $_instance;

    public static $mapUtmWithPropOrder = [
        'utm_term' => 'utm_term',
        'utm_content' => 'utm_content',
        'utm_campaign' => 'utm_campaign',
        'utm_medium' => 'utm_medium',
        'utm_source' => 'utm_source',
    ];

    protected static $utm = [
        'utm_source' => '',
        'utm_medium' => '',
        'utm_campaign' => '',
        'utm_content' => '',
        'utm_term' => ''
    ];

    private function __construct()
    {
    }

    protected function setUtmFromRequest()
    {
        if ($this->existUtm('utm_source')) {
            $this->setUtm('utm_source');
            $this->setUtm('utm_medium');
            $this->setUtm('utm_campaign');
            $this->setUtm('utm_content');
            $this->setUtm('utm_term');

            $_SESSION['UtmManager'] = static::$utm;
        }
    }

    public function getUtm()
    {
        if (isset($_SESSION['UtmManager'])) {
            return $_SESSION['UtmManager'];
        }
        return false;
    }

    public function getUtmByName($name)
    {
        $utm = $this->getUtm();
        if ($utm) {
            return $utm[$name];
        }
        return false;
    }

    protected function setUtm($utmName)
    {
        if (isset($_REQUEST[$utmName])) {
            static::$utm[$utmName] = $_REQUEST[$utmName];
        }
    }

    protected function existUtm($utmName)
    {
        if (isset($_REQUEST[$utmName]) && !empty($_REQUEST[$utmName])) {
            return true;
        }
        return false;
    }

    public function registerEvents()
    {
        AddEventHandler("main", "OnProlog", ['\Citfact\Seo\UtmManager', "OnProlog"]);
        AddEventHandler("main", "OnAfterUserAdd", ['\Citfact\Seo\UtmManager', "OnAfterUserAdd"]);
        AddEventHandler("sale", "OnSaleOrderBeforeSaved", ['\Citfact\Seo\UtmManager', "OnSaleOrderBeforeSaved"]);
//        AddEventHandler("sale", "OnBeforeOrderAdd", ['\Citfact\Seo\UtmManager', "OnBeforeOrderAdd"]);
    }

    public static function OnBeforeOrderAdd(&$arFields)
    {
        self::log('OnBeforeOrderAdd');
        $utmManager = UtmManager::getInstance();
        if ($utmManager->getUtmByName('utm_source')) {
            foreach (static::$mapUtmWithPropOrder as $nameUtm => $codeProp) {
                $idProp = \Citfact\Tools::getIdPropertyOrder($codeProp);
                $arFields['ORDER_PROP'][$idProp] = $utmManager->getUtmByName($nameUtm);
                self::log($codeProp);
            }
        }
        self::log($arFields['ORDER_PROP']);
    }

    public static function OnSaleOrderBeforeSaved($object)
    {
        if ($object instanceof \Bitrix\Main\Event) {
            $parameters = $object->getParameters();
            $order = $parameters['ENTITY'];
        } else if ($object instanceof \Bitrix\Sale\Order) {
            $order = $object;
        }
        if ($order instanceof \Bitrix\Sale\Order) {
            $utmManager = UtmManager::getInstance();
            $propertyCollection = $order->getPropertyCollection();

            $idPropUtmSource = \Citfact\Tools::getIdPropertyOrder('utm_source');
            $propertyUtmSource = $propertyCollection->getItemByOrderPropertyId($idPropUtmSource);
            if ($utmManager->getUtmByName('utm_source') && !$propertyUtmSource->getValue()) {
                foreach (static::$mapUtmWithPropOrder as $nameUtm => $codeProp) {
                    $idProp = \Citfact\Tools::getIdPropertyOrder($codeProp);
                    $property = $propertyCollection->getItemByOrderPropertyId($idProp);
                    $property->setValue($utmManager->getUtmByName($nameUtm));
                }
            }
        }
    }

    public static function OnAfterUserAdd(&$arFields)
    {
        if (isset($arFields['ID'])) {
            $utmManager = UtmManager::getInstance();
            if ($utmManager->getUtmByName('utm_source')) {
                $user = new \CUser;
                $fields = Array(
                    "UF_UTM_SOURCE" => $utmManager->getUtmByName('utm_source'),
                    "UF_UTM_MEDIUM" => $utmManager->getUtmByName('utm_medium'),
                    "UF_UTM_CAMPAIGN" => $utmManager->getUtmByName('utm_campaign'),
                    "UF_UTM_CONTENT" => $utmManager->getUtmByName('utm_content'),
                    "UF_UTM_TERM" => $utmManager->getUtmByName('utm_term')
                );
                $user->Update($arFields['ID'], $fields);
            }
        }
    }

    public static function OnProlog()
    {
        UtmManager::getInstance()->setUtmFromRequest();
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    protected static function log($var)
    {
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/local/var/logs/UtmManager.log",
            print_r($var, true) . PHP_EOL, FILE_APPEND);
    }
}