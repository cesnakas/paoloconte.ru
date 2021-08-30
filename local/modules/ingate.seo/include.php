<?
if (!defined('INGATE_SEO_MODULE_ID')) {
	require_once('prolog.php');
}
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

Loader::registerAutoLoadClasses(
	INGATE_SEO_MODULE_ID,
	array(
		"Ingate\Seo\Tools" => "lib/tools.php",
		"Ingate\Seo\Redirect" => "lib/component/redirect.php",
		"Ingate\Seo\PageTable" => "lib/page.php",
		"Ingate\Seo\Core" => "lib/core.php",
		"Ingate\Seo\RedirectTable" => "lib/redirect.php",
		"Ingate\Seo\CanonicalTable" => "lib/canonical.php",
		"Ingate\Seo\CounterTable" => "lib/counter.php",
		"Ingate\Seo\App" => "lib/app.php",
		"Ingate\Seo\Link" => "lib/component/link.php",
		"Ingate\Seo\Canonical" => "lib/component/canonical.php",
		"Ingate\Seo\Counter" => "lib/component/counter.php",
		"Ingate\Seo\Event" => "lib/event.php",
	)
);