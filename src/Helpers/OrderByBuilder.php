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

