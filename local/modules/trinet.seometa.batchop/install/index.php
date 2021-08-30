<?php
/**
 * Created by PhpStorm.
 * User: Maus <mausglov@yandex.ru>
 * Date: 10.03.16
 * 
 * Класс для инсталляции модуля.
 * печаль и тоска: class_alias() в данном случае не помогает, а использовать include.php
 */
require_once dirname(__DIR__).'/classes/Constants.php';

use \Trinet\Seometa\Batchop\Constants;

class trinet_seometa_batchop extends CModule {
    public $MODULE_ID = "";
    public $MODULE_CODE = "";

    private $installDir = '';

    function __construct()
    {
        $this->MODULE_ID = Constants::MODULE_ID;
        $this->MODULE_CODE = Constants::MODULE_CODE;
        $this->PARTNER_NAME = Constants::PARTNER_NAME;
        $this->MODULE_NAME = Constants::MODULE_NAME;

        /* @var $arModuleVersion array */
        $this->MODULE_VERSION = Constants::MODULE_VERSION;
        $this->MODULE_VERSION_DATE = Constants::MODULE_VERSION_DATE;
        $this->installDir = __DIR__;
    }

    function DoInstall()
    {
        $this->InstallFiles();
        \Bitrix\Main\ModuleManager::registerModule( $this->MODULE_ID );
        return true;
    }

    function DoUninstall()
    {
        /* @var $APPLICATION \CMain */
        $APPLICATION = $GLOBALS['APPLICATION'];
        $step = (int)$_REQUEST['step'];

        if ($step < 2)
        {
            $APPLICATION->IncludeAdminFile("Удаление модуля", $this->installDir. "/unstep1.php");
        }
        elseif ($step == 2)
        {
            $savedata = ( isset($_REQUEST["savedata"]) && $_REQUEST["savedata"] == 'Y' );
            if ( !$savedata ) {
                COption::RemoveOption( $this->MODULE_ID );
            }

            $this->UnInstallFiles();
            UnRegisterModule( $this->MODULE_ID );

            $APPLICATION->IncludeAdminFile("Удаление модуля", $this->installDir. "/unstep2.php");
        }

        return true;
    }

    function InstallFiles()
    {
        $this->InstallAdminFiles();
    }

    private function InstallAdminFiles()
    {
        $files = $this->getAdminFiles();
	    
	    $moduleDir = getLocalPath('modules/'.$this->MODULE_ID);
	    
        foreach ( $files as $item ) {
            file_put_contents($_SERVER['DOCUMENT_ROOT']."/bitrix/admin/{$this->MODULE_ID}_{$item}",
                '<'.'? require($_SERVER["DOCUMENT_ROOT"]. "'.$moduleDir.'/admin/'.$item.'");');
        }
    }

    private function getAdminFiles()
    {
        $path = dirname( $this->installDir ).'/admin';

        if ( is_dir( $path )  )
        {
            $files = scandir( $path );
            if ( !is_array( $files) )
            {
                $files = array();
            }

            foreach ( $files as $k => $item ) {
                if ($item == '..' || $item == '.' || $item == 'menu.php') {
                    unset( $files[$k]);
                }
            }
        } else {
            $files = array();
        }

        return $files;
    }


    private function UnInstallAdminFiles()
    {
        $files = $this->getAdminFiles();

        foreach ( $files as $item ) {
            $fullpath = $_SERVER['DOCUMENT_ROOT']."/bitrix/admin/{$this->MODULE_ID}_{$item}";
            if ( is_file( $fullpath ) ) {
                unlink( $fullpath );
            }
        }
    }

    function UnInstallFiles()
    {
        $this->UnInstallAdminFiles();
    }

    function loadProlog()
    {
        require_once dirname( $this->installDir ).'/prolog.php';
    }
}

