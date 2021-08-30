<?php
namespace Ingate\Seo;

use Bitrix\Main\Config\Option;
use Ingate\Seo\Core;

class Canonical
{
	const MODULE_ID = INGATE_SEO_MODULE_ID;
	const OPTION = INGATE_SEO_OPTION_CANONICAL;

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

			$this->data = Core::getCurrentUrlFromTableByField('\Ingate\Seo\CanonicalTable', 'URL');

			if (!empty($this->data)) {

				$patern = '/<link[^>]*?rel=[\'"]canonical[\'"](.*[\'"])?.*\s*>/iuU';

				if (preg_match($patern, $buffer, $matches)) {

					$buffer = preg_replace(
						$patern,
						'<link rel="canonical" href="'.$this->data['CANONICAL'].'" />', $buffer
					);

				} else {

					$buffer = preg_replace(
						'/<\/head>/U',
						'<link rel="canonical" href="'.$this->data['CANONICAL'].'" /></head>',
						$buffer
					);

				}
			}
		}


		return $buffer;
	}
}