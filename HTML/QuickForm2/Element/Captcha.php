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

require_once 'HTML/QuickForm2/Element/InputText.php';
require_once 'HTML/QuickForm2/Element/Captcha/Session.php';

/**
 * Base class for captcha elements for HTML_QuickForm2:
 * Completely Automated Public Turing test to tell Computers and Humans Apart.
 * Used as anti-spam measure.
 *
 * Features:
 * - Multiple forms on the same page may have captcha elements
 *   with the same name
 * - Once a captcha in a form is solved, it stays that way until
 *   the form is valid. No need to re-solve a captcha because you
 *   forgot a required field!
 * - Customizable status messages i.e. when captcha is solved
 *
 * When the form is valid and accepted, use clearCaptchaSession()
 * to destroy the captcha question and answer. Otherwise the
 * form catpcha is seen as already solved for the user.
 *
 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 *
 * @FIXME/@TODO
 * - clear session when form is valid / destroy captcha
 */
abstract class HTML_QuickForm2_Element_Captcha
    extends HTML_QuickForm2_Element_Input
{
    /**
     * Underlying captcha adapter.
     *
     * @var object
     */
    protected $adapter = null;

    /**
     * Prefix for session variable used to store captcha
     * settings in.
     *
     * @var string
     */
    protected $sessionPrefix = '_qf2_captcha_';

    /**
     * Array of input element attributes, with some predefined values
     *
     * @var array
     */
    protected $attributes = array('size' => 5);

    /**
     * If the captcha has been generated and initialized already
     *
     * @var boolean
     */
    protected $capGenerated = false;

    /**
     * Session object to store captcha data and solutions in.
     *
     * @var HTML_QuickForm2_Element_Captcha_Session
     */
    protected $session = null;

    /**
     * Create new instance.
     *
     * Captcha-specific data attributes:
     * - captchaSolved        - Text to show when the Captcha has been
     *                          solved
     * - captchaSolutionWrong - Error message to show when the captcha
     *                          solution entered by the user is wrong
     * - captchaRender        - Boolean to determine if the captcha itself
     *                          is to be rendered with the solution
     *                          input element
     *
     * @param string $name       Element name
     * @param mixed  $attributes Attributes (either a string or an array)
     * @param array  $data       Element data (special captcha settings)
     */
    public function __construct($name = null, $attributes = null, $data = array())
    {
        //we fill the class data array before it gets merged with $data
        $this->data['captchaSolutionWrong']  = 'Captcha solution is wrong';
        $this->data['captchaSolved']         = 'Captcha already solved';
        $this->data['captchaRender']         = true;
        $this->data['captchaHtmlAttributes'] = array(
            'class' => 'qf2-captcha-question'
        );

        parent::__construct($name, $attributes, $data);
    }

    /**
     * Prepares the session data for the captcha.
     * Child classes overwrite this method do do further intialization,
     * i.e. generating questions and answers.
     *
     * @return boolean True when the captcha has been created newly, false
     *                 if it already existed.
     *
     * @throws HTML_QuickForm2_Exception When the session is not started yet
     */
    protected function generateCaptcha()
    {
        $this->getSession()->init($this->getSessionVarName());
        $this->capGenerated = true;

        if ($this->getSession()->hasData()) {
            //data exist already, use them
            return false;
        }

        $this->getSession()->solved = false;

        return true;
    }

    /**
     * Returns the name to use for the session variable.
     * We include the element's ID to make sure we can use several
     * captcha elements in one form.
     * Also, the container IDs are included to make sure we can use
     * the same element in different forms.
     *
     * @return string Session variable name
     */
    protected function getSessionVarName()
    {
        $el     = $this;
        $idpath = '';

        do {
            $idpath .= '-' . $el->getId();
        } while ($el = $el->getContainer());

        return $this->sessionPrefix
            . $idpath
            . '-data';
    }

    /**
     * Returns the captcha session object
     *
     * @return HTML_QuickForm2_Element_Captcha_Session Session object
     */
    public function getSession()
    {
        if ($this->session === null) {
            $this->session = new HTML_QuickForm2_Element_Captcha_Session();
        }

        return $this->session;
    }

    /**
     * Sets a new session object.
     * Useful for providing own session storage methods.
     *
     * @param HTML_QuickForm2_Element_Captcha_Session $session Session object
     *
     * @return void
     */
    public function setSession(HTML_QuickForm2_Element_Captcha_Session $session)
    {
        $this->session = $session;
    }

    /**
     * Checks if the captcha is solved now.
     * Checks the session.
     *
     * Calls generateCaptcha() if it has not been called before.
     *
     * @uses generateCaptcha()
     *
     * @return boolean True if the captcha is solved
     */
    protected function verifyCaptcha()
    {
        if (!$this->capGenerated) {
            $this->generateCaptcha();
        }

        if ($this->getSession()->solved === true) {
            return true;
        }

        return false;
    }

    /**
     * Destroys all captcha session data, so that the previously solved
     * captcha re-appears as unsolved. Question and answers are discarded
     * as well.
     *
     * @return void
     */
    public function clearCaptchaSession()
    {
        $this->getSession()->clear();
    }

    /**
     * Performs the server-side validation.
     * Checks captcha validation first, continues with
     * defined rules if captcha is valid
     *
     * @return boolean Whether the element is valid
     */
    protected function validate()
    {
        // Alternative: use custom rule to get error messages
        if (!$this->verifyCaptcha()) {
            $this->setError($this->data['captchaSolutionWrong']);
            return false;
        }

        return parent::validate();
    }

    /**
     * Returns the CAPTCHA type.
     *
     * @return string captcha type
     */
    public function getType()
    {
        return 'captcha';
    }

    /**
     * Sets the input value
     *
     * @param string $value Input value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->setAttribute('value', $value);
        return $this;
    }

    /**
     * Returns the captcha answer input element value.
     * No value (null) when the element is disabled.
     *
     * @return string Input value
     */
    public function getValue()
    {
        return $this->getAttribute('disabled')
            ? null
            : $this->getAttribute('value');
    }

    /**
     * Renders the captcha into a HTML string
     *
     * @see getCaptchaHtml()
     * @see $data['captchaSolved']
     *
     * @return string HTML
     */
    public function __toString()
    {
        if ($this->frozen) {
            return $this->getFrozenHtml();
        } else {
            if ($this->verifyCaptcha()) {
                return $this->data['captchaSolved'];
            } else {
                return $this->getCaptchaHtml();
            }
        }
    }

    /**
     * Returns the HTML for the captcha
     * (question + input element if applicable)
     *
     * @uses $data['captchaHtmlAttributes'].
     *
     * @return string HTML code
     */
    abstract public function getCaptchaHtml();

    /**
     * Returns the HTML code when the form is frozen.
     *
     * @return string HTML code
     */
    public function getFrozenHtml()
    {
        return '';
    }
}
