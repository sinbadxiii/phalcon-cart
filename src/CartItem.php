<?php

namespace Sinbadxiii\Phalcon\Cart;

use InvalidArgumentException;

class CartItem
{
    /**
     * The rowID of the cart item.
     *
     * @var string
     */
    public $rowId;
    /**
     * The ID of the cart item.
     *
     * @var int|string
     */
    public $id;
    /**
     * The quantity for this cart item.
     *
     * @var int|float
     */
    public $qty;
    /**
     * The name of the cart item.
     *
     * @var string
     */
    public $name;
    /**
     * The price without TAX of the cart item.
     *
     * @var float
     */
    public $price;
    /**
     * The options for this cart item.
     *
     * @var array
     */
    public $options;
    /**
     * The FQN of the associated model.
     *
     * @var string|null
     */
    private $associatedModel = null;
    /**
     * The tax rate for the cart item.
     *
     * @var int|float
     */
    private $taxRate = 0;

    public function __construct($id, $name, $qty, $price, array $options = [])
    {
         $this->id       = $id;
         $this->name     = $name;
         $this->qty      = $qty;
         $this->price    = floatval($price);
         $this->options  = $options;
         $this->rowId = $this->generateRowId($id, $options);
    }

    /**
     * Returns the formatted price without TAX.
     *
     * @param int    $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    public function price(
        $decimals = null,
        $decimalPoint = null,
        $thousandSeperator = null
    ) {
        return $this->numberFormat(
            $this->price,
            $decimals,
            $decimalPoint,
            $thousandSeperator
        );
    }


    /**
     * Returns the formatted price with TAX.
     *
     * @param int    $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    public function priceTax(
        $decimals = null,
        $decimalPoint = null,
        $thousandSeperator = null
    ) {
        return $this->numberFormat(
            $this->priceTax,
            $decimals,
            $decimalPoint,
            $thousandSeperator
        );
    }

    /**
     * Returns the formatted subtotal.
     * Subtotal is price for whole CartItem without TAX
     *
     * @param int    $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    public function subtotal(
        $decimals = null,
        $decimalPoint = null,
        $thousandSeperator = null
    ) {
        return $this->numberFormat(
            $this->subtotal,
            $decimals,
            $decimalPoint,
            $thousandSeperator
        );
    }

    /**
     * Returns the formatted total.
     * Total is price for whole CartItem with TAX
     *
     * @param int    $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    public function total(
        $decimals = null,
        $decimalPoint = null,
        $thousandSeperator = null
    ) {
        return $this->numberFormat(
            $this->total,
            $decimals,
            $decimalPoint,
            $thousandSeperator
        );
    }

    /**
     * Returns the formatted tax.
     *
     * @param int    $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    public function tax(
        $decimals = null,
        $decimalPoint = null,
        $thousandSeperator = null
    ) {
        return $this->numberFormat(
            $this->tax,
            $decimals,
            $decimalPoint,
            $thousandSeperator
        );
    }

    /**
     * Returns the formatted tax.
     *
     * @param int    $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    public function taxTotal(
        $decimals = null,
        $decimalPoint = null,
        $thousandSeperator = null
    ) {
        return $this->numberFormat(
            $this->taxTotal,
            $decimals,
            $decimalPoint,
            $thousandSeperator
        );
    }

    /**
     * Set the tax rate.
     *
     * @param int|float $taxRate
     * @return CartItem
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    /**
     * Get an attribute from the cart item or get the associated model.
     *
     * @param string $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        if (property_exists($this, $attribute)) {
            return $this->{$attribute};
        }

        if ($attribute === 'priceTax') {
            return $this->price + $this->tax;
        }

        if ($attribute === 'subtotal') {
            return $this->qty * $this->price;
        }

        if ($attribute === 'total') {
            return $this->qty * ($this->priceTax);
        }
        if ($attribute === 'tax') {
            return $this->price * ($this->taxRate / 100);
        }

        if ($attribute === 'taxTotal') {
            return $this->tax * $this->qty;
        }

        return null;
    }

    protected function generateRowId($id, array $options)
    {
        ksort($options);
        return md5($id . serialize($options));
    }

    /**
     * Set the quantity for this cart item.
     *
     * @param int|float $qty
     */
    public function setQuantity($qty)
    {
        if (empty($qty) || ! is_numeric($qty)) {
            throw new InvalidArgumentException('Please supply a valid quantity.');
        }
        $this->qty = $qty;
    }

    /**
     * Get the formatted number.
     *
     * @param float  $value
     * @param int    $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    private function numberFormat(
        $value,
        $decimals,
        $decimalPoint,
        $thousandSeperator
    ) {
        return number_format($value, $decimals, $decimalPoint, $thousandSeperator);
    }
}
