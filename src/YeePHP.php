<?php

declare(strict_types=1);

namespace SharkEzz\Yeelight;

use Exception;
use SharkEzz\Yeelight\Interfaces\YeePHPInterface;

class YeePHP implements YeePHPInterface
{
    /**
     * The light IP adress
     * @var string
     */
    protected string $lightIP;

    /**
     * The light port (default: 55443)
     * @var int
     */
    protected int $lightPort;

    /**
     * The array containing all the jobs to do once the commit() method has been called
     *
     * @var array
     */
    protected array $jobs = [];

    /**
     * The list of allowed methods
     */
    public const ALLOWED_METHODS = [
        'get_prop',
        'toggle',
        'set_bright',
        'set_name',
        'set_rgb',
        'set_power',
        'set_default',
        'set_ct_abx',
        'set_hsv'
    ];

    /**
     * The list of allowed props for getting the light params
     */
    public const ALLOWED_PROPS = [
        'bright',
        'rgb',
        'name',
        'power'
    ];

    /**
     * The list of allowed fade effects
     */
    public const ALLOWED_FADE_EFFECTS = [
        'smooth',
        'sudden'
    ];

    /**
     * The list of allowed fade effects
     */
    public const ALLOWED_COLOR_TYPES = [
        'ct',
        'rgb',
        'hsv'
    ];

    /**
     * The default fade effect
     */
    public const DEDAULT_FADE_EFFECT = 'smooth';

    /**
     * The default fade delay
     */
    public const DEDAULT_FADE_DELAY = 300;

    /**
     * The socket connected to the light
     *
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
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            $this->lightIP = $ip;
        } else {
            throw new Exception('Invalid IP address : ' . $ip);
        }
        $this->lightPort = $port;

        if (!$this->connect())
            throw new Exception('Can\'t connect to Yeelight device');
    }

    /**
     * Close the socket when finished
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * @inheritDoc
     */
    public function isConnected(): bool
    {
        return $this->checkIsOnline();
    }

