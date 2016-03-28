<?php 
/**
 * 
 *
 * @author June Chen <594553417@qq.com>
 * @copyright  Copyright (c) 2015.
*/
interface core_database_interface{

    /**
	 * 
	 * 
	 * @param string 
	 * @return resource
	 */
    public function query($sql = '');


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