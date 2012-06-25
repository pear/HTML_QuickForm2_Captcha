<?php
declare(encoding = 'UTF-8');

/**
 * HTML_QuickForm2_Captcha package.
 *
 * PHP version 5
 *
 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Rico Sonntage <mail@ricosonntag.de>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 */

require_once 'Text/CAPTCHA.php';
require_once 'HTML/QuickForm2/Element/Captcha.php';

/**
 * Figlet Captcha element for QuickForm2.
 *
 * In case you need to customize the options, use getAdapter() and
 * modify the object.

 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Rico Sonntage <mail@ricosonntag.de>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 * @see      http://pear.php.net/package/Text_CAPTCHA
 */
class HTML_QuickForm2_Element_Captcha_Figlet
    extends HTML_QuickForm2_Element_Captcha
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
        parent::__construct($name, $attributes, $data);

        if (!isset($data['options']['font_file'])) {
            throw new HTML_QuickForm2_Exception('Font file required');
        }

        // Set adapter specific options
        $this->init($data);
    }

    /**
     * Init the Text_CAPTCHA object used for generating question and answer.
     * Create a new instance if the adapter is not set yet.
     *
     * @param array $data Element data (special captcha settings)
     *
     * @return void
     */
    public function init(array $data = array())
    {
        if ($this->adapter === null) {
            $this->adapter = Text_CAPTCHA::factory('Figlet');
        }

        $this->adapter->init($data);
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
            return false;
        }

        $this->getSession()->question = $this->adapter->getCAPTCHA();
        $this->getSession()->answer   = $this->adapter->getPhrase();

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
     * @return boolean TRUE if the captcha is solved
     */
    protected function verifyCaptcha()
    {
        // Check session and generate captcha if necessary
        if (parent::verifyCaptcha()) {
            return true;
        }

        //verify given answer with our answer
        $userSolution = $this->getValue();

        if ($this->getSession()->answer === null) {
            //no captcha answer?
            return false;
        } else {
            if ($this->getSession()->answer != $userSolution) {
                return false;
            } else {
                $this->getSession()->solved = true;
                return true;
            }
        }
    }

    /**
     * Returns the HTML for the captcha question and answer.
     *
     * Used in __toString() and to be used when $data['captchaRender']
     * is set to false.
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

        return $prefix
            . '<input' . $this->getAttributes(true) . ' />';
    }
}
