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

/**
 * HTML_QuickForm2 Captcha session storage
 *
 * PHP version 5
 *
 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 */
class HTML_QuickForm2_Element_Captcha_Session
{
    /**
     * Session variable name.
     *
     * @var string
     */
    protected $varname = null;



    /**
     * Initializes the captcha session.
     * Separate from __construct() because the variable name
     * of a form element may change its ID until the form gets
     * used.
     *
     * @param string $varname Variable name to use
     *
     * @return void
     *
     * @throws HTML_QuickForm2_Exception When the session has not been started
     */
    public function init($varname)
    {
        $this->varname = $varname;

        if (session_id() == '') {
            // Session has not been started yet. That's not acceptable
            // and breaks captcha answer storage
            throw new HTML_QuickForm2_Exception(
                'Session must be started'
            );
        }
    }

    /**
     * Clears the data stored in this session.
     *
     * @return void
     */
    public function clear()
    {
        if ($this->hasData()) {
            unset($_SESSION[$this->varname]);
        }
    }

    /**
     * If the session already has data
     *
     * @return boolean True if there are data, false if the session
     *                 has not been used yet.
     */
    public function hasData()
    {
        return isset($_SESSION[$this->varname]);
    }

    /**
     * Return session ID
     *
     * @return string ID that identifies the session
     */
    public function getSessionId()
    {
        return session_id();
    }

    /**
     * Returns a session variable.
     *
     * @param string $varname Name of the variable to retrieve
     *
     * @return mixed The value of the variable, null if not set.
     */
    public function __get($varname)
    {
        if (isset($_SESSION[$this->varname][$varname])) {
            return $_SESSION[$this->varname][$varname];
        }

        return null;
    }

    /**
     * Sets the value of a session variable.
     *
     * @param string $varname Name of the session variable to retrieve
     * @param string $value   Value for the variable
     *
     * @return void
     */
    public function __set($varname, $value)
    {
        $_SESSION[$this->varname][$varname] = $value;
    }
}
?>
