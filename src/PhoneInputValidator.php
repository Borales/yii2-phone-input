<?php

namespace borales\extensions\phoneInput;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use yii\validators\Validator;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Validates the given attribute value with the PhoneNumberUtil library.
 * @package borales\extensions\phoneInput
 */
class PhoneInputValidator extends Validator
{
    /**
     * @var mixed
     */
    public $region;

    /**
     * @inheritdoc
     */
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

            if ($this->region !== null) {
                if (is_array($this->region)) {
                    foreach ($this->region as $region) {
                        if ($phoneUtil->isValidNumberForRegion($phoneProto, $region)) {
                            $valid = true;
                            break;
                        }
                    }
                } else {
                    if ($phoneUtil->isValidNumberForRegion($phoneProto, $this->region)) {
                        $valid = true;
                    }
                }
            } else {
                if ($phoneUtil->isValidNumber($phoneProto)) {
                    $valid = true;
                }
            }

        } catch (NumberParseException $e) {
        }
        return $valid ? null : [$this->message, []];
    }

    /**
     * @inheritdoc
     */
    public function clientValidateAttribute($model, $attribute, $view) {

        $telInputId = Html::getInputId($model, $attribute);
        $options = Json::htmlEncode([
            'message' => \Yii::$app->getI18n()->format($this->message, [
                'attribute' => $model->getAttributeLabel($attribute)
            ], \Yii::$app->language)
        ]);

        return <<<JS
var options = $options, telInput = $("#$telInputId");

if($.trim(telInput.val())){
    if(!telInput.intlTelInput("isValidNumber")){
        messages.push(options.message);
    }
}
JS;
    }
}
