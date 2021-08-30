<?php
/**
 * Created by PhpStorm.
 * User: maus <mausglov@yandex.ru>
 * Date: 18.11.16
 * Time: 17:07
 */

namespace Trinet\Seometa\Batchop;


class Record
{
	public $title = '';
	public $header = '';
	public $description = '';
	public $crumb = '';
	
	public function isEmpty()
	{
		$filledProps = array_filter( get_object_vars($this) );
		return !count($filledProps);
	}
	
	public function capableForAdd()
	{
		return ( $this->title !== '' && $this->header !== '' );
	}
}