<?php

namespace SharkEzz\Yeelight\Tests;

use PHPUnit\Framework\TestCase;
use SharkEzz\Yeelight\Yeelight;

class LightTest extends TestCase
{
    private Yeelight $light;

    protected function setUp(): void
    {
        parent::setUp();
        /**
         * You must use a real Yeelight RGB light in order to run this tests and it must be turned on
         */
        $this->light = new Yeelight('192.168.0.103');
    }

    public function testCanSeeIfLightIsOnline(): void
    {
        $this->assertTrue($this->light->isOnline());
    }

    public function testLightCanChangeColor(): void
    {
        $this->light->setColor(0xFF8888);
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
}