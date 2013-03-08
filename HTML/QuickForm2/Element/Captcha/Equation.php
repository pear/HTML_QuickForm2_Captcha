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

/**
 * Equation captcha element for HTML_QuickForm2.
 * Displays an mathematical equation to solve by the user.
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
class HTML_QuickForm2_Element_Captcha_Equation
    extends HTML_QuickForm2_Element_Captcha_Text
{
    /**
     * Type of text captcha to create.
     *
     * @var string
     */
    protected $captchaType = 'Equation';
}
