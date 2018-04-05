<?php

namespace Rubix\Engine;

use Rubix\Engine\Tests\Test;

class Prototype extends Pipeline
{
    /**
     * The prototypes testing middleware stack.
     *
     * @var array
     */
    protected $tests = [
        //
    ];

    /**
     * @param  \Rubix\Engine\Estimator  $estimator
     * @param  array  $preprocessors
     * @param  array  $tests
     * @return void
     */
    public function __construct(Estimator $estimator, array $preprocessors = [], array $tests = [])
    {
        parent::__construct($estimator, $preprocessors);

        foreach ($tests as $test) {
            $this->addTest($test);
        }
    }

    /**
     * Run the tests on the prototype.
     *
     * @param  \Rubix\Engine\SupervisedDataset  $data
     * @return bool
     */
    public function test(SupervisedDataset $data) : bool
    {
        $samples = $data->samples();
        $outcomes = $data->outcomes();

        foreach ($this->preprocessors as $preprocessor) {
            $preprocessor->transform($samples);
        }

        $results = array_map(function ($test) use ($samples, $outcomes) {
            return $test->load($this->estimator)->test(new SupervisedDataset($samples, $outcomes));
        }, $this->tests);

        return !in_array(false, $results);
    }

    /**
     * Add a test afterware to the pipeline.
     *
     * @param  \Rubix\Engine\Tests\Test  $test
     * @return self
     */
    public function addTest(Test $test) : self
    {
        $this->tests[] = $test;

        return $this;
    }
}