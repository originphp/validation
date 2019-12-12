# Validation (alpha)

![license](https://img.shields.io/badge/license-MIT-brightGreen.svg)
[![build](https://travis-ci.org/originphp/validation.svg?branch=master)](https://travis-ci.org/originphp/validation)
[![coverage](https://coveralls.io/repos/github/originphp/validation/badge.svg?branch=master)](https://coveralls.io/github/originphp/validation?branch=master)

This is the new Validation library.

## Installation

To install this package

```linux
$ composer require originphp/validation
```

## Rules

### accepted

Validates a value is accepted (checkbox is checked)

```php
$result = Validation::accepted($_POST['accepted']);
```

### after

Validates a date is after a certain date

```php
$result = Validation::after('2019-01-01');
$result = Validation::after('01/01/2019','d/m/Y');
```

### alpha

Validates a string only contains alphabetic characters from the default locale

```php
$result = Validation::alpha('abc');
```

### alphaNumeric

Validates a string only contains alphanumeric characters from the default locale

```php
$result = Validation::alphaNumeric('abc1234');
```

### array

Validates a value is an array

```php
$result = Validation::array([]);
```

### before

Validates a date is before a certain date

```php
$result = Validation::before('2019-01-01');
$result = Validation::before('01/01/2019','d/m/Y');
```

### boolean

Validates a value is a boolean type

```php
$result = Validation::boolean(true);
```

### creditCard

Validates a credit card number

> All regex rules written from scratch using current IIN ranges, so whilst they are accurate the rules are not mature yet.

```php
$result = Validation::creditCard($data);
```

### date

Validates a date using a format compatible with the PHP `DateTime` class.

```php
$result = Validation::date('2019-01-01');
$result = Validation::date('01/01/2019','d/m/Y');
```

### dateFormat

Validates a date using a format compatible with the PHP `DateTime` class, this is used by the `Validation::date`, `Validation::time`, and `Validation::dateTime`.  

The format, which is the second argument is required

```php
$result = Validation::dateFormat('01/01/2019','d/m/Y');
```

### dateTime

Validates a datetime using a format compatible with the PHP DateTime class.

```php
$result = Validation::dateTime('2019-01-01 17:23:00');
$result = Validation::dateTime('01/01/2019 17:23','d/m/Y H:i');
```

### decimal

Validates a value is a float. Alias for float

```php
$result = Validation::decimal(0.007);
$result = Validation::decimal('0.007');
```

### email

Validation for an email address

```php
$result = Validation::email('foo@example.com');
```

You can also check that the email address has valid MX records using the `getmxrr` function.

```php
$result = Validation::email('foo@example.com',true);
```

### equalTo

Validates a value is equal to another value, only values are compared.

```php
$result = Validation::equalTo(5,5);
$result = Validation::equalTo('5',5);
```

### extension

Validates a value has an extension. If an array is supplied it will look

```php
$result = Validation::name('filename.jpg',['jpg','gif']);
```

You can also check a file that has been uploaded

```php
$result = Validation::name($_FILES['file1'],['jpg','gif']);
```

### float

Validates a value is a float.

```php
$result = Validation::float(0.007);
$result = Validation::float('0.007');
```

### fqdn

Validates a string is Fully Qualified Domain Name (FQDN). 

```php
$result = Validation::fqdn('www.originphp.com');
```

You can also check the DNS records using `checkdnsrr` to ensure that is really valid and not just looks like its valid.

```php
$result = Validation::fqdn('www.originphp.com',true);
```

### greaterThan

Validates a value is greater than

```php
$result = Validation::greaterThan(4,1);
```

### greaterThanOrEqual

Validates a value is greater than or equals a value.

```php
$result = Validation::greaterThanOrEqual(4,1);
```

### hexColor

Validates a value is a hex color

```php
$result = Validation::hexColor('#fffff');
```

### iban

Validates a value is an IBAN number

```php
$result = Validation::iban('DE89 3704 0044 0532 0130 00');
```

### in

Validates a value is in a list

```php
$result = Validation::in('foo',['foo','bar']);
```

### integer

Validates a value is an integer

```php
$result = Validation::integer('1');
$result = Validation::integer(1);
```

### ip

Validates a value is an IP Address, by default it validates as either IPV4 or IPV6.

```php
$result = Validation::ip($data);
```

To validate only IPV4 or IPV6

```php
$result = Validation::ip('192.168.1.1','ipv4');
$result = Validation::ip('2001:0db8:85a3:0000:0000:8a2e:0370:7334','ipv6');
```

### ipRange

Validates an IP address is in a range.

```php
$result = Validation::ipRange('192.168.1.5','192.168.168.1','192.168.1.10');
```

### json

Validates a value is a JSON string.

```php
$data = json_encode('foo');
$result = Validation::json($data);
```

### length

Validates a string has a certain length.

```php
$result = Validation::length('foo', 3);
```

### lessThan

Validates a value is less than another a value.

```php
$result = Validation::lessThan(3,5);
```

### lessThanOrEqual

Validates a value is less than or equal to another a value.

```php
$result = Validation::lessThanOrEqual(5,5);
```

### lowercase

Validates a string is in lowercase.

```php
$result = Validation::lowercase($data);
```

### luan

Validates a number using the LUAN algoritm. 

```php
$result = Validation::luan($data);
```

### macAddress

Validates a string is a valid mac address.

```php
$result = Validation::macAddress('00:0b:95:9d:00:17');
```

### maxLength

Validates a string has a maximum length.

```php
$result = Validation::maxLength('foo',3);
```

### md5

Validates a string is a MD5 hash.

```php
$result = Validation::md5('b1816172fd2ba98f3af520ef572e3a47');
```

You can also allow it to be case insensitive

```php
$result = Validation::md5('B1816172FD2BA98F3AF520EF572E3A47',true);
```

### mimeType

Validates a file has a particular mime type.

```php
$result = Validation::name('phpunit.xml','text/xml');
$result = Validation::name('import.csv',['text/csv''text/plain']);
$result = Validation::name($_FILES['upload1'],'application/pdf');
```

### minLength

```php
$result = Validation::name($data);
```

### notBlank

```php
$result = Validation::name($data);
```

### notEmpty

```php
$result = Validation::name($data);
```

### notIn

```php
$result = Validation::name($data);
```

### numeric

```php
$result = Validation::name($data);
```

### present

```php
$result = Validation::name($data);
```

### range

```php
$result = Validation::name($data);
```

### regex

```php
$result = Validation::name($data);
```

### time

```php
$result = Validation::name($data);
```

### upload

```php
$result = Validation::name($data);
```

### uppercase

```php
$result = Validation::name($data);
```

### url

```php
$result = Validation::name($data);
```

### uuid

```php
$result = Validation::name($data);
```

