<?php

namespace SharkEzz\Yeelight\Interfaces;

/**
 * Interface LightInterface
 */
interface LightInterface
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
     * @param int $hexColor
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
     * Send the parameters to the light.
     * @return bool
     */
    public function commit(): bool;
}