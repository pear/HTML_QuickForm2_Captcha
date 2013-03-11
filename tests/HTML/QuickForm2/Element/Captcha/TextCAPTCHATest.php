<?php
require_once 'HTML/QuickForm2/Element/Captcha/TextCAPTCHA.php';
require_once 'HTML/QuickForm2/Element/Captcha/Session/Mock.php';

class HTML_QuickForm2_Element_Captcha_TextCAPTCHATest
    extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        error_reporting(error_reporting() & ~E_STRICT);
    }

    public function testNoCaptchaType()
    {
        $tc = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA();
        $tc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());

        //force initialization
        $f = (string)$tc;
        $this->assertContains('Error: data[captchaType] is not set', $f);
    }

    public function testAdapterInitError()
    {
        $tc = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
            null, null,
            array(
                'captchaType' => 'Equation',
                'severity' => 100
            )
        );
        $tc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());

        //force initialization
        $f = (string)$tc;
        $this->assertContains(
            'Error: Equation complexity of 100 not supported', $f
        );
    }

    public function testGetAdapter()
    {
        $tc = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
            null, null, array('captchaType' => 'Word')
        );
        $tc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());

        //force initialization
        $f = (string)$tc;

        $adapter = $tc->getAdapter();
        $this->assertNotNull($adapter);
        $this->assertInstanceOf('Text_CAPTCHA_Driver_Word', $adapter);
    }
}
?>
