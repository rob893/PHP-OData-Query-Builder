<?php

namespace ODataQueryBuilder\Helpers;

use ODataQueryBuilder\ODataQueryBuilder;

class OrderByHelperStart
{

    private $oDataQueryBuilder;


    public function __construct(ODataQueryBuilder $queryBuilder)
    {
        $this->oDataQueryBuilder = $queryBuilder;
    }

    public function orderBy(string $property): OrderByHelper
    {
        return new OrderByHelper(new OrderByBuilder($this->oDataQueryBuilder), $property);
    }
}