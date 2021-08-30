<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class citfact_aqsi extends CModule {

    /**
     * @var string код модуля
     */
    public $MODULE_ID = "citfact.aqsi";

    /**
     * @var string версия
     */
    public $MODULE_VERSION;

    /**
     * @var string дата версии
     */
    public $MODULE_VERSION_DATE;

    /**
     * @var string название модуля
     */
    public $MODULE_NAME;

    /**
     * @var string описание модуля
     */
    public $MODULE_DESCRIPTION;

    /**
     * @var string разработчик модуля
     */
    public $PARTNER_NAME;

    /**
     * @var string сайт разработчика
     */
    public $PARTNER_URI;

    /**
     * Construct object
     */
    public function __construct() {
        $this->MODULE_NAME = Loc::getMessage("CORE_MODULE_NAME_CITFACT_AQSI");
        $this->MODULE_DESCRIPTION = Loc::getMessage("CORE_MODULE_DESCRIPTION_CITFACT_AQSI");
        $this->PARTNER_NAME = Loc::getMessage("PARTNER_NAME_CITFACT");
        $this->PARTNER_URI = Loc::getMessage("PARTNER_NAME_CITFACT");
        $this->MODULE_PATH = $this->getModulePath();

        $arModuleVersion = [];
        include $this->MODULE_PATH . "/install/version.php";

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
    }

    /**
     * Return path module
     *
     * @return string
     */
    protected function getModulePath() {
        $modulePath = explode("/", __FILE__);
        return implode("/", array_slice($modulePath, 0, array_search($this->MODULE_ID, $modulePath) + 1));
    }

    /**
     * Install module
     *
     * @return void
     */
    public function doInstall() {
        RegisterModule($this->MODULE_ID);
    }

    /**
     * Remove module
     *
     * @return void
     */
    public function doUninstall() {
        UnRegisterModule($this->MODULE_ID);
    }

    /**
     * Add tables to the database
     *
     * @return bool
     */
    public function installDB() {
        return true;
    }

    /**
     * Remove tables from the database
     *
     * @return bool
     */
    public function unInstallDB() {
        return true;
    }

    /**
     * Add post events
     *
     * @return bool
     */
    public function installEvents() {
        return true;
    }

    /**
     * Delete post events
     *
     * @return bool
     */
    public function unInstallEvents() {
        return true;
    }

    /**
     * Copy files module
     *
     * @return bool
     */
    public function installFiles() {
        return true;
    }

    /**
     * Remove files module
     *
     * @return bool
     */
    public function unInstallFiles() {
        return true;
    }

}
