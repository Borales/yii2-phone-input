<?php

namespace borales\extensions\phoneInput;

use yii\web\AssetBundle;

/**
 * Asset Bundle of the phone input widget. Registers required CSS and JS files.
 * @package borales\extensions\phoneInput
 */
class PhoneInputAsset extends AssetBundle
{
    /** @var string */
    public $sourcePath = '@bower/intl-tel-input';
    /** @var array */
    public $css = ['build/css/intlTelInput.css'];
    /** @var array */
    public $js = [
        'build/js/utils.js',
        'build/js/intlTelInput.min.js',
    ];
    /** @var array */
    public $depends = ['yii\web\JqueryAsset'];
}
