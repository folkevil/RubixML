<?php

use Rubix\ML\Metrics\Distance\Minkowski;
use Rubix\ML\Metrics\Distance\Distance;
use PHPUnit\Framework\TestCase;

class MinkowskiTest extends TestCase
{
    protected $distanceFunction;

    public function setUp()
    {
        $this->distanceFunction = new Minkowski();
    }

    public function test_build_distance_function()
    {
        $this->assertTrue($this->distanceFunction instanceof Minkowski);
        $this->assertTrue($this->distanceFunction instanceof Distance);
    }

    public function test_compute_distance()
    {
        $this->assertEquals(8.6, round($this->distanceFunction->compute(['x' => 2, 'y' => 1], ['x' => 7, 'y' => 9]), 2));
    }
}
