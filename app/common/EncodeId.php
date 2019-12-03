<?php
namespace app\common;
use function is_string;
use function substr;
use function base64_decode;
use function str_replace;
use function base64_encode;
use function microtime;
class EncodeId
{
    //-------------------------------------------------------------------------------------------------
    /**
     * 加密
     * @param string
     * @return string [加密后的数据]
     */
    public  function encode(?string $str = '',?int $expiry = 0) : ?string
    {
        if(!$str) return null;

        return $this->authcode($str,'ENCODE',$expiry);
    }
    /**
     * [decrypt aes解密]
     * @param string $str
     * @return string       [解密后的数据]
     */
    public  function decode(?string $str = '') : ?string
    {
        if(!$str) return null;
        return  $this->authcode($str,'DECODE');
    }

    //----------------------------------------------------------------------------------------------------------------------------
    /**
     * @param string $string
     * @param string $operation
     * @param int|0 $expiry
     * @return string
     */
    private function authcode(?string $string,?string $operation,?int $expiry = 0) : ?string
    {  
            if(!is_string($string)) return '';
            $ckey_length = 4;  
            $keya = md5(substr(\config('app.en_key'), 0, 16));  
            $keyb = md5(substr(\config('app.en_key'), 16, 16));  
            $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length):
        substr(md5(microtime()), -$ckey_length)) : '';  
            $cryptkey = $keya.md5($keya.$keyc);  
            $key_length = strlen($cryptkey);   
            $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : 
        sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;  
            $string_length = strlen($string);  
            $result = '';  
            $box = range(0, 255);  
            $rndkey = array();  
            for($i = 0; $i <= 255; $i++) {  
                $rndkey[$i] = ord($cryptkey[$i % $key_length]);  
            }  
            for($j = $i = 0; $i < 256; $i++) {  
                $j = ($j + $box[$i] + $rndkey[$i]) % 256;  
                $tmp = $box[$i];  
                $box[$i] = $box[$j];  
                $box[$j] = $tmp;  
            }  
            for($a = $j = $i = 0; $i < $string_length; $i++) {  
                $a = ($a + 1) % 256;  
                $j = ($j + $box[$a]) % 256;  
                $tmp = $box[$a];  
                $box[$a] = $box[$j];  
                $box[$j] = $tmp;  
                $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));  
            } 
 
            if($operation == 'DECODE') { 
                if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && 
        substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {  
                    return substr($result, 26);  
                } else {  
                    return null;  
                }  
            } else {  
                return $keyc.str_replace('=', '', base64_encode($result));  
            }  
    }
}