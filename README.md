# Validation (beta)

![license](https://img.shields.io/badge/license-MIT-brightGreen.svg)
[![build](https://travis-ci.org/originphp/validation.svg?branch=master)](https://travis-ci.org/originphp/validation)
[![coverage](https://coveralls.io/repos/github/originphp/validation/badge.svg?branch=master)](https://coveralls.io/github/originphp/validation?branch=master)

This is the new Validation library.

## Installation

To install this package

```linux
$ composer require originphp/validation
```

## Usage

For example

```php
$bool = Validation::ip('192.168.1.25');
```

## Rules

### accepted

Validates a value is accepted (checkbox is checked)

```php
Validation::accepted($_POST['accepted']);
```

### after

Validates a date is after a certain date

```php
Validation::after('2019-01-01');
Validation::after('01/01/2019','d/m/Y');
```

### alpha

Validates a string only contains alphabetic characters from the default locale

```php
Validation::alpha('abc');
```

### alphaNumeric

Validates a string only contains alphanumeric characters from the default locale

```php
Validation::alphaNumeric('abc1234');
```

### array

Validates a value is an array

```php
Validation::array([]);
```

### before

Validates a date is before a certain date

```php
Validation::before('2019-01-01');
Validation::before('01/01/2019','d/m/Y');
```

### boolean

Validates a value is a boolean type

```php
Validation::boolean(true);
```

### creditCard

Validates a credit card number

> All regex rules written from scratch using current IIN ranges, so whilst they are accurate the rules are not mature yet.

```php
Validation::creditCard('2222 9909 0525 7051');
```

### date

Validates a date using a format compatible with the PHP `DateTime` class.

```php
Validation::date('2019-01-01');
Validation::date('01/01/2019','d/m/Y');
```

### dateFormat

Validates a date using a format compatible with the PHP `DateTime` class, this is used by `Validation::date`, `Validation::time`, and `Validation::dateTime`.  

The format, which is the second argument is required

```php
Validation::dateFormat('01/01/2019','d/m/Y');
```

### dateTime

Validates a datetime using a format compatible with the PHP DateTime class.

```php
Validation::dateTime('2019-01-01 17:23:00');
Validation::dateTime('01/01/2019 17:23','d/m/Y H:i');
```

### decimal

Validates a value is a float. Alias for float

```php
Validation::decimal(0.007);
Validation::decimal('0.007');
```

### email

Validation for an email address

```php
Validation::email('foo@example.com');
```

You can also check that the email address has valid MX records using the `getmxrr` function.

```php
Validation::email('foo@example.com',true);
```

### equalTo

Validates a value is equal to another value, only values are compared.

```php
Validation::equalTo(5,5);
Validation::equalTo('5',5);
```

### extension

Validates a value has an extension. If an array is supplied it will look

```php
Validation::extension('filename.jpg',['jpg','gif']);
```

You can also check a file that has been uploaded

```php
Validation::extension($_FILES['file1'],['jpg','gif']);
```

### float

Validates a value is a float.

```php
Validation::float(0.007);
Validation::float('0.007');
```

### fqdn

Validates a string is Fully Qualified Domain Name (FQDN). 

```php
Validation::fqdn('www.originphp.com');
```

You can also check the DNS records using `checkdnsrr` to ensure that is really valid and not just looks like its valid.

```php
Validation::fqdn('www.originphp.com',true);
```

### greaterThan

Validates a value is greater than

```php
Validation::greaterThan(4,1);
```

### greaterThanOrEqual

Validates a value is greater than or equals a value.

```php
Validation::greaterThanOrEqual(4,1);
```

### hexColor

Validates a value is a hex color

```php
Validation::hexColor('#fffff');
```

### iban

Validates a value is an IBAN number

```php
Validation::iban('DE89 3704 0044 0532 0130 00');
```

### in

Validates a value is in a list

```php
Validation::in('foo',['foo','bar']);
```

### integer

Validates a value is an integer

```php
Validation::integer('1');
Validation::integer(1);
```

### ip

Validates a value is an IP Address, by default it validates as either IPV4 or IPV6.

```php
Validation::ip('192.168.1.1');
```

To validate only IPV4 or IPV6

```php
Validation::ip('192.168.1.10','ipv4');
Validation::ip('2001:0db8:85a3:0000:0000:8a2e:0370:7334','ipv6');
```

### ipRange

Validates an IP address is in a range.

```php
Validation::ipRange('192.168.1.5','192.168.168.1','192.168.1.10');
```

### json

Validates a value is a JSON string.

```php
$data = json_encode('foo');
Validation::json($data);
```

### length

Validates a string has a certain length.

```php
Validation::length('foo', 3);
```

### lessThan

Validates a value is less than another a value.

```php
Validation::lessThan(3,5);
```

### lessThanOrEqual

Validates a value is less than or equal to another a value.

```php
Validation::lessThanOrEqual(5,5);
```

### lowercase

Validates a string is in lowercase.

```php
Validation::lowercase('foo');
```

### luan

Validates a number using the LUAN algoritm. 

```php
Validation::luan('7992739871');
```

### macAddress

Validates a string is a valid mac address.

```php
Validation::macAddress('00:0b:95:9d:00:17');
```

### maxLength

Validates a string has a maximum length.

```php
Validation::maxLength('foo',3);
```

### md5

Validates a string is a MD5 hash.

```php
Validation::md5('b1816172fd2ba98f3af520ef572e3a47');
```

You can also allow it to be case insensitive

```php
Validation::md5('B1816172FD2BA98F3AF520EF572E3A47',true);
```

### mimeType

Validates a file has a particular mime type.

```php
Validation::mimeType('phpunit.xml','text/xml');
Validation::mimeType('import.csv',['text/csv''text/plain']);
Validation::mimeType($_FILES['upload1'],'application/pdf');
```

### minLength

Validates a string has a minimum length.

```php
Validation::minLength('foo',3);
```

### notBlank

Validates a value is not empty and has anything other than whitespaces.

```php
Validation::notBlank('foo');
```

### notIn

Validates a value is not in an array of values.

```php
Validation::notIn('fooz',['foo','bar']);
```

### numeric

Validates a value is an integer or a float.

```php
Validation::numeric('1');
Validation::numeric(1);
Validation::numeric(9.99);
Validation::numeric('9.99');
```

### present

Validates an array has a key.

```php
$data = ['foo'=>'bar'];
Validation::present($data,'foo');
```

### range

Validates a number or float is within a range.

```php
Validation::range(5,1,10);
Validation::range('5',1,10);
```

### regex

Validates a string using a REGEX pattern.

```php
Validation::regex('foo','/foo/');
```

### time

Validates a string is a time.

```php
Validation::time('10:20');
Validation::time('10:20:00','H:i:s');
```

### upload

Validates a file upload was successful.

```php
Validation::upload($_FILES['upload1']);
Validation::upload($_FILES['upload1']['error']);
```

You can also allow optional file upload, meaning if no file is upload then it will return true if there was no
error uploading it.

```php
Validation::upload($_FILES['upload1'],true);
```

### uppercase

Validates a string is uppercase.

```php
Validation::uppercase('FOO');
```

### url

Validates a string is an URL.

```php
Validation::url('www.example.com/q=foo');
Validation::url('http://www.google.com', true);
```

### uuid

Validates a string is a UUID

```php
Validation::uuid('10458466-a809-4e7a-b784-68d78c25d092');
```

You can also allow uppercase

```php
Validation::uuid('86E6E3FC-4924-4B5F-8BCA-E4C07F7CDDF9',true);
```