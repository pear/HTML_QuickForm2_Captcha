<?php session_start(); ?>
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
 <head>
  <title>HTML_QuickForm2 Numeral Captcha demo</title>
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
//you do not need this. for development purposes only
set_include_path(
    '../'
    . ':' . get_include_path()
);

//include main quickform class
require_once 'HTML/QuickForm2.php';
//include the numeral captcha class file. necessary because
// the QuickForm2 Captcha is separate from QuickForm2 itself.
require_once 'HTML/QuickForm2/Element/NumeralCaptcha.php';

//register the numeral captcha element with QuickForm2
HTML_QuickForm2_Factory::registerElement(
    'numeralcaptcha',
    'HTML_QuickForm2_Element_NumeralCaptcha'
);

//create a new form
$form = new HTML_QuickForm2(
    'register', 'post'
);

//add some normal elements
//username
$username = $form->addElement(
    'text', 'username'
)->setLabel('Your username');
$username->addRule('required', 'Username is required');
$username->addRule('minlength', 'Username is too short', 3);

//password
$password = $form->addElement(
    'password', 'password'
)->setLabel('Your password');
$password->addRule('required', 'Password is required');


//add the captcha element. no need to add rules!
$captcha = $form->addElement(
    'numeralcaptcha', 'captchaelem',
    array(
        'id'   => 'captchavalue',
    )
)->setLabel('Anti-Spam question');


//the following lines are optional. We use them to make the captcha
// a bit harder. remove the comments around them to see them in action
/*
$num = new Text_CAPTCHA_Numeral(
    //mathematical operations: + - *
    Text_CAPTCHA_Numeral::TEXT_CAPTCHA_NUMERAL_COMPLEXITY_HIGH_SCHOOL,
    //use numbers from 10 to 20
    10, 20
);
$captcha->setNumeral($num);
*/


//submit button
$form->addElement(
    'submit', 'submitted',
    array('id' => 'submit', 'value' => 'Try it')
);

if ($form->validate()) {
    echo '<h3>Form data valid</h3>';
    echo 'In a real form, we would register the user now with the following data:';
    echo '<pre>';
    var_dump($form->getValue());
    echo '</pre>';

    //clear the session, otherwise the user can re-submit the form
    // again and again without solving the captcha again
    $captcha->clearCaptchaSession();
} else {
    echo $form;
}

?>
 </body>
</html>