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
        $leftParens = [];

        for ($i = 0; $i < strlen($this->filterString); $i++) {
            if ($this->filterString[$i] === '(') {
                array_push($leftParens, '(');
            }
            else if ($this->filterString[$i] === ')') {
                if (empty($leftParens)) {
                    throw new \Exception('Invalid parentheses.', 500);
                }
                array_pop($leftParens);
            }
        }

        if (!empty($leftParens)) {
            throw new \Exception('Invalid parentheses.', 500);
        }
    }
}

