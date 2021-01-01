<?php

namespace SharkEzz\Yeelight\Tests;

use PHPUnit\Framework\TestCase;
use SharkEzz\Yeelight\Light;

class LightTest extends TestCase
{
    private Light $light;

    protected function setUp(): void
    {
        parent::setUp();
        /**
         * You must use a real Yeelight RGB light in order to run this tests
         */
        $this->light = new Light('192.168.0.103');
    }

    /**
     * Light must be on
     */
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
            ->setColor(0x00FF00)
            ->setBrightness(50)
            ->commit()
        );
    }
}