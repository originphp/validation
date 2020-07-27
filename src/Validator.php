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

declare(strict_types=1);

namespace Origin\Validation;

use Closure;
use InvalidArgumentException;

/**
 * Validates data in an array
 *
 * @internal Implementing this in the framework, will break
 *
 *  - regex rules which have the name as regex
 *  - using a model method
 *  - validation rules not having string key
 */
class Validator
{
    /**
     * @var array
     */
    protected $validationRules = [];

    /**
    * @var array
    */
    private $messageMap = [
        'accepted' => 'This must be accepted',
        'boolean' => 'This value must be true or false',
        'alpha' => 'This value can only contain letters',
        'alphaNumeric' => 'This value can only contain letters, numbers, dashes and underscores',
        'array' => 'This value must be an array',
        'confirm' => 'The confirmed value does not match',
        'date' => 'Invalid date',
        'datetime' => 'Invalid datetime',
        'dateFormat' => 'Invalid date format',
        'email' => 'Invalid email address',
        'ext' => 'Invalid file type',
        'extension' => 'Invalid file extension',
        'fqdn' => 'Invalid domain',
        'ip' => 'Invalid IP address',
        'ipRange' => 'Invalid IP address',
        'isUnique' => 'Has already been taken',
        'in' => 'Invalid value',
        'notIn' => 'Invalid value',
        'json' => 'Invalid JSON string',
        'macAddress' => 'Invalid MAC address',
        'mimeType' => 'Invalid mime type',
        'notBlank' => 'This field cannot be blank',
        'notEmpty' => 'This field cannot be empty',
        'present' => 'This field is must be present',
        'required' => 'This field is required',
        'time' => 'Invalid time',
        'uid' => 'Invalid UUID string',
        'upload' => 'File upload error',
        'url' => 'Invalid URL',
    ];

    /**
     * Adds a validation rule, if you provide just a string, then the rule will be created with the same name as
     * the rule.
     *
     * @example
     *
     *  // to create a single validation rule for a field (second param is string, unique alias)
     *
     *  $validator
     *      ->add('name','notEmpty')
     *      ->add('email','email', [
     *                 'message'=>'Invalid Email address'
     *            ])  // uses the alias for the rule name
     *      ->add('code','valid', [
     *                  'rule'=> ['in',[1,2,3]]
     *            ]) // set the rule
     *
     *  // to create multiple validation rules on the same field (second param is array)
     *
     *  $validator
     *      ->add('email',[
     *          'notEmpty',
     *          'email' => [
     *              'rule' => 'email',
     *              'message' => 'Invalid email address'
     *          ]
     *      ]);
     *
     * // you can also use objects
     *
     *  $validator->add('status', 'uniqueName', [
     *       'rule' => [$this, 'method']
     *   ]);
     *
     * // and closures
     *
     * $validator->add('status', 'uniqueName', [
     *       'rule' => function ($value) {
     *           return $value === 'active';
     *       }
     *   ]);
     *
     * @param string $field name of the field to validate
     * @param string|array $name rule name for single rule or an array for multiple rules
     * @param array $options options
    *    - rule: name of rule, array, callbable e.g. required, numeric, ['date', 'Y-m-d'],[$this,'method']
     *   - message: the error message to show if the rule fails
     *   - on: default:null. set to create or update to run the rule only on thos
     *   - allowEmpty: default:false validation will be pass on empty values
     *   - stopOnFail: default:false wether to continue if validation fails
     *   - present: default:false the field (key) must be present (but can be empty)
     *
     * @return \Origin\Validator
     */
    public function add(string $field, $name, array $options = [])
    {
        $rules = is_array($name) ? $name : [$name => $options];

        foreach ($rules as $name => $rule) {
            if (is_string($rule)) {
                $name = $rule;
                $rule = ['rule' => $name];
            }

            /**
             * Use unique keys to be able to remove, and it promotes cleaner code
             */
            if (is_int($name)) {
                throw new InvalidArgumentException('A rule must have a name key');
            }

            if (isset($this->validationRules[$field][$name])) {
                throw new InvalidArgumentException(sprintf('There is already a validation rule for %s with the name %s', $field, $name));
            }

            $rule += [
                'rule' => $name,
                'message' => null,
                'on' => null,
                'present' => false,
                'allowEmpty' => false,
                'stopOnFail' => false
            ];

            if ($rule['message'] === null) {
                $rule['message'] = $this->getDefaultMessage($rule['rule']);
            }
            
            $this->validationRules[$field][$name] = $rule;
        }

        return $this;
    }

