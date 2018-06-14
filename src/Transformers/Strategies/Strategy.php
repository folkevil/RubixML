<?php

namespace Rubix\ML\Transformers\Strategies;

use Rubix\ML\Datasets\Dataset;

interface Strategy
{
    const EPSILON = 1e-8;

    /**
     * Fit the imputer to the feature column of the training data.
     *
     * @param  array  $values
     * @return void
     */
    public function fit(array $values) : void;

    /**
     * Guess a value.
     *
     * @return mixed
     */
    public function guess();
}
