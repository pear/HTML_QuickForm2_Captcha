<?php
require_once 'HTML/QuickForm2/Element/Captcha/Numeral.php';
require_once 'HTML/QuickForm2/Element/Captcha/Session/Mock.php';

class HTML_QuickForm2_Element_CaptchaTest extends PHPUnit_Framework_TestCase
{
    public function testGetSession()
    {
        $c = new HTML_QuickForm2_Element_Captcha_Numeral();
        $ses = $c->getSession();
        $this->assertInstanceOf(
            'HTML_QuickForm2_Element_Captcha_Session', $ses
        );
    }
}

?>
