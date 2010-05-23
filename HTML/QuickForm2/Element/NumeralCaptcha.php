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
 * Features:
 * - Stable captcha: Question stays the same if you do not solve it
 *   correctly the first time
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://pear.php.net/package/HTML_QuickForm2
 * @see      http://pear.php.net/package/Text_CAPTCHA_Numeral
 *
 * @FIXME/@TODO
 * - set custom numeral object
 */
class HTML_QuickForm2_Element_NumeralCaptcha
    extends HTML_QuickForm2_Element_Captcha
{
    /**
     * Generates the captcha question and answer and prepares the
     * session data.
     *
     * @return boolean True when the captcha has been created newly, false
     *                 if it already existed.
     *
     * @throws HTML_QuickForm2_Exception When the session is not started yet
     */
    protected function generateCaptcha()
    {
        $varname = $this->getSessionVarName();
        if (!parent::generateCaptcha()) {
            return false;
        }

        $cn = new Text_CAPTCHA_Numeral();
        $this->getSession()->question = $cn->getOperation();
        $this->getSession()->answer   = $cn->getAnswer();

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
     * @return boolean True if the captcha is solved
     */
    protected function verifyCaptcha()
    {
        //check session and generate captcha if necessary
        if (parent::verifyCaptcha()) {
            return true;
        }

        //verify given answer with our answer
        $userSolution = $this->getValue();
        if ($this->getSession()->answer === null) {
            //no captcha answer?
            return false;
        } else if ($this->getSession()->answer != $userSolution) {
            return false;
        } else {
            $this->getSession()->solved = true;
            return true;
        }
    }



    /**
     * Returns the HTML for the captcha question and answer.
     *
     * Used in __toString() and to be used when $data['captchaRender']
     * is set to false.
     *
     * Uses $data['captchaHtmlAttributes'].
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
                . $this->getSession()->question
                . '</div>';
        }
        return $prefix
            . '<input' . $this->getAttributes(true) . ' />';
    }

}


?>