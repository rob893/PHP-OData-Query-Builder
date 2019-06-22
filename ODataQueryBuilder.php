<?php

namespace Libraries;

use Libraries\ODataQueryBuilder\Helpers\ODataFilterBuilder;
use Libraries\ODataQueryBuilder\Helpers\ODataComplexFilterBuilder;
use Libraries\ODataQueryBuilder\Helpers\ODataOrderByBuilder;
use Libraries\ODataQueryBuilder\Helpers\WhereBuilder;

/**
 * This class is used to build odata querys. The query is built by chaining the fluent funcitons and then the final odata url string 
 * is returned when the buildQuery() funciton is called. A new one of these classes should be instancied for each unique OData call.
 */
class ODataQueryBuilder {

    private $baseUrl = '';
    //entitySet => entity key
    private $entitySets = [];
    private $entitySetIndex = 0;
    private $filters = [];
    private $complexFilterStrings = [];
    private $selectedProperties = '';
    private $expands = '';
    private $orderBy = '';
    private $top = 0;
    private $skip = 0;
    private $count = false;
    private $encodeUrl = true;
    private $queryHasOption = false;
    private $finalQueryString = '';
    
    /**
     * Constructor. Builds an instance of the ODataQueryBuilder to be used with a single OData query.  
     * Be sure to create a new instance for every unique OData call.
     *
     * @param string $baseUrl    (Optional) This is the base url for the OData service. You can also pass in the the EntitySet at the end and it will automatcally separate them.
     * @param string $entitySet  (Optional) This is the entity set being queried for this OData call. This can also be set using the from() method.
     */
    public function __construct(string $baseUrl = '', string $entitySet = '') {
        //If the last character is not /, that means the entity set was passed in as part of the base url.
        if (substr($baseUrl, -1) !== '/') {
            $entitySet = substr($baseUrl, strrpos($baseUrl, '/') + 1);
            $baseUrl = substr($baseUrl, 0, (strlen($baseUrl) - strlen($entitySet)));
        }

        $this->baseUrl = $baseUrl;

        if ($entitySet !== '') {
            $this->entitySets[$entitySet] = null;
        }
    }

