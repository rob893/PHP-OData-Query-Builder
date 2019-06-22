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

    public function and(): WhereBuilder {
        $this->complexFilterString .= ' and ';

        return new WhereBuilder($this);
    }

    public function or(): WhereBuilder {
        $this->complexFilterString .= ' or ';
        
        return new WhereBuilder($this);
    }

    public function closeParentheses(): ODataComplexFilterBuilder {
        $this->complexFilterString .= ')';
        
        return $this;
    }

    public function addToQuery(): ODataQueryBuilder {
        $this->validateFilter();
        
        $this->oDataQueryBuilder->addComplexFilterString($this->complexFilterString);

        return $this->oDataQueryBuilder;
    }

    private function validateFilter() {

    }
}

class WhereBuilder {
    
    private $filterBuilder;


    public function __construct(ODataComplexFilterBuilder $filterBuilder) {
        $this->filterBuilder = $filterBuilder;
    }

    public function where(string $leftOperand): ComplexFilterBuilderHelper {
        return new ComplexFilterBuilderHelper($this->filterBuilder, $leftOperand);
    }

    public function openParentheses(): WhereBuilder {
        $this->filterBuilder->append('(');
        
        return $this;
    }
}

class ComplexFilterBuilderHelper {
    
    private $complexFilterBuilder;
    private $not = false;
    private $leftOperand;
    

    public function __construct(ODataComplexFilterBuilder $complexFilterBuilder, string $leftOperand) {
        $this->complexFilterBuilder = $complexFilterBuilder;
        $this->leftOperand = $leftOperand;
    }

    public function equals($rightOperand): ODataComplexFilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append($this->leftOperand . ' eq ' . $rightOperand);

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }
        
        return $this->complexFilterBuilder;
    }

    public function notEquals($rightOperand): ODataComplexFilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append($this->leftOperand . ' ne ' . $rightOperand);

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }
        
        return $this->complexFilterBuilder;
    }

    public function greaterThan($rightOperand): ODataComplexFilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append($this->leftOperand . ' gt ' . $rightOperand);

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }
        
        return $this->complexFilterBuilder;
    }

    public function greaterThanOrEqual($rightOperand): ODataComplexFilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append($this->leftOperand . ' ge ' . $rightOperand);

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }
        
        return $this->complexFilterBuilder;
    }

    public function lessThan($rightOperand): ODataComplexFilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append($this->leftOperand . ' lt ' . $rightOperand);

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }
        
        return $this->complexFilterBuilder;
    }

    public function lessThanOrEqual($rightOperand): ODataComplexFilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append($this->leftOperand . ' le ' . $rightOperand);

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }
        
        return $this->complexFilterBuilder;
    }

    public function contains($rightOperand): ODataComplexFilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append('contains(' . $this->leftOperand . ',' . $rightOperand . ')');

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }

        return $this->complexFilterBuilder;
    }

    public function not(): ComplexFilterBuilderHelper {
        $this->complexFilterBuilder->append('not (');
        $this->not = true;
        
        return $this;
    }
}

