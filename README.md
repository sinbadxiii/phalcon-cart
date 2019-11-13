# Phalcon Shopping Cart

A simple shoppingcart implementation for Phalcon.

### Installation

Install the package through Composer.

Run the Composer require command from the Terminal:

```
composer require sinbadxiii/phalcon-cart
```

## How use

Add in services

```
$di->set(
      'cart',
      function () use ($di) {
          return new Sinbadxiii\Phalcon\Cart\CartShopping(
              $di->getSession()
          );
      }
  );
```

or creating with name instance

```
$di->set(
      'compare',
      function () use ($di) {
          return new Sinbadxiii\Phalcon\Cart\CartShopping(
              $di->getSession(), 'compare'
          );
      }
  );
```

```
#add()
$this->cart->add('1', 'Product Name 1', 1, 100.99);

#update()
$rowId = '5d12249fdca4cb0fff77f49bbffc128c';
$this->cart->update($rowId, 10);

#remove()
$rowId = '5d12249fdca4cb0fff77f49bbffc128c';
$this->cart->remove($rowId);

#content()
$this->cart->content();

#destroy()
$this->cart->destroy();

#total() with Tax
$this->cart->total();

#total() without Tax
$this->cart->subtotal();

#count()
$this->cart->count();

#countTotal()
$this->cart->countTotal();
```

## Instances

The packages supports multiple instances of the cart. The way this works is like this:

You can set the current instance of the cart by calling $this->cart->instance('newInstance'). From this moment, the active instance of the cart will be newInstance, so when you add, remove or get the content of the cart, you're work with the newInstance instance of the cart. If you want to switch instances, you just call $this->cart->instance('otherInstance') again, and you're working with the otherInstance again.

So a little example:

```
$this->cart->instance('shop')->add('100', 'Product #1', 1, 100.00);

// Get the content of the 'shop' cart
$this->cart->content();

$this->cart->instance('wishlist')->add('200', 'Product #2', 1, 20.00);

// Get the content of the 'wishlist' cart
$this->cart->content();

// If you want to get the content of the 'shopping' cart again
$this->cart->instance('shop')->content();

// And the count of the 'wishlist' cart again
$this->cart->instance('wishlist')->count();
```

> N.B. Keep in mind that the cart stays in the last set instance for as long as you don't set a different one during script execution.

> N.B.2 The default cart instance is called default, so when you're not using instances, $this->cart->content(); is the same as $this->cart->instance('shop')->content().

