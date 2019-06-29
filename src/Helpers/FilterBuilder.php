<?php

namespace ODataQueryBuilder\Helpers;

use ODataQueryBuilder\ODataQueryBuilder;


class FilterBuilder {

    private $oDataQueryBuilder;
    private $filterString = '';
    private $prependedAndOr = 'and';


    public function __construct(ODataQueryBuilder $queryBuilder) {
        $this->oDataQueryBuilder = $queryBuilder;
    }

    public function append(string $stringToAppend): FilterBuilder {
        $this->filterString .= $stringToAppend;

        return $this;
    }

    public function setPrependedAndOr(string $prependedAndOr): FilterBuilder {
        if ($prependedAndOr !== 'and' && $prependedAndOr !== 'or') {
            throw new \Exception('Invalid argument. It should either be and or or.', 500);
        }

        $this->prependedAndOr = $prependedAndOr;

        return $this;
    }

    public function and(): FilterBuilderIntermediate {
        $this->filterString .= ' and ';

        return new FilterBuilderIntermediate($this);
    }

    public function or(): FilterBuilderIntermediate {
        $this->filterString .= ' or ';
        
        return new FilterBuilderIntermediate($this);
    }

    public function closeParentheses(): FilterBuilder {
        $this->filterString .= ')';
        
        return $this;
    }

    public function addToQuery(): ODataQueryBuilder {
        $this->validateFilter();
        
        $this->oDataQueryBuilder->addFilterString($this->filterString, $this->prependedAndOr);

        return $this->oDataQueryBuilder;
    }

    private function validateFilter() {
        //TODO Implement parentheses checking and stuff.
    }
}

class FilterBuilderIntermediate {
    
    protected $filterBuilder;


    public function __construct(FilterBuilder $filterBuilder) {
        $this->filterBuilder = $filterBuilder;
    }

    public function where(string $leftOperand): FilterBuilderHelper {
        return new FilterBuilderHelper($this->filterBuilder, $leftOperand);
    }

    public function openParentheses(): FilterBuilderIntermediate {
        $this->filterBuilder->append('(');
        
        return $this;
    }
}

class FilterBuilderStart extends FilterBuilderIntermediate {

    public function prependAnd(): FilterBuilderIntermediate {
        $this->filterBuilder->setPrependedAndOr('and');

        return new FilterBuilderIntermediate($this->filterBuilder);
    }

    public function prependOr(): FilterBuilderIntermediate {
        $this->filterBuilder->setPrependedAndOr('or');

        return new FilterBuilderIntermediate($this->filterBuilder);
    }
}

class FilterBuilderHelper {
    
    private $filterBuilder;
    private $not = false;
    private $leftOperand;
    

    public function __construct(FilterBuilder $filterBuilder, string $leftOperand) {
        $this->filterBuilder = $filterBuilder;
        $this->leftOperand = $leftOperand;
    }

    public function equals($rightOperand): FilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->filterBuilder->append($this->leftOperand . ' eq ' . $rightOperand);

        if ($this->not) {
            $this->filterBuilder->append(')');
        }
        
