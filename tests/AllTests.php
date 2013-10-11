<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'HTML_QuickForm2_Captcha_AllTests::main');
}

require_once 'PHPUnit/Autoload.php';

class HTML_QuickForm2_Captcha_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('HTML_QuickForm2_Captcha tests');
        /** Add testsuites, if there is. */
        $suite->addTestFiles(
            glob(__DIR__ . '/HTML/QuickForm2/Element/{,/*/}*Test.php', GLOB_BRACE)
        );

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'HTML_QuickForm2_Captcha_AllTests::main') {
    HTML_QuickForm2_Captcha_AllTests::main();
}
?>