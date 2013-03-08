<?php
declare(encoding = 'UTF-8');

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

/**
 * Includes
 */
require_once 'HTML/QuickForm2/Element/Captcha/Text.php';

/**
 * Numeral captcha element for HTML_QuickForm2.
 * Asks mathematical questions like "32 + 5".
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
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 * @see      http://pear.php.net/package/Text_CAPTCHA
 */
class HTML_QuickForm2_Element_Captcha_Numeral
    extends HTML_QuickForm2_Element_Captcha_Text
{
    /**
     * Type of text captcha to create.
     *
     * @var string
     */
    protected $captchaType = 'Numeral';
}
