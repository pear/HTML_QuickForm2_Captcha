<?php
require_once 'HTML/QuickForm2/Element/Captcha/Image.php';
require_once 'HTML/QuickForm2/Element/Captcha/Session/Mock.php';

class HTML_QuickForm2_Element_Captcha_ImageTest
    extends PHPUnit_Framework_TestCase
{
    protected $tmpDir;


    public function setUp()
    {
        error_reporting(error_reporting() & ~E_STRICT);
    }

    protected function cleanUp()
    {
        $imgfile = sys_get_temp_dir() . '/dummy-sid.png';
        if (file_exists($imgfile)) {
            unlink($imgfile);
        }
    }

    public function tearDown()
    {
        $this->cleanUp();
    }

    protected function getImageCaptcha()
    {
        $this->tmpDir = sys_get_temp_dir();
        $ic = new HTML_QuickForm2_Element_Captcha_Image(
            'foo', array('id' => 'foo'), array(
                'imageDir' => $this->tmpDir,
                'imageDirUrl' => '/path/to/temp/',
                'imageOptions' => array(
                    'font_path' => __DIR__ . '/../../../../data/',
                    'font_file' => 'LiberationMono-Regular.ttf',
                ),
                'phrase' => 'foo123'
            )
        );
        $ic->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());
        return $ic;
    }

    public function test__toStringNoFontFile()
    {
        $ic = new HTML_QuickForm2_Element_Captcha_Image();
        $ic->setSession(new HTML_QuickForm2_Element_Captcha_Session_Mock());

        //force initialization
        $f = (string)$ic;
        $this->assertEquals(
            '<div class="captcha-exception">'
            . 'Error: Error initializing Image_Text (You must supply a font file.)'
            . '</div>',
            $f
        );
    }

    public function test__toStringAllFine()
    {
        $ic = $this->getImageCaptcha();
        //force initialization
        $f = (string)$ic;
        $this->assertTrue(
            file_exists($this->tmpDir . '/dummy-sid.png'),
            'captcha image file missing'
        );
        $this->assertContains('/path/to/temp/dummy-sid.png?ts=', $f);
    }

    public function test__toStringSolved()
    {
        $ic = $this->getImageCaptcha();
        $ic->setValue('foo123');
        //force initialization
        $f = (string)$ic;
        $this->assertContains('Captcha already solved', $f);
    }

    public function test__toStringAgain()
    {
        $ic = $this->getImageCaptcha();
        $ses = $ic->getSession();
        //force initialization
        $f = (string)$ic;
        $firsthash = md5_file($this->tmpDir . '/dummy-sid.png');

        $ic2 = $this->getImageCaptcha();
        $ic2->setSession($ses);
        $f = (string)$ic2;

        $this->assertEquals(
            $firsthash,
            md5_file($this->tmpDir . '/dummy-sid.png'),
            'image file contents should be identical'
        );
    }

    public function test__toStringAgainDeleted()
    {
        $ic = $this->getImageCaptcha();
        $ses = $ic->getSession();
        //force initialization
        $f = (string)$ic;

        unlink($this->tmpDir . '/dummy-sid.png');

        $ic2 = $this->getImageCaptcha();
        $ic2->setSession($ses);
        $f = (string)$ic2;
        $this->assertTrue(
            file_exists($this->tmpDir . '/dummy-sid.png'),
            'captcha image should have been regenerated'
        );
    }
}

?>
