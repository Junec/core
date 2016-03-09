<?php
class distribute{

	//节点
	private $nodes = array();

	//每台物理服务器下的虚拟节点
	private $virtualNodeNum = 20;

	/**
	 * 初始化服务器节点
     *
     * @param $servers 服务器信息
	 * @return void
	 */
	public function init($servers = array()){
		foreach($servers as $server){
			for($n=1;$n<=$this->virtualNodeNum;$n++){
				$node = $server."_".$n;
				$hash = $this->hashkey( $node );
				$this->nodes[ $hash ] = $node;
			}
		}
		ksort($this->nodes, SORT_NUMERIC);
		$this->nodesKey = array_keys($this->nodes);
		$this->nodesKeyCount = count($this->nodesKey);
		return $this;
	}

	/**
	 * 获取节点
     *
     * @param $key 
	 * @return string
	 */
	public function getServer($key = ''){
		$hash = $this->hashkey($key);
		$server = $this->_getServer($hash);
		$node = explode("_", $this->nodes[$server]);
		$this->count[$node[0]] += 1;
		return $node[0];
	}

	/**
	 * 一致性Hash
     *
     * @param $key 
	 * @return hash
	 */
	private function hashkey($key = ''){
		$md5 = md5($key);
		$hash = 0;
		$len  = 32;
		$seed = 33;
		for($i = 0; $i < $len; $i++){
			$hash = sprintf("%u", $hash * $seed) + ord($md5{$i});
		}
		return $hash & 0x7FFFFFFF;
	}

	/**
	 * 二分法查找环内最近节点
     *
     * @param $hash 
     * @param $low 
     * @param $high 
	 * @return hash
	 */
	private function _getServer($hash = '', $low = 0, $high = 0){
		if($this->nodesKeyCount!=0 && $high == 0){  
		    $high = $this->nodesKeyCount - 2;  
		}  
		if($low <= $high){  
		    $mid = ($low+$high)/2;
		    if ($this->nodesKey[$mid] == $hash){   
		        return $this->nodesKey[$mid];   
		    }elseif ($hash < $this->nodesKey[$mid]){   
		        return $this->_getServer($hash, $low, $mid-1);   
		    }else{   
		        return $this->_getServer($hash, $mid+1, $high);   
		    }   
		}   
        if (abs($this->nodesKey[$high] - $hash) < abs($this->nodesKey[$low] - $hash))  return $this->nodesKey[$high];
        else return $this->nodesKey[$low];
	}
}
?>