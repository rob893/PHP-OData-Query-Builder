# PHP-OData-Query-Builder
This library is used to fluently build OData Query strings. 

To create a builder, pass in the service URL and entity set into the constructor.
$builder = new ODataQueryBuilder('http://services.odata.org/V4/TripPinService/', 'People');

Use the various functions followed by the buildQuery() function to generate and return the query string.
$builder->from('People')->top(10)->buildQuery();
$builder->from('People')->find('bobjoe')->buildQuery();
$builder->from('Books')->filter('Cost')->lessThan(20)->buildQuery();
$builder->from('People')->filter('LastName')->equals('Smith')->buildQuery();
$builder->from('People')->filter('LastName')->equals('Smith')->andFilter('FirstName')->equals('Joe')->buildQuery();
etc.