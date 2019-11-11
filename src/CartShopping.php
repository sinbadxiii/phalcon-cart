<?php

namespace Sinbadxiii\Phalcon\Cart;

use Sinbadxiii\Phalcon\Cart\Exceptions\RowIDNotFoundException;
use Phalcon\Session\Adapter\Files as Session;

class CartShopping
{
    protected const DEFAULT_IDENTIFICATOR = 'shop';

    /**
     * Instance of the session manager.
     *
     * @var \Phalcon\Session\Adapter\Files
     */
    private $session;

    /**
     * Holds the current cart instance.
     *
     * @var string
     */
    private $instance;

    /**
     * @desc - log phalcon
     */
    private $logger;

    public function __construct(Session $session)
    {
        $this->session = $session;

        $this->instance(self::DEFAULT_IDENTIFICATOR);
    }

    /**
     * Set the current cart instance.
     *
     * @param string|null $instance
     * @return \Sinbadxiii\Phalcon\Cart\CartShopping
     */
    public function instance($instance = null)
    {
        $instance = $instance ?: self::DEFAULT_IDENTIFICATOR;
        $this->instance = sprintf('%s.%s', 'cart', $instance);
        return $this;
    }

    /**
     * Get the current cart instance.
     *
     * @return string
     */
    public function currentInstance()
    {
        return str_replace('cart.', '', $this->instance);
    }

    /**
     * Get the content of the cart.
     *
     * @return array
     */
    public function content()
    {
        if (is_null($this->session->get($this->instance))) {
            new CartContent();
        }

        if ($this->session->has($this->instance)) {
            return $this->session->get($this->instance)->getItems();
        }
    }

    protected function getCartContent()
    {
        $content = $this->session->has($this->instance)
            ? $this->session->get($this->instance)
            : new CartContent();
        return $content;
    }


    /**
     * Add an item to the cart.
     *
     * @param mixed $id
     * @param mixed $name
     * @param int|float $qty
     * @param float $price
     * @param array $options
     * @return CartItem
     */
    public function add(
        $id,
        $name = null,
        $qty = null,
        $price = null,
        array $options = []
    ) {
        if ($this->isMulti($id)) {
            return array_map(function ($item) {
                return $this->add($item);
            }, $id);
        }

        $cartItem = new CartItem($id, $name, $qty, $price, $options);

        $content = $this->getCartContent();

        if ($content->has($cartItem->rowId)) {
            $cartItem->qty += $content->get($cartItem->rowId)->qty;
        }

        $content->put($cartItem->rowId, $cartItem);
        $this->session->set($this->instance, $content);
        return $cartItem;
    }

    /**
     * Update the cart item with the given rowId.
     *
     * @param string $rowId
     * @param mixed $qty
     * @return CartItem
     */
    public function update($rowId, $qty)
    {
        $cartItem = $this->get($rowId);
        $cartItem->qty = $qty;
        $content = $this->getCartContent();

        if ($rowId !== $cartItem->rowId) {
            $content->pull($rowId);
            if ($content->has($cartItem->rowId)) {
                $existingCartItem = $this->get($cartItem->rowId);
                $cartItem->setQuantity($existingCartItem->qty + $cartItem->qty);
            }
        }
        if ($cartItem->qty <= 0) {
            $this->remove($cartItem->rowId);
            return;
        } else {
            $content->put($cartItem->rowId, $cartItem);
        }
        $this->session->set($this->instance, $content);
        return $cartItem;
    }

    /**
     * Remove the cart item with the given rowId from the cart.
     *
     * @param string $rowId
     * @return void
     */
    public function remove($rowId)
    {
        $cartItem = $this->get($rowId);
        $content = $this->getCartContent();
        $content->pull($cartItem->rowId);
        $this->session->set($this->instance, $content);
    }

    public function get($rowId)
    {
        $content = $this->getCartContent();

        if (!$content->has($rowId)) {
            throw new RowIDNotFoundException(
                "The cart does not contain rowId {$rowId}."
            );
        }
        return $content->get($rowId);
    }

    /**
     * Get the total price of the items in the cart.
     *
     * @param int $decimals
     * @param string $decimalPoint
     * @param string $thousandSeperator
     * @return string
     */
    public function total(
        $decimals = null,
        $decimalPoint = null,
        $thousandSeperator = null
    ) {
        $content = $this->getCartContent();

        $total = $content->reduce(function ($total, CartItem $cartItem) {
            return $total + ($cartItem->qty * $cartItem->priceTax);
        }, 0);

        return $this->numberFormat(
            $total,
            $decimals,
            $decimalPoint,
            $thousandSeperator
        );
    }

    /**
     * @param null $decimals
     * @param null $decimalPoint
     * @param null $thousandSeperator
     * @return string
     */
    public function subtotal(
        $decimals = null,
        $decimalPoint = null,
        $thousandSeperator = null
    ) {
        $content = $this->getCartContent();

        $subTotal = $content->reduce(function ($subTotal, CartItem $cartItem) {
            return $subTotal + ($cartItem->qty * $cartItem->price);
        }, 0);

        return $this->numberFormat(
            $subTotal,
            $decimals,
            $decimalPoint,
            $thousandSeperator
        );
    }

    /**
     * Get the number of all qty items in the cart.
     *
     * @return int|float
     */
    public function countTotal()
    {
        $content = $this->getCartContent();

        $count = $content->reduce(function ($count, CartItem $cartItem) {
            return $count + $cartItem->qty;
        }, 0);

        return $count;
    }

    /**
     * Get the number of items in the cart.
     *
     * @return int|float
     */
    public function count()
    {
        $content = $this->getCartContent();

        return $content->count();
    }

    /**
     * @param $item
     * @return bool
     */
    private function isMulti($item)
    {
        return is_array($item);
    }

    /**
     * Destroy the current cart instance.
     *
     * @return void
     */
    public function destroy()
    {
        $this->session->remove($this->instance);
    }

    /**
     * Get the Formated number
     *
     * @param $value
     * @param $decimals
     * @param $decimalPoint
     * @param $thousandSeperator
     * @return string
     */
    private function numberFormat(
        $value,
        $decimals = 2,
        $decimalPoint = '.',
        $thousandSeperator = ','
    ) {
        return number_format($value, $decimals, $decimalPoint, $thousandSeperator);
    }
}
