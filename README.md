# Yeelight-PHP
## A simple standalone PHP library to interact with Yeelight RGB bulbs (still in development)

### Yeelight-PHP ?

Yes, it's a very simple PHP library to allows you to communicate with your Yeelight RGB lights.
This library is all based on the official [documentation from Yeelight](https://www.yeelight.com/download/Yeelight_Inter-Operation_Spec.pdf).

### Requirements
- PHP 7.4 or up (tested with PHP 8.0)
- PHP sockets extension
- PHP JSON extension

### How does it work ?

Yeelight-PHP use PHP sockets to communicate with your lights, that make it fast and reliable!
It can as well be used in your 'from scratch' project or in the framework of your choice!

#### Examples

Imagine that the Yeelight has the IP 192.168.0.15 and is using the default port (55443), in order to change the light color, you can do this:

##### Change the light color

```php
<?php

require_once './vendor/autoload.php';

use SharkEzz\Yeelight\Yeelight;

// Create a new instance of Yeelight-PHP
$yeelight = new Yeelight('192.168.0.15');

// Set the color of the light, notice that all the 'set' methods are fluent, it means that you can use them in chain !
$yeelight->setColor(0xFF0000)
    ->commit(); // The commit method has to be called at the end to send the commands to the light
```

##### Change the light brightness

```php
<?php

require_once './vendor/autoload.php';

use SharkEzz\Yeelight\Yeelight;

$yeelight = new Yeelight('192.168.0.15');

$yeelight->setBrightness(50)
    ->commit();
```

##### Change the color and the brightness in one line of code

```php
<?php

require_once './vendor/autoload.php';

use SharkEzz\Yeelight\Yeelight;

$yeelight = new Yeelight('192.168.0.15');

$yeelight->setBrightness(50)
    ->setColor(0x00FF00)
    ->commit();
```

### How to install
#### With composer
 - `composer require sharkezz/yeelight-php`
 - Require the composer autoloader (if it is not done yet)
 - Instantiate a new Yeelight object
 - Use it!

### Currently implemented methods
 - [x] Set the color (RGB) of the light
 - [x] Set the light brightness
 - [ ] Get light infos
 - [ ] Set the light power
 - [ ] Color Flow control
 - [ ] Proper error handling
