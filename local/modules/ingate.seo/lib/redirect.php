<?php
namespace Ingate\Seo;

use Bitrix\Main;
use Bitrix\Main\Entity;

class RedirectTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'ingate_seo_redirects';
	}

	public static function getUfId()
	{
		return 'INGATE_SEO_REDIRECTS';
	}

	public static function getMap()
	{
		return array(
			new Entity\IntegerField(
				'ID',
				array(
					'primary' => true,
					'autocomplete' => true
				)
			),
			new Entity\StringField(
				'ACTIVE',
				array(
					'default_value' => 'Y',
				)
			),
			new Entity\DatetimeField(
				'TIMESTAMP_X',
				array(
					'default_value' => new Main\Type\DateTime(),
				)
			),
			new Entity\DatetimeField(
				'DATE_CREATE',
				array(
					'default_value' => new Main\Type\DateTime(),
				)
			),
			new Entity\TextField(
				'OLD',
				array(
					'required' => true,
				)
			),
			new Entity\TextField(
				'NEW',
				array(
					'required' => true,
				)
			),
			new Entity\BooleanField(
				'STATUS',
				array(
					'values' => array('301', '302'),
					'default_value' => '301',
				)
			),
		);
	}

	public static function onBeforeUpdate(Entity\Event $event)
	{
		$result = new Entity\EventResult;
		$data = $event->getParameter("fields");

		$result->modifyFields(
			array(
				'TIMESTAMP_X' => new Main\Type\DateTime(),
			)
		);

		return $result;
	}

	/**
	 * Table comparing for import or export
	 * @return array key = field in table, value = field's name in file
	 */
	public static function getCompareForCSV()
	{
		return array(
			"OLD" => "OLD URL",
			"NEW" => "NEW URL",
		);
	}
}