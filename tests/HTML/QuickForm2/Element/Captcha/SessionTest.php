<?php
require_once 'HTML/QuickForm2/Element/Captcha/Session.php';

/**
 * @preserveGlobalState disabled
 */
class HTML_QuickForm2_Element_Captcha_SessionTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException HTML_QuickForm2_Element_Captcha_Exception
     * @expectedExceptionMessage Session must be started for CAPTCHA to work
     */
    public function testSetVarnameException()
    {
        $ses = new HTML_QuickForm2_Element_Captcha_Session();
        $ses->setVarname('foo');
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetVarname()
    {
        session_start();
        $ses = new HTML_QuickForm2_Element_Captcha_Session();
        $ses->setVarname('foo');
        $ses->key = 'value';
        $this->assertEquals('value', $ses->key);

        $ses->setVarname('bar');
        $ses->key = 'other';
        $this->assertEquals('other', $ses->key);

        $ses->setVarname('foo');
        $this->assertEquals('value', $ses->key);
    }

    /**
     * @runInSeparateProcess
     */
    public function test__get()
    {
        session_start();
        $ses = new HTML_QuickForm2_Element_Captcha_Session();
        $ses->setVarname('foo');
        $this->assertNull($ses->key);

        $ses->key = 'other';
        $this->assertEquals('other', $ses->key);
    }

    /**
     * @runInSeparateProcess
     */
    public function testClear()
    {
        session_start();
        $ses = new HTML_QuickForm2_Element_Captcha_Session();
        $ses->setVarname('foo');
        $ses->key = 'other';
        $ses->clear();
        $this->assertNull($ses->key);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetSessionId()
    {
        session_start();
        $ses = new HTML_QuickForm2_Element_Captcha_Session();
        $this->assertNotNull($ses->getSessionId());
    }
}
?>