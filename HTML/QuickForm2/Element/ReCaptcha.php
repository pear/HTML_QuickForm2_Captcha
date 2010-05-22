<?php


class HTML_QuickForm2_Element_ReCaptcha
    extends HTML_QuickForm2_Element_Captcha
{
    protected function generateCaptchaQA()
    {
        include_once 'Services/ReCAPTCHA.php';

        $this->capQuestion = $cap->getCAPTCHA();
        $this->capAnswer   = $cap->getPhrase();
    }
}


?>