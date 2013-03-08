<?php
declare(encoding = 'UTF-8');

/**
 * HTML_QuickForm2_Captcha package.
 *
 * PHP version 5
 *
 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Rico Sonntag <mail@ricosonntag.de>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 */

/**
 * Includes
 */
require_once 'Text/CAPTCHA.php';
require_once 'HTML/QuickForm2/Element/Captcha/Text.php';

/**
 * Image captcha element for HTML_QuickForm2.
 * Displays an captcha rendered as image. Some obfuscation is
 * applied to the image.
 *
 * In case you need to customize the options, use getAdapter() method
 * and modify that object or pass options as $data.
 *
 * Features:
 * - Stable captcha: Question stays the same if you do not solve it
 *   correctly the first time
 *
 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Rico Sonntag <mail@ricosonntag.de>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 * @see      http://pear.php.net/package/Text_CAPTCHA
 */
class HTML_QuickForm2_Element_Captcha_Image
    extends HTML_QuickForm2_Element_Captcha_Text
{
    /**
     * Type of text captcha to create.
     *
     * @var string
     */
    protected $captchaType = 'Image';

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
    protected $imageUrl = null;

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
        parent::__construct($name, $attributes, $data);

        if (isset($data['imageDir'])) {
            $this->imageDir = self::fixPath($data['imageDir']);
        }

        if (isset($data['imageUrl'])) {
            $this->imageUrl = self::fixPath($data['imageUrl']);
        }

        if (!isset($data['output'])) {
            $data['output'] = 'png';
        }

        $this->imageSuffix = '.' . $data['output'];
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
            if (is_file($this->imageDir . $this->getSession()->question)) {
                return false;
            }

            // Create a new captcha if file no longer exists
            $this->getSession()->clear();
            $this->getSession()->solved = false;
        }

        $captchaFile = md5(session_id()) . $this->imageSuffix;

        $this->getSession()->question = $captchaFile;
        $this->getSession()->answer   = $this->adapter->getPhrase();

        // Clean up old stuff
        if (mt_rand(1, 10) == 1) {
            $this->garbageCollection();
        }

        // Save image to file
        file_put_contents(
            $this->imageDir . $captchaFile,
            $this->adapter->getCAPTCHA()
        );

        return true;
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
                . '<img width="' . $this->adapter->_width
                . '" height="' . $this->adapter->_height
                . '" alt="" src="' . $this->imageUrl
                . $this->getSession()->question
                . '?ts=' . time() . '" />'
                . '</div>';
        }

        return $prefix
            . '<input' . $this->getAttributes(true) . ' />';
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
