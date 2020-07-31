<?php

namespace ODataQueryBuilder\Helpers;

class FilterBuilderIntermediate
{

    protected $filterBuilder;


    public function __construct(FilterBuilder $filterBuilder)
    {
        $this->filterBuilder = $filterBuilder;
    }

    public function where(string $leftOperand): FilterBuilderHelper
    {
        return new FilterBuilderHelper($this->filterBuilder, $leftOperand);
    }

    public function openParentheses(): FilterBuilderIntermediate
    {
        $this->filterBuilder->append('(');

        return $this;
    }
}