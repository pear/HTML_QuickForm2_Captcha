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

require_once 'Text/CAPTCHA.php';
require_once 'HTML/QuickForm2/Element/Captcha.php';
require_once 'HTML/QuickForm2/Element/Captcha/Exception.php';

/**
 * Quickform2 captcha element that uses the Text_CAPTCHA package
 * as captcha generator.
 *
 * In case you need to customize the options, use getAdapter() and
 * modify the object.

 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Rico Sonntag <rico.sonntag@netresearch.de>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 * @see      http://pear.php.net/package/Text_CAPTCHA
 */
class HTML_QuickForm2_Element_Captcha_TextCAPTCHA
    extends HTML_QuickForm2_Element_Captcha
{
    /**
     * @var Text_CAPTCHA
     */
    protected $adapter;

    /**
     * Constructor. Set adapter specific data attributes.
     *
     * Text_CAPTCHA settings are provided in $data['captcha'].
     * $data['captchaType'] specifies the Text_CAPTCHA driver name,
     * e.g. "Equation", "Figlet" or "Word".
     * All other data['captcha'] settings are passed to it's init() method.
     *
     * Do not use this class directly for the "Image" type,
     * there is an own class for that one.
     *
     * @param string $name       Element name
     * @param mixed  $attributes Attributes (either a string or an array)
     * @param array  $data       Element data (special captcha settings)
     */
    public function __construct(
        $name = null, $attributes = null, $data = array()
    ) {
        parent::__construct($name, $attributes, $data);
    }

    /**
     * Return the Text_CAPTCHA adapter
     *
     * @return Text_CAPTCHA Captcha generator
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Set the captcha generator
     *
     * @param Text_CAPTCHA $adapter Captcha generator
     *
     * @return void
     */
    public function setAdapter(Text_CAPTCHA $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Generates the captcha question and answer and prepares the
     * session data.
     *
     * @return boolean TRUE when the captcha has been created newly, FALSE
     *                 if it already existed.
     *
     * @throws HTML_QuickForm2_Element_Captcha_Exception When the Text_CAPTCHA
     *         adapter cannot be initialized correctly
     */
    protected function generateCaptcha()
    {
        if (!parent::generateCaptcha()) {
            return false;
        }
        $this->loadAdapter();

        return true;
    }

    /**
     * Load the adapter instance
     *
     * @return void
     * @throws HTML_QuickForm2_Element_Captcha_Exception When the Text_CAPTCHA
     *         adapter cannot be initialized correctly
     */
    protected function loadAdapter()
    {
        if (!$this->adapter) {
            if (!isset($this->data['captchaType'])) {
                throw new HTML_QuickForm2_Element_Captcha_Exception(
                    'data[captchaType] is not set'
                );
            }
            $this->adapter = Text_CAPTCHA::factory(
                $this->data['captchaType']
            );
            $captchaOptions = $this->data;
            unset($captchaOptions['captchaType']);
            $this->adapter->init($captchaOptions);
        }

        $question = $this->adapter->getCAPTCHA();
        if (PEAR::isError($question)) {
            throw new HTML_QuickForm2_Element_Captcha_Exception(
                $question->message, $question->code
            );
        }
        $this->getSession()->question = $question;
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

        return $prefix . '<input' . $this->getAttributes(true) . ' />';
    }
}
?>
