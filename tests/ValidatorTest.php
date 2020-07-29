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

use InvalidArgumentException;
use Origin\Validation\Validator;

class Assert
{
    public function assertTrue($value): bool
    {
        return $value === true;
    }

    public function assertContains($value, array $array, bool $works = true): bool
    {
        return in_array($value, $array) && $works;
    }
}

class ValidatorTest extends \PHPUnit\Framework\TestCase
{
   
    /**
     * Tests that it handles the different type of rules, string, single rule array and multiple rule array
     *
     * @return void
     */
    public function testAdd()
    {
        $rules = (new Validator())->add('name', 'notEmpty')->rules('name');
        $expected = [
            'rule' => 'notEmpty',
            'message' => 'This field cannot be empty',
            'on' => null,
            'allowEmpty' => false,
            'stopOnFail' => false
        ];

        $this->assertEquals($expected, $rules['notEmpty']);
        
        $rules = (new Validator())->add('name', 'notEmpty', ['message' => 'Need input'])->rules('name');
        $expected = [
            'rule' => 'notEmpty',
            'message' => 'Need input',
            'on' => null,
            'allowEmpty' => false,
            'stopOnFail' => false
        ];
        $this->assertEquals($expected, $rules['notEmpty']);
        
        $rules = (new Validator())->add('name', [
            'required',
            'email' => ['rule' => 'email','message' => 'Bad email address']
        ])->rules('name');
 
        $expected = [
            'rule' => 'required',
            'message' => 'This field is required',
            'on' => null,
            'allowEmpty' => false,
            'stopOnFail' => false
        ];

        $this->assertEquals($expected, $rules['required']);

        $expected = [
            'rule' => 'email',
            'message' => 'Bad email address',
            'on' => null,
            'allowEmpty' => false,
            'stopOnFail' => false
        ];
        $this->assertEquals($expected, $rules['email']);
    }

    public function testDefaultMessageMap()
    {
        $rules = (new Validator())->add('name', 'notEmpty')->rules('name');
        $this->assertEquals('This field cannot be empty', $rules['notEmpty']['message']);

        // test other
        $rules = (new Validator())->add('name', 'test', [
            'rule' => ['url', false]
        ])->rules('name');
      
        $this->assertEquals('Invalid URL', $rules['test']['message']);

        // unkown rule
        $rules = (new Validator())->add('name', '<-0->')->rules('name');
        $this->assertEquals('Invalid value', $rules['<-0->']['message']);
    }

    public function testAddInvalidRule()
    {
        // This validation rule exists, but this not how you call it
        $this->expectException(InvalidArgumentException::class);
        (new Validator())->add('name', ['in', ['silver','gold','platinum']]);
    }

    public function testAddNotUsingName()
    {
        $this->expectException(InvalidArgumentException::class);
        (new Validator())->add('name', [
            'required',
            0 => ['rule' => 'email','message' => 'Invalid email address']
        ])->rules('name');
    }

    public function testAddUniqueName()
    {
        $this->expectException(InvalidArgumentException::class);
        (new Validator())->add('email', 'email')->add('email', 'email');
    }

    public function testRemove()
    {
        $validator = new Validator();
        $validator->add('name', 'notEmpty');
        $this->assertNotEmpty($validator->rules('name'));
    
        // remove all
        $validator->remove('name');
        $this->assertEmpty($validator->rules('name'));
        $validator->remove('name'); // no errs

        // test remove single
        $validator->add('name', 'notEmpty')->add('name', 'email');
        $this->assertNotEmpty($validator->rules('name'));

        $validator->remove('name', 'email');
        $rules = $validator->rules('name');
        $this->assertArrayHasKey('notEmpty', $rules);
        $this->assertArrayNotHasKey('email', $rules);

        $validator->remove('name', 'email'); // test no errs
    }

    public function testValidate()
    {
        $data = [
            'name' => 'jim',
            'email' => 'jim@originphp.com'
        ];
     
        // test Valdation rule no arguments
        $validator = new Validator();
        $validator->add('email', 'email');
        $this->assertEmpty($validator->validate($data));
        $this->assertNotEmpty($validator->validate(['email' => 'foo']));
    }

    // unreachable
    public function testValidateInvalidRule()
    {
        $this->expectException(InvalidArgumentException::class);
        $validator = new Validator();
        $validator->add('foo', 'foo');
        $this->assertEmpty($validator->validate(['foo' => 'bar']));
        $this->assertNotEmpty($validator->validate(['email' => 'foo']));
    }

    public function testRules()
    {
        $validator = new Validator();
   
        $validator->add('name', 'notEmpty');
        $expected = [
            'rule' => 'notEmpty',
            'message' => 'This field cannot be empty',
            'on' => null,
            'allowEmpty' => false,
            'stopOnFail' => false
        ];

        $this->assertArrayHasKey('name', $validator->rules());
        $this->assertNotEmpty($validator->rules()['name']);
        $this->assertEquals(['notEmpty' => $expected], $validator->rules()['name']);
        $this->assertEquals(['notEmpty' => $expected], $validator->rules('name'));
    }

