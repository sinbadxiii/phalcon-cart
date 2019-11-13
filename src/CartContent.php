<?php

namespace Sinbadxiii\Phalcon\Cart;

class CartContent
{
    /**
     * @var
     */
    protected $items = [];

    /**
     * @param $key_item
     * @return bool
     */
    public function has($key_item)
    {
        return array_key_exists($key_item, $this->items);
    }

    /**
     * @param $key_item
     * @param $item
     */
    public function put($key_item, $item)
    {
        $this->items[$key_item] = $item;
    }

    /**
     * @param $key_item
     * @return mixed
     */
    public function get($key_item)
    {
        return $this->items[$key_item];
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param $key_item
     */
    public function pull($key_item)
    {
        unset($this->items[$key_item]);
    }

    /**
     * @param callable $callback
     * @param null $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * Count the number of items in the collection.
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }
}
