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

declare(strict_types=1);
namespace Origin\Validation;

use DateTime;
use Exception;

class Validation
{
    /**
     * Validates a value is accepted (checkbox checked)
     *
     * @param bool $value
     * @param array $acceptables
     * @return boolean
     */
    public static function accepted($value, array $acceptables = [true, 1, '1']): bool
    {
        return in_array($value, $acceptables, true);
    }

    /**
     * Validates a date is after a certain date
     *
     * @param string $value strtotime compatabile date
     * @param string $afterDate strtotime compatabile date
     * @return boolean
     */
    public static function after($value, string $afterDate = 'now'): bool
    {
        if (! is_string($value)) {
            return false;
        }

        $result = false;
        $value = strtotime($value);
        $afterDate = strtotime($afterDate);
        if ($value && $afterDate) {
            $result = $value > $afterDate;
        }

        return $result;
    }

    /**
     * Validates a string only contains alphabetic characters from the default locale
     *
     * @param string $value
     * @return boolean
     */
    public static function alpha($value): bool
    {
        return ctype_alpha($value);
    }

    /**
     * Validates a string only contains alphanumeric characters from the default locale
     *
     * @param string $value
     * @return boolean
     */
    public static function alphaNumeric($value): bool
    {
        return ctype_alnum($value);
    }

    /**
     * Validates a value is an array
     *
     * @param array $value
     * @return boolean
     */
    public static function array($value): bool
    {
        return is_array($value);
    }

    /**
     * Validates a date is before a certain date
     *
     * @param string $value strtotime compatabile date
     * @param string $beforeDate strtotime compatabile date
     * @return boolean
     */
    public static function before($value, string $beforeDate = 'now'): bool
    {
        if (! is_string($value)) {
            return false;
        }

        $result = false;
        $value = strtotime($value);
        $beforeDate = strtotime($beforeDate);
        if ($value && $beforeDate) {
            $result = $value < $beforeDate;
        }

        return $result;
    }

    /**
     * Validates a value is a boolean type
     *
     * @param bool|int|string $value
     * @param array $values array of boolean values
     * @return boolean
     */
    public static function boolean($value, array $values = [true, false, 0, 1, '0', '1']): bool
    {
        return ($value !== null && in_array($value, $values, true));
    }

