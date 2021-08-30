<?php


namespace Citfact\CloudLoyalty;


class DataLoyalty
{
    protected static $instance;

    public function getUseCloudScore()
    {
        return $_SESSION['USE_CLOUD_SCORE'];
    }

    public function getOriginalDiscount()
    {
        return $_SESSION['ORIGINAL_DISCOUNT'];
    }

    public function setOriginalDiscount($value)
    {
        $_SESSION['ORIGINAL_DISCOUNT'] = $value;
    }

    public function getCardId()
    {
        return $_SESSION['CARD_ID'];
    }

    public function getOriginalPrices()
    {
        return $_SESSION['ORIGINAL_PRICES'];
    }

    public function setOriginalPrices($value)
    {
        $_SESSION['ORIGINAL_PRICES'] = $value;
    }

    public function getCloudScoreApplied()
    {
        return $_SESSION['CLOUD_SCORE_APPLIED'];
    }

    public function getBonusData()
    {
        return $_SESSION['BONUS_DATA'];
    }

    public function setBonusData($value)
    {
        $_SESSION['BONUS_DATA'] = $value;
    }

    public function getDataAll()
    {
        return [
            'USE_CLOUD_SCORE' => $this->getUseCloudScore(),
            'ORIGINAL_DISCOUNT' => $this->getOriginalDiscount(),
            'ORIGINAL_PRICES' => $this->getOriginalPrices(),
            'CLOUD_SCORE_APPLIED' => $this->getCloudScoreApplied(),
            'CARD_ID' => $this->getCardId(),
            'BONUS_DATA' => $this->getBonusData()
        ];
    }

    public function setUseCloudScore($value)
    {
        $_SESSION['USE_CLOUD_SCORE'] = $value;
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    protected function __construct()
    {
    }

    public function deleteOriginalDiscount()
    {
        unset($_SESSION['ORIGINAL_DISCOUNT']);
    }

    public function deleteCardId()
    {
        unset($_SESSION['CARD_ID']);
    }

    public function setCardId($value)
    {
        $_SESSION['CARD_ID'] = $value;
    }

    public function deleteOriginalPrices()
    {
        unset($_SESSION["ORIGINAL_PRICES"]);
    }

    public function setCloudScoreApplied($value)
    {
        $_SESSION['CLOUD_SCORE_APPLIED'] = $value;
    }

    public function clearBonusData()
    {
        $_SESSION['BONUS_DATA'] = [];
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }
}