<?php

use Rubix\ML\Metrics\Distance\Spherical;
use Rubix\ML\Metrics\Distance\Distance;
use PHPUnit\Framework\TestCase;

class SphericalTest extends TestCase
{
    protected $distanceFunction;

    public function setUp()
    {
        $this->distanceFunction = new Spherical();
    }

    public function test_build_distance_function()
    {
        $this->assertTrue($this->distanceFunction instanceof Spherical);
        $this->assertTrue($this->distanceFunction instanceof Distance);
    }

    public function test_compute_distance()
    {
        $this->assertEquals(866.71 , round($this->distanceFunction->compute(['x' => 2, 'y' => 3], ['x' => 7, 'y' => 9]), 2));
    }
}
