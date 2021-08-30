<?php

namespace Citfact\Entity\MarkingCodeGroup;

class MarkingCodeGroupEntity
{
    const XML_ID_FOOTWEAR = 5408;

    /** @var MarkingCodeGroupTable */
    protected $tableOrm;

    public function __construct()
    {
        $this->tableOrm = new MarkingCodeGroupTable();
    }

    public function getIdFootwear()
    {
        $el = current($this->getList(['UF_XML_ID' => static::XML_ID_FOOTWEAR]));
        if (is_array($el)) {
            return $el['ID'];
        }
        return false;
    }

    public function getList($filter = [], $order = [], $limit = null, $offset = null)
    {
        $params = [
            'select' => ['*'],
            'filter' => $filter,
            'order' => $order,
        ];
        if (!is_null($limit)) {
            $params['limit'] = $limit;
        }
        if (!is_null($offset)) {
            $params['offset'] = $offset;
        }
        $result = $this->tableOrm->getList($params);

        return $result->fetchAll();
    }
}
