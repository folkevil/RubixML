<?php

namespace Rubix\ML\Extractors\Tokenizers;

interface Tokenizer
{
    /**
     * Tokenize a string.
     *
     * @param  string  $string
     * @return array
     */
    public function tokenize(string $string) : array;
}
