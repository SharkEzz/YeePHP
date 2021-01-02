# :bulb: YeePHP

- [Introduction](#introduction)
- [Requirements](#requirements)
- [How to install](#how-to-install)
- [How does it work?](#how-it-work)
- [Currently implemented method](#implemented-methods)
- [Example](#examples)
    - [Change light color](#change-color)
    - [Change light brightness](#change-brightness)
    - [Change many parameters at once](#change-many-at-once)
    - [Toggle light](#toggle-light)
    - [Set light name](#set-light-name)
    - [Changing default port](#using-different-port)
- [Available methods](#api-methods)
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
- Require the library using Composer: `composer require sharkezz/yeephp`
- Require the composer autoloader (if it is not done yet)
- Instantiate a new Yeelight object
- Use it!

<a name="how-it-work"></a>
### How does it work ?

Yeelight-PHP use PHP sockets to communicate with your lights, that make it fast and reliable!

It can as well be used in your 'from scratch' project or in the framework of your choice!

<a name="implemented-methods"></a>
### Currently implemented methods

*This list is temporary, expect more methods to come*

- [x] Set the color (RGB) of the light
- [x] Set the light brightness
- [x] Set the light name
- [x] Set the light power (on of off)
- [x] Get light infos (partially completed)
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

use SharkEzz\Yeelight\YeePHP;

// Create a new instance of Yeelight-PHP
$yeelight = new YeePHP('192.168.0.15');

// Set the color of the light, notice that all the 'set' methods are fluent, it means that you can use them in chain !
$yeelight->setColor(0xFF0000)
    ->commit(); // The commit method has to be called at the end to send the commands to the light
```
<a name="change-brightness"></a>
##### Change the light brightness

```php
<?php

require_once './vendor/autoload.php';

use SharkEzz\Yeelight\YeePHP;

$yeelight = new YeePHP('192.168.0.15');

$yeelight->setBrightness(50)
    ->commit();
```

<a name="change-many-at-once"></a>
##### Change the color and the brightness in one line of code

```php
<?php

require_once './vendor/autoload.php';

use SharkEzz\Yeelight\YeePHP;

$yeelight = new YeePHP('192.168.0.15');

$yeelight->setBrightness(50)
    ->setColor(0x00FF00)
    ->commit();
```

<a name="toggle-light"></a>
##### Toggle the light

```php
<?php

require_once './vendor/autoload.php';

use SharkEzz\Yeelight\YeePHP;

$yeelight = new YeePHP('192.168.0.15');

// Set the light off
$yeelight->setPower('off')->commit();

// Set the light on
$yeelight->setPower('on')->commit();
```

<a name="set-light-name"></a>
##### Set the light name

```php
<?php

require_once './vendor/autoload.php';

use SharkEzz\Yeelight\YeePHP;

$yeelight = new YeePHP('192.168.0.15');

$yeelight->setName('YeelightLoremIpsum')->commit();
```

<a name="using-different-port"></a>
##### Working with a different port

All Yeelight RGB lights can be accessed by default on the port 55443, if your light doesn't use this port, no problems!

You can set your light port in the constructor like this:

```php
<?php

require_once './vendor/autoload.php';

use SharkEzz\Yeelight\YeePHP;

$yeelight = new YeePHP('192.168.0.15', 12345);
```

<a name="api-methods"></a>
### API

All the methods are documented in the class and in the interface, here it is a global reminder:

| Name                | Parameters            | Return type | Description                                                                                              |
|---------------------|-----------------------|-------------|----------------------------------------------------------------------------------------------------------|
| __construct         | string $ip  int $port | null        | Class constructor                                                                                        |
| isConnected         | x                     | bool        | Return true if the light is reacheable on the local network                                              |
| isOn                | x                     | bool        | Return true if the light is turned on                                                                    |
| getIP               | x                     | string      | Return the current light IP adress                                                                       |
| getPort             | x                     | int         | Return the current light port                                                                            |
| getBrightness       | x                     | int         | Return the current light brightness between 0 and 100                                                    |
| getColor            | x                     | string      | return an hexadecimal representation or the light color                                                  |
| getName             | x                     | string      | Return the current light name                                                                            |
| toggle              | x                     | self        | Toggle the light state                                                                                   |
| setColor            | int $hexColor         | self        | Set the light color, must be an hexadecimal string (eg: 0xFFFFFF)                                        |
| setBrightness       | int $amount           | self        | Set the light brightness, $amount must be between 0 and 100                                              |
| setName             | string $name          | self        | Set the light name                                                                                       |
| setPower            | string $power         | self        | Set the light power (off or on), $power must be "on" or "off"                                            |
| commit              | x                     | bool        | Send all the jobs (previous commands) to the light, return true if the changes has been applied          |
| disconnect          | x                     | bool        | Close the current opened socket to the light, return true if the socket has been closed, false otherwise |

<a name="testing"></a>
### Testing

If you want to test this library, make sure you have a working Yeelight RGB light in your local network with LAN control enabled.

You juste have to set the `$ip` variable in the `LightTest.php` file in the `tests` folder.

Then just run the PHPUnit executable: `./vendor/bin/phpunit`

<a name="credits"></a>
### Credits

[Official Yeelight documentation](https://www.yeelight.com/download/Yeelight_Inter-Operation_Spec.pdf)

Inspired by [Yeelight-PHP from itskenny0](https://github.com/itskenny0/Yeelight-PHP)
