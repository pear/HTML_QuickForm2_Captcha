<?php session_start(); ?>
<?xml version="1.0" encoding="utf-8"?>
<html>
 <head>
  <title>HTML_QuickForm2_Element_Captcha demo</title>
 </head>
 <body>
<?php
require_once 'HTML/QuickForm2.php';
require_once 'HTML/QuickForm2/Renderer.php';
require_once 'Captcha.php';

HTML_QuickForm2_Factory::registerElement(
    'captcha',
    'HTML_QuickForm2_Element_Captcha'
);

$form = new HTML_QuickForm2(
    'captchaform1', 'post',
    array(),
    false//change to true to use special ID
);

$form->addElement(
    'captcha', 'captchaelem',
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
    'captcha', 'captchaelem',
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