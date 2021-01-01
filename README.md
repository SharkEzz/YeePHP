# Yeelight-PHP
## A simple standalone PHP library to interact with Yeelight RGB bulbs (still in development)

- [Introduction](#introduction)
- [Requirements](#requirements)
- [How to install](#how-to-install)
- [How does it work?](#how-it-work)
- [Currently implemented method](#implemented-methods)
- [Example](#examples)
    - [Change light color](#change-color)
    - [Change light brightness](#change-brightness)
    - [Change many parameters at once](#change-many-at-once)
- [Testing the library](#testing)
- [Credits](#credits)

<a name="introduction"></a>
### Yeelight-PHP ?

It's a very simple PHP library to allows you to communicate with your Yeelight RGB lights.
This library is all based on the official [documentation from Yeelight](https://www.yeelight.com/download/Yeelight_Inter-Operation_Spec.pdf).

<a name="requirements"></a>
### Requirements
- PHP 7.4 or up (tested with PHP 8.0)
- PHP sockets extension
- PHP JSON extension
- Yeelight local network control enabled on your lights

<a name="how-to-install"></a>
### How to install
- Require the library using Composer: `composer require sharkezz/yeelight-php`
- Require the composer autoloader (if it is not done yet)
- Instantiate a new Yeelight object
- Use it!

<a name="how-it-work"></a>
### How does it work ?

Yeelight-PHP use PHP sockets to communicate with your lights, that make it fast and reliable!
It can as well be used in your 'from scratch' project or in the framework of your choice!

<a name="implemented-methods"></a>
### Currently implemented methods
- [x] Set the color (RGB) of the light
- [x] Set the light brightness
- [ ] Get light infos
- [ ] Set the light power
- [ ] Color Flow control
- [ ] Proper error handling

<a name="examples"></a>
#### Examples

Imagine that the Yeelight has the IP 192.168.0.15 and is using the default port (55443), in order to change the light color, you can do this:

<a name="change-color"></a>
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
<a name="change-brightness"></a>
##### Change the light brightness

```php
<?php

require_once './vendor/autoload.php';

use SharkEzz\Yeelight\Yeelight;

$yeelight = new Yeelight('192.168.0.15');

$yeelight->setBrightness(50)
    ->commit();
```

<a name="change-many-at-once"></a>
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
<a name="testing"></a>
### Testing

If you want to test this library, make sure you have a working Yeelight RGB light in your local network.
You juste have to set the `$ip` variable in the `LightTest.php` file in the `tests` folder.
Then just run the PHPUnit executable: `./vendor/bin/phpunit`

<a name="credits"></a>
### Credits

Inspired by [Yeelight-PHP from itskenny0](https://github.com/itskenny0/Yeelight-PHP)