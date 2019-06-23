<?php

namespace ODataQueryBuilder\Helpers;

use ODataQueryBuilder\ODataQueryBuilder;


class OrderByBuilder {

    private $oDataQueryBuilder;
    private $orderByString;


    public function __construct(ODataQueryBuilder $queryBuilder) {
        $this->oDataQueryBuilder = $queryBuilder;
    }

    public function append(string $stringToAppend): OrderByBuilder {
        $this->orderByString .= $stringToAppend;
        
        return $this;
    }

    public function thenBy(string $property): OrderByHelper {
        $this->orderByString .= ', ';

        return new OrderByHelper($this, $property);
    }

    public function addToQuery(): ODataQueryBuilder {
        $this->oDataQueryBuilder->setOrderByString($this->orderByString);
        
        return $this->oDataQueryBuilder;
    }
}

class OrderByHelperStart {

    private $oDataQueryBuilder;


    public function __construct(ODataQueryBuilder $queryBuilder) {
        $this->oDataQueryBuilder = $queryBuilder;
    }

    public function orderBy(string $property): OrderByHelper {
        return new OrderByHelper(new OrderByBuilder($this->oDataQueryBuilder), $property);
    }
}

class OrderByHelper {

    private $orderByBuilder;
    private $property;


    public function __construct(OrderByBuilder $orderByBuilder, string $property) {
        $this->orderByBuilder = $orderByBuilder;
        $this->property = $property;
    }

    public function ascending(): OrderByBuilder {
        $this->orderByBuilder->append($this->property . ' asc');
        
        return $this->orderByBuilder;
    }

    public function descending(): OrderByBuilder {
        $this->orderByBuilder->append($this->property . ' desc');
        
        return $this->orderByBuilder;
    }
}

class SimpleOrderByBuilder {

    private $oDataQueryBuilder;
    private $property;


    public function __construct(ODataQueryBuilder $queryBuilder, string $property) {
        $this->oDataQueryBuilder = $queryBuilder;
        $this->property = $property;
    }

    public function ascending(): ODataQueryBuilder {
        $this->oDataQueryBuilder->setOrderByString($this->property . ' asc');
        
        return $this->oDataQueryBuilder;
    }

    public function descending(): ODataQueryBuilder {
        $this->oDataQueryBuilder->setOrderByString($this->property . ' desc');
        
        return $this->oDataQueryBuilder;
    }
}