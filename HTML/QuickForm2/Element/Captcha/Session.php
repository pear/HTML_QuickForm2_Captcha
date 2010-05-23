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

/**
 * HTML_QuickForm2 Captcha session storage
 *
 * PHP version 5
 *
 * @category HTML
 * @package  HTML_QuickForm2
 * @author   Christian Weiske <cweiske@php.net>
 * @license  http://opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://pear.php.net/package/HTML_QuickForm2
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
     * Creates a new QuickForm2 session object
     *
     * @param string $varname Variable name to use
     */
    public function __construct($varname)
    {
        $this->varname = $varname;
    }



    public function init()
    {
        if (session_id() == '') {
            //Session has not been started yet. That's not acceptable
            // and breaks captcha answer storage
            throw new HTML_QuickForm2_Exception(
                'Session must be started'
            );
        }
    }



    public function clear()
    {
        if ($this->hasData()) {
            unset($_SESSION[$this->varname]);
        }
    }



    /**
     * If the session already has data
     *
     * @return boolean True if there are data, false if the session has not been used yet.
     */
    public function hasData()
    {
        return isset($_SESSION[$this->varname]);
    }



    public function isSolved()
    {
        return $this->solved;
    }



    public function setSolved($solved)
    {
        $this->solved = (bool)$solved;
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