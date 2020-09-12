<?php

namespace ODataQueryBuilder\Helpers;

class FilterBuilderStart extends FilterBuilderIntermediate
{

    public function prependAnd(): FilterBuilderIntermediate
    {
        $this->filterBuilder->setPrependedAndOr('and');

        return new FilterBuilderIntermediate($this->filterBuilder);
    }

    public function prependOr(): FilterBuilderIntermediate
    {
        $this->filterBuilder->setPrependedAndOr('or');

        return new FilterBuilderIntermediate($this->filterBuilder);
    }
}