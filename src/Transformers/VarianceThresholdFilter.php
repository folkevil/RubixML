<?php

namespace Rubix\ML\Transformers;

use Rubix\ML\Datasets\Dataset;
use MathPHP\Statistics\Descriptive;

class VarianceThresholdFilter implements Transformer
{
    /**
     * The minimum variance a feature column must have in order to be selected.
     *
     * @var float
     */
    protected $threshold;

    /**
     * The feature columns that have been selected.
     *
     * @var array
     */
    protected $selected = [
        //
    ];

    /**
     * @param  float  $threshold
     * @return void
     */
    public function __construct(float $threshold = 0.0)
    {
        if ($threshold < 0.0) {
            throw new InvalidArgumentException('Threshold must be a positive'
                . ' value.');
        }

        $this->threshold = $threshold;
    }

    /**
     * @return array
     */
    public function selected() : array
    {
        return array_keys($this->selected);
    }

    /**
     * Chose the columns with a variance greater than the given threshold.
     *
     * @param  \Rubix\ML\Datasets\Dataset  $dataset
     * @return void
     */
    public function fit(Dataset $dataset) : void
    {
        $this->selected = [];

        foreach ($dataset->columnTypes() as $column => $type) {
            if ($type === self::CATEGORICAL) {
                $counts = array_count_values($dataset->column($column));

                $variance = Descriptive::populationVariance($counts);
            } else {
                $variance = Descriptive::populationVariance($dataset->column($column));
            }

            if ($variance > $this->threshold) {
                $this->selected[$column] = true;
            }
        }
    }

    /**
     * Transform an array of samples by removing the feature columns that did
     * not meet the variance threshold.
     *
     * @param  array  $samples
     * @return void
     */
    public function transform(array &$samples) : void
    {
        foreach ($samples as &$sample) {
            $sample = array_values(array_intersect_key($sample, $this->selected));
        }
    }
}
