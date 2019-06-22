<?php

namespace Libraries\ODataQueryBuilder\Helpers;

use Libraries\ODataQueryBuilder;


class ODataOrderByBuilder {

    private $oDataQueryBuilder;
    private $property;


    public function __construct(ODataQueryBuilder $queryBuilder, string $property) {
        $this->oDataQueryBuilder = $queryBuilder;
        $this->property = $property;
    }

    public function ascending(): ODataQueryBuilder {
        $this->oDataQueryBuilder->addOrderBy($this->property, 'asc');
        
        return $this->oDataQueryBuilder;
    }

    public function descending(): ODataQueryBuilder {
        $this->oDataQueryBuilder->addOrderBy($this->property, 'desc');
        
        return $this->oDataQueryBuilder;
    }
}