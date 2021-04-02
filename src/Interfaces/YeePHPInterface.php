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
    public function isConnected(): bool;

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
    public function getBrightness(): string;

    /**
     * Return the current light color
     *
     * @param string $type The type of color 
     * @return string
     */
    public function getColor(string $type): string;

    /**
     * Return the current light name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Toggle the light
     *
     * @return YeePHPInterface
     */
    public function toggle(): self;

    /**
     * Set the color of the light
     *
     * @param int $color The light color value in hexadecimal, color temperature or hue (eg: 0xFFFFFF)
     * @param array $params The parameters for the color change.
     * @return $this
     */
    public function setColor(int $color, array $params): self;

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
     * @param string $name The name of the light
     * @return $this
     */
    public function setName(string $name): self;

    /**
     * Set the light on or off
     *
     * @param string $power The desired state, can be "on" or "off"
     * @return $this
     */
    public function setPower(string $power): self;

    /**
     * Save the current state to the device memory
     *
     * @return $this
     */
    public function setDefault(): self;

    /**
     * Send the parameters to the light.
     * @return bool
     */
    public function commit(): bool;

    /**
     * Close the opened socket. Return true if the socket has been closed
     *
     * @return bool
     */
    public function disconnect(): bool;
}
