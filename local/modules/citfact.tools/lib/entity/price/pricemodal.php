<?php

namespace Citfact\Entity\Price;

class PriceModal
{
    public $basePrice = 0;
    public $actionPrice = 0;
    protected $discountPercent = 0;
    protected $discount = 0;
    protected $isDiscount = false;

    public function getDiscountPercent()
    {
        if ($this->basePrice > $this->actionPrice) {
            $this->discountPercent = round(100 - ($this->actionPrice / $this->basePrice * 100));
        }
        return $this->discountPercent;
    }

    public function getDiscount()
    {
        $this->discount = 0;
        if ($this->basePrice > $this->actionPrice) {
            $this->discount = $this->basePrice - $this->actionPrice;
        }
        return $this->discount;
    }

    /**
     * @return bool
     */
    public function isDiscount()
    {
        return $this->isDiscount;
    }

    /**
     * @param bool $isDiscount
     */
    public function setIsDiscount($isDiscount)
    {
        $this->isDiscount = $isDiscount;
    }

    public function updatePriceForTemplate($price)
    {
        $price['RATIO_BASE_PRICE'] = $this->basePrice;
        $price['PRINT_BASE_PRICE'] = number_format($this->basePrice, 0, ',', ' ');
        $price['PRINT_RATIO_BASE_PRICE'] = $price['PRINT_BASE_PRICE'];
        $price['DISCOUNT'] = number_format($this->getDiscount(), 0, ',', ' ');
        $price['PRINT_DISCOUNT'] = $price['DISCOUNT'];
        $price['RATIO_DISCOUNT'] = $price['DISCOUNT'];
        $price['PERCENT'] = $this->getDiscountPercent();

        if ($this->isDiscount) {
            $price['PRINT_RATIO_PRICE'] = number_format($this->actionPrice, 0, ',', ' ');
        } else {
            $price['PRINT_RATIO_PRICE'] = number_format($this->basePrice, 0, ',', ' ');
        }

        return $price;
    }
}
