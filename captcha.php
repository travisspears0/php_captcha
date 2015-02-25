<?php

    include './captchaClass.php';
    
    echo (new Captcha())->generate();
    
    
    //<img src="<?php echo (new Captcha())->generate(); >" style="width:200px;height:100px;" />
    
