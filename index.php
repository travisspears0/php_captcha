<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        
        <style>
            
            html,body
            {
                background-color: #000000;
            }
            
            form > input
            {
                display: block;
                margin: 10px 0;
            }
            
            form > img
            {
                display: block;
                width: 400px;
                height: 100px;
                border: 3px solid #FF0000;
            }
            
        </style>
        
    </head>
    <body>
        
        <form action="check.php" method="post">
            <img id="captchaImage" />
            <input id="captcha" name="captcha" />
            <input type="submit" id="submit" />
        </form>
        
        <script src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script>
        
            document.addEventListener("DOMContentLoaded",function(){
                
                $("#captcha").focus();
                
                function getCaptchaImage() {
                    $.ajax({
                        url:"captcha.php",
                        type: 'GET',
                        success: function (data, textStatus, jqXHR) {
                            $("#captchaImage").attr("src",data);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log( textStatus );
                        }
                    });
                }
                
                getCaptchaImage();
                
                $("#captchaImage").click(getCaptchaImage);
                
                $("#submit").click(function(e){
                    
                    e.preventDefault();
                    
                    $.ajax({
                        url:"check.php",
                        data:{
                            captcha:$("#captcha").val()
                        },
                        type: 'POST',
                        success: function (data, textStatus, jqXHR) {
                            alert( data );
                            getCaptchaImage();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.log( textStatus );
                        }
                    });
                    
                });
                
            });
        
        </script>
        
    </body>
</html>