        return $this->filterBuilder;
    }

    public function notEquals($rightOperand): FilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->filterBuilder->append($this->leftOperand . ' ne ' . $rightOperand);

        if ($this->not) {
            $this->filterBuilder->append(')');
        }
        
        return $this->filterBuilder;
    }

    public function greaterThan($rightOperand): FilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->filterBuilder->append($this->leftOperand . ' gt ' . $rightOperand);

        if ($this->not) {
            $this->filterBuilder->append(')');
        }
        
        return $this->filterBuilder;
    }

    public function greaterThanOrEqual($rightOperand): FilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->filterBuilder->append($this->leftOperand . ' ge ' . $rightOperand);

        if ($this->not) {
            $this->filterBuilder->append(')');
        }
        
        return $this->filterBuilder;
    }

    public function lessThan($rightOperand): FilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->filterBuilder->append($this->leftOperand . ' lt ' . $rightOperand);

        if ($this->not) {
            $this->filterBuilder->append(')');
        }
        
        return $this->filterBuilder;
    }

    public function lessThanOrEqual($rightOperand): FilterBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }
        
        $this->filterBuilder->append($this->leftOperand . ' le ' . $rightOperand);

        if ($this->not) {
            $this->filterBuilder->append(')');
        }
        
        return $this->filterBuilder;
    }

    public function contains(string $rightOperand): FilterBuilder {
        $rightOperand = '\'' . $rightOperand . '\'';
        
        $this->filterBuilder->append('contains(' . $this->leftOperand . ',' . $rightOperand . ')');

        if ($this->not) {
            $this->filterBuilder->append(')');
        }

        return $this->filterBuilder;
    }

    public function endsWith(string $rightOperand): FilterBuilder {
        $rightOperand = '\'' . $rightOperand . '\'';
        
        $this->filterBuilder->append('endswith(' . $this->leftOperand . ',' . $rightOperand . ')');

        if ($this->not) {
            $this->filterBuilder->append(')');
        }

        return $this->filterBuilder;
    }

    public function startsWith(string $rightOperand): FilterBuilder {
        $rightOperand = '\'' . $rightOperand . '\'';
        
        $this->filterBuilder->append('startswith(' . $this->leftOperand . ',' . $rightOperand . ')');

        if ($this->not) {
            $this->filterBuilder->append(')');
        }

        return $this->filterBuilder;
    }

    public function substring(int $rightOperand): FilterBuilderHelper {
        $this->leftOperand = 'substring(' . $this->leftOperand . ',' . $rightOperand . ')';

        return $this;
    }

    public function toLower(): FilterBuilderHelper {
        $this->leftOperand = 'tolower(' . $this->leftOperand . ')';

        return $this;
    }

    public function toUpper(): FilterBuilderHelper {
        $this->leftOperand = 'toupper(' . $this->leftOperand . ')';

        return $this;
    }

    public function trim(): FilterBuilderHelper {
        $this->leftOperand = 'trim(' . $this->leftOperand . ')';

        return $this;
    }

    public function concat(string $rightOperand): FilterBuilderHelper {
        $rightOperand = '\'' . $rightOperand . '\'';

        $this->leftOperand = 'concat(' . $this->leftOperand . ',' . $rightOperand . ')';

        return $this;
    }

    public function indexOf(string $rightOperand): FilterBuilderHelper {
        $rightOperand = '\'' . $rightOperand . '\'';
        
        $this->leftOperand = 'indexof(' . $this->leftOperand . ',' . $rightOperand . ')';

        return $this;
    }

    public function length(): FilterBuilderHelper {
        $this->leftOperand = 'length(' . $this->leftOperand . ')';

        return $this;
    }

    public function not(): FilterBuilderHelper {
        $this->filterBuilder->append('not (');
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
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }

        $this->oDataQueryBuilder->addFilterString($this->leftOperand . ' eq ' . $rightOperand, $this->andOr);
        
        return $this->oDataQueryBuilder;
    }

    public function notEquals($rightOperand): ODataQueryBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }

        $this->oDataQueryBuilder->addFilterString($this->leftOperand . ' ne ' . $rightOperand, $this->andOr);
        
        return $this->oDataQueryBuilder;
    }

    public function greaterThan($rightOperand): ODataQueryBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }

        $this->oDataQueryBuilder->addFilterString($this->leftOperand . ' gt ' . $rightOperand, $this->andOr);
        
        return $this->oDataQueryBuilder;
    }

    public function greaterThanOrEqual($rightOperand): ODataQueryBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }

        $this->oDataQueryBuilder->addFilterString($this->leftOperand . ' ge ' . $rightOperand, $this->andOr);
        
        return $this->oDataQueryBuilder;
    }

    public function lessThan($rightOperand): ODataQueryBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }

        $this->oDataQueryBuilder->addFilterString($this->leftOperand . ' lt ' . $rightOperand, $this->andOr);
        
        return $this->oDataQueryBuilder;
    }

    public function lessThanOrEqual($rightOperand): ODataQueryBuilder {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }

        $this->oDataQueryBuilder->addFilterString($this->leftOperand . ' le ' . $rightOperand, $this->andOr);
        
        return $this->oDataQueryBuilder;
    }
}