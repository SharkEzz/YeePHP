<?php

namespace SharkEzz\Yeelight\Interfaces;

interface SocketInterface
{
    /**
     * The standard Yeelight packet length
     *
     * @var string
     */
    public const PACKET_LENGTH = 4096;

    /**
     * Return the current IP
     *
     * @return string
     */
    public function getIP(): string;

    /**
     * Return the current port number
     *
     * @return int
     */
    public function getPort(): int;

    /**
     * Return the current status of the socket.
     *
     * @return bool The socket status, true is connected, false otherwise
     */
    public function isSocketConnected(): bool;

    /**
     * Initiate a connection between the socket and the light
     *
     * @return bool True if the socket is connected, false otherwise
     */
    public function connect(): bool;

    /**
     * Close the connection to the light and the socket
     *
     * @return bool True if the socket is disconnected, false otherwise
     */
    public function disconnect(): bool;

    /**
     * Write data to the socket
     *
     * @param string $data
     * @return bool True if the data has been successfully wrote, false otherwise
     */
    public function write(string $data): bool;

    /**
     * Read and return the data in the socket buffer
     *
     * @return string|null Return the read data, null if there is none
     */
    public function read(): ?string;

    /**
     * Get the latest socket error (if there is one)
     *
     * @return string|null
     */
    public function getSocketError(): ?string;
}