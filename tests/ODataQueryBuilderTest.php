<?php
// ./vendor/bin/phpunit --bootstrap ./vendor/autoload.php ./tests/ODataQueryBuilderTest.php 
// Be sure to run composer dump-autoload anytime different files are required or when new classes are added to existing files.

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use ODataQueryBuilder\ODataQueryBuilder;
use PHPUnit\Framework\MockObject\Stub\Exception;

final class ODataQueryBuilderTest extends TestCase {

    public function testServiceRoot() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');
        
        $query = $builder->buildServiceRootQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/', $query);
    }

    public function testRootMetadata() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');
        
        $query = $builder->buildMetadataQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/$metadata', $query);
    }

    public function testEntitySetMetadata() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');
        
        $query = $builder->buildMetadataQuery('People');

        $this->assertEquals('http://services.odata.org/V4/TripPinService/$metadata#People', $query);
    }

    public function testFromAndFind() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');
        
        $query = $builder->from('People')->find('bob')->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People(\'bob\')', $query);
    }

    public function testFromThenFromAndFind() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');
        
        $query = $builder->from('People')->find('bob')->thenFrom('Trips')->find(0)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People(\'bob\')/Trips(0)', $query);
    }

    public function testSimpleFilter() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->filterWhere('FirstName')->equals('John')->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\'', $query);
    }

    public function testSimpleFilter2() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->filterWhere('FirstName')->equals('John')->filterWhere('Age')->lessThan(24)->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\' and Age lt 24', $query);
    }

    public function testSimpleFilter3() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->filterWhere('FirstName')->equals('John')->filterWhere('Age')->greaterThan(24)->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\' and Age gt 24', $query);
    }

    public function testSimpleFilter4() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->filterWhere('FirstName')->equals('John')->filterWhere('Age')->greaterThanOrEqual(24)->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\' and Age ge 24', $query);
    }

    public function testSimpleFilter5() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->filterWhere('FirstName')->equals('John')->filterWhere('Age')->lessThanOrEqual(24)->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\' and Age le 24', $query);
    }

    public function testSimpleOrFilter() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->filterWhere('FirstName')->equals('John')->orFilterWhere('LastName')->equals('Smith')->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\' or LastName eq \'Smith\'', $query);
    }

    public function testSimpleOrFilter2() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->orFilterWhere('FirstName')->equals('John')->orFilterWhere('LastName')->equals('Smith')->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\' or LastName eq \'Smith\'', $query);
    }

    public function testSimpleOrFilter3() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('Airports')->filterWhere('Location/City/Region')->equals('California')->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/Airports?$filter=Location/City/Region eq \'California\'', $query);
    }

    public function testSimpleAndFilter() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->filterWhere('FirstName')->equals('John')->andFilterWhere('LastName')->equals('Smith')->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\' and LastName eq \'Smith\'', $query);
    }

    public function testSimpleAndFilter2() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->andFilterWhere('FirstName')->equals('John')->andFilterWhere('LastName')->equals('Smith')->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\' and LastName eq \'Smith\'', $query);
    }

    public function testSelect() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->select('FirstName')->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$select=FirstName', $query);
    }

    public function testSelect2() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->select('FirstName')->select('LastName ')->select('MiddleName, Age, Address ,  ')->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$select=FirstName,LastName,MiddleName,Age,Address', $query);
    }

    public function testSelectCommaDel() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->select('FirstName,LastName,DateOfBirth')->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$select=FirstName,LastName,DateOfBirth', $query);
    }

    public function testSelectMultiple() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->select(['FirstName', 'LastName', 'DateOfBirth'])->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$select=FirstName,LastName,DateOfBirth', $query);
    }

    public function testTop() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->filterWhere('FirstName')->equals('John')->top(5)->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\'&$top=5', $query);
    }

    public function testSkip() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->filterWhere('FirstName')->equals('John')->skip(5)->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\'&$skip=5', $query);
    }

    public function testTopAndSkip() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->filterWhere('FirstName')->equals('John')->skip(5)->top(5)->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\'&$skip=5&$top=5', $query);
    }

    public function testCount() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->filterWhere('FirstName')->equals('John')->count()->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\'&$count=true', $query);
    }

    public function testSimpleOrderByAsc() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->filterWhere('FirstName')->equals('John')->orderBy('LastName')->ascending()->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\'&$orderby=LastName asc', $query);
    }

    public function testSimpleOrderByDesc() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $query = $builder->from('People')->filterWhere('FirstName')->equals('John')->orderBy('LastName')->descending()->encodeUrl(false)->buildQuery();

        $this->assertEquals('http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'John\'&$orderby=LastName desc', $query);
    }

    public function testComplex() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');
        
        $query = $builder->from('People')->find('bob')
            ->thenFrom('Trips')
                ->filterBuilder()
                    ->openParentheses()
                        ->where('Name')->contains('US')->or()->where('Name')->equals('TestName')
                    ->closeParentheses()
                    ->and()->where('Cost')->lessThan(3000)
                ->addToQuery()
                ->orderByBuilder()
                    ->orderBy('Name')->ascending()->thenBy('Cost')->descending()
                ->addToQuery()
            ->encodeUrl(false)
            ->buildQuery();

        $this->assertEquals(
            'http://services.odata.org/V4/TripPinService/People(\'bob\')/Trips?$filter=(contains(Name,\'US\') or Name eq \'TestName\') and Cost lt 3000&$orderby=Name asc, Cost desc',
            $query
        );
    }

    public function testComplex2() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');
        
        $query = $builder->from('People')->filterWhere('FirstName')->equals('Joe')
            ->filterBuilder()
                ->where('LastName')->toLower()->toUpper()->toLower()->toUpper()->contains('JO')
            ->addToQuery()
            ->encodeUrl(false)
            ->buildQuery();
        
        $this->assertEquals(
            'http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'Joe\' and contains(toupper(tolower(toupper(tolower(LastName)))),\'JO\')',
            $query
        );
    }

    public function testComplex3() {
        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');
        
        $query = $builder->from('People')->filterWhere('FirstName')->equals('Joe')
            ->filterBuilder()
                ->where('LastName')->toLower()->toUpper()->toLower()->toUpper()->contains('JO')
            ->addToQuery()
            ->filterWhere('LastName')->equals('Smith')
            ->filterBuilder()
                ->where('MiddleName')->length()->lessThan(4)
            ->addToQuery()
            ->encodeUrl(false)
            ->buildQuery();
        
        $this->assertEquals(
            'http://services.odata.org/V4/TripPinService/People?$filter=FirstName eq \'Joe\' and contains(toupper(tolower(toupper(tolower(LastName)))),\'JO\') and LastName eq \'Smith\' and length(MiddleName) lt 4',
            $query
        );
    }

    public function testParenValidation() {
        $this->expectException(\Exception::class);

        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $builder->from('People')->filterBuilder()
            ->where('FirstName')->equals('Joe')
            ->closeParentheses()->addToQuery()
            ->buildQuery();
    }

    public function testParenValidation2() {
        $this->expectException(\Exception::class);

        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $builder->from('People')->filterBuilder()
            ->openParentheses()
                ->where('FirstName')->equals('Joe')
            ->closeParentheses()->closeParentheses()->addToQuery()
            ->buildQuery();
    }

    public function testParenValidation3() {
        $this->expectException(\Exception::class);

        $builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/');

        $builder->from('People')->filterBuilder()
            ->openParentheses()->openParentheses()
                ->where('FirstName')->equals('Joe')
            ->closeParentheses()->addToQuery()
            ->buildQuery();
    }
}