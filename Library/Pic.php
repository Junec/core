<?php
/**
 * Picture
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2016.
 */
class Core_Library_Pic{
    /**
     * 
     * 
     * @param string $name session名称
     * @param string $value session值,默认为null
     * @param boolean $encode 是否使用 MIME base64 对数据进行编码,默认是false即不进行编码
     * @return boolean 设置成功返回true,失败返回false
     */
    public function set($name, $value = null, $encode = false) {
        if (empty($name)) return false;
        $value && $value = serialize($value);
        $encode && $value = base64_encode($value);
        $_SESSION[$name] = $value;
        return true;
    }

}

?>