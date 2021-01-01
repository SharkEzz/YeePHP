<?php

namespace SharkEzz\Yeelight\Interfaces;

/**
 * Interface LightInterface
 */
interface YeePHPInterface
{
    /**
     * Return true if the light is reachable on the local network
     *
     * @return bool
     */
    public function isOnline(): bool;

    /**
     * Return true if the light is turned on, false otherwise
     *
     * @return bool
     */
    public function isOn(): bool;

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
     * Return the current light brightness
     *
     * @return int
     */
    public function getBrightness(): int;

    /**
     * Return the current light color
     *
     * @return string
     */
    public function getColor(): string;

    /**
     * Return the current light name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set the color of the light
     *
     * @param int $hexColor The light color in hexadecimal (eg: 0xFFFFFF)
     * @return $this
     */
    public function setColor(int $hexColor): self;

    /**
     * Define the desired light brightness.
     *
     * @param int $amount The brightness between 1 and 100
     * @return $this
     */
    public function setBrightness(int $amount): self;

    /**
     * Set the light name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self;

    /**
     * Send the parameters to the light.
     * @return bool
     */
    public function commit(): bool;
}