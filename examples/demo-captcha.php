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
require_once 'HTML/QuickForm2/Element/NumeralCaptcha.php';
require_once 'HTML/QuickForm2/Element/ReCaptcha.php';

HTML_QuickForm2_Factory::registerElement(
    'numeralcaptcha',
    'HTML_QuickForm2_Element_NumeralCaptcha'
);
HTML_QuickForm2_Factory::registerElement(
    'recaptcha',
    'HTML_QuickForm2_Element_ReCaptcha'
);

$form = new HTML_QuickForm2(
    'captchaform1', 'post',
    array(),
    false//change to true to use special ID in POST data
);

$form->addElement(
    'numeralcaptcha', 'captchaelem',
    array(
        'id'   => 'captchavalue',
    )
);
$form->addElement(
    'recaptcha', 'recaptchaelem',
    array(
        'id'   => 'recaptchavalue',
    ),
    array(
        //Please get your own keys. This here is for demo purposes only.
        'public-key'  => '6LduXLoSAAAAAOH1LKWCyyqRsfE6SD6ZHOQg9kpr',
        'private-key' => '6LduXLoSAAAAAH65fkp-xQHvekEBsNrt31SjBRZX'
    )
);

$form->addElement(
    'submit', 'submitted',
    array('id' => 'submit', 'value' => 'Try it')
);

if ($form->validate()) {
    echo '<h3>valid</h3>';
    //clear the session, otherwise the user can re-submit the form
    // again and again
    foreach ($form->getElements() as $element) {
        if ($element instanceof HTML_QuickForm2_Element_Captcha) {
            $element->clearCaptchaSession();
        }
    }
} else {
    echo '<h3>INvalid</h3>';
    $renderer = HTML_QuickForm2_Renderer::factory('default');
    echo $form->render($renderer);
}
echo '<pre>Data: ';
var_dump($form->getValue());
echo '</pre>';


?>
 </body>
</html>