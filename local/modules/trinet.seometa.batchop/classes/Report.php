<?php
/**
 * Created by PhpStorm.
 * User: Maus <mausglov@yandex.ru>
 * Date: 23.01.14
 * Time: 8:26
 */

namespace Trinet\Seometa\Batchop;

class Report
{
    public $errors = array();
    public $skipped = array();
    public $added = array();
    public $updated = array();
    public $removed = array();

    public $total = 0;

    public function exists($v)
    {
        return (
            in_array($v, $this->skipped) ||
            in_array($v, $this->added) ||
            in_array($v, $this->updated) ||
            in_array($v, $this->removed)
        );
    }

    public function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * сообщает, вносились ли изменения в результате импорта
     *
     * @return bool
     */
    public function hasChanges()
    {
        return (!(
            empty($this->added) ||
            empty($this->updated) ||
            empty($this->removed)
        ));
    }

    public function get( $delimeter = "\n" )
    {
        $messages = array();
	    $count = count($this->updated);
	    if ($count)
	    {
		    $messages[] = "Изменено: {$count}.";
	    }
	    $count = count($this->added);
	    if ($count)
	    {
		    $messages[] = "Создано: {$count}.";
	    }
	    $count = count($this->removed);
	    if ($count)
	    {
		    $messages[] = "Удалено: {$count}.";
	    }
	    return implode($delimeter, $messages);
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }
}