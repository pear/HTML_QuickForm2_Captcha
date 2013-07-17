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
 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Rico Sonntag <rico.sonntag@netresearch.de>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @version  Release: @version@
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
     * Set adapter specific data attributes.
     *
     * Special $data attributes:
     * - imageDir - Full path to captcha image storage directory
     * - imageDirUrl - URL-based path to captcha image storage directory
     * - output      - Image format ("png", "jpg", "gif", "resource")
     * - width       - Image width
     * - height      - Image height
     * - phrase        - Pre-defined captcha answer
     * - phraseOptions - Array of options for Text_Password
     * - imageOptions: array(
     *       font_size
     *       font_path
     *       font_file
     *       text_color
     *       background_color
     *       lines_color
     *       antialias
     *   )
     *
     * @param string $name       Element name
     * @param mixed  $attributes Attributes (either a string or an array)
     * @param array  $data       Element data (special captcha settings)
     */
    public function __construct(
        $name = null, $attributes = null, $data = array()
    ) {
        $this->data['captchaType'] = 'Image';

        if (!isset($this->data['captchaHtmlAttributes']['class'])) {
            $this->data['captchaHtmlAttributes']['class'] = '';
        }
        $this->data['captchaHtmlAttributes']['class']
            .= ' qf2-captcha-image';

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
            if (is_file($this->imageDir . $this->getSession()->question)) {
                return false;
            }

            // Create a new captcha if file no longer exists
            $this->getSession()->clear();
            $this->getSession()->solved = false;
            $this->loadAdapter();
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

        $this->garbageCollection();

        return true;
    }

    /**
     * Renders the CAPTCHA question in HTML and returns it.
     * Returns empty string when "captchaRender" option is false.
     *
     * @return string HTML
     */
    protected function renderQuestion()
    {
        if (!$this->data['captchaRender']) {
            return '';
        }

        return  '<div'
            . self::getAttributesString(
                $this->data['captchaHtmlAttributes']
            ) . '>'
            . '<img width="' . intval($this->getSession()->imageWidth) . '"'
            . ' height="' . intval($this->getSession()->imageHeight) . '"'
            . ' alt="CAPTCHA"'
            . ' src="' . htmlspecialchars(
                $this->imageDirUrl . $this->getSession()->question
                . '?ts=' . time()
            ) . '"'
            . '/>'
            . '</div>';
    }

    /**
     * Remove old files from image directory.
     *
     * @return void
     */
    protected function garbageCollection()
    {
        // Clean up old images
        if (mt_rand(1, 10) != 1) {
            return;
        }
        if (!$this->imageDir || (strlen($this->imageDir) < 2)) {
            return;
        }

        $suffixLength = strlen($this->imageSuffix);
        $expireTime   = time() - 600;

        foreach (new DirectoryIterator($this->imageDir) as $file) {
            if (!$file->isDot() && !$file->isDir()) {
                if ($file->getMTime() < $expireTime) {
                    if (substr($file->getFilename(), -$suffixLength) == $this->imageSuffix) {
                        unlink($file->getPathname());
                    }
                }
            }
        }
    }
}
?>
