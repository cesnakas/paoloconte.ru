<?php
namespace Ingate\Seo;

use Bitrix\Main\Config\Option;
use Ingate\Seo\Core;
use Ingate\Seo\CounterTable;

class Counter
{
	const MODULE_ID = INGATE_SEO_MODULE_ID;
	const OPTION = INGATE_SEO_OPTION_COUNTER;

	protected static $_instance;
	protected $data = array();
	protected $option;

	protected $enabled = false;

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	private function __construct()
	{
		$this->option = ($this->option)
			? $this->option
			: Option::get(self::MODULE_ID, self::OPTION);

		if ($this->option == 'Y')
			$this->enabled = true;
	}

	private function __clone()
	{
	}

	private function __wakeup()
	{
	}

	public function isEnabled()
	{

		return $this->enabled;

	}

	public function setBuffer($buffer)
	{
		if ($this->isEnabled()) {

			$this->data = CounterTable::getList(array(
				'filter' => array('ACTIVE'=>'Y')
			))->fetchAll();

			if (!empty($this->data)) {

				$head = '';
				$footer = '';

				foreach ($this->data as $key => $value) {

					if ($value['POSITION'] == 'H') {
						$head .= PHP_EOL.$value['COUNTER'].PHP_EOL;
					} elseif ($value['POSITION'] == 'T') {
						$top .= PHP_EOL.$value['COUNTER'].PHP_EOL;
					} elseif ($value['POSITION'] == 'B') {
						$body .= PHP_EOL.$value['COUNTER'].PHP_EOL;
					} elseif ($value['POSITION'] == 'F') {
						$footer .= PHP_EOL.$value['COUNTER'].PHP_EOL;
					}
				}

				// 4 replacements, not several in a cycle
				if (!empty($top)) {
					$buffer = preg_replace('/(<head>|<head\s[^>]*>)/iU', '${1}'.$top, $buffer);
				}

				if (!empty($body)) {
					$buffer = preg_replace('/(<body>|<body\s[^>]*>)/iU', '${1}'.$body, $buffer);
				}

				if (!empty($head)) {
					$buffer = preg_replace('/(<\/head>)/i', $head.'$1', $buffer);
				}

				if (!empty($footer)) {
					$buffer = preg_replace('/(<\/body>)/i', $footer.'$1', $buffer);
				}
			}
		}

		return $buffer;
	}
}