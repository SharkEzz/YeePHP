<?php

namespace SharkEzz\Yeelight\Tests;

use PHPUnit\Framework\TestCase;
use SharkEzz\Yeelight\YeePHP;

class LightTest extends TestCase
{
    private string $ip = '192.168.0.103';

    private YeePHP $light;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        /**
         * You must use a real Yeelight RGB light in order to run this tests and it must be turned on
         */
        $this->light = new YeePHP($this->ip);
    }

    public function testCanSeeIfLightIsOnline(): void
    {
        $this->assertTrue($this->light->isConnected());
    }

    public function testLightCanChangeColor(): void
    {
        $this->light->setColor(0xFF0000);
        $this->assertTrue($this->light->commit());
    }

    public function testLightCanChangeBrightness(): void
    {
        $this->light->setBrightness(50);
        $this->assertTrue($this->light->commit());
    }

    public function testLightCanChangeColorAndBrightness(): void
    {
        $this->assertTrue(
            $this->light
            ->setColor(0xFF8888)
            ->setBrightness(100)
            ->commit()
        );
    }

    public function testCanGetLightProps()
    {
        $this->assertNotEmpty($this->light->getName());
        $this->assertNotEmpty($this->light->getBrightness());
        $this->assertNotEmpty($this->light->getColor());
    }
}