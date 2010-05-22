<?php
/**
 * HTML_QuickForm2 package.
 *
 * PHP version 5
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://opensource.org/licenses/bsd-license.php New BSD License
 * @version  SVN: $Id: InputText.php 294057 2010-01-26 21:10:28Z avb $
 * @link     http://pear.php.net/package/HTML_QuickForm2
 */

require_once 'Text/CAPTCHA/Numeral.php';
require_once 'HTML/QuickForm2/Element/Captcha.php';

/**
 * Numeral Captcha element for QuickForm2.
 * Asks mathematical questions like "32 + 5".
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://pear.php.net/package/HTML_QuickForm2
 *
 * @FIXME/@TODO
 * - set custom numeral object
 * - support options
 */
class HTML_QuickForm2_Element_NumeralCaptcha
    extends HTML_QuickForm2_Element_Captcha
{
    /**
     * Returns an array with captcha question and captcha answer
     *
     * @return array Array with first value the captcha question
     *               and the second one the captcha answer.
     */
    protected function generateCaptchaQA()
    {
        $cn = new Text_CAPTCHA_Numeral();
        return array(
            $cn->getOperation(),
            $cn->getAnswer()
        );
    }
}


?>