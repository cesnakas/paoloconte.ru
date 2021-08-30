<?php

namespace Sprint\Migration;


class VersionCAPTCHA20210225144734 extends Version
{

    protected $description = "";

    public function up()
    {
        $helper = $this->getHelperManager();

        $formHelper = $helper->Form();
        $formId = $formHelper->saveForm(array (
  'NAME' => 'Анкета франчайзи',
  'SID' => 'SIMPLE_FORM_1',
  'BUTTON' => 'Отправить',
  'C_SORT' => '100',
  'FIRST_SITE_ID' => NULL,
  'IMAGE_ID' => NULL,
  'USE_CAPTCHA' => 'Y',
  'DESCRIPTION' => '',
  'DESCRIPTION_TYPE' => 'text',
  'FORM_TEMPLATE' => '',
  'USE_DEFAULT_TEMPLATE' => 'Y',
  'SHOW_TEMPLATE' => NULL,
  'MAIL_EVENT_TYPE' => 'FORM_FILLING_SIMPLE_FORM_1',
  'SHOW_RESULT_TEMPLATE' => NULL,
  'PRINT_RESULT_TEMPLATE' => NULL,
  'EDIT_RESULT_TEMPLATE' => NULL,
  'FILTER_RESULT_TEMPLATE' => '',
  'TABLE_RESULT_TEMPLATE' => '',
  'USE_RESTRICTIONS' => 'Y',
  'RESTRICT_USER' => '11',
  'RESTRICT_TIME' => '11',
  'RESTRICT_STATUS' => '1',
  'STAT_EVENT1' => 'form',
  'STAT_EVENT2' => 'franch',
  'STAT_EVENT3' => '',
  'LID' => NULL,
  'C_FIELDS' => '1',
  'QUESTIONS' => '10',
  'STATUSES' => '1',
  'arSITE' => 
  array (
    0 => 's1',
  ),
  'arMENU' => 
  array (
    'ru' => 'Франчайзинг (анкета)',
    'en' => 'Франчайзинг (анкета)',
  ),
  'arGROUP' => 
  array (
  ),
  'arMAIL_TEMPLATE' => 
  array (
    0 => '86',
  ),
));



    }

    public function down()
    {
        $helper = $this->getHelperManager();

        //your code ...
    }

}

