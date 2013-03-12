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
 * HTML_QuickForm2 Captcha dummy session storage
 *
 * @category HTML
 * @package  HTML_QuickForm2_Captcha
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://www.gnu.org/copyleft/lesser.html LGPL License
 * @link     http://pear.php.net/package/HTML_QuickForm2_Captcha
 */
class HTML_QuickForm2_Element_Captcha_Session_Mock
    extends HTML_QuickForm2_Element_Captcha_Session
{
    protected $data = array();

    /**
     * Initializes the captcha session.
     * Separate from __construct() because the variable name
     * of a form element may change its ID until the form gets
     * used.
     *
     * @param string $varname Session variable name to use
     *
     * @return void
     */
    public function setVarname($varname)
    {
        $this->varname = $varname;
    }

    /**
     * Clears the data stored in this session.
     *
     * @return void
     */
    public function clear()
    {
        $this->data = array();
    }

    /**
     * If the session already has data
     *
     * @return boolean True if there are data, false if the session
     *                 has not been used yet.
     */
    public function hasData()
    {
        return count($this->data);
    }

    /**
     * Return session ID
     *
     * @return string ID that identifies the session
     */
    public function getSessionId()
    {
        return 'dummy-sid';
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
        if (isset($this->data[$this->varname][$varname])) {
            return $this->data[$this->varname][$varname];
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
        $this->data[$this->varname][$varname] = $value;
    }
}
?>
