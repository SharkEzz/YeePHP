<?php

namespace SharkEzz\Yeelight;

use Exception;
use SharkEzz\Yeelight\Interfaces\YeelightInterface;

class Yeelight implements YeelightInterface
{
    protected string $lightIP;
    protected int $lightPort;

    protected array $jobs = [];

    public const ALLOWED_METHODS = [
        'get_prop',
        'toggle',
        'set_bright',
        'set_name',
        'set_rgb'
    ];

    /**
     * @var resource
     */
    protected $socket;

    /**
     * Light constructor.
     * @param string $ip The light IP
     * @param int $port Light port (default 55443)
     * @throws Exception
     */
    public function __construct(string $ip, int $port = 55443)
    {
        if(filter_var($ip, FILTER_VALIDATE_IP))
        {
            $this->lightIP = $ip;
        }
        else
        {
            throw new Exception('Invalid IP address : ' . $ip);
        }
        $this->lightPort = $port;

        if(!$this->connect())
            throw new Exception('Can\'t connect to Yeelight device');
    }

    public function __destruct()
    {
        fclose($this->socket);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function isOnline(): bool
    {
        return $this->checkIsOnline();
    }

    /**
     * @inheritDoc
     */
    public function isOn(): bool
    {
        // TODO: Implement isOn() method.
    }

    /**
     * @inheritDoc
     */
    public function getIP(): string
    {
        return $this->lightIP;
    }

    /**
     * @inheritDoc
     */
    public function getPort(): int
    {
        return $this->lightPort;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setColor(int $hexColor): self
    {
        $this->createJob('set_rgb', [
            $hexColor,
            'smooth',
            500
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setBrightness(int $amount): self
    {
        $this->createJob('set_bright', [
            $amount,
            'smooth',
            500
        ]);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function commit(): bool
    {
        $success = true;

        foreach ($this->jobs as $job)
        {
            if(!$this->makeRequest($job))
                $success = false;
        }

        return $success;
    }

    protected function connect(): bool
    {
        $sock = fsockopen($this->lightIP, $this->lightPort, $errCode, $errStr, 5);
        if(!$sock) return false;

        stream_set_blocking($sock, false);
        $this->socket = $sock;

        return true;
    }

    /**
     * Check if the light is online.
     *
     * @throws Exception
     */
    protected function checkIsOnline(): bool
    {
        if(feof($this->socket))
            throw new Exception('Device is offline!');

        return true;
    }

    /**
     * @param array $job
     * @return bool
     * @throws Exception
     */
    protected function makeRequest(array $job): bool
    {
        $success = false;

        $this->checkIsOnline();

        $requestStr = json_encode($job) . "\r\n";

        fwrite($this->socket, $requestStr, strlen($requestStr));
        fflush($this->socket);

        usleep(100 * 700); // 0.7s -> wait for the light response

        $res = fgets($this->socket);

        if($res)
        {
            $res = json_decode($res, true);

            if(!array_key_exists('error', $res))
                $success = true;
        }
        else
            $success = true;

        return $success;
    }

    /**
     * Create a new job to be committed
     *
     * @param string $method
     * @param array $params
     * @throws Exception
     */
    private function createJob(string $method, array $params): void
    {
        if(!in_array($method, self::ALLOWED_METHODS))
            throw new Exception('Invalid method supplied ' . $method);

        $this->jobs[] = [
            'id' => (count($this->jobs) + 1),
            'method' => $method,
            'params' => $params
        ];
    }
}
