<?php
namespace Ingate\Seo;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpApplication;
use Ingate\Seo\Tools;
use Ingate\Seo\Redirect;
use Ingate\Seo\App;
use Ingate\Seo\Link;
use Ingate\Seo\Canonical;
use Ingate\Seo\Counter;

Loc::loadMessages(__FILE__);

class Event
{
	const MODULE_ID = INGATE_SEO_MODULE_ID;

	function OnBuildGlobalMenu(&$arGlobalMenu, &$arModuleMenu)
	{
		if ($GLOBALS['APPLICATION']->GetGroupRight(self::MODULE_ID) < "W")
			return;

		$arMenu = array(
			"parent_menu" => "global_menu_services",
			"section" => self::MODULE_ID,
			"sort" => 50,
			"text" => Loc::getMessage("INGATE_SEO_MODULE_NAME"),
			"title" => Loc::getMessage("INGATE_SEO_MODULE_NAME"),
			"icon" => "",
			"page_icon" => "",
			"items_id" => self::MODULE_ID. "_items",
			"more_url" => array(
				""
			),
			"items" => array(
				array(
					"module_id" => self::MODULE_ID,
					"text" => Loc::getMessage("INGATE_SEO_LIST"),
					"title" => Loc::getMessage("INGATE_SEO_LIST"),
					"url" => self::MODULE_ID."_list.php",
					"more_url" => array(
						self::MODULE_ID."_list.php",
						self::MODULE_ID."_edit.php"
					),
				),
				array(
					"module_id" => self::MODULE_ID,
					"text" => Loc::getMessage("INGATE_SEO_MENU_REDIRECTS"),
					"title" => Loc::getMessage("INGATE_SEO_MENU_REDIRECTS"),
					"url" => self::MODULE_ID."_redirects.php",
					"more_url" => array(
						self::MODULE_ID."_redirects.php",
						self::MODULE_ID."_redirect_edit.php"
					),
				),
				array(
					"module_id" => self::MODULE_ID,
					"text" => Loc::getMessage("INGATE_SEO_MENU_CANONICAL"),
					"title" => Loc::getMessage("INGATE_SEO_MENU_CANONICAL"),
					"url" => self::MODULE_ID."_canonical_list.php",
					"more_url" => array(
						self::MODULE_ID."_canonical_list.php",
						self::MODULE_ID."_canonical_edit.php"
					),
				),
				array(
					"module_id" => self::MODULE_ID,
					"text" => Loc::getMessage("INGATE_SEO_MENU_COUNTER"),
					"title" => Loc::getMessage("INGATE_SEO_MENU_COUNTER"),
					"url" => self::MODULE_ID."_counter_list.php",
					"more_url" => array(
						self::MODULE_ID."_counter_list.php",
						self::MODULE_ID."_counter_edit.php"
					),
				),
				array(
					"module_id" => self::MODULE_ID,
					"text" => Loc::getMessage("INGATE_SEO_IMPORT"),
					"title" => Loc::getMessage("INGATE_SEO_IMPORT"),
					"url" => self::MODULE_ID."_csv_import.php",
					"more_url" => array(),
				),
				array(
					"module_id" => self::MODULE_ID,
					"text" => Loc::getMessage("INGATE_SEO_EXPORT"),
					"title" => Loc::getMessage("INGATE_SEO_EXPORT"),
					"url" => self::MODULE_ID."_csv_export.php",
					"more_url" => array(),
				),
			)
		);

		$arModuleMenu[] = $arMenu;
	}

	function OnPageStart()
	{

		if (!Tools::primaryCheckBeforeEvent())
			return;

		Redirect::getInstance()->init();

	}

	function OnEpilog()
	{
		if (!Tools::primaryCheckBeforeEvent())
			return;

		App::getInstance()->setProperties();

	}

	function OnEndBufferContent(&$content)
	{
		if (!Tools::primaryCheckBeforeEvent())
			return;

		$content = App::getInstance()->setBufferProperties($content);

		if (Link::getInstance()->isEnabled())
			$content = Link::getInstance()->setBuffer($content);

		if (Canonical::getInstance()->isEnabled())
			$content = Canonical::getInstance()->setBuffer($content);

		if (Counter::getInstance()->isEnabled())
			$content = Counter::getInstance()->setBuffer($content);

	}
}