<?php

namespace borales\extensions\phoneInput;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use yii\base\Event;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

/**
 * Behavior of the phone input widget. Auto-formats the phone value for the JS-widget.
 * @package borales\extensions\phoneInput
 */
class PhoneInputBehavior extends AttributeBehavior
{
    /**
     * @var int
     */
    public $saveformat = PhoneNumberFormat::E164;
    /**
     * @var int
     */
    public $displayFormat = PhoneNumberFormat::INTERNATIONAL;
    /**
     * @var string
     */
    public $phoneAttribute = 'phone';

    public function init()
    {
        parent::init();
        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_VALIDATE => $this->phoneAttribute,
                BaseActiveRecord::EVENT_AFTER_FIND => $this->phoneAttribute,
            ];
        }
    }

    /**
     * @return array
     */
    public function events()
    {
        $events = parent::events();
        $events[BaseActiveRecord::EVENT_AFTER_FIND] = 'formatAttributes';
        return $events;
    }

    /**
     * Evaluates the attribute value and assigns it to the current attributes.
     * @param Event $event
     */
    public function evaluateAttributes($event)
    {
        if (!empty($this->attributes[$event->name])) {
            $attributes = (array)$this->attributes[$event->name];
            foreach ($attributes as $attribute) {
                if (is_string($attribute) && $this->owner->$attribute) {
                    try {
                        $phoneValue = $this->getPhoneUtil()->parse($this->owner->$attribute, null);
                        $this->owner->$attribute = $this->getPhoneUtil()->format($phoneValue, $this->saveformat);
                    } catch (NumberParseException $e) {
                    }
                }
            }
        }
    }

    /**
     * @param $event
     */
    public function formatAttributes($event)
    {
        if (!empty($this->attributes[$event->name])) {
            $attributes = (array)$this->attributes[$event->name];
            foreach ($attributes as $attribute) {
                if (is_string($attribute) && $this->owner->$attribute) {
                    try {
                        $phoneValue = $this->getPhoneUtil()->parse($this->owner->$attribute, null);
                        $this->owner->$attribute = $this->getPhoneUtil()->format($phoneValue, $this->displayFormat);
                    } catch (NumberParseException $e) {
                    }
                }
            }
        }
    }

    /**
     * @return PhoneNumberUtil
     */
    protected function getPhoneUtil()
    {
        return PhoneNumberUtil::getInstance();
    }
}