<?php session_start(); ?>
<?xml version="1.0" encoding="utf-8"?>
<html>
 <head>
  <title>HTML_QuickForm2_Element_Captcha demo</title>
  <style type="text/css">
span.error {
  color: red;
}
div.element.error input {
  background-color: #FAA;
}
  </style>
 </head>
 <body>
<?php
set_include_path(
    '../'
    . ':' . get_include_path()
);
require_once 'HTML/QuickForm2.php';
require_once 'HTML/QuickForm2/Renderer.php';
require_once '../HTML/QuickForm2/Element/NumeralCaptcha.php';

HTML_QuickForm2_Factory::registerElement(
    'numeralcaptcha',
    'HTML_QuickForm2_Element_NumeralCaptcha'
);

$form = new HTML_QuickForm2(
    'captchaform1', 'post',
    array(),
    false//change to true to use special ID
);

$form->addElement(
    'numeralcaptcha', 'captchaelem',
    array(
        'id'   => 'captchavalue',
    )
);

$form->addElement(
    'submit', 'submitted',
    array('id' => 'submit', 'value' => 'Try it')
);

if ($form->validate()) {
    echo '<h3>valid</h3>';
} else {
    echo '<h3>INvalid</h3>';
}
echo '<pre>Data: ';
var_dump($form->getValue());
echo '</pre>';

$renderer = HTML_QuickForm2_Renderer::factory('default');
echo $form->render($renderer);




//----------------------
$form = new HTML_QuickForm2(
    'captcha2', 'post',
    array(),
    true
);

$form->addElement(
    'numeralcaptcha', 'captchaelem',
    array(
        'id'   => 'captchavalue',
    )
);

$form->addElement(
    'submit', 'submitted',
    array('id' => 'submit', 'value' => 'Try it on form 2')
);

if ($form->validate()) {
    echo '<h3>valid</h3>';
} else {
    echo '<h3>INvalid</h3>';
}
echo '<pre>Data: ';
var_dump($form->getValue());
echo '</pre>';

$renderer = HTML_QuickForm2_Renderer::factory('default');
echo $form->render($renderer);



?>
 </body>
</html>