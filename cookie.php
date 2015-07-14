<?php
/**
 * Cookie
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
 */
class core_cookie{

	/**
	 * 设置cookie
	 * 
	 * @param string $name cookie名称
	 * @param string $value cookie值,默认为null
	 * @param boolean $encode 是否使用 MIME base64 对数据进行编码,默认是false即不进行编码
	 * @param string|int $expires 过期时间,默认为null即会话cookie,随着会话结束将会销毁
	 * @param string $path cookie保存的路径,默认为null即采用默认
	 * @param string $domain cookie所属域,默认为null即不设置
	 * @return boolean 设置成功返回true,失败返回false
	 */
	public function set($name, $value = null, $encode = false, $expires = null, $path = null, $domain = null) {
		if (empty($name)) return false;
		$value && $value = serialize($value);
		$encode && $value = base64_encode($value);
		$path = $path ? $path : '/';
        $expires = $expires ? time()+$expires : null;
		setcookie($name, $value, $expires, $path, $domain);
		return true;
	}

	/**
	 * 根据cookie的名字删除cookie
	 * 
	 * @param string $name cookie名称
	 * @return boolean 删除成功返回true
	 */
	public function del($name) {
		if ($this->exist($name)) {
			$this->set($name, '');
			unset($_COOKIE[$name]);
		}
		return true;
	}

	/**
	 * 取得指定名称的cookie值
	 * 
	 * @param string $name cookie名称
	 * @param boolean $dencode 是否对cookie值进行过解码,默认为false即不用解码
	 * @return mixed 获取成功将返回保存的cookie值,获取失败将返回false
	 */
	public function get($name, $dencode = false) {
		if ($this->exist($name)) {
			$value = $_COOKIE[$name];
			$value && $dencode && $value = base64_decode($value);
			return $value ? unserialize($value) : $value;
		}
		return false;
	}

	/**
	 * 移除全部cookie
	 * 
	 * @return boolean 移除成功将返回true
	 */
	public function clean() {
		$_COOKIE = array();
		return true;
	}

	/**
	 * 判断cookie是否存在
	 * 
	 * @param string $name cookie名称
	 * @return boolean 如果不存在则返回false,否则返回true
	 */
	public function exist($name) {
		return isset($_COOKIE[$name]);
	}
}