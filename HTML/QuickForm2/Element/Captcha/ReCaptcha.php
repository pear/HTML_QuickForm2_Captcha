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

require_once 'Services/ReCaptcha.php';
require_once 'HTML/QuickForm2/Element/Captcha.php';
require_once 'HTML/QuickForm2/Element/Captcha/Exception.php';

/**
 * Captcha element utilizing the ReCaptcha service.
 *
 * In case you need to change ReCaptcha settings, use getReCaptcha()
 * and modify the resulting Services_ReCaptcha object.
 *
 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @version  Release: @version@
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 * @link     http://www.recaptcha.net/
 *
 * @FIXME/@TODO
 * - multiple recaptchas in one form
 * - automatically set recaptcha language option
 */
class HTML_QuickForm2_Element_Captcha_ReCaptcha
    extends HTML_QuickForm2_Element_Captcha
{
    /**
     * ReCaptcha instance
     *
     * @var Services_ReCaptcha
     */
    protected $reCaptcha = null;

    /**
     * Checks if the captcha is solved now.
     *
     * @return boolean True if the captcha is solved, false if not.
     */
    protected function verifyCaptcha()
    {
        //check session and generate captcha if necessary
        if (parent::verifyCaptcha()) {
            return true;
        }

        //recaptcha_response_field
        //recaptcha_challenge_field

        //Services_ReCaptcha::validate() may only be called if
        // the form has been submitted. Otherwise we get nasty
        // errors.
        $isSubmitted = false;
        foreach ($this->getContainer()->getDataSources() as $ds) {
            if ($ds instanceof HTML_QuickForm2_DataSource_Submit) {
                $isSubmitted = true;
                break;
            }
        }
        if (!$isSubmitted) {
            return false;
        }

        if ($this->getReCaptcha()->validate()) {
            $this->getSession()->solved = true;
            return true;
        }

        return false;
    }

    /**
     * Returns the HTML containing the ReCaptcha element.
     *
     * Used in __toString() and to be used when $data['captchaRender']
     * is set to false.
     * It is not called when the element is frozen, see getFrozenHtml()
     * for that case.
     * This method is also not called when the captcha has been solved,
     * since $data['captchaSolved'] is shown then.
     *
     * @usedby __toString()
     *
     * @return string HTML code
     */
    public function getCaptchaHtml()
    {
        return (string)$this->getReCaptcha();
    }

    /**
     * Returns the Services_ReCaptcha instance.
     * May be used to configure the ReCaptcha settings.
     *
     * @return Services_ReCaptcha ReCaptcha instance
     */
    public function getReCaptcha()
    {
        if ($this->reCaptcha !== null) {
            return $this->reCaptcha;
        }

        if (!isset($this->data['public-key'])) {
            //no public key set
            throw new HTML_QuickForm2_Element_Captcha_Exception(
                'Captcha element requires "public-key" data to be set'
            );
        }
        if (!isset($this->data['private-key'])) {
            //no private key set
            throw new HTML_QuickForm2_Element_Captcha_Exception(
                'Captcha element requires "private-key" data to be set'
            );
        }

        $this->reCaptcha = new Services_ReCaptcha(
            $this->data['public-key'],
            $this->data['private-key']
        );
        return $this->reCaptcha;
    }
}

?>
