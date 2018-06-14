<?php

namespace Rubix\ML\Metrics\Distance;

interface Distance
{
    /**
     * Compute the distance between given two coordinates.
     *
     * @param  array  $a
     * @param  array  $b
     * @return float
     */
    public function compute(array $a, array $b) : float;
}
