<?php

namespace Libraries\ODataQueryBuilder\Helpers;

use Libraries\ODataQueryBuilder;


class ODataComplexFilterBuilder {

    private $oDataQueryBuilder;
    private $complexFilterString = '';


    public function __construct(ODataQueryBuilder $queryBuilder) {
        $this->oDataQueryBuilder = $queryBuilder;
    }

    public function append(string $stringToAppend): ODataComplexFilterBuilder {
        $this->complexFilterString .= $stringToAppend;

        return $this;
    }

    public function where(string $leftOperand): ComplexFilterBuilderHelper {
        return new ComplexFilterBuilderHelper($this, $leftOperand);
    }

    public function and(): ODataComplexFilterBuilder {
        $this->complexFilterString .= ' and ';
        
        return $this;
    }

    public function or(): ODataComplexFilterBuilder {
        $this->complexFilterString .= ' or ';
        
        return $this;
    }

    public function openParentheses(): ODataComplexFilterBuilder {
        $this->complexFilterString .= '(';
        
        return $this;
    }

    public function closeParentheses(): ODataComplexFilterBuilder {
        $this->complexFilterString .= ')';
        
        return $this;
    }

    public function addToQuery(): ODataQueryBuilder {
        $this->oDataQueryBuilder->addComplexFilterString($this->complexFilterString);

        return $this->oDataQueryBuilder;
    }
}

class ComplexFilterBuilderHelper {
    
    private $complexFilterBuilder;
    private $leftOperand;
    

    public function __construct(ODataComplexFilterBuilder $complexFilterBuilder, string $leftOperand) {
        $this->complexFilterBuilder = $complexFilterBuilder;
        $this->leftOperand = $leftOperand;
    }

    public function equals(string $rightOperand): ODataComplexFilterBuilder {
        $this->complexFilterBuilder->append($this->leftOperand . ' eq ' . $rightOperand);
        
        return $this->complexFilterBuilder;
    }

    public function notEquals(string $rightOperand): ODataComplexFilterBuilder {
        $this->complexFilterBuilder->append($this->leftOperand . ' ne ' . $rightOperand);
        
        return $this->complexFilterBuilder;
    }

    public function greaterThan(string $rightOperand): ODataComplexFilterBuilder {
        $this->complexFilterBuilder->append($this->leftOperand . ' gt ' . $rightOperand);
        
        return $this->complexFilterBuilder;
    }

    public function greaterThanOrEqual(string $rightOperand): ODataComplexFilterBuilder {
        $this->complexFilterBuilder->append($this->leftOperand . ' ge ' . $rightOperand);
        
        return $this->complexFilterBuilder;
    }

    public function lessThan(string $rightOperand): ODataComplexFilterBuilder {
        $this->complexFilterBuilder->append($this->leftOperand . ' lt ' . $rightOperand);
        
        return $this->complexFilterBuilder;
    }

    public function lessThanOrEqual(string $rightOperand): ODataComplexFilterBuilder {
        $this->complexFilterBuilder->append($this->leftOperand . ' le ' . $rightOperand);
        
        return $this->complexFilterBuilder;
    }
}