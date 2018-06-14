<?php

namespace Rubix\ML\Classifiers;

use Rubix\ML\Supervised;
use Rubix\ML\Persistable;
use Rubix\ML\Datasets\Dataset;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Transformers\Strategies\Categorical;
use Rubix\ML\Transformers\Strategies\PopularityContest;

class DummyClassifier implements Supervised, Multiclass, Persistable
{
    /**
     * The guessing strategy that the dummy employs.
     *
     * @var \Rubix\ML\Transformers\Strategies\Categorical
     */
    protected $strategy;

    /**
     * @param  \Rubix\ML\Transformers\Strategies\Categorical  $strategy
     * @return void
     */
    public function __construct(Categorical $strategy = null)
    {
        if (!isset($strategy)) {
            $strategy = new PopularityContest();
        }

        $this->strategy = $strategy;
    }

    /**
     * Fit the training set to the given guessing strategy.
     *
     * @param  \Rubix\ML\Datasets\Labeled  $dataset
     * @return void
     */
    public function train(Labeled $dataset) : void
    {
        $this->strategy->fit($dataset->labels());
    }

    /**
     * Make a prediction of a given sample dataset.
     *
     * @param  \Rubix\ML\Datasets\Dataset  $samples
     * @return array
     */
    public function predict(Dataset $samples) : array
    {
        $predictions = [];

        foreach ($samples as $sample) {
            $predictions[] = $this->strategy->guess();
        }

        return $predictions;
    }
}
