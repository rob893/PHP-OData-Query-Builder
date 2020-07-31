<?php

namespace ODataQueryBuilder\Helpers;

class OrderByHelper
{

    private $orderByBuilder;
    private $property;


    public function __construct(OrderByBuilder $orderByBuilder, string $property)
    {
        $this->orderByBuilder = $orderByBuilder;
        $this->property = $property;
    }

    public function ascending(): OrderByBuilder
    {
        $this->orderByBuilder->append($this->property . ' asc');

        return $this->orderByBuilder;
    }

    public function descending(): OrderByBuilder
    {
        $this->orderByBuilder->append($this->property . ' desc');

        return $this->orderByBuilder;
    }
}