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

require_once 'Text/CAPTCHA/Numeral.php';
require_once 'HTML/QuickForm2/Element/Captcha.php';
require_once 'HTML/QuickForm2/Element/Captcha/Exception.php';

/**
 * Numeral captcha element for HTML_QuickForm2.
 * Asks mathematical questions like "32 + 5".
 *
 * In case you need to customize the numeral options,
 * use getNumeral() and modify that object.
 *
 * Features:
 * - Stable captcha: Question stays the same if you do not solve it
 *   correctly the first time
 *
 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @version  Release: @version@
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 * @see      http://pear.php.net/package/Text_CAPTCHA_Numeral
 */
class HTML_QuickForm2_Element_Captcha_Numeral
    extends HTML_QuickForm2_Element_Captcha
{
    /**
     * Captcha generator
     *
     * @var Text_CAPTCHA_Numeral
     */
    protected $numeral = null;

    /**
     * Returns the Text_CAPTCHA_Numeral object used for
     * generating question and answer.
     * Useful for changing options.
     *
     * @return Text_CAPTCHA_Numeral Captcha generator
     */
    public function getNumeral()
    {
        if ($this->numeral === null) {
            $this->numeral = new Text_CAPTCHA_Numeral();
        }
        return $this->numeral;
    }

    /**
     * Sets the Text_CAPTCHA_Numeral object.
     * Useful for changing options.
     *
     * @param Text_CAPTCHA_Numeral $numeral New numeral captcha object
     *
     * @return void
     */
    public function setNumeral(Text_CAPTCHA_Numeral $numeral)
    {
        $this->numeral = $numeral;
    }

    /**
     * Generates the captcha question and answer and prepares the
     * session data.
     *
     * @return boolean True when the captcha has been created newly, false
     *                 if it already existed.
     *
     * @throws HTML_QuickForm2_Element_Captcha_Exception
     *         When the session is not started yet
     */
    protected function generateCaptcha()
    {
        if (!parent::generateCaptcha()) {
            return false;
        }

        $cn = $this->getNumeral();
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
            //no stored captcha answer?
            return false;
        } else if ($userSolution === null
            || $this->getSession()->answer != $userSolution
        ) {
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
     * It is not called when the element is frozen, see getFrozenHtml()
     * for that case.
     * This method is also not called when the captcha has been solved,
     * since $data['captchaSolved'] is shown then.
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
                . $this->getSession()->question
                . '</div>';
        }
        return $prefix . '<input' . $this->getAttributes(true) . ' />';
    }

    /**
     * Returns the HTML code when the form is frozen.
     *
     * @return string HTML code
     */
    public function getFrozenHtml()
    {
        if (!$this->data['captchaRender']) {
            return '';
        }

        return $this->getSession()->question;
    }
}

?>
