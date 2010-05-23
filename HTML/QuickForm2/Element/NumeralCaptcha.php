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
     * Captcha question. Automatically stored in session
     * to make sure the user gets the same captcha every time.
     *
     * @var string
     */
    protected $capQuestion = null;

    /**
     * Answer to the captcha question.
     * The user must input this value.
     *
     * @var string
     */
    protected $capAnswer = null;



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
            $this->capQuestion = $_SESSION[$varname]['question'];
            $this->capAnswer   = $_SESSION[$varname]['answer'];
            return false;
        }

        $cn = new Text_CAPTCHA_Numeral();
        $this->capQuestion = $cn->getOperation();
        $this->capAnswer   = $cn->getAnswer();
        $_SESSION[$varname]['question'] = $this->capQuestion;
        $_SESSION[$varname]['answer']   = $this->capAnswer;
        return true;
    }



    /**
     * Checks if the captcha is solved now.
     * Uses $capSolved variable or user input, which is compared
     * with the pre-set correct answer in $capAnswer.
     *
     * Calls generateCaptcha() if it has not been called before.
     *
     * In case user solution and answer match, a session variable
     * is set so that the captcha is seen as completed across
     * form submissions.
     *
     * @uses $capAnswer
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
        if ($this->capAnswer === null) {
            //no captcha answer?
            return false;
        } else if ($this->capAnswer != $userSolution) {
            return false;
        } else {
            $_SESSION[$this->getSessionVarName()]['solved'] = true;
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
                . $this->capQuestion
                . '</div>';
        }
        return $prefix
            . '<input' . $this->getAttributes(true) . ' />';
    }

}


?>