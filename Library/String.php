<?php
/**
 * 字符串处理类
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2016.
 */
class Core_Library_String{
    private $hashfrom = 'rPeAk3%aqJ7R0Xw1cpEVLsIyh5Mt8dTGfz6CiB4DmQlWKo+HbZjn9YUOxuNFvS2g';//q6RClj%p1PdwYhemFcXOrSQkNWbv9It0LfguVz84xyU3K+i5M7EToZA2naDHsBJG
    private $hashto =   'g2SvFNuxOUY9njZbH$oKWlQmD4BiC6zfGTd8tM5hyIsLVEpc1wX0R7Jqa*3kAePr';//GJBsHDan2AZoTE7M5i$K3Uyx48zVugfL0tI9vbWNkQSrOXcFmehYwdP1p*jlCR6q


    /**
     * 字符串加密
     * 
     * @param string $str 加密的字符串
     * @param string $isCompress 是否使用zlib字符串压缩
     * @return string
     */
    public function encode($string = '',$isCompress = false){
        if($isCompress == true) $string = gzcompress($string);
        $string = rtrim(base64_encode($string),'=');
        $string = strtr($string, $this->hashfrom, $this->hashto);
        return $string;
    }
    /**
     * 字符串解密
     * 
     * @param string $str 解密的字符串
     * @param string $isCompress 是否使用zlib字符串压缩
     * @return string
     */
    public function decode($string = '',$isCompress = false){
        $string = strtr($string, $this->hashto, $this->hashfrom);
        $string = base64_decode(str_pad($string,strlen($data)%4,'=',STR_PAD_RIGHT));
        if($isCompress == true) $string = gzuncompress($string);
        return $string;
    }

}

?>