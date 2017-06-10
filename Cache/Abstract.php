<?php

abstract class Core_Cache_Abstract{
	protected function hashKey($key = ''){
        return md5($key);
    }
}

?>