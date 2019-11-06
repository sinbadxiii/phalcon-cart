#Phalcon Shopping Cart

A simple shoppingcart implementation for Phalcon.

###Installation

Install the package through Composer.

Run the Composer require command from the Terminal:

```
composer require sinbadxiii/phalcon-cart:dev-master
```

##How use

Add in services

```
$di->set(
      'cart',
      function () use ($di) {
          return new Phalcon\Cart\CartShopping(
              $di->getSession()
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

#total()
$this->cart->total();

#count()
$this->cart->count();

#sum_total()
$this->cart->sum_total();
```