    /**
     * @inheritDoc
     */
    public function isOn(): bool
    {
        return $this->getProp('power');
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
     */
    public function getBrightness(): string
    {
        return dechex($this->getProp('bright'));
    }

    /**
     * @inheritDoc
     */
    public function getColor(): string
    {
        return dechex($this->getProp('rgb'));
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->getProp('name');
    }

    /**
     * @inheritDoc
     */
    public function toggle(): self
    {
        $this->createJob('toggle', []);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setColor(int $color, array $params = []): self
    {
        if (!array_key_exists('type', $params))
            $params['type'] = 'rgb';
        if (!in_array($params['type'], self::ALLOWED_COLOR_TYPES, true))
            throw new Exception('Invalid color type ' . $params['type'] . ' available effects : ' . implode(" ", self::ALLOWED_COLOR_TYPES));

        $params = $this->checkColorValue($color, $params);
        $params = $this->checkFadeParams($params);

        $this->createColorJob($color, $params);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setBrightness(int $amount): self
    {
        if ($amount > 100)
            $amount = 100;
        else if ($amount < 0)
            $amount = 0;

        $this->createJob('set_bright', [
            $amount,
            'smooth',
            500
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): self
    {
        $this->createJob('set_name', [$name]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPower(string $power): self
    {
        if (!$power === 'on' || !$power === 'off')
            throw new Exception('Invalid power state: ' . $power);

        $this->createJob('set_power', [
            $power,
            self::DEDAULT_FADE_EFFECT,
            self::DEDAULT_FADE_DELAY
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDefault(): self
    {
        $this->createJob('set_default', []);

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function commit(): bool
    {
        $success = false;

        foreach ($this->jobs as $job) {
            $res = $this->makeRequest($job);
            if (!empty($res) || is_null($res))
                $success = true;
        }

        $this->jobs = [];

        return $success;
    }

    /**
     * @inheritDoc
     */
    public function disconnect(): bool
    {
        try {
            return fclose($this->socket);
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * Create socket connection to the light
     *
     * @return bool
     * @throws Exception
     */
    protected function connect(): bool
    {
        $sock = fsockopen($this->lightIP, $this->lightPort, $errCode, $errStr, 30);
        if (!$sock) return false;

        // stream_set_blocking($sock, false);
        $this->socket = $sock;

        return true;
    }

    /**
     * Get a certain prop value
     *
     * @param string $prop The prop name (refer to doc)
     * @return string|null
     * @throws Exception
     */
    protected function getProp(string $prop): string
    {
        if (!in_array($prop, self::ALLOWED_PROPS))
            throw new Exception('Invalid prop supplied ' . $prop);

        $job = $this->createJobArray('get_prop', [$prop]);
        $res = $this->makeRequest($job);

        if (!$res)
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
        if (socket_get_status($this->socket) === [])
            throw new Exception('Device is offline!');

        return true;
    }

    /**
     * Make a request to the light
     *
     * @param array $job The job created by the createJob() method
     * @return string|null
     * @throws Exception
     */
    protected function makeRequest(array $job): ?string
    {
        $this->checkIsOnline();

        $requestStr = json_encode($job) . "\r\n";

        fwrite($this->socket, $requestStr);
        fflush($this->socket);

        // usleep(100 * 1000); // 0.7s -> wait for the light response 


        $res = fgets($this->socket);


        $resultStr = null;

        if (!empty($res)) {
            $res = json_decode($res, true);

            if (!array_key_exists('error', $res) && array_key_exists('result', $res))
                $resultStr = $res['result'][0];
        }

        var_dump($requestStr, $res);

        return $resultStr;
    }

    /**
     * Create a new job to be committed
     *
     * @param string $method The method
     * @param array $params The params
     * @throws Exception
     */
    protected function createJob(string $method, array $params): void
    {
        $this->jobs[] = $this->createJobArray($method, $params);
    }




    /**
     * Convert an method and his params into a job array
     *
     * @return array
     * @throws Exception
     */
    private function createJobArray(string $method, array $params): array
    {
        if (!in_array($method, self::ALLOWED_METHODS))
            throw new Exception('Invalid method supplied ' . $method);

        return [
            'id' => !empty($this->jobs) ? count($this->jobs) : 0,
            'method' => $method,
            'params' => array_filter($params, fn ($value) => !is_null($value))
        ];
    }

    /**
     * Check if the color value is correct
     * 
     * @param int $value the color value
     * @param array $params The parameters for the color change.
     * @return array
     * @throws Exception
     */
    private function checkColorValue(int $value, array $params)
    {
        switch ($params['type']) {
            case 'ct': {
                    if ($value < 1700 || $value > 6500)
                        throw new Exception('Invalid color value ' . $value . '! value must be range from 1700 to 6500');
                };
                break;
            case 'rgb': {
                    if ($value < 0 || $value > 16777215)
                        throw new Exception('Invalid color value ' . $value . '! value must be range from 0 to 16777215');
                };
                break;
            case 'hsv': {
                    if ($value < 0 || $value > 359)
                        throw new Exception('Invalid color value ' . $value . '! value must be range from 0 to 359');
                    if (!array_key_exists('sat', $params)) {
                        $params['sat'] = 50;
                    } else {
                        if ($params['sat'] > 100)
                            $params['sat'] = 100;
                        else if ($params['sat'] < 0)
                            $params['sat'] = 0;
                    }
                };
                break;
            default:
                throw new Exception('Invalid color type !');
        }
        return $params;
    }

    /**
     * Check if the fade params are correct
     * 
     * @param array $params The fade parameters.
     * @param string $defaultFadeEffect The default fade effect value.
     * @return array
     * @throws Exception
     */
    private function checkFadeParams(array $params, string $defaultFadeEffect = self::DEDAULT_FADE_EFFECT)
    {
        if (array_key_exists('effect', $params)) {
            if (!in_array($params['effect'], self::ALLOWED_FADE_EFFECTS))
                throw new Exception('Invalid effect value ! available effects : ' . implode(" ", self::ALLOWED_FADE_EFFECTS));
        } else {
            $params['effect'] = $defaultFadeEffect;
        }
        if ($params['effect'] === 'sudden') {
            if (array_key_exists('delay', $params)) {
                unset($params['delay']);
            }
            return $params;
        } else {
            if (array_key_exists('delay', $params)) {
                if ($params['delay'] < 30 || $params['delay'] > 3000)
                    throw new Exception('Invalid delay value ' . $params['delay'] . '! value must be range from 30 to 3000');
            } else {
                $params['delay'] = self::DEDAULT_FADE_DELAY;
            }
        }

        return $params;
    }

    /**
     * Create a job for color changing
     * 
     * @param int $value The light value color in hexadecimal, color temperature or hue (eg: 0xFFFFFF)
     * @param array $params The parameters for the color change.
     * @return void
     * @throws Exception
     */
    private function createColorJob(int $value, array $params)
    {

        switch ($params['type']) {
            case 'ct':
                $this->createJob('set_ct_abx', [
                    $value,
                    $params['effect'],
                    (isset($params['delay']) ? $params['delay'] : 0)
                ]);
                break;
            case 'rgb':
                $this->createJob('set_rgb', [
                    $value,
                    $params['effect'],
                    (isset($params['delay']) ? $params['delay'] : 0)
                ]);
                break;
            case 'hsv':
                $this->createJob('set_hsv', [
                    $value,
                    $params['sat'],
                    $params['effect'],
                    (isset($params['delay']) ? $params['delay'] : 0)
                ]);
                break;
            default:
                throw new Exception('Invalid color type !');
        }
    }
}
