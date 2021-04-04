<?php

namespace SharkEzz\Yeelight\Net;

/**
 * Class Discovery
 * @package SharkEzz\Yeelight\Net
 */
class Discovery
{
    private const DISCOVERY_REQUEST = "M-SEARCH * HTTP/1.1\r\n
    HOST: 239.255.255.250:1982\r\n
    MAN: \"ssdp:discover\"\r\n
    ST: wifi_bulb\r\n";
    private const MULTICAST_ADDRESS = "239.255.255.250";
    private const MULTICAST_PORT = 1982;

    /**
     * @var resource The socket
     */
    private $socket;

    public function __construct()
    {
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_set_block($this->socket);
    }

    public function search(): array
    {
        $result = socket_sendto(
            $this->socket,
            self::DISCOVERY_REQUEST,
            strlen(self::DISCOVERY_REQUEST),
            0,
            self::MULTICAST_ADDRESS,
            self::MULTICAST_PORT
        );

        if(!$result)
            throw new \Exception('SocketInterface exception');

        $res = socket_read($this->socket, 4096);

        if(!$res)
            throw new \Exception('SocketInterface exception');
        
        socket_close($this->socket);
        
        return [];
    }

    private function processResult(string $res): array
    {
        // TODO
        return [];
    }
}
