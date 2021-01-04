<?php
/**
 * OriginPHP Framework
 * Copyright 2018 - 2021 Jamiel Sharief.
 *
 * Licensed under The MIT License
 * The above copyright notice and this permission notice shall be included in all copies or substantial
 * portions of the Software.
 *
 * @copyright   Copyright (c) Jamiel Sharief
 * @link        https://www.originphp.com
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Origin\Validation\Test;

use Origin\Validation\Validation;
use Origin\Validation\Validator;

class ValidationTest extends \PHPUnit\Framework\TestCase
{
    public function testAccepted()
    {
        $this->assertTrue(Validation::accepted(true));
        $this->assertTrue(Validation::accepted(1));
        $this->assertFalse(Validation::accepted(true, [1]));
        $this->assertFalse(Validation::accepted(0));
        $this->assertFalse(Validation::accepted(null));
    }

    public function testAfter()
    {
        $this->assertFalse(Validation::after('foo', 'tomorrow'));
        $this->assertFalse(Validation::after('today', 'tomorrow'));
        $this->assertTrue(Validation::after('tomorrow', 'today'));
        $this->assertFalse(Validation::after(null, 'tomorrow'));
    }

    public function testArray()
    {
        $this->assertTrue(Validation::array(['foo' => 'bar']));
        $this->assertFalse(Validation::array((object) ['foo' => 'bar']));
        $this->assertFalse(Validation::array(null));
    }

    public function testBefore()
    {
        $this->assertFalse(Validation::before('foo', 'now'));
        $this->assertTrue(Validation::before('today', 'tomorrow'));
        $this->assertFalse(Validation::before('tomorrow', 'today'));
        $this->assertFalse(Validation::before(null, 'today'));
    }
    public function testAlpha()
    {
        $this->assertTrue(Validation::alpha('foo'));
        $this->assertFalse(Validation::alpha('f00'));
        $this->assertFalse(Validation::alpha(null));
    }

    public function testAlphaNumeric()
    {
        $this->assertTrue(Validation::alphaNumeric('foo123'));
        $this->assertFalse(Validation::alphaNumeric('foo-'));
        $this->assertFalse(Validation::alphaNumeric(null));
    }

    public function testBoolean()
    {
        $this->assertFalse(Validation::boolean(null));

        $this->assertTrue(Validation::boolean(true));
        $this->assertTrue(Validation::boolean(false));
        $this->assertTrue(Validation::boolean(1));
        $this->assertTrue(Validation::boolean(0));
        $this->assertTrue(Validation::boolean('1'));
        $this->assertTrue(Validation::boolean('0'));
    }

    public function testDate()
    {
        $this->assertTrue(Validation::date('2017-01-01'));
        $this->assertFalse(Validation::date('2017-01-32'));

        $this->assertTrue(Validation::date('01-01-2017', 'd-m-Y'));
        $this->assertFalse(Validation::date('01-14-2017', 'd-m-Y'));

        $this->assertTrue(Validation::date('01/01/2017', 'd/m/Y'));
        $this->assertFalse(Validation::date('01/14/2017', 'd/m/Y'));
        $this->assertFalse(Validation::date(null, 'd/m/Y'));
    }

    public function testDateTime()
    {
        $this->assertTrue(Validation::datetime('2017-01-01 17:32:00'));
        $this->assertFalse(Validation::datetime('2017-01-01 28:32:00'));
        $this->assertFalse(Validation::datetime(null));
    }

    public function testDecimal()
    {
        $this->assertTrue(Validation::decimal(256.0));
        $this->assertTrue(Validation::decimal('512.00'));
        $this->assertTrue(Validation::decimal(1024.256));
        $this->assertTrue(Validation::decimal('2048.512'));
        $this->assertFalse(Validation::decimal(32));
        $this->assertFalse(Validation::decimal(64));
        $this->assertFalse(Validation::decimal(null));
    }

    public function testFloat()
    {
        $this->assertTrue(Validation::float(123.45));
        $this->assertTrue(Validation::float('123.45'));
        $this->assertFalse(Validation::float(123456));
        $this->assertFalse(Validation::float('one'));
        $this->assertFalse(Validation::float('12345'));
        $this->assertFalse(Validation::float(null));
    }

    public function testLuhn()
    {
        $this->assertFalse(Validation::luhn('79927398714'));
        $this->assertTrue(Validation::luhn('79927398713'));
       
        $this->assertTrue(Validation::luhn('4556737586899855'));
        $this->assertFalse(Validation::luhn(null));
    }
    //19.117.63.126
    public function testIp()
    {
        $this->assertTrue(Validation::ip('19.117.63.126'));
        $this->assertTrue(Validation::ip('19.117.63.126', 'ipv4'));
        $this->assertFalse(Validation::ip('19.117.63.126', 'ipv6'));
        $this->assertFalse(Validation::ip(null));
        
        $this->assertTrue(Validation::ip('684D:1111:222:3333:4444:5555:6:77'));
        $this->assertTrue(Validation::ip('684D:1111:222:3333:4444:5555:6:77', 'ipv6'));
        $this->assertFalse(Validation::ip('684D:1111:222:3333:4444:5555:6:77', 'ipv4'));
    }

    public function testIpRange()
    {
        $this->assertFalse(Validation::ipRange(null, '192.168.1.1', '192.168.1.10'));
        $this->assertTrue(Validation::ipRange('192.168.1.7', '192.168.1.1', '192.168.1.10'));
        $this->assertFalse(Validation::ipRange('192.168.1.1', '192.168.1.7', '192.168.1.10'));
        $this->assertFalse(Validation::ipRange('foo', '192.168.1.1', '192.168.1.10'));
    }

    public function testInteger()
    {
        $this->assertTrue(Validation::integer(1));
        $this->assertTrue(Validation::integer('1'));
        $this->assertFalse(Validation::integer('one'));
        $this->assertFalse(Validation::integer(1.2));
        $this->assertFalse(Validation::integer('12-000'));
        $this->assertFalse(Validation::integer('1234.56'));
        $this->assertFalse(Validation::integer(10.00));
        $this->assertFalse(Validation::integer('10.00'));
        $this->assertFalse(Validation::integer(-10.00));
        $this->assertFalse(Validation::integer('-10.00'));
        $this->assertFalse(Validation::integer(true));
        $this->assertFalse(Validation::integer(null));
    }

    public function testIban()
    {
        $this->assertTrue(Validation::iban('GB33BUKB20201555555555'));
        $this->assertFalse(Validation::iban('FOO'));
        $this->assertFalse(Validation::iban(null));
        $this->assertFalse(Validation::iban('GB33BUKB20201555555554'));
    }

    /**
     * Note: Check before adding cards that they are valid, not outdated.
     */
    public function testCreditCard()
    {
        $this->assertFalse(Validation::creditCard(null));
        $this->assertTrue(Validation::creditCard('5500 0000 0000 0004'));
        $this->assertTrue(Validation::creditCard(5500000000000004));

        $this->assertFalse(Validation::creditCard('5500 0000 0000 0004', 'foo')); // invalid type
        $this->assertFalse(Validation::creditCard('1234 5678 9012 3456')); // invalid card number
        $this->assertFalse(Validation::creditCard('5500 0000 0000 0002')); // luhn failure

        # Amex
        $this->assertTrue(Validation::creditCard('370000000000002', 'amex'));
        $this->assertTrue(Validation::creditCard('340000000000009', 'amex'));
        $this->assertFalse(Validation::creditCard('3400000000000018', 'amex'));

        # Diners
        $this->assertTrue(Validation::creditCard('36000000000008', 'diners'));
        $this->assertTrue(Validation::creditCard('360000000000004', 'diners'));
        $this->assertTrue(Validation::creditCard('3600000000000008', 'diners'));
        $this->assertTrue(Validation::creditCard('36000000000000004', 'diners'));
        $this->assertTrue(Validation::creditCard('360000000000000008', 'diners'));
        $this->assertTrue(Validation::creditCard('3600000000000000004', 'diners'));
        $this->assertFalse(Validation::creditCard('36000000000000000008', 'diners'));

        $this->assertTrue(Validation::creditCard('5400000000000005', 'diners'));
        $this->assertTrue(Validation::creditCard('5500000000000004', 'diners'));
       
        $this->assertTrue(Validation::creditCard('3000000000000004', 'diners'));
        $this->assertTrue(Validation::creditCard('3010000000000002', 'diners'));
        $this->assertTrue(Validation::creditCard('3020000000000018', 'diners'));
        $this->assertTrue(Validation::creditCard('3030000000000008', 'diners'));
        $this->assertTrue(Validation::creditCard('3040000000000006', 'diners'));
        $this->assertTrue(Validation::creditCard('3050000000000003', 'diners'));

        $this->assertTrue(Validation::creditCard('3800000000000006', 'diners'));
        $this->assertTrue(Validation::creditCard('3900000000000005', 'diners'));
        $this->assertTrue(Validation::creditCard('3095000000000018', 'diners'));
       
        // Test mastercard range 2221-2720 using various regex patterns
        $this->assertTrue(Validation::creditCard('2221000000000009', 'mastercard'));
        $this->assertTrue(Validation::creditCard('2222000000000008', 'mastercard'));
        $this->assertTrue(Validation::creditCard('2700000000000009', 'mastercard'));
        $this->assertTrue(Validation::creditCard('2720000000000005', 'mastercard'));
        $this->assertTrue(Validation::creditCard('2367000000000003', 'mastercard'));

        // test mastercard range 51/55 pattern
        $this->assertTrue(Validation::creditCard('5200000000000007', 'mastercard'));
      
        // Put some valid cards through
        $this->assertTrue(Validation::creditCard('5500 0000 0000 0004', 'mastercard'));
        $this->assertTrue(Validation::creditCard('5424000000000015', 'mastercard'));
        $this->assertTrue(Validation::creditCard('5125 8600 0000 0006', 'mastercard'));
        $this->assertTrue(Validation::creditCard('5555555555554444', 'mastercard'));
        
        $this->assertTrue(Validation::creditCard('4111 1111 1111 1111', 'visa'));
        $this->assertTrue(Validation::creditCard('4002 6200 0000 0005', 'visa'));

        $this->assertTrue(Validation::creditCard('6011 0000 0000 0004', 'discover'));
        
        $this->assertTrue(Validation::creditCard('3530111333300000', 'jcb'));
        
        $this->assertTrue(Validation::creditCard('6304000000000000', 'maestro'));

        // Valid card but for a different type
        $this->assertFalse(Validation::creditCard('5500 0000 0000 0004', 'visa'));
    }

    public function testEmail()
    {
        $this->assertTrue(Validation::email('foo@originphp.com'));
        $this->assertTrue(Validation::email('foo@some-random-domain-that-definietley-does-not-exist.com'));
        $this->assertFalse(Validation::email('foo@some-random-domain-that-definietley-does-not-exist.com', true));
        $this->assertFalse(Validation::email(null));
    }

    public function testExtension()
    {
        $this->assertTrue(Validation::extension('bootstrap.css', 'css'));
        $this->assertTrue(Validation::extension('bootstrap.css', ['js', 'css']));
        $this->assertTrue(Validation::extension('Logo.JPG', ['gif', 'png', 'jpg']));
        $this->assertFalse(Validation::extension('bootstrap.js', 'css'));

        $this->assertFalse(Validation::extension(null, 'css'));

        $post = ['name' => 'bootstrap.css'];
        $this->assertTrue(Validation::extension($post, 'css'));
    }

    public function testJson()
    {
        $this->assertTrue(Validation::json('{"foo":"bar"}'));
        $this->assertFalse(Validation::json('{"foo":."bar"}'));
        $this->assertFalse(Validation::json(''));
        $this->assertFalse(Validation::json(null));
    }

    public function testEqualTo()
    {
        $this->assertTrue(Validation::equalTo(5, '5'));
        $this->assertTrue(Validation::equalTo(null, null));
        $this->assertFalse(Validation::equalTo(10, 5));
        $this->assertFalse(Validation::equalTo(null, 5));
        $this->assertFalse(Validation::equalTo(10, null));
    }

    public function testGreaterThan()
    {
        $this->assertTrue(Validation::greaterThan(2, 1));
        $this->assertFalse(Validation::greaterThan(2, 2));
        $this->assertFalse(Validation::greaterThan(null, 2));
    }

    public function testLessThan()
    {
        $this->assertTrue(Validation::lessThan(1, 2));
        $this->assertFalse(Validation::lessThan(2, 2));
    }

    public function testUppercase()
    {
        $this->assertTrue(Validation::uppercase('FOO'));
        $this->assertFalse(Validation::uppercase('FOo'));
        $this->assertFalse(Validation::uppercase(''));
        $this->assertFalse(Validation::uppercase(null));
    }

    public function testLowercase()
    {
        $this->assertTrue(Validation::lowercase('foo'));
        $this->assertFalse(Validation::lowercase('Foo'));
        $this->assertFalse(Validation::lowercase(''));
        $this->assertFalse(Validation::lowercase(null));
    }

    public function testGreaterThanEquals()
    {
        $this->assertTrue(Validation::greaterThanOrEqual(2, 1));
        $this->assertTrue(Validation::greaterThanOrEqual(2, 2));
        $this->assertFalse(Validation::greaterThanOrEqual(2, 3));
    }

    public function testLessThanEquals()
    {
        $this->assertTrue(Validation::lessThanOrEqual(1, 2));
        $this->assertTrue(Validation::lessThanOrEqual(2, 2));
        $this->assertFalse(Validation::lessThanOrEqual(3, 2));
    }

    public function testHexColor()
    {
        $this->assertTrue(Validation::hexColor('#f5f5f5'));
        $this->assertFalse(Validation::hexColor('#f5f')); // this can be the case but now validate its not
        $this->assertFalse(Validation::hexColor(null));
    }

    public function testInList()
    {
        $this->assertTrue(Validation::in('new', ['draft', 'new', 'published']));
        $this->assertFalse(Validation::in('dropped', ['draft', 'new', 'published']));
        $this->assertFalse(Validation::in(null, ['draft', 'new', 'published']));
    }

    public function testNotInList()
    {
        $this->assertFalse(Validation::notIn('new', ['draft', 'new', 'published']));
        $this->assertFalse(Validation::notIn(null, [null, 'new', 'published']));
        $this->assertTrue(Validation::notIn('dropped', ['draft', 'new', 'published']));
    }

    public function testLength()
    {
        $this->assertTrue(Validation::length('foo', 3));
        $this->assertFalse(Validation::length('foo', 2));
        $this->assertFalse(Validation::length(null, 2));
    }

    public function testFQDN()
    {
        // Check if it looks like FQDN
        $this->assertTrue(Validation::fqdn('google.com'));
        $this->assertTrue(Validation::fqdn('google.co.uk'));
        $this->assertTrue(Validation::fqdn('ncsc.gov.uk'));

        // Check invalid
        $this->assertFalse(Validation::fqdn('https://www.google.co.uk'));
        $this->assertFalse(Validation::fqdn(null));

        // check using DNS records
        $this->assertTrue(Validation::fqdn('www.google.com', true));
        $this->assertFalse(Validation::fqdn('dota2.google.com', true));
    }

    public function testPresent()
    {
        $this->assertTrue(Validation::present(['foo' => null], 'foo'));
        $this->assertTrue(Validation::present(['foo' => 'bar'], 'foo'));
        $this->assertFalse(Validation::present(['foo' => 'bar'], 'bar'));
        $this->assertFalse(Validation::present([], 'bar'));
        $this->assertFalse(Validation::present(null, 'bar'));
        $this->assertFalse(Validation::present('foo', null));
    }

    public function testMacAddress()
    {
        $this->assertTrue(Validation::macAddress('00:0a:95:9d:68:16'));
        $this->assertFalse(Validation::macAddress('00:0a:9Z:9d:68:16'));
        $this->assertFalse(Validation::macAddress(null));
    }

    public function testMd5()
    {
        $this->assertTrue(Validation::md5('b1816172fd2ba98f3af520ef572e3a47'));
        $this->assertTrue(Validation::md5('B1816172fd2ba98f3af520ef572e3a47', true));
        $this->assertFalse(Validation::md5('B1816172fd2ba98f3af520ef572e3a47'));
    }

    public function testMimeType()
    {
        $file = dirname(__DIR__) .'/phpunit.xml.dist';
        $post = ['tmp_name' => $file];
        $this->assertTrue(Validation::mimeType($post, 'text/xml'));
        $this->assertTrue(Validation::mimeType($post, ['text/xml']));
        $this->assertFalse(Validation::mimeType($post, 'text/plain'));
        $this->assertFalse(Validation::mimeType($post, ['text/plain']));

        $this->assertTrue(Validation::mimeType($file, ['text/xml']));
    }

    public function testUpload()
    {
        $post = ['tmp_name' => null,'error' => UPLOAD_ERR_NO_FILE];
        $this->assertFalse(Validation::upload(UPLOAD_ERR_NO_FILE));
        $this->assertFalse(Validation::upload($post));

        $this->assertTrue(Validation::upload($post, true));
        $this->assertTrue(Validation::upload(UPLOAD_ERR_NO_FILE, true));

        // test array
        $post = ['tmp_name' => null,'error' => UPLOAD_ERR_OK];
        $this->assertTrue(Validation::upload($post));
    }

    public function testMaxLength()
    {
        $this->assertTrue(Validation::maxLength('string', 10));
        $this->assertFalse(Validation::maxLength('string', 3));

        $this->assertTrue(Validation::maxLength(123456, 8));
        $this->assertFalse(Validation::maxLength(123456, 4));
        $this->assertFalse(Validation::maxLength(null, 3));
    }

    public function testMinLength()
    {
        $this->assertTrue(Validation::minLength('password', 8));
        $this->assertFalse(Validation::minLength('nada', 8));

        $this->assertTrue(Validation::minLength(123456, 4));
        $this->assertFalse(Validation::minLength(123456, 8));
        $this->assertFalse(Validation::minLength(null, 3));
    }
    public function testNotBlank()
    {
        $this->assertTrue(Validation::notBlank('foo'));
        $this->assertFalse(Validation::notBlank(''));
        $this->assertFalse(Validation::notBlank(null));
        $this->assertTrue(Validation::notBlank(0));
        $this->assertTrue(Validation::notBlank('0'));
        $this->assertFalse(Validation::notBlank(' '));
        $this->assertFalse(Validation::notBlank('   '));
        $this->assertTrue(Validation::notBlank(' o'));
        $this->assertTrue(Validation::notBlank('o '));
    }

    public function testNotEmpty()
    {
        $this->assertTrue(Validation::notEmpty('foo'));
        $this->assertTrue(Validation::notEmpty(['key' => 'value']));
        $this->assertTrue(Validation::notEmpty(['tmp_name' => 'foo']));
       
        $this->assertFalse(Validation::notEmpty(null));
        $this->assertFalse(Validation::notEmpty(''));
        $this->assertFalse(Validation::notEmpty([]));
        $this->assertFalse(Validation::notEmpty(['tmp_name' => null]));
    }

    public function testNumeric()
    {
        $this->assertTrue(Validation::numeric(1));
        $this->assertTrue(Validation::numeric('1'));
        $this->assertFalse(Validation::numeric('one'));
        $this->assertTrue(Validation::numeric(1.2));
        $this->assertTrue(Validation::numeric('1.7'));
    }

    public function testRange()
    {
        $this->assertFalse(Validation::range('xxx', 5, 10));
        $this->assertTrue(Validation::range(5, 5, 10));
        $this->assertTrue(Validation::range('5', 5, 10));
        $this->assertTrue(Validation::range(5.2, 5.1111, 10.999));
        $this->assertTrue(Validation::range(10, 5, 10));
        $this->assertFalse(Validation::range(1, 5, 10));
        $this->assertFalse(Validation::range(11, 5, 10));
    }

    public function testTime()
    {
        $this->assertTrue(Validation::time('10:15'));
        $this->assertTrue(Validation::time('10:15:00', 'H:i:s'));
        $this->assertFalse(Validation::time('10.15', 'H:i'));
    }

    public function testUrl()
    {
        $this->assertTrue(Validation::url('http://www.google.com', true));
        $this->assertTrue(Validation::url('www.google.com', false));

        $this->assertFalse(Validation::url('www.google.com', true));

        $this->assertFalse(Validation::url('http://www.google.com', false));
        $this->assertFalse(Validation::url('https://www.google.com', false));
        $this->assertTrue(Validation::url('www.google.com', false));

        $this->assertFalse(Validation::url('ftp://www.google.com', false));
        $this->assertFalse(Validation::url('origin://www.google.com', false));
    }

    public function testUUID()
    {
        $this->assertTrue(Validation::uuid('60837a81-2aa1-4568-b5a9-e4385957b21b'));
        $this->assertFalse(Validation::uuid('60837a81-2aa1-4568-b5a9e4385957b21b'));
        $this->assertFalse(Validation::uuid('60837A81-2AA1-4568-B5A9-E4385957B21B'));
        $this->assertTrue(Validation::uuid('60837A81-2AA1-4568-B5A9-E4385957B21B', true));
    }

    public function testHex()
    {
        $this->assertTrue(Validation::hex('cbd18db4cc2f85cedef654fccc4a4d8'));
        $this->assertFalse(Validation::hex('a-Z'));
    }

    public function testString()
    {
        $this->assertTrue(Validation::string('foo'));
        $this->assertFalse(Validation::string(1234));
    }
}