    /**
     * @depends testValidate
     */
    public function testValidateOnOptionUpdate()
    {
        $validator = new Validator();
        $validator->add('email', 'email', [
            'rule' => 'email',
            'on' => 'update'
        ]);

        $this->assertEmpty($validator->validate(['email' => 'foo'], true));
        $this->assertNotEmpty($validator->validate(['email' => 'foo'], false));
    }

    /**
    * @depends testValidate
    */
    public function testValidateOnOptionCreate()
    {
        $validator = new Validator();
        $validator->add('email', 'email', [
            'rule' => 'email',
            'on' => 'create'
        ]);

        $this->assertNotEmpty($validator->validate(['email' => 'foo'], true));
        $this->assertEmpty($validator->validate(['email' => 'foo'], false));
    }

    public function testValidateRequired()
    {
        $validator = new Validator();
        $validator->add('email', [
            'required',
            'email'
        ]);
        $errors = $validator->validate(['name' => 'foo']);

        $this->assertArrayHasKey('email', $errors);
        $this->assertEquals('This field is required', $errors['email'][0]);

        $this->assertEmpty($validator->validate(['email' => 'phpunit@originphp.com']));
    }

    public function testValidateOptional()
    {
        $validator = new Validator();
        $validator->add('email', [
            'optional',
            'email'
        ]);

        $this->assertEmpty($validator->validate(['email' => '']));

        $this->assertEmpty($validator->validate(['email' => 'phpunit@originphp.com']));
    }

    public function testValidatePresent()
    {
        $validator = new Validator();
        $validator->add('email', [
            'present',
            'email'
        ]);
        $errors = $validator->validate(['name' => 'foo']);

        $this->assertArrayHasKey('email', $errors);
        $this->assertEquals('This field is must be present', $errors['email'][0]);

        $this->assertEmpty($validator->validate(['email' => 'phpunit@originphp.com']));
    }

    public function testValidateAllowEmptyOption()
    {
        $validator = new Validator();
        $validator->add('email', [
            'email' => [
                'rule' => 'email',
                'allowEmpty' => true
            ]
        ]);
        $this->assertEmpty($validator->validate(['email' => '']));
    }

    public function testValidateMultipleFailures()
    {
        $validator = new Validator();
        $validator->add('email', [
            'email',
            'ip'
        ]);
        $errors = $validator->validate(['email' => '1-2-3']);
        $this->assertArrayHasKey('email', $errors);
        $this->assertCount(2, $errors['email']);
    }

    public function testValidateConfirm()
    {
        $validator = new Validator();
        $validator->add('password', [
            'confirm'
        ]);
        $errors = $validator->validate(['password'=>'foo']);
        $this->assertNotEmpty($errors);
        $errors = $validator->validate(['password'=>'foo','password_confirm'=>'bar']);
        $this->assertNotEmpty($errors);
        $errors = $validator->validate(['password'=>'foo','password_confirm'=>'foo']);
        $this->assertEmpty($errors);
    }

    /**
     * @depends testValidateMultipleFailures
     */
    public function testValidateStopOnFail()
    {
        $validator = new Validator();
        $validator->add('email', [
            'email' => [
                'rule' => 'email',
                'stopOnFail' => true
            ],
            'ip'
        ]);
        $errors = $validator->validate(['email' => '1-2-3']);
        $this->assertArrayHasKey('email', $errors);
        $this->assertCount(1, $errors['email']);
    }

    /**
     * test calling the various types of validation rules
     *
     * @depends testValidate
     *
     * @return void
     */
    public function testIsValid()
    {
        $data = [
            'name' => 'jim',
            'email' => 'jim@originphp.com'
        ];
     
        // test Valdation rule no arguments
        $validator = new Validator();
        $validator->add('email', 'email');
        $this->assertEmpty($validator->validate($data));
        $this->assertNotEmpty($validator->validate(['email' => 'foo']));

        // test Valdation rule with arguments
        $validator = new Validator();
        $validator->add('name', 'valid', [
            'rule' => ['in',['jim','john'],false]
        ]);
        $this->assertEmpty($validator->validate($data));
        $this->assertNotEmpty($validator->validate(['name' => 'tony']));

        // Test Calling Object
        $validator = new Validator();
        $validator->add('published', 'na', [
            'rule' => [new Assert(),'assertTrue']
        ]);
        $this->assertEmpty($validator->validate(['published' => true]));
         
        $validator = new Validator();
        $validator->add('status', 'na', [
            'rule' => [new Assert(),'assertContains',['active']]
        ]);
        $this->assertEmpty($validator->validate(['status' => 'active']));

        // Check that works argument was passed, which will make the test fail
        $validator = new Validator();
        $validator->add('status', 'na', [
            'rule' => [new Assert(),'assertContains',['active'],false]
        ]);
        $this->assertNotEmpty($validator->validate(['status' => 'active']));

        // Test Closure
        $validator = new Validator();
        $validator->add('status', 'na', [
            'rule' => function ($value) {
                return $value === 'active';
            }
        ]);
        $this->assertEmpty($validator->validate(['status' => 'active']));
        $this->assertNotEmpty($validator->validate(['status' => 'foo']));
    }
}
