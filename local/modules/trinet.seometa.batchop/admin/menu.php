<?php
namespace Trinet\Seometa\Batchop;

use Bitrix\Main\Loader;

if (!Loader::includeModule( basename( dirname(__DIR__) ) ) ) {
    return false;
}

$module = new Installer();

return array(
    "parent_menu" => "global_menu_marketing",
    "section" => $module->MODULE_CODE,
    "sort" => 2000,
    "text" => $module->MODULE_NAME,
    "title" => $module->MODULE_NAME,
    "module_id" => $module->MODULE_ID,
    "icon" => "adm-submenu-item-link-icon sale_menu_icon",
    "url" => "{$module->MODULE_ID}_index.php?lang=".\LANGUAGE_ID,
);
