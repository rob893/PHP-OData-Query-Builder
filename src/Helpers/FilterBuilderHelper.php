<?php

namespace ODataQueryBuilder\Helpers;

class FilterBuilderHelper
{

    private $filterBuilder;
    private $not = false;
    private $leftOperand;


    public function __construct(FilterBuilder $filterBuilder, string $leftOperand)
    {
        $this->filterBuilder = $filterBuilder;
        $this->leftOperand = $leftOperand;
    }

    public function equals($rightOperand): FilterBuilder
    {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }

        $this->filterBuilder->append($this->leftOperand . ' eq ' . $rightOperand);

        if ($this->not) {
            $this->filterBuilder->append(')');
        }

        return $this->filterBuilder;
    }

    public function notEquals($rightOperand): FilterBuilder
    {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }

        $this->filterBuilder->append($this->leftOperand . ' ne ' . $rightOperand);

        if ($this->not) {
            $this->filterBuilder->append(')');
        }

        return $this->filterBuilder;
    }

    public function greaterThan($rightOperand): FilterBuilder
    {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }

        $this->filterBuilder->append($this->leftOperand . ' gt ' . $rightOperand);

        if ($this->not) {
            $this->filterBuilder->append(')');
        }

        return $this->filterBuilder;
    }

    public function greaterThanOrEqual($rightOperand): FilterBuilder
    {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }

        $this->filterBuilder->append($this->leftOperand . ' ge ' . $rightOperand);

        if ($this->not) {
            $this->filterBuilder->append(')');
        }

        return $this->filterBuilder;
    }

    public function lessThan($rightOperand): FilterBuilder
    {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }

        $this->filterBuilder->append($this->leftOperand . ' lt ' . $rightOperand);

        if ($this->not) {
            $this->filterBuilder->append(')');
        }

        return $this->filterBuilder;
    }

    public function lessThanOrEqual($rightOperand): FilterBuilder
    {
        if (is_string($rightOperand)) {
            $rightOperand = '\'' . $rightOperand . '\'';
        }

        $this->filterBuilder->append($this->leftOperand . ' le ' . $rightOperand);

        if ($this->not) {
            $this->filterBuilder->append(')');
        }

        return $this->filterBuilder;
    }

    public function contains(string $rightOperand): FilterBuilder
    {
        $rightOperand = '\'' . $rightOperand . '\'';

        $this->filterBuilder->append('contains(' . $this->leftOperand . ',' . $rightOperand . ')');

        if ($this->not) {
            $this->filterBuilder->append(')');
        }

        return $this->filterBuilder;
    }

    public function in(array $rightOperand): FilterBuilder
    {
        $this->filterBuilder->append($this->leftOperand . ' in (');

        $rightOperand = array_map(function ($value) {
            if (is_string($value)) {
                return '\'' . $value . '\'';
            }

            return $value;
        }, $rightOperand);

        $this->filterBuilder->append(implode(', ', $rightOperand));
        $this->filterBuilder->append(')');

        return $this->filterBuilder;
    }

    public function endsWith(string $rightOperand): FilterBuilder
    {
        $rightOperand = '\'' . $rightOperand . '\'';

        $this->filterBuilder->append('endswith(' . $this->leftOperand . ',' . $rightOperand . ')');

        if ($this->not) {
            $this->filterBuilder->append(')');
        }

        return $this->filterBuilder;
    }

    public function startsWith(string $rightOperand): FilterBuilder
    {
        $rightOperand = '\'' . $rightOperand . '\'';

        $this->filterBuilder->append('startswith(' . $this->leftOperand . ',' . $rightOperand . ')');

        if ($this->not) {
            $this->filterBuilder->append(')');
        }

        return $this->filterBuilder;
    }

    public function substring(int $rightOperand): FilterBuilderHelper
    {
        $this->leftOperand = 'substring(' . $this->leftOperand . ',' . $rightOperand . ')';

        return $this;
    }

    public function toLower(): FilterBuilderHelper
    {
        $this->leftOperand = 'tolower(' . $this->leftOperand . ')';

        return $this;
    }

    public function toUpper(): FilterBuilderHelper
    {
        $this->leftOperand = 'toupper(' . $this->leftOperand . ')';

        return $this;
    }

    public function trim(): FilterBuilderHelper
    {
        $this->leftOperand = 'trim(' . $this->leftOperand . ')';

        return $this;
    }

    public function concat(string $rightOperand): FilterBuilderHelper
    {
        $rightOperand = '\'' . $rightOperand . '\'';

        $this->leftOperand = 'concat(' . $this->leftOperand . ',' . $rightOperand . ')';

        return $this;
    }

    public function indexOf(string $rightOperand): FilterBuilderHelper
    {
        $rightOperand = '\'' . $rightOperand . '\'';

        $this->leftOperand = 'indexof(' . $this->leftOperand . ',' . $rightOperand . ')';

        return $this;
    }

    public function length(): FilterBuilderHelper
    {
        $this->leftOperand = 'length(' . $this->leftOperand . ')';

        return $this;
    }

    public function not(): FilterBuilderHelper
    {
        $this->filterBuilder->append('not (');
        $this->not = true;

        return $this;
    }
}