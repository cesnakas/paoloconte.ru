<?php
namespace Ingate\Seo;

use \Bitrix\Main\Config\Option;
use \Ingate\Seo\Tools;
use \Ingate\Seo\Core;
use \Bitrix\Main\HttpApplication;
use Bitrix\Main\Web\Uri;

class Redirect
{
	const MODULE_ID = INGATE_SEO_MODULE_ID;

	protected static $_instance;
	protected $domain;
	protected $uri;
	protected $isPost = false;
	protected $scheme;
	protected $isHttps;
	protected $page;
	protected $query;
	protected $data;
	protected $status = '301 Moved Permanently';

	protected $redirect = false;

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	private function __construct()
	{
		$request = HttpApplication::getInstance()->getContext()->getRequest();
		$server = HttpApplication::getInstance()->getContext()->getServer();

		$this->isPost = $request->isPost();
		$this->domain = $server->getServerName();
		$this->uri = $request->getRequestUri();

		$arUri = explode('?', $this->uri);

		$this->page = $arUri[0];
		$this->query = $arUri[1];
		$this->isHttps = $request->isHttps();
		$this->scheme = ($this->isHttps) ? 'https' : 'http';

		$this->data = Core::getCurrentUrlFromTableByField('\Ingate\Seo\RedirectTable', 'OLD');
	}

	private function __clone()
	{
	}

	private function __wakeup()
	{
	}

	private function isWWW()
	{

		if (preg_match('#^www\.(.*)?$#iu', $this->domain, $matches)) {
			return true;
		}

		return false;

	}

	private function isFile()
	{

		if (preg_match('#\.[^/]{1,10}$#iu', $this->page)) {
			return true;
		}

		return false;

	}

	private function isSlash()
	{

		if (preg_match('#\/$#iu', $this->page)) {
			return true;
		}

		return false;

	}

	private function set()
	{

		if (
			!empty($this->scheme) &&
			!empty($this->domain) &&
			!empty($this->page)
		) {
			$url = $this->scheme.'://'.$this->domain.$this->page;

			if (!empty($this->query)) {
				$url .= '?'.$this->query;
			}

			header('HTTP/1.1 '.$this->status);
			header('Location: '.$url);
			exit();
		}

		return false;
	}

	/**
	 * Set HTTP status for redirect by new url property
	 */
	private function setStatus()
	{

		if ($this->data['STATUS'] == 301) {

			$this->status = '301 Moved Permanently';

		} elseif ($this->data['STATUS'] == 302) {

			$this->status = '302 Moved Temporarily';

		}

	}

	private function setScheme()
	{

		$redirectScheme = Option::get(self::MODULE_ID, INGATE_SEO_OPTION_SCHEME);

		if ($redirectScheme == 'N')
			return false;

		if ($redirectScheme == 'S' && !$this->isHttps) {

			$scheme = 'https';

		} elseif ($redirectScheme == 'W' && $this->isHttps) {

			$scheme = 'http';

		}

		if (!empty($scheme)) {
			$this->scheme = $scheme;
			$this->redirect = true;
		}
	}

	private function setWWW()
	{

		$redirectWWW = Option::get(self::MODULE_ID, INGATE_SEO_OPTION_WWW);

		if ($redirectWWW == 'N')
			return false;

		if ($redirectWWW == 'Y' && self::isWWW()) {

			$domain = preg_replace('#^www\.#iu', '', $this->domain);

		} elseif ($redirectWWW == 'W' && !self::isWWW()) {

			$domain = 'www.'.$this->domain;
		}

		if (!empty($domain)) {
			$this->domain = $domain;
			$this->redirect = true;
		}
	}

	private function setEnding()
	{

		$redirectEnding = Option::get(self::MODULE_ID, INGATE_SEO_OPTION_SLASH);
		$redirectEndingCustom = Option::get(self::MODULE_ID, INGATE_SEO_OPTION_ENDING);

		if ($redirectEnding == 'N' || self::isFile() || $this->page == '/')
			return false;

		if ($redirectEnding == 'S' && !self::isSlash()) {

			$page = $this->page.'/';

		} elseif ($redirectEnding == 'W' && self::isSlash()) {

			$page = preg_replace('#/$#', '', $this->page);

		} elseif ($redirectEnding == 'A' && !empty($redirectEndingCustom)) {

			$page = preg_replace('#/$#', '', $this->page).$redirectEndingCustom;

		}

		if (!empty($page)) {
			$this->page = $page;
			$this->redirect = true;
		}
	}

	private function setCustom()
	{

		if (!empty($this->data) && !empty($this->data['NEW'])) {

			$uri = new Uri($this->data['NEW']);
			$host = $uri->getHost();

			if (!empty($host) && $this->domain != $host) {

				$this->scheme = $uri->getScheme();
				$this->domain = $host;
				$this->page = $uri->getPath();
				$this->query = $uri->getQuery();

				$this->redirect = true;
				return false;
			}

			$new = explode('?', $this->data['NEW']);

			$this->page = preg_replace('/^(https?:\/\/)??(.*)?\/(.*)$/iU', '/$3', $new[0]);
			$this->query = (!empty($new[1])) ? $new[1] : $uri->getQuery();

			$this->redirect = true;

		}

		return true;
	}

	public function init()
	{
		if ($this->isPost)
			return;

		$this->setStatus();

		if (self::setCustom()) {
			self::setWWW();
			self::setScheme();
			self::setEnding();
		}

		if ($this->redirect) {
			self::set();
		}
	}
}