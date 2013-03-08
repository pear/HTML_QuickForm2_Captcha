<?php
/**
 * HTML_QuickForm2_Captcha package.
 *
 * PHP version 5
 *
 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 */

require_once 'HTML/QuickForm2/Element/Captcha/TextCAPTCHA.php';

/**
 * Image captcha element for HTML_QuickForm2.
 * Displays an captcha rendered as image.
 * Some obfuscation is applied to the image.
 *
 *
 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Rico Sonntag <rico.sonntag@netresearch.de>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 * @see      http://pear.php.net/package/Text_CAPTCHA
 */
class HTML_QuickForm2_Element_Captcha_Image
    extends HTML_QuickForm2_Element_Captcha_TextCAPTCHA
{
    /**
     * Image cache path.
     *
     * @var string
     */
    protected $imageDir = null;

    /**
     * URL to cache path.
     *
     * @var string
     */
    protected $imageDirUrl = null;

    /**
     * Image cache path.
     *
     * @var string
     */
    protected $imageSuffix = '.png';

    /**
     * Constructor. Set adapter specific data attributes.
     *
     * @param string $name       Element name
     * @param mixed  $attributes Attributes (either a string or an array)
     * @param array  $data       Element data (special captcha settings)
     */
    public function __construct(
        $name = null, $attributes = null, $data = array()
    ) {
        $this->data['captchaType'] = 'Image';
        if (isset($data['imageDir'])) {
            $this->imageDir = self::fixPath($data['imageDir']);
        }

        if (isset($data['imageDirUrl'])) {
            $this->imageDirUrl = self::fixPath($data['imageDirUrl']);
        }

        if (!isset($data['output'])) {
            $data['output'] = 'png';
        }

        $this->imageSuffix = '.' . $data['output'];

        parent::__construct($name, $attributes, $data);
    }

    /**
     * Fix trailing slashes of path.
     *
     * @param string $path Path to fix
     *
     * @return string
     */
    protected static function fixPath($path)
    {
        return rtrim($path, '/') . '/';
    }

    /**
     * Generates the captcha question and answer and prepares the
     * session data.
     *
     * @return boolean TRUE when the captcha has been created newly, FALSE
     *                 if it already existed.
     */
    protected function generateCaptcha()
    {
        if (!parent::generateCaptcha()) {
            return false;
        }

        $session = $this->getSession();

        $captchaFile = $session->getSessionId() . $this->imageSuffix;
        $session->question    = $captchaFile;
        $session->imageHeight = $this->adapter->_height;
        $session->imageWidth  = $this->adapter->_width;

        // Save image to file
        file_put_contents(
            $this->imageDir . $captchaFile,
            $this->adapter->getCAPTCHA()
        );

        // Clean up old images
        if (mt_rand(1, 10) == 1) {
            $this->garbageCollection();
        }

        return true;
    }

    /**
     * Checks if the captcha is solved now.
     * Uses $capSolved variable or user input, which is compared
     * with the pre-set correct answer.
     *
     * Calls generateCaptcha() if it has not been called before.
     *
     * In case user solution and answer match, a session variable
     * is set so that the captcha is seen as completed across
     * form submissions.
     *
     * @uses $capGenerated
     * @uses generateCaptcha()
     *
     * @return boolean TRUE if the captcha is solved
     */
    protected function verifyCaptcha()
    {
        // Check session and generate captcha if necessary
        if (parent::verifyCaptcha()) {
            return true;
        }

        //verify given answer with our answer
        $userSolution = $this->getValue();

        if ($this->getSession()->answer === null) {
            //no captcha answer?
            return false;
        } else {
            if ($this->getSession()->answer != $userSolution) {
                return false;
            } else {
                $this->getSession()->solved = true;
                return true;
            }
        }
    }

    /**
     * Returns the HTML for the captcha question and answer.
     *
     * Used in __toString() and to be used when $data['captchaRender']
     * is set to false.
     *
     * @uses $data['captchaHtmlAttributes'].
     *
     * @return string HTML code
     */
    public function getCaptchaHtml()
    {
        $prefix = '';

        if ($this->data['captchaRender']) {
            $prefix = '<div'
                . self::getAttributesString(
                    $this->data['captchaHtmlAttributes']
                ) . '>'
                . '<img width="' . intval($this->getSession()->imageWidth) . '"'
                . ' height="' . intval($this->getSession()->imageHeight) . '"'
                . ' alt="CAPTCHA"'
                . ' src="' . htmlspecialchars(
                    $this->imageDirUrl . $this->getSession()->question
                ) . '"'
                . '/>'
                . '</div>';
        }

        return $prefix . '<input' . $this->getAttributes(true) . ' />';
    }

    /**
     * Remove old files from image directory.
     *
     * @return void
     */
    protected function garbageCollection()
    {
        if (!$this->imageDir || (strlen($this->imageDir) < 2)) {
            return;
        }

        $suffixLength = strlen($this->imageSuffix);
        $expireTime   = time() - 600;

        foreach (new DirectoryIterator($this->imageDir) as $file) {
            if (!$file->isDot() && !$file->isDir()) {
                if ($file->getMTime() < $expireTime) {
                    if (substr($file->getFilename(), -$suffixLength)
                        == $this->imageSuffix
                    ) {
                        unlink($file->getPathname());
                    }
                }
            }
        }
    }
}
?>
