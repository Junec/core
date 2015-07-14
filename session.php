<?php
/**
 * Session
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
 */
class core_session{

	/**
	 * 设置session
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

	/**
	 * 根据session的名字删除session
	 * 
	 * @param string $name session名称
	 * @return boolean 删除成功返回true
	 */
	public function del($name) {
		if ($this->exist($name)) {
            $_SESSION[$name] = null;
			unset($_SESSION[$name]);
		}
		return true;
	}

	/**
	 * 取得指定名称的session值
	 * 
	 * @param string $name session名称
	 * @param boolean $dencode 是否对session值进行过解码,默认为false即不用解码
	 * @return mixed 获取成功将返回保存的session值,获取失败将返回false
	 */
	public function get($name, $dencode = false) {
		if ($this->exist($name)) {
			$value = $_SESSION[$name];
			$value && $dencode && $value = base64_decode($value);
			return $value ? unserialize($value) : $value;
		}
		return false;
	}

	/**
	 * 移除全部session
	 * 
	 * @return boolean 移除成功将返回true
	 */
	public function clean() {
		$_SESSION = array();
		return true;
	}

	/**
	 * 判断session是否存在
	 * 
	 * @param string $name session名称
	 * @return boolean 如果不存在则返回false,否则返回true
	 */
	public function exist($name) {
		return isset($_SESSION[$name]);
	}
}