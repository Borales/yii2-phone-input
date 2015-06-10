<?php

namespace borales\extensions\phoneInput;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use yii\validators\Validator;

/**
 * Validates the given attribute value with the PhoneNumberUtil library.
 * @package borales\extensions\phoneInput
 */
class PhoneInputValidator extends Validator
{
    public function init()
    {
        if (!$this->message) {
            $this->message = \Yii::t('yii', 'The format of {attribute} is invalid.');
        }
        parent::init();
    }

    /**
     * @param mixed $value
     * @return array|null
     */
    protected function validateValue($value)
    {
        $valid = false;
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $phoneProto = $phoneUtil->parse($value, null);
            if ($phoneUtil->isValidNumber($phoneProto)) {
                $valid = true;
            }
        } catch (NumberParseException $e) {
        }
        return $valid ? null : [$this->message, []];
    }
}