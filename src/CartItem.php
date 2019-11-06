<?php

namespace Phalcon\Cart;


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
        if(empty($qty) || ! is_numeric($qty))
            throw new InvalidArgumentException('Please supply a valid quantity.');
        $this->qty = $qty;
    }
}