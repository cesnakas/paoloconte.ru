<?php
namespace Ingate\Seo;

use Bitrix\Main;
use Bitrix\Main\Entity;

class CounterTable extends Entity\DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'ingate_seo_counter';
	}

	public static function getUfId()
	{
		return 'INGATE_SEO_COUNTER';
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
			new Entity\StringField(
				'NAME',
				array()
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
				'COUNTER',
				array(
					'required' => true,
					'size' => 100,
				)
			),
			new Entity\BooleanField(
				'POSITION',
				array(
					'values' => array('T', 'H', 'B', 'F'),
					'default_value' => 'H',
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
}