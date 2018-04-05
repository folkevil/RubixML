<?php

namespace Rubix\Engine;

use InvalidArgumentException;
use Countable;

class SupervisedDataset implements Countable
{
    /**
     * The feature vectors or columns of a data table.
     *
     * @var array
     */
    protected $samples;

    /**
     * The labeled outcomes used for supervised training.
     *
     * @var array
     */
    protected $outcomes;

    /**
     * Build a supervised dataset used for training and testing models. The assumption
     * is the that dataset contain 0 < n < ∞ feature columns where the last column is
     * always the labeled outcome.
     *
     * @param  iterable  $data
     * @return self
     */
    public static function build(iterable $data) : self
    {
        $samples = $outcomes = [];

        foreach ($data as $row) {
            $outcomes[] = array_pop($row);
            $samples[] = array_values($row);
        }

        return new static($samples, $outcomes);
    }

    /**
     * @param  array  $samples
     * @param  array  $outcomes
     * @throws \InvalidArgumentException
     * @return void
     */
    public function __construct(array $samples, array $outcomes)
    {
        if (count($samples) !== count($outcomes)) {
            throw new InvalidArgumentException('The number of samples must equal the number of outcomes.');
        }

        foreach ($samples as &$sample) {
            if (count($sample) !== count($samples[0])) {
                throw new InvalidArgumentException('The number of feature columns must be equal for all samples.');
            }

            foreach ($sample as &$feature) {
                if (!is_string($feature) && !is_numeric($feature)) {
                    throw new InvalidArgumentException('Feature values must be a string or numeric type, ' . gettype($feature) . ' found.');
                }

                if (is_string($feature) && is_numeric($feature)) {
                    if (is_float($feature + 0)) {
                        $feature = (float) $feature;
                    } else {
                        $feature = (int) $feature;
                    }
                }
            }
        }

        $this->samples = $samples;
        $this->outcomes = $outcomes;
    }

    /**
     * @return array
     */
    public function samples() : array
    {
        return $this->samples;
    }

    /**
     * @return int
     */
    public function rows() : int
    {
        return count($this->samples);
    }

    /**
     * The number of feature columns in this dataset.
     *
     * @return int
     */
    public function columns() : int
    {
        return count($this->samples[0] ?? []);
    }

    /**
     * @return array
     */
    public function outcomes() : array
    {
        return $this->outcomes;
    }

    /**
     * All possible labeled outcomes.
     *
     * @return array
     */
    public function labels() : array
    {
        return array_unique($this->outcomes);
    }

    /**
     * Randomize the dataset.
     *
     * @return self
     */
    public function randomize() : self
    {
        $order = range(0, count($this->outcomes) - 1);

        shuffle($order);

        array_multisort($order, $this->samples, $this->outcomes);

        return $this;
    }

    /**
     * Split the dataset into two stratified subsets with a given ratio of samples.
     *
     * @param  float  $ratio
     * @return array
     */
    public function split(float $ratio = 0.5) : array
    {
        if ($ratio <= 0.0 || $ratio >= 0.9) {
            throw new InvalidArgumentException('Split ratio must be a float value between 0.0 and 0.9.');
        }

        $strata = $this->stratify($this->samples, $this->outcomes);

        $training = $testing = [0 => [], 1 => []];

        foreach ($strata[0] as $i => $stratum) {
            $testing[0] = array_merge($testing[0], array_splice($stratum, 0, round($ratio * count($stratum))));
            $testing[1] = array_merge($testing[1], array_splice($strata[1][$i], 0, round($ratio * count($strata[1][$i]))));

            $training[0] = array_merge($training[0], $stratum);
            $training[1] = array_merge($training[1], $strata[1][$i]);
        }

        return [
            new static(...$training),
            new static(...$testing),
        ];
    }

    /**
     * Take n samples and outcomes from this dataset and return them in a new dataset.
     *
     * @param  int  $n
     * @return self
     */
    public function take(int $n = 1) : self
    {
        return new static(array_splice($this->samples, 0, $n), array_splice($this->outcomes, 0, $n));
    }

    /**
     * Remove a feature column from the dataset given by the column's offset.
     *
     * @param  int  $offset
     * @return self
     */
    public function removeColumn(int $offset) : self
    {
        foreach ($this->samples as &$sample) {
            unset($sample[$offset]);

            $sample = array_values($sample);
        }
    }

    /**
     * Group samples by outcome and return an array of strata.
     *
     * @param  array  $samples
     * @param  array  $outcomes
     * @return array
     */
    public function stratify(array $samples, array $outcomes) : array
    {
        $strata = [];

        foreach ($outcomes as $i => $outcome) {
            $strata[0][$outcome][] = $samples[$i];
            $strata[1][$outcome][] = $outcome;
        }

        return $strata;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return [
            $this->samples,
            $this->outcomes,
        ];
    }

    /**
     * @return int
     */
    public function count() : int
    {
        return $this->rows();
    }
}