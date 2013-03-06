<?php session_start(); ?>

<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title>HTML_QuickForm2_Element_Captcha_Numeral demo</title>
        <style type="text/css">
            span.error {
                color: red;
            }
            div.row {
                clear: both;
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
error_reporting(E_ALL & ~E_STRICT);

// You may not need to do this. For development purposes only
set_include_path(
    '../../'
    . ':' . get_include_path()
);

// Include main quickform class
require_once 'HTML/QuickForm2.php';

// Include the numeral captcha class file. necessary because
// the QuickForm2 Captcha is separate from QuickForm2 itself.
require_once 'HTML/QuickForm2/Element/Captcha/Numeral.php';

// Register the numeral captcha element with QuickForm2
HTML_QuickForm2_Factory::registerElement(
    'numeralcaptcha',
    'HTML_QuickForm2_Element_Captcha_Numeral'
);

// Create a new form
$form = new HTML_QuickForm2(
    'register', 'post'
);

// Add some normal elements

// Username
$username = $form->addElement('text', 'username')
    ->setLabel('Your username');
$username->addRule('required', 'Username is required');
$username->addRule('minlength', 'Username is too short', 3);

// Password
$password = $form->addElement('password', 'password')
    ->setLabel('Your password');
$password->addRule('required', 'Password is required');

// Add the captcha element. no need to add rules!
$captcha = $form->addElement(
    'numeralcaptcha',
    'captchaelem',
    array(
        'id' => 'captchavalue',
    ),

    // Set some captcha specific options
    array(
        'minValue' => 100,
        'maxValue' => 200,
    )
)->setLabel('Anti-Spam question');

// Submit button
$form->addElement(
    'submit', 'submitted',
    array(
        'id'    => 'submit',
        'value' => 'Try it',
    )
);

if ($form->validate()) {
    echo '<h3>Form data valid</h3>';
    echo 'In a real form, we would register the user now with the following data:';
    echo '<pre>';
    var_dump($form->getValue());
    echo '</pre>';

    // Clear the session, otherwise the user can re-submit the form
    // again and again without solving the captcha again
    $captcha->clearCaptchaSession();
} else {
    echo $form;
}

?>

    </body>
</html>