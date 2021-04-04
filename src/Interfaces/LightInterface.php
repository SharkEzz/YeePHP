<?php

namespace SharkEzz\Yeelight\Interfaces;

use SharkEzz\Yeelight\Light\LightProperties;

interface LightInterface
{
    /**
     * Return a prop of the light
     *
     * @param string $propName The property name, defined as constants in LightProperties class
     * @param array $params The optional additional params TODO
     * @return string The property
     * @see LightProperties
     */
    public function getProp(string $propName, array $params = []): string;

    /**
     * Return the current light IP address
     *
     * @return string
     */
    public function getIP(): string;

    /**
     * Return the current light port
     *
     * @return int
     */
    public function getPort(): int;

    /**
     * Toggle the current light state
     *
     * @return bool The light state (0 is off, 1 is on)
     */
    public function toggle(): bool;
}