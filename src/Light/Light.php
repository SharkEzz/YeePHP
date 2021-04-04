<?php

namespace SharkEzz\Yeelight\Light;

use SharkEzz\Yeelight\Interfaces\LightInterface;
use SharkEzz\Yeelight\Net\Socket;

/**
 * Class Light
 * Represent a light
 *
 * @package SharkEzz\Yeelight\Light
 */
class Light implements LightInterface
{
    private string $ip;

    private string $port;

    private Socket $socket;

    public function __construct(string $ip, int $port = 55443)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->socket = new Socket($ip, $port);
    }

    /**
     * @inheritDoc
     */
    public function getProp(string $propName, array $params = []): string
    {
        // TODO: Implement getProp() method.
    }

    /**
     * @inheritDoc
     */
    public function getIP(): string
    {
        return $this->ip;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): int
    {
        return $this->port;
    }

    /**
     * @inheritDoc
     */
    public function toggle(): bool
    {
        // TODO: Implement toggle() method.
    }
}