    /**
     * Validates a credit card number
     *
     * @internal Paypal,Intuit provide a test card number for Diners 38520000023237, but according to
     * Discover network the length for the 38 range should be 16-19. Similar to 30569309025904
     * @see https://www.discoverglobalnetwork.com/downloads/IPP_VAR_Compliance.pdf
     *
     * @param string $value
     * @param string $type any, amex, diners, discover, jcb, maestro, mastercard, visa
     * @return boolean
     */
    public static function creditCard($value, string $type = 'any'): bool
    {
        if (! is_string($value) && ! is_integer($value)) {
            return false;
        }

        $value = str_replace([' ', '-'], '', (string) $value);

        // 06.12.2019 - Decided to write the regex rules from scratch since
        // the public domain ones seem to be outdated.
        // @see https://en.wikipedia.org/wiki/Payment_card_number

        // all these cards use Luhn algorithm for validation
        $map = [
            // IIN Range 37,34 length 15,
            'amex' => '/^(37|34)\d{13}$/',
            // IIN range 36 length 14-19  Diners Club International
            // IIN range 300–305, 3095, 38–39 length 16-19 Diners Club International
            // IIN range 54, 55, length 16  MasterCard co-branded
            'diners' => '/^36\d{12,17}$|^(54|55)\d{14}$|^30[0-5]\d{13,16}$|^(38|39)\d{14,17}$|^3095\d{12,15}$/',
            // IIN ranges 	6011, 622126 - 622925, 624000 - 626999, 628200 - 628899, 64, 65 - length 16-19
            // @todo 622126 - 622925 breaks into 622200-622800,622900-622925, 622126-622199
            'discover' => '/^6011\d{12,15}$|^(64|65)\d{14,17}$|^622[1-9]\d{12,15}$|^62[4-6][0-9]\d{12,15}$|^628[2-8]\d{12,15}$/', // 16-19

            // IIN range 3528–3589 length 16-19
            'jcb' => '/^35[3-8]\d{13,16}$|^352[8-9]\d{12,15}$/',
            // IIN range 50, 56–69 length 12 - 19 (uk is also covered)
            'maestro' => '/^(50|5[6-9]|6\d)\d{10,17}$/',
            // IIN Range 51–55, 2221-2720  length 16
            'mastercard' => '/^5[1-5]\d{14}$|^222[1-9]\d{12}$|^27[0-1]\d{13}$|^2720\d{12}$|^2[3-6]\d{14}$/',
            // IIN range 4 length 16
            'visa' => '/^4\d{15}$/',
        ];

        if ($type !== 'any' && ! isset($map[$type])) {
            return false;
        }

        if (! static::luhn($value)) {
            return false;
        }

        $patterns = ($type === 'any') ? array_values($map) : [$map[$type]];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validates a date using a format compatible with the PHP DateTime class.
     *
     * @param string $value
     * @param string $format Y-m-d
     * @return bool
     */
    public static function date($value, string $format = 'Y-m-d'): bool
    {
        return static::dateFormat($value, $format);
    }

    /**
     * Validates a string by a datetime format
     *
     * @param string $value
     * @param string $format Y-m-d H:i:s,Y-m-d,H:i:s
     * @return boolean
     */
    public static function dateFormat($value, string $format): bool
    {
        if (! is_string($value)) {
            return false;
        }
        $dateTime = DateTime::createFromFormat($format, $value);

        return ($dateTime !== false && $dateTime->format($format) === $value);
    }

    /**
     * Validates a datetime using a format compatible with the PHP DateTime class.
     *
     * @param string $value
     * @param string $format Y-m-d H:i:s
     * @return bool
     */
    public static function datetime($value, string $format = 'Y-m-d H:i:s'): bool
    {
        return static::dateFormat($value, $format);
    }

    /**
     * Validates a value is a float. Alias for float
     *
     * @param float $value
     * @return boolean
     */
    public static function decimal($value): bool
    {
        return static::float($value);
    }

    /**
     * Validation for an email address
     *
     * @param string $value
     * @param boolean $checkDNS Wether to check if the domain part has MX records
     * @return boolean
     */
    public static function email($value, bool $checkDNS = false): bool
    {
        $result = (filter_var($value, FILTER_VALIDATE_EMAIL) !== false);

        if ($result && $checkDNS) {
            list($account, $domain) = explode('@', $value);

            return getmxrr($domain, $mxhosts);
        }

        return $result;
    }

    /**
     * Validates a value is equal to
     *
     * @param mixed $value
     * @param mixed $comparedTo
     * @return boolean
     */
    public static function equalTo($value, $comparedTo): bool
    {
        return ($value == $comparedTo);
    }

    /**
     * Validates a value has an extension. If an array is supplied it will look for the name key.
     *
     * @param string|array $value
     * @param string|array $extensions ['gif','jpg']
     * @return boolean
     */
    public static function extension($value, $extensions = []): bool
    {
        if (! is_string($value) && ! is_array($value)) {
            return false;
        }
        if (is_array($value)) {
            $value = $value['name'] ?? 'none';
        }
        if (is_string($extensions)) {
            $extensions = [$extensions];
        }
        $extension = mb_strtolower(pathinfo($value, PATHINFO_EXTENSION));

        return static::in($extension, $extensions, true);
    }

    /**
     * Validates a float value e.g 123.56
     *
     * @param float $value
     * @return bool
     */
    public static function float($value): bool
    {
        if (is_string($value)) {
            return (bool) filter_var($value, FILTER_VALIDATE_FLOAT) && filter_var($value, FILTER_VALIDATE_INT) === false;
        }

        return is_float($value);
    }

    /**
     * Validates a string is Fully Qualified Domain Name (FQDN). Use checkDNS to
     * ensure that is really valid and not just looks like its valid.
     *
     * @see https://tools.ietf.org/html/rfc1035
     *
     * @param string $value
     * @param boolean $checkDNS
     * @return boolean
     */
    public static function fqdn($value, bool $checkDNS = false): bool
    {
        $pattern = '/^((?=[a-z0-9-]{1,63}\.)[a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,63}$/';

        $result = (is_string($value) && (bool) preg_match($pattern, $value));

        if ($result && $checkDNS) {
            $result = (checkdnsrr($value, 'A') !== false);
        }

        return $result;
    }

    /**
     * Validates a value is greater than
     *
     * @param int|float $value
     * @param int|float $max
     * @return boolean
     */
    public static function greaterThan($value, $max): bool
    {
        return $value > $max;
    }

    /**
     * Validates a value is greater than or equals
     *
     * @param int|float $value
     * @param int|float $max
     * @return boolean
     */
    public static function greaterThanOrEqual($value, $max): bool
    {
        return $value >= $max;
    }
    
    /**
     * Validates a string only contains hexidecimal characters
     *
     * @param string $value
     * @return boolean
     */
    public static function hex($value, bool $insensitive = false) :bool
    {
        $pattern = '/^[0-9a-f]+$/' . ($insensitive ? 'i' : null);

        return is_string($value) && static::regex($value, $pattern);
    }

    /**
     * Validates a value is a hex color
     *
     * @param string $value
     * @return boolean
     */
    public static function hexColor($value): bool
    {
        $pattern = '/^#[0-9a-f]{6}$/i';

        return is_string($value) && static::regex($value, $pattern);
    }

    /**
     * Validates a value is in a list
     *
     * @param mixed $value
     * @param array $list
     * @param boolean $caseInSensitive
     * @return boolean
     */
    public static function in($value, array $list, bool $caseInSensitive = false): bool
    {
        if ($caseInSensitive) {
            $list = array_map('mb_strtolower', $list);
            $value = mb_strtolower($value);
        }

        return in_array($value, $list);
    }

    /**
     * Validates a value is JSON string
     *
     * @param string $value
     * @return boolean
     */
    public static function json($value): bool
    {
        if (! is_string($value) || empty($value)) {
            return false;
        }
        json_decode($value);

        return (json_last_error() === JSON_ERROR_NONE);
    }

    /**
     * Validates a value is greater than
     *
     * @param int|float $value
     * @param int|float $min
     * @return boolean
     */
    public static function lessThan($value, $min): bool
    {
        return $value < $min;
    }

    /**
     * Validates a value is greater than
     *
     * @param int|float $value
     * @param int|float $min
     * @return boolean
     */
    public static function lessThanOrEqual($value, $min): bool
    {
        return $value <= $min;
    }

    /**
     * Validates an IBAN number. Does not check the country code is valid nor does it check
     * that length for that particular country. However it does check its the correct format
     * then it converts it and tests algorithm is corret.
     *
     * @param string $value
     * @return boolean
     */
    public static function iban($value): bool
    {
        if (! is_string($value)) {
            return false;
        }
        $value = str_replace([' ', '-'], '', strtoupper($value));

        if (! preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/', $value)) {
            return false;
        }

        // Extract info
        $countryCode = substr($value, 0, 2);
        $valueDigits = intval(substr($value, 2, 2));
        $accountNo = substr($value, 4);

        // Convert to number
        foreach (range(10, 35) as $tmp) {
            $replace[] = strval($tmp);
        }

        $numericString = str_replace(range('A', 'Z'), $replace, $accountNo . $countryCode . '00');

        // Calculate the checksum
        $valuesum = intval(substr($numericString, 0, 1));
        $length = strlen($numericString);
        for ($i = 1; $i < $length; $i++) {
            $valuesum = ($valuesum * 10) + intval(substr($numericString, $i, 1));
            $valuesum %= 97;
        }

        return ((98 - $valuesum) === $valueDigits);
    }

    /**
     * Validates a value is an integer
     *
     * @param int $value
     * @return boolean
     */
    public static function integer($value): bool
    {
        return is_int($value) || ctype_digit($value);
    }

    /**
     * Checks that an IP address is valid
     *
     * @param string $value
     * @param string $type both,ipv4,ipv6
     * @return boolean
     */
    public static function ip($value, string $type = 'both'): bool
    {
        $flags = 0;
        if ($type === 'ipv4') {
            $flags = FILTER_FLAG_IPV4;
        } elseif ($type === 'ipv6') {
            $flags = FILTER_FLAG_IPV6;
        }
      
        return (bool) filter_var($value, FILTER_VALIDATE_IP, ['flags' => $flags]);
    }

    /**
     * Validates an IP address is in a range
     *
     * @param string $value
     * @param string $start
     * @param string $end
     * @return boolean
     */
    public static function ipRange($value, string $start, string $end): bool
    {
        if (static::ip($value) && static::ip($start) && static::ip($end)) {
            $ip = ip2long($value);

            return $ip >= ip2long($start) && $ip <= ip2long($end);
        }

        return false;
    }

    /**
     * Validates a value has a certain length
     *
     * @param string $value
     * @param int $length
     * @return boolean
     */
    public static function length($value, int $length): bool
    {
        return (is_string($value) && mb_strlen($value) === $length);
    }

    /**
     * Validates a value is in lowercase
     *
     * @param string $value
     * @return boolean
     */
    public static function lowercase($value): bool
    {
        return (is_string($value) && ! empty($value) && strtolower($value) === $value);
    }

    /**
     * Checks a number checksum using the LUHN (modulus 10) algorigthm which is used by
     * credit cards, identity cards etc.
     *
     * @see https://en.wikipedia.org/wiki/Luhn_algorithm
     *
     * @param string $value
     * @return boolean
     */
    public static function luhn($value): bool
    {
        if ((! is_string($value) && ! is_integer($value)) || strlen($value) < 2) {
            return false;
        }

        // extract the checkdigit from the number
        $valueDigit = (int) $value[strlen($value) - 1];

        // reverse the number order and calculate length
        $string = strrev(substr($value, 0, -1));
        $length = strlen($string);

        // Calculate checksum
        // double every second digit starting from the first after checkDigit
        $valuesum = 0;
        for ($i = 0; $i < $length; $i++) {
            $number = ($i % 2 === 0) ? (int) $string[$i] * 2 : (int) $string[$i];
            $valuesum += ($number > 9) ? $number - 9 : $number;
        }

        return ($valuesum + $valueDigit) % 10 === 0;
    }

    /**
     * Validates a string is a MAC address
     *
     * @param string $value
     * @return boolean
     */
    public static function macAddress($value): bool
    {
        return is_string($value) && filter_var($value, FILTER_VALIDATE_MAC) !== false;
    }

    /**
     * Validates a value has a max length
     *
     * @param string $value
     * @param int $max
     * @return boolean
     */
    public static function maxLength($value, int $max): bool
    {
        return (is_scalar($value) && mb_strlen((string) $value) <= $max);
    }

    /**
     * Validates a value is MD5 hash
     *
     * @param string $value
     * @param boolean $caseInsensitive
     * @return boolean
     */
    public static function md5($value, bool $caseInsensitive = false): bool
    {
        $pattern = '/^[0-9a-f]{32}$/' . ($caseInsensitive ? 'i' : null);

        return is_string($value) && static::regex($value, $pattern);
    }

    /**
     * Checks the mime type of a file
     *
     * @param string|array $result
     * @param string|array $mimeTypes
     * @return boolean
     */
    public static function mimeType($result, $mimeTypes = []): bool
    {
        if (is_array($result) && isset($result['tmp_name'])) {
            $result = $result['tmp_name'];
        }
        if (is_string($mimeTypes)) {
            $mimeTypes = [$mimeTypes];
        }

        $mimeType = mime_content_type($result);
        if ($mimeType === false) {
            throw new Exception('Unable to determine the mimetype');
        }

        return in_array($mimeType, $mimeTypes);
    }

    /**
     * Validates a value has a minimum length
     *
     * @param string $value
     * @param int $min
     * @return boolean
     */
    public static function minLength($value, int $min): bool
    {
        return (is_scalar($value) && mb_strlen((string) $value) >= $min);
    }

    /**
     * Validates a value is a string not empty and has anything other than whitespaces.
     *
     * @param string $value
     * @return boolean
     */
    public static function notBlank($value): bool
    {
        if (empty($value) && ! is_numeric($value) && ! is_bool($value)) {
            return false;
        }

        return (bool) preg_match('/[^\s]+/', (string) $value);
    }

    /**
     * Checks that a value is not empty, a value is empty when :
     *
     * - value is `null`
     * - value is an empty string
     * - value is an empty array
     * - value is an empty file upload
     *
     * @param mixed $value
     * @return boolean
     */
    public static function notEmpty($value): bool
    {
        return static::empty($value) === false;
    }

    /**
    * Checks if a value is empty, it is only empty when
    *
    * - value is `null`
    * - value is an empty string
    * - value is an empty array
    * - value is an empty file upload
    *
    * @param mixed $value
    * @return boolean
    */
    private static function empty($value): bool
    {
        if (is_null($value)) {
            return true;
        }
        if (is_string($value) && trim($value) === '') {
            return true;
        }
      
        if (is_array($value)) {
            return empty($value) || (array_key_exists('tmp_name', $value) && empty($value['tmp_name']));
        }

        return false;
    }

    /**
     * Validates a value is not in a list
     *
     * @param mixed $value
     * @param array $list
     * @param boolean $caseInSensitive
     * @return boolean
     */
    public static function notIn($value, array $list, bool $caseInSensitive = false): bool
    {
        return ! static::in($value, $list, $caseInSensitive);
    }

    /**
     * Checks if a value is numeric (integer or float)
     *
     * @param mixed $value
     * @return boolean
     */
    public static function numeric($value): bool
    {
        return is_numeric($value);
    }

    /**
     * Validates an array has a key present
     *
     * @param array $value
     * @param string|int $key
     * @return boolean
     */
    public static function present($value, $key): bool
    {
        return is_array($value) && array_key_exists($key, $value);
    }

    /**
     * Validates a number is in a range
     *
     * @param int|float $value
     * @param int|float $min
     * @param int|float $max
     * @return boolean
     */
    public static function range($value, $min, $max): bool
    {
        if (! is_numeric($value) || ! is_numeric($min) || ! is_numeric($max)) {
            return false;
        }

        return ($value >= $min && $value <= $max);
    }

    /**
     * Validates using a REGEX pattern
     *
     * @param string $value
     * @param string $pattern
     * @return boolean
     */
    public static function regex($value, string $pattern): bool
    {
        return is_string($value) && (bool) preg_match($pattern, $value);
    }

    /**
     * Validates a value is a string
     *
     * @param mixed $value
     * @return boolean
     */
    public static function string($value) : bool
    {
        return is_string($value);
    }

    /**
     * Validates a date using a format compatible with the PHP DateTime class.
     *
     * @param string $value
     * @param string $format H:i
     * @return bool
     */
    public static function time($value, string $format = 'H:i'): bool
    {
        return static::dateFormat($value, $format);
    }

    /**
     * Validates a file was uploaded
     *
     * @param int|array $result the result of $_FILES['field'] || $_FILES['field']['error']
     * @param boolean $optional
     * @return boolean
     */
    public static function upload($result, bool $optional = false): bool
    {
        if (is_array($result) && isset($result['error'])) {
            $result = $result['error'];
        }
        // Let test pass if the upload is optional and no file was uploaded
        if ($optional && $result === UPLOAD_ERR_NO_FILE) {
            return true;
        }

        return $result === UPLOAD_ERR_OK;
    }

    /**
     * Validates a value is in uppercase
     *
     * @param string $value
     * @return boolean
     */
    public static function uppercase($value): bool
    {
        return (is_string($value) && ! empty($value) && strtoupper($value) === $value);
    }

    /**
     * Validates a value is a valid URL
     *
     * @internal filter_var says https://https://www.google.com is valid
     *
     * @param string $value
     * @param boolean $protocol set to false to validate without protocol. e.g. http:// or https://
     * @return boolean
     */
    public static function url($value, bool $protocol = true): bool
    {
        if ($protocol) {
            return (filter_var($value, FILTER_VALIDATE_URL) !== false);
        }
        // if a protocol is included invalidate
        if (preg_match('/^https?|:\/\//', $value)) {
            return false;
        }

        return filter_var('https://' . $value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validates a value is a valid UUID
     *
     * @see https://tools.ietf.org/html/rfc4122
     *
     * @param string $value
     * @param boolean $caseInsensitive Wether to allow uppercase letters
     * @return boolean
     */
    public static function uuid($value, bool $caseInsensitive = false): bool
    {
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/' . ($caseInsensitive ? 'i' : null);

        return is_string($value) && static::regex($value, $pattern);
    }
}
