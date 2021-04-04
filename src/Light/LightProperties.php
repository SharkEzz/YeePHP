<?php

namespace SharkEzz\Yeelight\Light;

/**
 * Class LightProperties
 * Contain all the different light properties
 *
 * @package SharkEzz\Yeelight\Light
 */
class LightProperties
{
    public const BRIGHTNESS = 'bright',
        NAME = 'name',
        COLOR_MODE = 'color_mode',
        RGB = 'rgb',
        HUE = 'hue',
        SATURATION = 'sat';

    public const COLOR_RGB = 'rgb',
        COLOR_CR = 'ct',
        COLOR_HSV = 'hsv';
}