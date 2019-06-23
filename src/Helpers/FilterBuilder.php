<?php

namespace ODataQueryBuilder\Helpers;

use ODataQueryBuilder\ODataQueryBuilder;


class FilterBuilder {

    private $oDataQueryBuilder;
    private $complexFilterString = '';


    public function __construct(ODataQueryBuilder $queryBuilder) {
        $this->oDataQueryBuilder = $queryBuilder;
    }

    public function append(string $stringToAppend): FilterBuilder {
        $this->complexFilterString .= $stringToAppend;

        return $this;
    }

    public function and(): FilterBuilderStart {
        $this->complexFilterString .= ' and ';

        return new FilterBuilderStart($this);
    }

    public function or(): FilterBuilderStart {
        $this->complexFilterString .= ' or ';
        
        return new FilterBuilderStart($this);
    }

    public function closeParentheses(): FilterBuilder {
        $this->complexFilterString .= ')';
        
        return $this;
    }

    public function addToQuery(): ODataQueryBuilder {
        $this->validateFilter();
        
        $this->oDataQueryBuilder->addFilterString($this->complexFilterString);

        return $this->oDataQueryBuilder;
    }

    private function validateFilter() {

    }
}

class FilterBuilderStart {
    
    private $filterBuilder;


    public function __construct(FilterBuilder $filterBuilder) {
        $this->filterBuilder = $filterBuilder;
    }

    public function where(string $leftOperand): FilterBuilderHelper {
        return new FilterBuilderHelper($this->filterBuilder, $leftOperand);
    }

    public function openParentheses(): FilterBuilderStart {
        $this->filterBuilder->append('(');
        
        return $this;
    }
}

class FilterBuilderHelper {
    
    private $complexFilterBuilder;
    private $not = false;
    private $leftOperand;
    

    public function __construct(FilterBuilder $complexFilterBuilder, string $leftOperand) {
        $this->complexFilterBuilder = $complexFilterBuilder;
        $this->leftOperand = $leftOperand;
    }

    public function equals($rightOperand): FilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append($this->leftOperand . ' eq ' . $rightOperand);

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }
        
        return $this->complexFilterBuilder;
    }

    public function notEquals($rightOperand): FilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append($this->leftOperand . ' ne ' . $rightOperand);

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }
        
        return $this->complexFilterBuilder;
    }

    public function greaterThan($rightOperand): FilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append($this->leftOperand . ' gt ' . $rightOperand);

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }
        
        return $this->complexFilterBuilder;
    }

    public function greaterThanOrEqual($rightOperand): FilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append($this->leftOperand . ' ge ' . $rightOperand);

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }
        
        return $this->complexFilterBuilder;
    }

    public function lessThan($rightOperand): FilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append($this->leftOperand . ' lt ' . $rightOperand);

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }
        
        return $this->complexFilterBuilder;
    }

    public function lessThanOrEqual($rightOperand): FilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append($this->leftOperand . ' le ' . $rightOperand);

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }
        
        return $this->complexFilterBuilder;
    }

    public function contains($rightOperand): FilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->complexFilterBuilder->append('contains(' . $this->leftOperand . ',' . $rightOperand . ')');

        if ($this->not) {
            $this->complexFilterBuilder->append(')');
        }

        return $this->complexFilterBuilder;
    }

    public function not(): FilterBuilderHelper {
        $this->complexFilterBuilder->append('not (');
        $this->not = true;
        
        return $this;
    }
}

class SimpleFilterBuilder {

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