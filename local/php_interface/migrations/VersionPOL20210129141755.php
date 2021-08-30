<?php

namespace Sprint\Migration;


class VersionPOL20210129141755 extends Version
{

    protected $description = "";

    public function up() {
        $helper = $this->getHelperManager();

    
            $iblockId = $helper->Iblock()->getIblockIdIfExists('main_catalog','catalog');
    
    
                $helper->Iblock()->saveProperty($iblockId, array (
  'NAME' => 'Пол',
  'ACTIVE' => 'Y',
  'SORT' => '600',
  'CODE' => 'POL',
  'DEFAULT_VALUE' => '',
  'PROPERTY_TYPE' => 'S',
  'ROW_COUNT' => '1',
  'COL_COUNT' => '30',
  'LIST_TYPE' => 'L',
  'MULTIPLE' => 'N',
  'XML_ID' => '49850c31-0054-11df-868e-00241d705ca4',
  'FILE_TYPE' => '',
  'MULTIPLE_CNT' => '5',
  'LINK_IBLOCK_ID' => '0',
  'WITH_DESCRIPTION' => 'N',
  'SEARCHABLE' => 'N',
  'FILTRABLE' => 'Y',
  'IS_REQUIRED' => 'N',
  'VERSION' => '1',
  'USER_TYPE' => NULL,
  'USER_TYPE_SETTINGS' => NULL,
  'HINT' => '',
));
        
    
    
    
        }

    public function down()
    {
        $helper = $this->getHelperManager();

        //your code ...
    }

}
