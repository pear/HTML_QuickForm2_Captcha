<?php
require_once 'HTML/QuickForm2/Element/Captcha/Numeral.php';
require_once 'HTML/QuickForm2/Element/Captcha/Session/Mock.php';

class HTML_QuickForm2_Element_Captcha_NumeralTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HTML_QuickForm2_Element_Captcha_Numeral
     */
    protected $nc;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->nc = new HTML_QuickForm2_Element_Captcha_Numeral();
        $this->nc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());
    }

    /**
     * Test getNumeral()
     */
    public function testGetNumeral()
    {
        $num = $this->nc->getNumeral();
        $this->assertInstanceOf(
            'Text_CAPTCHA_Numeral', $num
        );

        //check if the same object is returned every time
        $this->assertEquals($num, $this->nc->getNumeral());
    }

    /**
     * Test setNumeral() by setting it to a new value, retrieving
     * it again and verifying that the retrieved and the set ones
     * are the same.
     */
    public function testSetNumeral()
    {
        $num = $this->nc->getNumeral();

        $cap = new Text_CAPTCHA_Numeral();
        $this->assertNotEquals($cap, $num);
        $this->nc->setNumeral($cap);

        $num2 = $this->nc->getNumeral();
        $this->assertEquals($cap, $num2);
    }

    public function testGenerateCaptchaTwoTimes()
    {
        $ses = new HTML_QuickForm2_Element_Captcha_Session_Mock($ses);
        $this->nc->setSession($ses);

        $gc = new ReflectionMethod(get_class($this->nc), 'generateCaptcha');
        $gc->setAccessible(true);
        $this->assertTrue($gc->invoke($this->nc));

        $this->assertFalse(
            $gc->invoke($this->nc),
            'Calling generateCaptcha() a second time should not regenerate it'
        );
    }

    public function testVerifyCaptchaNoAnswerInSession()
    {
        $ses = new HTML_QuickForm2_Element_Captcha_Session_Mock($ses);
        $this->nc->setSession($ses);

        $vc = new ReflectionMethod(get_class($this->nc), 'verifyCaptcha');
        $vc->setAccessible(true);
        $this->assertFalse($vc->invoke($this->nc));

        //remove answer to simulate a broken session
        $ses->answer = null;
        $this->assertFalse($vc->invoke($this->nc));
    }

    public function testVerifyCaptchaAlreadySolved()
    {
        $ses = new HTML_QuickForm2_Element_Captcha_Session_Mock($ses);
        $this->nc->setSession($ses);

        $vc = new ReflectionMethod(get_class($this->nc), 'verifyCaptcha');
        $vc->setAccessible(true);
        $this->assertFalse($vc->invoke($this->nc));

        //set captcha in session to solved
        $ses->solved = true;
        $this->assertTrue($vc->invoke($this->nc));
    }

    /**
     * Check the generated HTML for well-formedness.
     */
    public function testGetCaptchaHtml()
    {
        //we cannot test getCaptchaHtml() alone because
        // verifyCaptcha() is not called yet at that place.
        // Using __toString() before is needed to generate
        // the captcha question and answer itself.
        (string)$this->nc;

        $str = $this->nc->getCaptchaHtml();
        $xml = '<?xml version="1.0" encoding="utf-8"?>'
            . "\n<test>\n"
            . $str
            . "\n</test>";

        //this is a cheap way to see if the xml is well-formed
        $this->assertTag(array('test'), $xml, '', false);
    }

    public function testSolutionZero()
    {
        //make sure we have a known captcha question
        $cap = new Text_CAPTCHA_Numeral(
            Text_CAPTCHA_Numeral::TEXT_CAPTCHA_NUMERAL_COMPLEXITY_ELEMENTARY,
            10, 10
        );
        $p = new ReflectionProperty('Text_CAPTCHA_Numeral', 'operator');
        $p->setAccessible(true);
        $p->setValue($cap, '-');

        $m = new ReflectionMethod('Text_CAPTCHA_Numeral', 'setOperation');
        $m->setAccessible(true);
        $m->invoke($cap);
        $this->nc->setNumeral($cap);

        //force session and question intialisation
        (string)$this->nc;
        $this->assertFalse(
            $this->nc->getSession()->solved,
            'Captcha should not be solved already'
        );
        $this->assertSame(null, $this->nc->getValue());

        $str = (string)$this->nc;
        $data = $this->nc->getData();
        $this->assertContains('10 - 10', $str);
    }


    /**
     * Tests if __toString() renders the captcha question
     * and input element in the normal case (form not filled yet)
     * when the captcha is not solved yet.
     */
    public function test__toStringNormal()
    {
        //make sure we have a known captcha question
        $cap = new Text_CAPTCHA_Numeral(
            Text_CAPTCHA_Numeral::TEXT_CAPTCHA_NUMERAL_COMPLEXITY_ELEMENTARY,
            10, 11
        );
        $this->nc->setNumeral($cap);

        $str = (string)$this->nc;
        $xml = '<?xml version="1.0" encoding="utf-8"?>'
            . "\n<test>\n"
            . $str
            . "\n</test>";

        //this is a cheap way to see if the xml is well-formed
        $this->assertTag(array('test'), $xml, '', false);
    }



    /**
     * Tests if __toString() renders the "captcha solved" message
     * when the captcha is solved.
     */
    public function test__toStringNormalSolved()
    {
        //make sure we have a known captcha question
        $cap = new Text_CAPTCHA_Numeral(
            Text_CAPTCHA_Numeral::TEXT_CAPTCHA_NUMERAL_COMPLEXITY_ELEMENTARY,
            10, 11
        );
        $this->nc->setNumeral($cap);

        //force session and question intialisation
        (string)$this->nc;
        $this->nc->setValue($this->nc->getSession()->answer);

        $str = (string)$this->nc;

        //not empty string
        $this->assertNotEquals('', $str, 'Solved string is empty');

        $data = $this->nc->getData();
        $this->assertEquals($data['captchaSolved'], $str);
    }



    /**
     * Tests if __toString() renders the captcha question
     * and input element in the frozen case when the captcha
     * is not solved yet.
     */
    public function test__toStringFrozen()
    {
        $this->nc->toggleFrozen(true);
        //make sure we have a known captcha question
        $cap = new Text_CAPTCHA_Numeral(
            Text_CAPTCHA_Numeral::TEXT_CAPTCHA_NUMERAL_COMPLEXITY_ELEMENTARY,
            10, 11
        );
        $this->nc->setNumeral($cap);

        $str = (string)$this->nc;
        //not empty string
        $this->assertNotEquals('', $str, 'Frozen string is empty');

        //FIXME: check for equation when we get a new numeral captcha
        // version
    }



    /**
     * Tests if __toString() renders the "captcha solved" message
     * when the captcha is solved and frozen.
     */
    public function test__toStringFrozenSolved()
    {
        $this->nc->toggleFrozen(true);

        //make sure we have a known captcha question
        $cap = new Text_CAPTCHA_Numeral(
            Text_CAPTCHA_Numeral::TEXT_CAPTCHA_NUMERAL_COMPLEXITY_ELEMENTARY,
            10, 11
        );
        $this->nc->setNumeral($cap);

        //force session and question intialisation
        (string)$this->nc;
        $this->nc->setValue($this->nc->getSession()->answer);

        $str = (string)$this->nc;

        //not empty string
        $this->assertNotEquals('', $str, 'Frozen solved string is empty');

        $data = $this->nc->getData();
        $this->assertEquals($data['captchaSolved'], $str);
    }



    /**
     * Tests if __toString() does not render the captcha question
     * when question rendering is turned off and the element is frozen.
     */
    public function test__toStringFrozenNoRender()
    {
        $this->nc = new HTML_QuickForm2_Element_Captcha_Numeral(
            null, null, array('captchaRender' => false)
        );
        $this->nc->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());
        $this->nc->toggleFrozen(true);

        $str = (string)$this->nc;
        //not empty string
        $this->assertEquals('', $str, 'Frozen string is not empty');
    }
}
?>
