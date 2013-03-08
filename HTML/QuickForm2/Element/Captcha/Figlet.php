<?php
/**
 * HTML_QuickForm2_Captcha package.
 *
 * PHP version 5
 *
 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Rico Sonntag <rico.sonntag@netresearch.de>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 */

require_once 'HTML/QuickForm2/Element/Captcha/TextCAPTCHA.php';

/**
 * Figlet captcha element for HTML_QuickForm2.
 * Displays a word rendered with an ascii art font.
 *
 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Rico Sonntag <rico.sonntag@netresearch.de>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 * @see      http://pear.php.net/package/Text_CAPTCHA
 */
class HTML_QuickForm2_Element_Captcha_Figlet
    extends HTML_QuickForm2_Element_Captcha_TextCAPTCHA
{
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
        $this->data['captchaType'] = 'Figlet';
        parent::__construct($name, $attributes, $data);
        //fixme: remove it, handle in TextCAPTCHA?
        if (!isset($this->data['options']['font_file'])) {
            throw new HTML_QuickForm2_Exception('Font file required');
        }
    }
}
?>
