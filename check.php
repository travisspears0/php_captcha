<?php
    
    include './captchaClass.php';
    
    if( (new Captcha())->checkCaptcha($_POST["captcha"]) ) {
        echo "OK";
    }
    else {
        echo "error";
    }
    