    /**
     * Sets the base url for the odata call. Do not pass in the entity set into this funciton. Use the from() method or pass it in the constructor.
     * 
     * @example $builder->setBaseUrl('http://services.odata.org/V4/TripPinService/'); 
     *
     * @param string $baseUrl
     * @return ODataQueryBuilder
     */
    public function setBaseUrl(string $baseUrl): ODataQueryBuilder {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Url encoding of the odata call is set to true by defualt. 
     * Pass in false into this function for the buildQuery() function to return the query string without url encoding.
     * 
     * @example $builder->from('People')->filter('FirstName')->equals('Bob')->encodeUrl(false)->buildQuery(); Will return the query string without urn encoding.
     *
     * @param boolean $encodeUrl
     * @return ODataQueryBuilder
     */
    public function encodeUrl(bool $encodeUrl): ODataQueryBuilder {
        $this->encodeUrl = $encodeUrl;

        return $this;
    }

    /**
     * Sets the entity set for the odata call to query.
     * 
     * @example $builder->from('People')->top(10)->buildQuery(); Will return the top 10 entities of the People entity set.
     * @example $builder->from('StorageLocSet')->top(10)->buildQuery(); Will return the top 10 entities of the StorageLocSet.
     *
     * @param string $entitySet
     * @return ODataQueryBuilder
     */
    public function from(string $entitySet): ODataQueryBuilder {
        $this->entitySets = [];
        $this->entitySetIndex = 0;
        $this->entitySets[$entitySet] = null;
        
        return $this;
    }

    public function thenFrom(string $entitySet): ODataQueryBuilder {
        $entitySetKeys = array_keys($this->entitySets);

        if (empty($this->entitySets) || $this->entitySets[$entitySetKeys[$this->entitySetIndex]] === null) {
            throw new \Exception("Invalid use of thenFrom() funciton. thenFrom() must be preceded by a find() function.", 500);
        }
        
        $this->entitySets[$entitySet] = null;
        $this->entitySetIndex++;
        
        return $this;
    }

    /**
     * Find a single entity by the entity's primary key. 
     * Use the $metadata xml document to see which property is the primary key. The primary key is in the <Key> tags.
     * 
     * @example builder->from('People')->find('bobjoe')->buildQuery(); This assumes the primary key is something like a username. This query will return the person with username of 'bobjoe'.
     *
     * @param mixed $entityKey
     * @return ODataQueryBuilder
     */
    public function find($entityKey): ODataQueryBuilder {
        $entitySetKeys = array_keys($this->entitySets);

        if (is_string($entityKey)) {
            $entityKey = '\'' . $entityKey . '\'';
        }
        
        $this->entitySets[$entitySetKeys[$this->entitySetIndex]] = '(' . $entityKey . ')';

        return $this;
    }

    /**
     * Start building a single filter. 
     * This function returns a filter builder object which then has the filter operator functions which take in the right operand and returns the query builder back. 
     * This function defaults to an and filter. 
     * NOTE: A filter builder function must be chained after calling this function in order to apply an operator and a right operand.
     * 
     * @example $builder->from('People')->filter('LastName')->equals('Smith')->buildQuery();
     * @example $builder->from('Books')->filter('Cost')->lessThan(20)->buildQuery();
     * @example $builder->from('People')->filter('LastName')->buildQuery(); WRONG!!! There must be an operator funciton called after filter()
     *
     * @param string $leftOperand
     * @return ODataFilterBuilder
     */
    public function filter(string $leftOperand): ODataFilterBuilder {
        return new ODataFilterBuilder($this, $leftOperand, 'and');
    }

    /**
     * Start building a single filter with or conditional. 
     * This function returns a filter builder object which then has the filter operator functions which take in the right operand and returns the query builder back. 
     * NOTE: A filter builder function must be chained after calling this function in order to apply an operator and a right operand.
     * 
     * @example $builder->from('People')->filter('LastName')->equals('Smith')->orFilter('LastName')->equals('Black')->buildQuery(); Returns people with LastName of Smith or Black.
     * @example $builder->from('People')->filter('LastName')->equals('Smith')->orFilter('LastName')->buildQuery(); WRONG!!! There must be an operator funciton called after filter().
     *
     * @param string $leftOperand
     * @return ODataFilterBuilder
     */
    public function orFilter(string $leftOperand): ODataFilterBuilder {
        return new ODataFilterBuilder($this, $leftOperand, 'or');
    }

    /**
     * Start building a single filter with and conditional. 
     * This function returns a filter builder object which then has the filter operator functions which take in the right operand and returns the query builder back. 
     * NOTE: A filter builder function must be chained after calling this function in order to apply an operator and a right operand.
     * 
     * @example $builder->from('People')->filter('LastName')->equals('Smith')->andFilter('FirstName')->equals('Joe')->buildQuery(); Returns people with LastName of Smith and FirstName of Joe.
     * @example $builder->from('People')->filter('LastName')->equals('Smith')->andFilter('FirstName')->buildQuery(); WRONG!!! There must be an operator funciton called after filter().
     *
     * @param string $leftOperand
     * @return ODataFilterBuilder
     */
    public function andFilter(string $leftOperand): ODataFilterBuilder {
        return new ODataFilterBuilder($this, $leftOperand, 'and');
    }

    public function filterComplex(): WhereBuilder {
        return new WhereBuilder(new ODataComplexFilterBuilder($this));
    }

    /**
     * Manually add a filter. Used by the filter builder class to support the fluent nature of this library. 
     * Can be used by anyone who does not like the fluent behavior of this library.
     * 
     * @example $builder->from('People')->addFilter('FirstName', 'eq', 'Joe', 'and')->buildQuery();
     *
     * @param string $leftOperand
     * @param string $operator
     * @param mixed $rightOperand
     * @param string $andOr Used to determine logical operator to precede this filter.
     * @return ODataQueryBuilder
     */
    public function addFilter(string $leftOperand, string $operator, $rightOperand, string $andOr): ODataQueryBuilder {
        if (!$this->isValidOdataOperator($operator)) {
            throw new \Exception('Invalid OData operator.', 400);
        }

        if ($andOr !== 'and' && $andOr !== 'or') {
            throw new \Exception('Invalid andOr parameter. It should be either and or or.', 400);
        }

        if (is_string($rightOperand)) {
            $filter = $leftOperand . ' ' . $operator . ' \'' . $rightOperand . '\'';
        }
        else {
            $filter = $leftOperand . ' ' . $operator . ' ' . $rightOperand;
        }
        
        
        $this->filters[$filter] = $andOr;

        return $this;
    }

    public function addComplexFilterString(string $complexFilterString): ODataQueryBuilder {
        $this->complexFilterStrings[] = $complexFilterString;

        return $this;
    }

    /**
     * Include the passed in navigation properties in the results. 
     * Can pass in a comma separated string to expand on multiple navigation properties.
     * 
     * @example $builder->from('People')->expand('Friends')->buildQuery();
     * @example $builder->from('People')->expand('Friends,Trips,Photos')->buildQuery();
     *
     * @param string $navigationProperty
     * @return ODataQueryBuilder
     */
    public function expand(string $navigationProperty): ODataQueryBuilder {
        if (strlen($this->expands) > 0) {
            $this->expands .= ',';
        }

        $this->expands .= $navigationProperty;
        
        return $this;
    }

    /**
     * Select one or many properties to return from the odata service. 
     * You can specify a single property or multiple by using a comma to separate the properties.
     *  
     * @example builder->from('People')->select('FirstName')->buildQuery(); Will select only the first name property.
     * @example builder->from('People')->select('FirstName,LastName,DateOfBirth')->buildQuery(); Will select FirstName, LastName, and DateOfBirth properties.
     *
     * @param string $selectedProperty
     * @return ODataQueryBuilder
     */
    public function select(string $selectedProperty): ODataQueryBuilder {
        if (strlen($this->selectedProperties) > 0) {
            $this->selectedProperties .= ',';
        }
        
        $this->selectedProperties .= $selectedProperty;

        return $this;
    }

    /**
     * Select multiple properties from an entity set using an array instead of a comma separated string.
     * 
     * @example $builder->from('People')->selectMultiple(['FirstName', 'LastName', 'DateOfBirth'])->buildQuery();
     *
     * @param array $selectedProperties
     * @return ODataQueryBuilder
     */
    public function selectMultiple(array $selectedProperties): ODataQueryBuilder {
        $this->selectedProperties .= implode(',', $selectedProperties);

        return $this;
    }

    /**
     * Add an order by clause to the query. Returns and order by builder to support fluency. 
     * Must be followed by an order by operator function.
     * 
     * @example $builder->from('People')->orderBy('FirstName')->ascending()->buildQuery();
     * @example $builder->from('People')->orderBy('FirstName')->buildQuery(); !!! WRONG !!! Missing an operator function.   
     *
     * @param string $property
     * @return ODataOrderByBuilder
     */
    public function orderBy(string $property): ODataOrderByBuilder {
        return new ODataOrderByBuilder($this, $property); //new ODataOrderByBuilder($this, $property);
    }

    /**
     * Manually add an orderBy clause. Used by the order by builder helper class to provide fluency. 
     * Can also be used by anyone who does not like the fluent nature of this library.
     * 
     * @example $builder->from('People')->addOrderBy('FirstName', 'desc')->buildQuery();
     *
     * @param string $property
     * @param string $ascDesc
     * @return ODataQueryBuilder
     */
    public function addOrderBy(string $property, string $ascDesc): ODataQueryBuilder {
        if ($ascDesc !== 'asc' && $ascDesc !== 'desc') {
            throw new \Exception('Invalid ascDesc parameter. It should be either asc or desc.', 400);
        }
        
        $this->orderBy = $property . ' ' . $ascDesc;
        
        return $this;
    }

    /**
     * Returns the passed in amount of entities from the results of the query. Similar to LIMIT in SQL.
     * 
     * @example $builder->from('People')->top(2)->buildQuery(); Will return only the top 2 results from the people set
     *
     * @param integer $top
     * @return ODataQueryBuilder
     */
    public function top(int $top): ODataQueryBuilder {
        $this->top = $top;

        return $this;
    }

    /**
     * Skip the given number of entities and return the rest. 
     * Often used with top() to achieve pagination.
     * 
     * @example $builder->from('People')->skip(10)->top(10); Skip the first 10 results and return the next 10
     *
     * @param integer $skip
     * @return ODataQueryBuilder
     */
    public function skip(int $skip): ODataQueryBuilder {
        $this->skip = $skip;
        
        return $this;
    }

    /**
     * Adds a count attribute of the total number of returned results to the results.
     * 
     * @example $builder->from('People')->count()->filter('LastName').equals('Smith')->buildQuery();
     * @example $builder->from('People')->filter('FirstName').equals('joe')->count()->buildQuery();
     *
     * @return ODataQueryBuilder
     */
    public function count(): ODataQueryBuilder {
        $this->count = true;

        return $this;
    }

    /**
     * Get root query.
     *
     * @return string
     */
    public function buildServiceRootQuery(): string {
        return $this->baseUrl;
    }

    /**
     * Get a metadata query. 
     * Pass in an entity set to get a specific metadata file. Otherwise, this query string will get the metadata file for the entire service.
     *
     * @param string $entitySet
     * @return string
     */
    public function buildMetadataQuery(string $entitySet = ''): string {
        if ($entitySet === '') {
            return $this->baseUrl . '$metadata';
        }

        return $this->baseUrl . '$metadata#' . $entitySet;
    }

    /**
     * Builds and returns the odata query string based on all of the previously called builder functions.
     * 
     * @example $builder->from('People')->find('bobjoe')->buildQuery();
     *
     * @return string
     */
    public function buildQuery(): string {
        $this->appendServiceUrlToQuery();
        $this->appendEntitySetsToQuery();
        $this->appendFiltersToQuery();
        $this->appendSelectsToQuery();
        $this->appendExpandsToQuery();
        $this->appendOrderByToQuery();
        $this->appendTopToQuery();
        $this->appendSkipToQuery();
        $this->appendCountToQuery();

        //remove the ? if the query has no query options.
        if (!$this->queryHasOption) {
            $this->finalQueryString = substr($this->finalQueryString, 0, -1);
        }

        return $this->finalQueryString;
    }

    private function appendServiceUrlToQuery() {
        $this->finalQueryString .= $this->baseUrl;
    }

    private function appendEntitySetsToQuery() {
        if (empty($this->entitySets)) {
            throw new \Exception('The query is missing an entity set!', 500);
        }

        foreach ($this->entitySets as $entitySet => $entityKey) {
            if (substr($this->finalQueryString, -1) !== '/') {
                $this->finalQueryString .= '/';
            }

            $this->finalQueryString .= $entityKey !== null ? $entitySet . $entityKey : $entitySet;
        }

        $this->finalQueryString .= '?';
    }

    private function appendFiltersToQuery() {
        //append complex filters
        foreach ($this->complexFilterStrings as $complexFilter) {
            if (!$this->queryHasOption) {
                $this->queryHasOption = true;
                $this->finalQueryString .= '$filter=';
            }

            $this->finalQueryString .= $this->encodeUrl ? urlencode($complexFilter) : $complexFilter;
        }
        
        //append simple filters
        foreach ($this->filters as $filter => $andOr) {
            if (!$this->queryHasOption) {
                $this->queryHasOption = true;
                $this->finalQueryString .= '$filter=';
                $this->finalQueryString .= $this->encodeUrl ? urlencode($filter) : $filter;
            }
            else {
                $this->finalQueryString .= $this->encodeUrl ? urlencode(' ' . $andOr . ' ' .$filter) : ' ' . $andOr . ' ' .$filter;
            }
        }
    }

    private function appendSelectsToQuery() {
        if ($this->selectedProperties !== '') {
            if ($this->queryHasOption) {
                $this->finalQueryString .= '&';
            }

            $this->queryHasOption = true;
            $this->finalQueryString .= $this->encodeUrl ? '$select=' . urlencode($this->selectedProperties) : '$select=' . $this->selectedProperties;
        }
    }

    private function appendExpandsToQuery() {
        if ($this->expands !== '') {
            if ($this->queryHasOption) {
                $this->finalQueryString .= '&';
            }

            $this->queryHasOption = true;
            $this->finalQueryString .= '$expand=';
            $this->finalQueryString .= $this->encodeUrl ?  urlencode($this->expands) : $this->expands;
        }
    }

    private function appendOrderByToQuery() {
        if ($this->orderBy !== '') {
            if ($this->queryHasOptionn) {
                $this->finalQueryString .= '&';
            }

            $this->queryHasOption = true;
            $this->finalQueryString .= $this->encodeUrl ? '$orderby=' . urlencode($this->orderBy) : '$orderby=' . $this->orderBy;
        }
    }

    private function appendCountToQuery() {
        if ($this->count) {
            if ($this->queryHasOption) {
                $this->finalQueryString .= '&';
            }

            $this->queryHasOption = true;
            $this->finalQueryString .= $this->encodeUrl ? '$count=' . urlencode('true') : '$count=' . 'true';
        }
    }

    private function appendSkipToQuery() {
        if ($this->skip > 0) {
            if ($this->queryHasOption) {
                $this->finalQueryString .= '&';
            }

            $this->queryHasOption = true;
            $this->finalQueryString .= $this->encodeUrl ? '$skip=' . urlencode($this->skip) : '$skip=' . $this->skip;
        }
    }

    private function appendTopToQuery() {
        if ($this->top > 0) {
            if ($this->queryHasOption) {
                $this->finalQueryString .= '&';
            }

            $this->queryHasOption = true;
            $this->finalQueryString .= $this->encodeUrl ? '$top=' . urlencode($this->top) : '$top=' . $this->top;
        }
    }

    /**
     * Checks if a filter operator string is a valid odata filter operator or not.
     *
     * @param string $operator
     * @return boolean
     */
    private function isValidOdataOperator(string $operator): bool {
        switch ($operator) {
            case 'eq':
                return true;
            case 'ne':
                return true;
            case 'ge':
                return true;
            case 'gt':
                return true;
            case 'lt':
                return true;
            case 'le':
                return true;
            case 'and':
                return true;
            case 'or':
                return true;
            case 'not':
                return true;
            case 'has':
                return true;
            default:
                return false;
        }
    }
}