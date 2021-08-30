<?php

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CitfactReserve extends CBitrixComponent
{
    /**
     * Fatal error list.
     * @var string[] Array of fatal errors.
     */
    protected $errorsFatal = [];

    /**
     * Non-fatal error list.
     * @var string[] Array of non-fatal errors.
     */
    protected $errorsNonFatal = [];

    public function __construct($component = null)
    {
        parent::__construct($component);

        Loc::loadMessages(__FILE__);
    }

    /**
     * Function checks and prepares all the parameters passed. Everything about $arParam modification is here.
     * @param mixed[] $arParams List of unchecked parameters
     * @return mixed[] Checked and valid parameters
     */
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    /**
     * Function processes $_REQUEST.
     * @return void
     */
    protected function processRequest()
    {

    }

    /**
     * Prepare all required data.
     * @return void
     * @throws User\Exception\NotFoundException
     */
    protected function prepareData()
    {

    }

    /**
     * Move data to $arResult
     * @return void
     */
    protected function makeResult()
    {
        $arResult = [];
        if (!empty($this->arParams["RESULT"])) {
            $arResult = $this->arParams["RESULT"];
        }

        $this->arResult = $arResult;
    }

    /**
     * Function implements all the life cycle of our component
     * @return void
     */
    public function executeComponent()
    {
        try {
            $this->setFrameMode(false);
            $this->processRequest();
            $this->prepareData();
            $this->makeResult();
        } catch (Exception $e) {
            $this->errorsFatal[$e->getCode()] = $e->getMessage();
        }

        $this->formatResultErrors();

        $this->includeComponentTemplate();
    }

    /**
     * Move all errors to $arResult, if there were any
     * @return void
     */
    protected function formatResultErrors()
    {
        $errors = array();
        if (!empty($this->errorsFatal))
            $errors['FATAL'] = $this->errorsFatal;
        if (!empty($this->errorsNonFatal))
            $errors['NONFATAL'] = $this->errorsNonFatal;

        if (!empty($errors))
            $this->arResult['ERRORS'] = $errors;

        // backward compatiblity
        $error = each($this->errorsFatal);
        if (!empty($error['value']))
            $this->arResult['ERROR_MESSAGE'] = $error['value'];
    }
}
