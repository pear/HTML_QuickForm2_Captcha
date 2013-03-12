<?php
require_once 'HTML/QuickForm2/Element/Captcha/Numeral.php';
require_once 'HTML/QuickForm2/Element/Captcha/TextCAPTCHA.php';
require_once 'HTML/QuickForm2/Element/Captcha/Session/Mock.php';

class HTML_QuickForm2_Element_CaptchaTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        //for text_captcha
        error_reporting(error_reporting() & ~E_STRICT);
    }

    public function testGetSession()
    {
        $c = new HTML_QuickForm2_Element_Captcha_Numeral();
        $ses = $c->getSession();
        $this->assertInstanceOf(
            'HTML_QuickForm2_Element_Captcha_Session', $ses
        );
    }

    public function testValidateCorrect()
    {
        $tc = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
            null, null, array('captchaType' => 'Word', 'phrase' => '123')
        );
        $tc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());
        $tc->setValue('123');

        $vcm = new ReflectionMethod(get_class($tc), 'validate');
        $vcm->setAccessible(true);
        $this->assertTrue($vcm->invoke($tc));
    }

    public function testValidateWrong()
    {
        $tc = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
            null, null, array('captchaType' => 'Word', 'phrase' => '123')
        );
        $tc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());
        $tc->setValue('234');

        $vcm = new ReflectionMethod(get_class($tc), 'validate');
        $vcm->setAccessible(true);
        $this->assertFalse($vcm->invoke($tc));
    }

    public function testValidateNoAnswerGiven()
    {
        $tc = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
            null, null, array('captchaType' => 'Word', 'phrase' => '123')
        );
        $tc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());

        $vcm = new ReflectionMethod(get_class($tc), 'validate');
        $vcm->setAccessible(true);
        $this->assertFalse($vcm->invoke($tc));
    }

    public function testClearCaptchaSession()
    {
        $ses = $this->getMock(
            'HTML_QuickForm2_Element_Captcha_Session_Mock',
            array('clear')
        );
        $ses->expects($this->once('clear'))->method('clear');
        $tc = new HTML_QuickForm2_Element_Captcha_Numeral();
        $tc->setSession($ses);
        $tc->clearCaptchaSession();
    }

    public function testGetType()
    {
        $tc = new HTML_QuickForm2_Element_Captcha_Numeral();
        $this->assertEquals('captcha', $tc->getType());
    }
}

?>
