<?php

namespace ODataQueryBuilder\Helpers;

use ODataQueryBuilder\ODataQueryBuilder;


class SearchBuilder {

    private $odataQueryBuilder;
    private $searchString = '';


    public function __construct(ODataQueryBuilder $odataQueryBuilder) {
        $this->odataQueryBuilder = $odataQueryBuilder;
    }
}