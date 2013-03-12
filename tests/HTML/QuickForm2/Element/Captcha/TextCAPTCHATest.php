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

    public function testLoadAdapterError()
    {
        $tc = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
            null, null,
            array(
                'captchaType' => 'Figlet',
                'options' => array('fontFile' => 'doesnotexist.flf')
            )
        );
        $tc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());

        //force initialization
        $f = (string)$tc;
        $this->assertContains(
            'Error: Error loading Text_Figlet font', $f
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

    public function testSetAdapter()
    {
        $tc = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA();
        $tc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());

        $tca = Text_CAPTCHA::factory('Word');
        $tc->setAdapter($tca);

        $f = (string)$tc;

        $adapter = $tc->getAdapter();
        $this->assertNotNull($adapter);
        $this->assertSame($adapter, $tca);
    }

    public function testVerifyCaptchaCorrect()
    {
        $tc = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
            null, null, array('captchaType' => 'Word', 'phrase' => '123')
        );
        $tc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());

        $tc->setValue('123');

        $vcm = new ReflectionMethod(get_class($tc), 'verifyCaptcha');
        $vcm->setAccessible(true);
        $this->assertTrue($vcm->invoke($tc));
    }

    public function testVerifyCaptchaWrong()
    {
        $tc = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
            null, null, array('captchaType' => 'Word', 'phrase' => '123')
        );
        $tc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());

        $tc->setValue('234');

        $vcm = new ReflectionMethod(get_class($tc), 'verifyCaptcha');
        $vcm->setAccessible(true);
        $this->assertFalse($vcm->invoke($tc));
    }

    public function testVerifyCaptchaWrongZero()
    {
        $tc = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
            null, null, array('captchaType' => 'Word', 'phrase' => '000')
        );
        $tc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());
        //no setValue() call
        $vcm = new ReflectionMethod(get_class($tc), 'verifyCaptcha');
        $vcm->setAccessible(true);
        $this->assertFalse($vcm->invoke($tc));
    }

    public function testAlreadySolved()
    {
        $tc = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
            null, null, array('captchaType' => 'Word', 'phrase' => '123')
        );
        $tc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());

        $tc->setValue(123);
        $one = (string)$tc;
        $two = (string)$tc;

        $this->assertContains('Captcha already solved', $one);
        $this->assertEquals($one, $two);
    }

    public function testGenerateCaptchaSameSession()
    {
        $ses = new HTML_QuickForm2_Element_Captcha_Session_Mock();
        $tc = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
            'foo', array('id' => 'foo'),
            array('captchaType' => 'Word', 'phrase' => '123')
        );
        $tc->setSession($ses);
        $one = (string)$tc;

        $tc2 = new HTML_QuickForm2_Element_Captcha_TextCAPTCHA(
            'foo', array('id' => 'foo'),
            array('captchaType' => 'Word', 'phrase' => '123')
        );
        $tc2->setSession($ses);
        $two = (string)$tc2;

        $this->assertEquals($one, $two);
    }
}
?>
