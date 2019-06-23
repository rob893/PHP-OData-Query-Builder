<?php

namespace Libraries\ODataQueryBuilder\Helpers;

use Libraries\ODataQueryBuilder;


class SearchBuilder {

    private $odataQueryBuilder;
    private $searchString = '';


    public function __construct(ODataQueryBuilder $odataQueryBuilder) {
        $this->odataQueryBuilder = $odataQueryBuilder;
    }
}