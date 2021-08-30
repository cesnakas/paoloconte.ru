<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config as Conf;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity\Base;
use \Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

if (class_exists("ingate_seo")) return;

Class ingate_seo extends CModule
{
	function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__."/version.php");

		$this->exclusionAdminFiles=array(
			'..',
			'.',
			'menu.php',
			'operation_description.php',
			'task_description.php'
		);

		$this->MODULE_ID = 'ingate.seo';
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = Loc::getMessage("INGATE_SEO_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("INGATE_SEO_MODULE_DESCRIPTION");
		$this->PARTNER_NAME = Loc::getMessage("INGATE_SEO_PARTNER_NAME");
	}

	public function GetPath($notDocumentRoot = false)
	{
		if ($notDocumentRoot) {
			$path = str_ireplace($_SERVER["DOCUMENT_ROOT"], '', str_ireplace('\\', '/', dirname(__DIR__)));

			return (preg_match('/\/(local|bitrix)\/modules.*/i', $path, $matches))
				? $matches[0]
				: $path;
		} else {
			return dirname(__DIR__);
		}
	}

	public function isVersionD7()
	{
		$currentVersion = '';

		if (class_exists("\Bitrix\Main\ModuleManager") && method_exists('\Bitrix\Main\ModuleManager', 'getVersion')) {
			$currentVersion = \Bitrix\Main\ModuleManager::getVersion('main');
		} elseif (defined('SM_VERSION')) {
			$currentVersion = SM_VERSION;
		}

		return CheckVersion($currentVersion, '14.5.1');
	}

	function GetModuleRightList()
	{
		return array(
			'reference_id' => array('D', 'K', 'S', 'W'),
			'reference' => array(
				'[D] '.Loc::getMessage('INGATE_SEO_DENIED'),
				'[K] '.Loc::getMessage('INGATE_SEO_READ_COMPONENT'),
				'[S] '.Loc::getMessage('INGATE_SEO_WRITE_SETTING'),
				'[W] '.Loc::getMessage('INGATE_SEO_FULL'),
			)
		);
	}

	function InstallDB()
	{
		Loader::includeModule($this->MODULE_ID);

		if (
			!Application::getConnection(\Ingate\Seo\PageTable::getConnectionName())->isTableExists(
				Base::getInstance('\Ingate\Seo\PageTable')->getDBTableName()
			)
		) {
			Base::getInstance('\Ingate\Seo\PageTable')->createDbTable();
		} else {

			$arPages = \Ingate\Seo\PageTable::getList(array(
				'select'=>array('ID', 'URL'),
				'filter'=>array(
					'LOGIC' => 'OR',
					array('URL' => '%%20%'),
					array('URL' => '% %'),
				)
			))->fetchAll();

			if (!empty($arPages)) {

				foreach ($arPages as $page) {

					if ($page['ID'] > 0)
						\Ingate\Seo\PageTable::update($page['ID'], array(
							'URL' => preg_replace('/(%20|\s)/iu', '+', $page['URL'])
						));
				}
			}
		}

		if (
			!Application::getConnection(\Ingate\Seo\RedirectTable::getConnectionName())->isTableExists(
				Base::getInstance('\Ingate\Seo\RedirectTable')->getDBTableName()
			)
		) {
			Base::getInstance('\Ingate\Seo\RedirectTable')->createDbTable();
		}

		if (
			!Application::getConnection(\Ingate\Seo\CanonicalTable::getConnectionName())->isTableExists(
				Base::getInstance('\Ingate\Seo\CanonicalTable')->getDBTableName()
			)
		) {
			Base::getInstance('\Ingate\Seo\CanonicalTable')->createDbTable();
		}

		if (
			!Application::getConnection(\Ingate\Seo\CounterTable::getConnectionName())->isTableExists(
				Base::getInstance('\Ingate\Seo\CounterTable')->getDBTableName()
			)
		) {
			Base::getInstance('\Ingate\Seo\CounterTable')->createDbTable();
		}

		if (!Option::get($this->MODULE_ID, 'meta'))
			Option::set($this->MODULE_ID, 'meta', 'N');

		if (!Option::get($this->MODULE_ID, 'h1'))
			Option::set($this->MODULE_ID, 'h1', 'N');

		if (!Option::get($this->MODULE_ID, 'robots'))
			Option::set($this->MODULE_ID, 'robots', 'N');

		if (!Option::get($this->MODULE_ID, 'GROUP_DEFAULT_RIGHT'))
			Option::set($this->MODULE_ID, 'GROUP_DEFAULT_RIGHT', 'W');

		if (!Option::get($this->MODULE_ID, 'redirect_www'))
			Option::set($this->MODULE_ID, 'redirect_www', 'N');

		if (!Option::get($this->MODULE_ID, 'redirect_slash_or_custom'))
			Option::set($this->MODULE_ID, 'redirect_slash_or_custom', 'N');

		if (!Option::get($this->MODULE_ID, 'redirect_ending'))
			Option::set($this->MODULE_ID, 'redirect_ending', '');

		if (!Option::get($this->MODULE_ID, 'redirect_scheme'))
			Option::set($this->MODULE_ID, 'redirect_scheme', 'N');

		if (!Option::get($this->MODULE_ID, 'links_main_mirror'))
			Option::set($this->MODULE_ID, 'links_main_mirror', 'N');

		if (!Option::get($this->MODULE_ID, 'links_mixed'))
			Option::set($this->MODULE_ID, 'links_mixed', 'N');

		if (!Option::get($this->MODULE_ID, 'links_nofollow'))
			Option::set($this->MODULE_ID, 'links_nofollow', 'N');

		if (!Option::get($this->MODULE_ID, 'set_counter'))
			Option::set($this->MODULE_ID, 'set_counter', 'N');

        if (!Option::get($this->MODULE_ID, 'set_exclusions'))
            Option::set(
                $this->MODULE_ID,
                'set_exclusions',
                'utm_'.PHP_EOL.
                '_openstat'.PHP_EOL.
                'frommarket'.PHP_EOL.
                'ymclid'.PHP_EOL.
                'gclid'
            );
	}

	function UnInstallDB()
	{
		Loader::includeModule($this->MODULE_ID);

		Application::getConnection(\Ingate\Seo\PageTable::getConnectionName())->
			queryExecute('drop table if exists '.Base::getInstance('\Ingate\Seo\PageTable')->getDBTableName());

		Application::getConnection(\Ingate\Seo\RedirectTable::getConnectionName())->
			queryExecute('drop table if exists '.Base::getInstance('\Ingate\Seo\RedirectTable')->getDBTableName());

		Application::getConnection(\Ingate\Seo\CanonicalTable::getConnectionName())->
			queryExecute('drop table if exists '.Base::getInstance('\Ingate\Seo\CanonicalTable')->getDBTableName());

		Application::getConnection(\Ingate\Seo\CounterTable::getConnectionName())->
			queryExecute('drop table if exists '.Base::getInstance('\Ingate\Seo\CounterTable')->getDBTableName());

		Option::delete($this->MODULE_ID);
	}

	function InstallEvents()
	{
		$event = \Bitrix\Main\EventManager::getInstance();

		$event->registerEventHandler("main", "OnBuildGlobalMenu", $this->MODULE_ID, "\Ingate\Seo\Event", "OnBuildGlobalMenu", 900);
		$event->registerEventHandler("main", "OnEndBufferContent", $this->MODULE_ID, "\Ingate\Seo\Event", "OnEndBufferContent", 900);
		$event->registerEventHandler("main", "OnEpilog", $this->MODULE_ID, "\Ingate\Seo\Event", "OnEpilog", 900);
		$event->registerEventHandler("main", "OnPageStart", $this->MODULE_ID, "\Ingate\Seo\Event", "OnPageStart", 900);
	}
	function UnInstallEvents()
	{
		$event = \Bitrix\Main\EventManager::getInstance();

		$event->unRegisterEventHandler("main", "OnBuildGlobalMenu", $this->MODULE_ID, "\Ingate\Seo\Event", "OnBuildGlobalMenu");
		$event->unRegisterEventHandler("main", "OnEndBufferContent", $this->MODULE_ID, "\Ingate\Seo\Event", "OnEndBufferContent");
		$event->unRegisterEventHandler("main", "OnEpilog", $this->MODULE_ID, "\Ingate\Seo\Event", "OnEpilog");
		$event->unRegisterEventHandler("main", "OnPageStart", $this->MODULE_ID, "\Ingate\Seo\Event", "OnPageStart");
	}

	function InstallFiles()
	{
		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath().'/admin')) {
			if ($dir = opendir($path)) {
				while (false !== $item = readdir($dir)) {
					if (in_array($item,$this->exclusionAdminFiles))
						continue;

					file_put_contents(
						$_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$this->MODULE_ID.'_'.$item,
						'<'.'? require($_SERVER["DOCUMENT_ROOT"]."'.$this->GetPath(true).'/admin/'.$item.'");?'.'>'
					);
				}
				closedir($dir);
			}
		}

		return true;
	}

	function UnInstallFiles()
	{
		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath().'/admin')) {
			if ($dir = opendir($path)) {
				while (false !== $item = readdir($dir)) {
					if (in_array($item, $this->exclusionAdminFiles))
						continue;

					\Bitrix\Main\IO\File::deleteFile(
						$_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$this->MODULE_ID.'_'.$item
					);
				}

				closedir($dir);
			}
		}

		return true;
	}

	function DoInstall()
	{
		global $APPLICATION;

		if ($this->isVersionD7()) {
			\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

			$this->InstallFiles();
			$this->InstallDB();
			$this->InstallEvents();
		} else {
			$APPLICATION->ThrowException(Loc::getMessage("INGATE_SEO_INSTALL_ERROR_VERSION"));
		}

		$APPLICATION->IncludeAdminFile(
			Loc::getMessage("INGATE_SEO_INSTALL_TITLE"),
			$this->GetPath()."/install/step.php"
		);
	}

	function DoUninstall()
	{
		global $APPLICATION;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();

		if ($request["step"]<2) {
			$APPLICATION->IncludeAdminFile(
				Loc::getMessage("INGATE_SEO_UNINSTALL_TITLE"),
				$this->GetPath()."/install/unstep1.php"
			);
		} elseif ($request["step"]==2) {

			if ($request["savedata"] != "Y")
				$this->UnInstallDB();

			$this->UnInstallEvents();
			$this->UnInstallFiles();

			\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);

			$APPLICATION->IncludeAdminFile(
				Loc::getMessage("INGATE_SEO_UNINSTALL_TITLE"),
				$this->GetPath()."/install/unstep2.php"
			);
		}
	}
}
?>
