<?php

namespace SharkEzz\Yeelight;

use Exception;
use SharkEzz\Yeelight\Interfaces\YeePHPInterface;

class YeePHP implements YeePHPInterface
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

    public const ALLOWED_PROPS = [
        'bright',
        'rgb',
        'name'
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
    public function getBrightness(): int
    {
        return dechex($this->getProp('bright'));
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getColor(): string
    {
        return dechex($this->getProp('rgb'));
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getName(): string
    {
        return $this->getProp('name');
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

    public function setName(string $name): YeePHPInterface
    {
        // TODO: Implement setName() method.
    }

    /**
     * @throws Exception
     */
    public function commit(): bool
    {
        $success = false;

        foreach ($this->jobs as $job)
        {
            $res = $this->makeRequest($job);
            if(!empty($res) || is_null($res))
                $success = true;
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
     * Get a certain prop value
     *
     * @param string $prop
     * @return string|null
     * @throws Exception
     */
    protected function getProp(string $prop): string
    {
        if(!in_array($prop, self::ALLOWED_PROPS))
            throw new Exception('Invalid prop supplied ' . $prop);

        $job = $this->createJobArray('get_prop', [$prop]);
        $res = $this->makeRequest($job);

        if(!$res)
            $res = '';

        return $res;
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
     * Make a request to the light
     *
     * @param array $job
     * @return string|null
     * @throws Exception
     */
    protected function makeRequest(array $job): ?string
    {
        $success = false;

        $this->checkIsOnline();

        $requestStr = json_encode($job) . "\r\n";

        fwrite($this->socket, $requestStr, strlen($requestStr));
        fflush($this->socket);

        usleep(100 * 700); // 0.7s -> wait for the light response

        $res = fgets($this->socket);

        $resultStr = null;

        if($res)
        {
            $res = json_decode($res, true);

            if(!array_key_exists('error', $res) && array_key_exists('result', $res))
                $resultStr = $res['result'][0];
        }

        return $resultStr;
    }

    /**
     * Create a new job to be committed
     *
     * @param string $method
     * @param array $params
     * @throws Exception
     */
    protected function createJob(string $method, array $params): void
    {
        $this->jobs[] = $this->createJobArray($method, $params);
    }

    private function createJobArray(string $method, array $params): array
    {
        if(!in_array($method, self::ALLOWED_METHODS))
            throw new Exception('Invalid method supplied ' . $method);

        return [
            'id' => (count($this->jobs) + 1),
            'method' => $method,
            'params' => $params
        ];
    }
}
