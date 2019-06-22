<?php

namespace Libraries\ODataQueryBuilder\Helpers;

use Libraries\ODataQueryBuilder;


class ODataFilterBuilder {

    private $oDataQueryBuilder;
    private $leftOperand;
    private $andOr;


    public function __construct(ODataQueryBuilder $queryBuilder, string $leftOperand, string $andOr) {
        $this->oDataQueryBuilder = $queryBuilder;
        $this->leftOperand = $leftOperand;
        $this->andOr = $andOr;
    }

    public function equals($rightOperand): ODataQueryBuilder {
        $this->oDataQueryBuilder->addFilter($this->leftOperand, 'eq', $rightOperand, $this->andOr);
        
        return $this->oDataQueryBuilder;
    }

    public function notEquals($rightOperand): ODataQueryBuilder {
        $this->oDataQueryBuilder->addFilter($this->leftOperand, 'ne', $rightOperand, $this->andOr);
        
        return $this->oDataQueryBuilder;
    }

    public function greaterThan($rightOperand): ODataQueryBuilder {
        $this->oDataQueryBuilder->addFilter($this->leftOperand, 'gt', $rightOperand, $this->andOr);
        
        return $this->oDataQueryBuilder;
    }

    public function greaterThanOrEqual($rightOperand): ODataQueryBuilder {
        $this->oDataQueryBuilder->addFilter($this->leftOperand, 'ge', $rightOperand, $this->andOr);
        
        return $this->oDataQueryBuilder;
    }

    public function lessThan($rightOperand): ODataQueryBuilder {
        $this->oDataQueryBuilder->addFilter($this->leftOperand, 'lt', $rightOperand, $this->andOr);
        
        return $this->oDataQueryBuilder;
    }

    public function lessThanOrEqual($rightOperand): ODataQueryBuilder {
        $this->oDataQueryBuilder->addFilter($this->leftOperand, 'le', $rightOperand, $this->andOr);
        
        return $this->oDataQueryBuilder;
    }
}