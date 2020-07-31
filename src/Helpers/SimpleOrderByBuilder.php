<?php

namespace ODataQueryBuilder\Helpers;

use ODataQueryBuilder\ODataQueryBuilder;

class SimpleOrderByBuilder
{

    private $oDataQueryBuilder;
    private $property;


    public function __construct(ODataQueryBuilder $queryBuilder, string $property)
    {
        $this->oDataQueryBuilder = $queryBuilder;
        $this->property = $property;
    }

    public function ascending(): ODataQueryBuilder
    {
        $this->oDataQueryBuilder->setOrderByString($this->property . ' asc');

        return $this->oDataQueryBuilder;
    }

    public function descending(): ODataQueryBuilder
    {
        $this->oDataQueryBuilder->setOrderByString($this->property . ' desc');

        return $this->oDataQueryBuilder;
    }
}