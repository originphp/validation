<?php
/**
 * OriginPHP Framework
 * Copyright 2018 - 2020 Jamiel Sharief.
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

use Origin\Validation\ValidateTrait;

class Popo
{
    use ValidateTrait;

    public $name;
    public $description;
    public $email;
}

class ValidateTraitTest extends \PHPUnit\Framework\TestCase
{
    public function testValidate()
    {
        $popo = new Popo();
        $popo->validate('name', 'notBlank');
        $this->assertFalse($popo->validates());

        $this->assertNotEmpty($popo->errors());
        $this->assertNotEmpty($popo->errors('name'));

        $popo->name = 'Plain Old PHP Object';
        $this->assertTrue($popo->validates());

        $this->assertEmpty($popo->errors());
        $this->assertEmpty($popo->errors('name'));

        $popo->invalidate('foo', 'bar');
        $this->assertEquals(['bar'], $popo->errors('foo'));
    }

    public function testValidateOverride()
    {
        $popo = new Popo();
        $popo->validate('email', ['required','email']);
        $popo->validate('email', ['optional','email']);

        $rules = $popo->validator()->rules('email');

        $this->assertArrayHasKey('optional', $rules);
        $this->assertArrayNotHasKey('required', $rules);
    }
}
