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

/**
 * ValidateTrait turns any object into something that can be easily validated
 */
trait ValidateTrait
{
    /**
     * @var \Origin\Validation\Validator
     */
    private $validator;

    /**
     * @var array
     */
    private $validationErrors = [];


    /**
     * If validation fails you can use this to work with errors
     *
     * @param string $field
     * @return array|null
     */
    public function errors(string $field = null): ? array
    {
        if ($field === null) {
            return $this->validationErrors;
        }

        return $this->validationErrors[$field] ?? null;
    }

    /**
     * Validates a field
     *
     * @param string $field name of the field to validate
     * @param string|array $name rule name for single rule or an array for multiple rules
     * @param array $options options
    *    - rule: name of rule, array, callbable e.g. required, numeric, ['date', 'Y-m-d'],[$this,'method']
     *   - message: the error message to show if the rule fails
     *   - on: default:null. set to create or update to run the rule only on those
     *   - allowEmpty: default:false validation will be pass on empty values.
     *   - stopOnFail: default:false wether to continue if validation fails
     * @return void
     */
    public function validate(string $field, $name, array $options = []): void
    {
        $this->validator()->add($field, $name, $options);
    }

    /**
     * Validates this object
     *
     * @param boolean $isNewRecord
     * @return boolean
     */
    public function validates(bool $isNewRecord = true): bool
    {
        $this->validationErrors = $this->validator()->validate(
            get_object_vars($this),
            $isNewRecord
        );
        return empty($this->validationErrors);
    }

    /**
     * Gets the Validator Object
     *
     * @return \Origin\Validation\Validator
     */
    protected function validator(): Validator
    {
        if (! $this->validator) {
            $this->validator = new Validator();
        }

        return $this->validator;
    }
}
