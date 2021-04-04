<?php

namespace SharkEzz\Yeelight\Net;

use SharkEzz\Yeelight\Interfaces\SocketInterface;

/**
 * Interface SocketInterface
 * Object Oriented Wrapper around PHP socket specifically designed to work with Yeelight lights
 *
 * @package SharkEzz\Yeelight\Interfaces
 */
class Socket implements SocketInterface
{
    /**
     * @var resource The socket
     */
    private $socket;

    private string $ip;

    private int $port;

    private bool $isConnected = false;

    public function __construct(string $ip, int $port = 55443)
    {
        if(!filter_var($ip, FILTER_VALIDATE_IP))
            throw new \Exception("Invalid IP address");

        $this->ip = $ip;
        $this->port = $port;
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_block($this->socket);
    }

    public function getIP(): string
    {
        return $this->ip;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function isSocketConnected(): bool
    {
        // TODO : optimize this
        return $this->isConnected;
    }

    public function connect(): bool
    {
        if(socket_connect($this->socket, $this->ip, $this->port))
        {
            $this->isConnected = true;
            return true;
        }
        else return false;
    }

    public function disconnect(): bool
    {
        $this->isConnected = false;
        socket_close($this->socket);

        return true;
    }

    public function sendData(string $data): ?array
    {
        if($data[-2] !== "\r" && $data[-1] !== "\n")
        {
            print 'not ending with :(';
            $data .= "\r\n";
        }


        if(socket_write($this->socket, $data, strlen($data)))
        {
            if($result = socket_read($this->socket, self::PACKET_LENGTH, PHP_BINARY_READ))
            {
                return json_decode($result, true);
            }
            else return null;
        }
        else
            throw new \Exception("Can't write into the socket");
    }

    public function getSocketError(): ?string
    {
        $err = socket_last_error($this->socket);
        return $err !== 0 ? socket_strerror($err) : null;
    }
}