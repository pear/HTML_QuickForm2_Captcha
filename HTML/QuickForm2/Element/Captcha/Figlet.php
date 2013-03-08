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
require_once 'HTML/QuickForm2/Element/Captcha/Text.php';
require_once 'HTML/QuickForm2/Exception.php';

/**
 * Figlet captcha element for HTML_QuickForm2.
 * Displays a word rendered with an ascii art font.
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
class HTML_QuickForm2_Element_Captcha_Figlet
    extends HTML_QuickForm2_Element_Captcha_Text
{
    /**
     * Type of text captcha to create.
     *
     * @var string
     */
    protected $captchaType = 'Figlet';

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
        if (!isset($data['options']['font_file'])) {
            throw new HTML_QuickForm2_Exception('Font file required');
        }

        parent::__construct($name, $attributes, $data);
    }
}
