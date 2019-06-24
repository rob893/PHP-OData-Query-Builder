# PHP-OData-Query-Builder
This library is used to fluently build OData Query strings.
[View on Packagist.](https://packagist.org/packages/rherb/php-odata-query-builder)

## Install the Library
You can install the PHP Library with Composer.
```
composer require rherb/php-odata-query-builder
```

To create a builder, pass in the service URL and entity set into the constructor.
```php
$builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/', 'People');
```
### Build the Query String
Use the various functions followed by the buildQuery() function to generate and return the query string.
```php
$builder->from('People')->top(10)->buildQuery();
$builder->from('People')->find('bobjoe')->buildQuery();
$builder->from('Books')->filter('Cost')->lessThan(20)->buildQuery();
$builder->from('People')->filter('LastName')->equals('Smith')->buildQuery();
$builder->from('People')->filter('LastName')->equals('Smith')->andFilter('FirstName')->equals('Joe')->buildQuery();
```
Please view the test cases for more complex examples.
