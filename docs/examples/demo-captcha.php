<?php session_start(); ?>
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>HTML_QuickForm2_Element_Captcha demo</title>
        <style type="text/css">
            span.error {
                color: red;
            }
            div.row {
                clear: both;
                overflow: hidden;
                width: 100%
            }
            label {
                width: 200px;
                float: left;
            }
            div.element {
                float: left;
            }
            div.element.error input {
                background-color: #FAA;
            }
        </style>
    </head>
    <body>

<?php

// Ignore E_STRICT errors coming from some modules
error_reporting(E_ALL & ~E_STRICT & ~E_DEPRECATED);

// You may not need to do this. For development purposes only
set_include_path(
    '../../' . PATH_SEPARATOR . get_include_path()
);

require_once 'PEAR/Config.php';
require_once 'HTML/QuickForm2.php';
//require_once 'HTML/QuickForm2/Element/Captcha/Equation.php';
require_once 'HTML/QuickForm2/Element/Captcha/Image.php';
require_once 'HTML/QuickForm2/Element/Captcha/Numeral.php';
require_once 'HTML/QuickForm2/Element/Captcha/ReCaptcha.php';
//require_once 'HTML/QuickForm2/Element/Captcha/Word.php';

$form = new HTML_QuickForm2(
    'captchaform1',
    'post',
    array(),
    false // Change to true to use special ID in POST data
);

$form->addElement(
    new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
        'captcha[equation]',
        array(
            'id' => 'captcha_equation',
        ),
        array(
            'label' => 'Equation',
            'captchaType' => 'Equation',
        )
    )
);

$form->addElement(
    new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
        'captcha[figlet]',
        array('id' => 'captcha_figlet'),
        array(
            'label' => 'Figlet',
            'captchaType' => 'Figlet',
            'options' => array(
                'font_file' => 'makisupa.flf'
            ),
        )
    )
);

$form->addElement(
    new HTML_QuickForm2_Element_Captcha_Image(
        'captcha[image]',
        array('id' => 'captcha_image'),
        array(
            'label' => 'Image',

            // Captcha options
            'output' => 'png',
            'width'  => 300,
            'height' => 100,

            // Path where to store images
            'imageDir' => __DIR__ . '/tmp/',
            'imageDirUrl' => 'tmp/',

            'imageOptions' => array(
                'font_path'        => '/usr/share/fonts/truetype/ttf-dejavu/',
                'font_file'        => 'DejaVuSans.ttf',
                'text_color'       => '#000000',
                'background_color' => '#ffffff',
                'lines_color'      => '#000000',
            )
        )
    )
);

$form->addElement(
    new HTML_QuickForm2_Element_Captcha_Numeral(
        'captcha[numeral]',
        array('id' => 'captcha_numeral'),
        array(
            'label' => 'Numeral',
        )
    )
);

$form->addElement(
    new HTML_QuickForm2_Element_Captcha_ReCaptcha(
        'captcha[recaptcha]',
        array('id' => 'captcha_recaptcha'),
        array(
            'label' => 'ReCaptcha',
            // Captcha options
            // Please get your own keys. This here is for demo purposes only.
            'public-key'  => '6LduXLoSAAAAAOH1LKWCyyqRsfE6SD6ZHOQg9kpr',
            'private-key' => '6LduXLoSAAAAAH65fkp-xQHvekEBsNrt31SjBRZX'
        )
    )
);

$form->addElement(
    new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
        'captcha[word]',
        array('id' => 'captcha_word'),
        array(
            'label' => 'Word',
            'captchaType' => 'Word',
            'locale' => 'en_US',//de
        )
    )
);

$form->addElement(
    'submit', 'submitted',
    array('id' => 'submit', 'value' => 'Try it')
);

if ($form->validate()) {
    echo '<h3>valid</h3>';

    // Clear the session, otherwise the user can re-submit the form
    // again and again
    foreach ($form->getElements() as $element) {
        if ($element instanceof HTML_QuickForm2_Element_Captcha) {
            $element->clearCaptchaSession();
        }
    }
} else {
    echo '<h3>Form</h3>';
    echo $form;
}

echo '<h2>Form data</h2>';
echo '<pre>';
var_export($form->getValue(), true);
echo '</pre>';

?>

    </body>
</html>