    /**
     * Gets the default message for a rule
     *
     * @param string|array|closure $rule
     * @return string
     */
    private function getDefaultMessage($rule): string
    {
        $key = null;
        if (is_string($rule)) {
            $key = $rule;
        }
        if (is_array($rule) && isset($rule[0]) && is_string($rule[0])) {
            $key = $rule[0];
        }
   
        return $this->messageMap[$key] ?? 'Invalid value';
    }

    /**
     * Gets the validation rules for a particular field or all of them
     *
     * @param string $field
     * @return array|null
     */
    public function rules(string $field = null): ? array
    {
        if ($field === null) {
            return $this->validationRules;
        }

        return $this->validationRules[$field] ?? null;
    }

    /**
     * Removes a validation rule
     *
     * @param string $field
     * @param string $rule
     * @return \Origin\Validator
     */
    public function remove(string $field, string $rule = null): Validator
    {
        if ($rule) {
            unset($this->validationRules[$field][$rule]);
        } else {
            unset($this->validationRules[$field]);
        }

        return $this;
    }

    /**
     * Validates data in an array
     *
     * @param array $data
     * @param boolean $isNewRecord
     * @return array
     */
    public function validate(array $data, bool $isNewRecord = true): array
    {
        $errors = [];

        foreach ($this->validationRules as $field => $validationRules) {
            $present = array_key_exists($field, $data);
            $value = $data[$field] ?? null;
            $isEmpty = ! Validation::notEmpty($value);

            foreach ($validationRules as $validationRule) {
                if (! $this->runValidationRule($isNewRecord, $validationRule['on'])) {
                    continue;
                }
                # Handle special rules required, optional, present (which all break )
                if ($validationRule['rule'] === 'required') {
                    if (! $present || $isEmpty) {
                        $errors[$field][] = $validationRule['message'];
                        break;
                    }
                    continue; // all done here
                }
                if ($validationRule['rule'] === 'optional') {
                    if ((! $present || $isEmpty)) {
                        break;
                    }
                    continue;
                }
                if ($validationRule['rule'] === 'present') {
                    if (! $present) {
                        $errors[$field][] = $validationRule['message'];
                        break;
                    }
                    continue;
                }

                # Check Options (present & allowEmpty)
                if ($validationRule['present'] === true && ! $present) {
                    $errors[$field][] = $this->messageMap['present'];
                    if ($validationRule['stopOnFail']) {
                        break;
                    }
                    continue;
                }

                if ($validationRule['allowEmpty'] === true && $isEmpty) {
                    continue;
                }

                # Carry out validation
                if (! $this->isValid($value, $validationRule)) {
                    $errors[$field][] = $validationRule['message'];
                    if ($validationRule['stopOnFail']) {
                        break;
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Run
     *
     * @param mixed $value
     * @param array $validationRule
     * @return boolean
     */
    protected function isValid($value, array $validationRule): bool
    {
        if ($validationRule['rule'] instanceof Closure) {
            return $validationRule['rule']($value);
        }

        // handle args etc
        $rule = $validationRule['rule'];
        $args = [$value];

        if (is_array($validationRule['rule'])) {
            $rule = array_shift($validationRule['rule']);
        }
      
        if (is_object($rule)) {
            $method = array_shift($validationRule['rule']);
            $args = array_merge([$value], $validationRule['rule']);

            return call_user_func_array([$rule, $method], $args);
        }

        if (is_array($validationRule['rule'])) {
            $args = array_merge([$value], $validationRule['rule']);
        }
       
        // Check validation class
        if (is_string($rule) && method_exists(Validation::class, $rule)) {
            return forward_static_call([Validation::class, $rule], ...$args);
        }

        throw new InvalidArgumentException('Invalid validation rule');
    }

    /**
     * Checks if a validation rule should run
     *
     * @param boolean $isNewRecord
     * @param string $on
     * @return boolean
     */
    protected function runValidationRule(bool $isNewRecord, string $on = null): bool
    {
        if ($on === null || ($isNewRecord && $on === 'create') || (! $isNewRecord && $on === 'update')) {
            return true;
        }

        return false;
    }
}
