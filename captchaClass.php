<?php

    class Captcha
    {
        
        private $length,$value,$key,$timeout,$type,$caseSensitive;
        
        /*
         * @params:
         *      $length(int): Number of signs in the captcha
         *      $type(int): Value included in [0,1]:
         *          0 -> Tha captcha is just a string to rewrite
         *          1 -> The captcha is a simple math task
         *      $timeout(int): captcha expiration timeout in seconds.
         *      $caseSensitive(boolean): true= captcha is case sensitive, false= it is not.
         */
        function __construct($length=5,$type=0,$timeout=0,$caseSensitive=false) {
            $this->length = ( gettype($length) !== "NULL" ) ? $length : rand(4,7) ;
            $this->type = ( $type >= 0 ) ? $type : rand(0, 1) ;
            $this->timeout = $timeout;
            $this->caseSensitive = $caseSensitive;
            if( $this->type ) $this->length = 5;
            
        }
        
        public function generate() {
            
            session_start();
            
            $string = ( $this->type ) ? $this->getRandomMath() : $this->getRandomString() ;
             
            $width = $this->length*20;
            $height = 30;

            $image = ImageCreate($width, $height);

            $white = ImageColorAllocate($image, 255, 255, 255);
            $red = ImageColorAllocate($image, 255, 0, 0);
            $green = ImageColorAllocate($image, 0, 255, 0);
            $blue = ImageColorAllocate($image, 0, 0, 255);
            $grey = ImageColorAllocate($image, 187, 187, 187);
            $black = ImageColorAllocate($image, 0, 0, 0);
            $purple = ImageColorAllocate($image, 153, 0, 153);
            $orange = ImageColorAllocate($image, 255, 102, 0);
            $pink = ImageColorAllocate($image, 255, 28, 174);
            
            $textColors = array($red,$green,$blue,$purple,$orange,$pink);
            
            $fillColor = (rand(0,1)) ? $black : $white ;
            ImageFill($image, 0, 0, $fillColor);
            $mode = rand(0,2);
            
            switch( $mode ) {
                case 0: {
                    $linesCount = rand(7,12);
                    for( $i=0 ; $i<$linesCount ; ++$i ) {
                        imageline($image, rand(0,$width), rand(0,$height), rand(0,$width), rand(0,$height), $grey);
                    }
                    break;
                }
                case 1: {
                        for( $i=0 ; $i<$width ; ++$i ) {
                            for( $k=0 ; $k<$height ; ++$k ) {
                                $color = ( rand(0,1) ) ? $white : $grey ;
                                imagesetpixel($image, $i, $k, $color);
                            }
                        }
                    break;
                }
                case 2: {
                    $arcsCount = rand(7,12);
                    for( $i=0 ; $i<$arcsCount ; ++$i ) {
                        $r = ( $width > $height ) ? rand($height/7,$height/3) : rand($width/7,$width/3) ;
                        imagearc($image,  rand(0,$width),  rand(0,$height), $r, $r,  0, 360, $grey);
                    }
                    break;
                }
            }
            
            $font = rand(3,6);
            for( $i=0,$len=strlen($string) ; $i<$len ; ++$i ) {
                ImageString($image, $font, rand(0,10)+$i*20, rand(0,10), $string[$i], $textColors[rand(0,count($textColors)-1)]);
            }
            
            ob_start();
            imagepng($image);
            imagedestroy($image);
            $image = ob_get_clean();
            
            $expiration = ( (int)$this->timeout ) ? (time()+$this->timeout) : 0 ;
            
            $_SESSION["captcha"] = array(
                "value"=>$this->key,
                "expiration"=>$expiration,
                "ip"=>  $this->getIp()
            );
            
            return "data:image/jpeg;base64," . base64_encode($image);
        }
        
        private function getRandomString() {
            
            /*$this->value = substr(md5(uniqid()), 0, $this->length);
            $this->key = $this->value;*/
            $signs = "qwertyuioplkjhgfdsazxcvbnm1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
            $signsSize = strlen($signs);
            for( $i=0 ; $i<$this->length ; ++$i ) {
                $this->value .= $signs[rand(0, $signsSize)] ;
            }
            $this->key = $this->value;
            
            return $this->value;
        }
        
        private function getRandomMath() {
            
            $sign = ( rand(0,1) ) ? "+" : "-" ;
            $num1 = rand(10,99);
            $num2 = rand(10,99);
            $this->value = "$num1$sign$num2" ;
            $this->key = ( $sign === "+" ) ? $num1+$num2 : $num1-$num2 ;
            
            return $this->value;
        }
        
        public function checkCaptcha($captcha) {
            
            session_start();
            $captcha = (string)addslashes($captcha);
            $sessionCaptcha = (string)addslashes($_SESSION["captcha"]["value"]);
            if( !$this->caseSensitive ) {
                $captcha = strtolower($captcha);
                $sessionCaptcha = strtolower($sessionCaptcha);
            }
            if( $captcha !== $sessionCaptcha ) {
                return false;
            }
            $expiration = (int)$_SESSION["captcha"]["expiration"];
            if( $expiration && $expiration < time() ) {
                return false;
            }
            if( $this->getIp() !== $_SESSION["captcha"]["ip"] ) {
                return false;
            }
            return true;
        }
        
        private function getIp() {
            
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            else {
                    $ip = $_SERVER['REMOTE_ADDR'];
            }
            return $ip;
        }
        
    }