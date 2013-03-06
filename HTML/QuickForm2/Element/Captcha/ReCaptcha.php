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

require_once 'Services/ReCaptcha.php';
require_once 'HTML/QuickForm2/Element/Captcha.php';

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
     * Array of input element attributes, with some predefined values
     *
     * @var array
     */
    protected $attributes = array(
        'size' => 5,
    );

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

        // Set adapter specific options
        $this->init($data);
    }

    /**
     * Get the Services_ReCaptcha instance.
     * May be used to configure the ReCaptcha settings.
     *
     * @param array $data Element data (special captcha settings)
     *
     * @return Services_ReCaptcha ReCaptcha instance
     */
    public function init(array $data = array())
    {
        if ($this->adapter !== null) {
            return $this->adapter;
        }

        if (!isset($data['public-key'])) {
            // No public key set
            throw new HTML_QuickForm2_Exception(
                'Captcha element requires "public-key" data to be set'
            );
        }

        if (!isset($data['private-key'])) {
            // No private key set
            throw new HTML_QuickForm2_Exception(
                'Captcha element requires "private-key" data to be set'
            );
        }

        $this->adapter = new Services_ReCaptcha(
            $data['public-key'],
            $data['private-key']
        );
    }

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

        // Services_ReCaptcha::validate() may only be called if
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

        if ($this->adapter->validate()) {
            $this->getSession()->solved = true;
            return true;
        }

        return false;
    }

    /**
     * Returns the HTML containing the ReCaptcha element.
     *
     * @return string HTML code
     */
    public function getCaptchaHtml()
    {
        return (string) $this->adapter;
    }
}
