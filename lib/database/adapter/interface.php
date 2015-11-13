<?php 
/**
 * 适配器接口
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
interface core_database_adapter_interface{

    /**
	 * 
	 * 
	 * @param string 
	 * @return resource
	 */
    public function query($sql = '');


    /**
	 * 
	 * @param string 
	 * @return array
	 */
    public function exec($sql = '');


    /**
	 * 
	 * 
	 * @return array
	 */
    public function fetch();


    /**
	 * 
	 * 
	 * @return array
	 */
    public function fetchAll();

}

?>