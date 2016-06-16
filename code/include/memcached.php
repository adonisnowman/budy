<?php
Class iplayfun_memcached extends Memcached_Obj
{

	
	function SQLfunc($use){		
		$return = function() use ($use){
				$result = mysql_query($use);
				if($result)  while($row = mysql_fetch_assoc($result)) $SQLdata[] = $row;
				
// 				if($result && mysql_num_rows($result) == 1) return array_pop($SQLdata);
				return $SQLdata;
			};
		return $return;
	}
	
	
	function getMultiWithSet($keyValues=array(),$expire = -1){

		$return = array();
// 		
		$emptyData = array();
		$data = $this->getMulti($keyValues,true);
		
		if(!empty($data['data']) && is_array($data['data']))  $return = $data['data'];
		if(array_key_exists('empty', $data) && !empty($data['empty'])) {
			$emptyData = $this->setByKeyValues($data['empty'],$expire);
			$return = array_merge($return, $emptyData);
		}
		
		
		return $return;
	}
	
	
}


Class Memcached_Obj
{
	public static $memcached;
	public static $server_name;
	public static $prefix;
	public static $def_config = array(
			'host' 			=> 'localhost', 
			'port' 			=> 11211,
			'expire' 		=> 3600,
			'extend' 		=> false,
			'prefix' 		=> ''
	);
	
	public static $keyValues;
	public static $emptyKeyValues;
	public static $cacheKeyValues;
	public static $keyValuesGet;
	
	function __construct($server_name = '', $config=array()){
		
		if(!empty($config)&& is_array($config))
		foreach ($config AS $key => $item){
			if(isset(self::$def_config[$key]))  self::$def_config[$key] = $item;
		}
	
		$this->set_serverName($server_name);
		self::$memcached = new Memcached();
		if(self::$memcached) self::$memcached->addServer(self::get_config('host'), self::get_config('port'));
		else self::$memcached = false;
	
		self::$prefix  = $this->set_prefix();
	}
	
	public function setKeys($keys){			
		$this->getKeyValuesSet(self::$keyValues = self::keyValues_check($keys));
		return $this;	
	}
	
	public function get($keys = false){
		$keyValuesGet = self::$keyValuesGet;
		if(false === $keys) return $keyValuesGet;		
		if(array_key_exists($keys, $keyValuesGet) ) return $keyValuesGet[$keys];
		
		return false;
	}
	
	public function cacheValue($keyValues=false){
		if(false != $keyValues ) $this->setKeys($keyValues);
		
		$keyValues = self::$keyValues;
		if( $keyValues ){
				
		
			$getKeys = array_keys($keyValues);
			if(self::$memcached ){
				
				$cacheKeyValues = $this->removeServerName( self::$memcached->getMulti($this->addServerName($getKeys)) ,true);
				$this->getKeyValuesSet(self::$cacheKeyValues =  $cacheKeyValues );				
				
				
			}else echo 'Non Memcached Server';
			
			self::$emptyKeyValues = not_in_array($getKeys,$cacheKeyValues);
		}
		return $this;
	}
	
	private function getKeyValuesSet($keyValues){		
		self::$keyValuesGet = $keyValues;
	}
	
	function deleteAllKeys(){		
		$allKeys = $this->getAllKeys();
		return self::deleteMulti($this->removeServerName($allKeys));
	}
	
	function getAllKeys($showFullKeys = false, $showServerAllKeys = false){
		$serverAllKeys = self::$memcached->getAllKeys();
		if($showServerAllKeys) return $serverAllKeys;
		
		$prefix = self::$prefix;		
		$allKeys = array();
		if(!empty($serverAllKeys) && is_array($serverAllKeys))
		foreach( $serverAllKeys AS $v){			
			if(preg_match('/'.preg_quote(self::$prefix,'/').'/', $v))  $allKeys[] = $v;			
		}
		
		if(false === $showFullKeys) $allKeys = $this->removeServerName($allKeys);
		return $allKeys;
	}
	
	function set_serverName($serverName){		
		self::$server_name = $serverName;
	}
	
	function set_prefix($def_prefix = '',$serverName=''){
		
		$serverName = ($serverName)?$serverName:self::$server_name;
		$def_prefix = ($def_prefix)?$def_prefix:self::$def_config['prefix'];
		
		
		return self::$prefix = $serverName.':'.$def_prefix;
		
	}
	
	
	public static function get_config($key){
		return self::$def_config[$key];
	}
	
	
	
	
	public function getMulti($keyValues=array(),$withEmpty = false ){
		$return['data'] = false;
		$return['empty'] = false;
		$data = array();
		if( $keyValues = self::keyValues_check($keyValues)){
			
		
			$getKeys = array_keys($keyValues);	
			if(self::$memcached ){				
				$data = $this->removeServerName( self::$memcached->getMulti($this->addServerName($getKeys)) ,true);
			} 
			$emptyKeys = not_in_array($getKeys,$data);			
		}
		
		if(!empty($data) && is_array($data)) $return['data'] = $data;
		if(false === $withEmpty) return $return['data'];
		
		if(!empty($emptyKeys) && is_array($emptyKeys) ){
			
			$emptyKeyValues = array_intersect_key($keyValues, array_flip($emptyKeys));			
			$return['empty'] = $this->removeServerName($emptyKeyValues,true);
		}
		
		return $return;
	}
	
	public function setMulti($keyValues=array(),$expire = -1){
		if( $expire < 0) $expire = self::$def_config['expire'];
		
		$keyValues = $this->addServerName($keyValues,true);
		
		if(self::$memcached) self::$memcached->setMulti($keyValues, $expire);
	}
	
	public function deleteMulti($keyArrays=array()){
		
		$return = false;
		
		$keyArrays = self::keyArrays_check($keyArrays);
		
		$keyArrays = $this->addServerName($keyArrays);
		
		if(self::$memcached && $keyArrays) {			
			$return = self::$memcached->deleteMulti($keyArrays);		
		}
		return $return;
	}
	
	public function setByKeyValues($emptyKeyValues = array(),$expire = -1){
		
		$emptyData = array();
		$emptyMutiData = array();
		
		foreach ($emptyKeyValues AS $key =>$emptyKey){
			$required = array('value','func');
				
			if( count(array_intersect_key(array_flip($required), $emptyKey)) ){
				if(array_key_exists('func', $emptyKey) && is_callable($emptyKey['func']) ) 	$sqlData = $emptyKey['func']();
				if(array_key_exists('value', $emptyKey) && !is_callable($emptyKey['value']) ) 	$sqlData = $emptyKey['value'];
				
				if(!empty($emptyKey['expire']) && $emptyKey['expire'] > 0){
					$expire = (int) $emptyKey['expire'];
// 					echo 'SF:'.$expire;
					$this->setMulti( array($key => $sqlData),$expire);
		
					$emptyData[$key] = $sqlData;
				}
				else  $emptyMutiData[$key] = $sqlData;
		
			}
		
		}
			
		if(!empty($emptyMutiData) && is_array( $emptyMutiData )){
			if( $expire < 0) $expire = self::$def_config['expire'];
// 			echo 'M:'.$expire;
			
			$this->setMulti( $emptyMutiData , $expire);
			$emptyData = array_merge($emptyData,$emptyMutiData);
		}
		
		
		$emptyData = $this->removeServerName($emptyData,true);
		
		return $emptyData;	
	}
	
	public function addServerName($keys,$byArrayKey = false){
		$keyName = array();
		$prefix = self::$prefix;
		
		if( $byArrayKey && is_array($keys) )
			foreach($keys AS $key=>$v) $keyName[$prefix.$key] = $v;
		
		else if(!empty($keys) && false == is_array($keys) ) return  $prefix.$keys;
		else if(is_array($keys)) foreach($keys AS $key) $keyName[]= $prefix.$key;
		
		
		return $keyName;
	}
	
	public function removeServerName($keys,$byArrayKey = false){
		$keyName = array();
		
		$prefix = self::$prefix;
		
		if( $byArrayKey && is_array($keys) )
			foreach($keys AS $key=>$v) $keyName[str_replace($prefix, '', $key)] = $v;
	
		else if(!empty($keys) && false == is_array($keys) ) $keyName[]= str_replace($prefix, '', $keys);
		else foreach($keys AS $key) $keyName[]= str_replace($prefix, '', $key);
		
		return $keyName;
	}
	
	private static function keyArrays_check($keys){
		$keyArrays = false;
		if(!empty($keys) && false == is_array($keys) ) $keyArrays = array( $keys );
		else if(is_array($keys) && array_key_exists('key', $keys)) $keyArrays = array( $keys['key'] );
		else foreach($keys AS $keyArray) { 
			if(!empty($keyArray) && false == is_array($keyArray)  ) $keyArrays[] = $keyArray;
			else if(array_key_exists('key', $keyArray)) $keyArrays[] = $keyArray['key'];
		}
		
// 		pre_dump($keyArrays,'delete');
		return $keyArrays;	
	}
	
	private static function keyValues_check($keys){
		$keyValues = false;
// 		var_dump($keys);
		
		if(!empty($keys) && false == is_array($keys) ) $keyValues[$keys] = array($keys);
		else if(is_array($keys) && array_key_exists('key', $keys)) $keyValues[$keys['key']] = $keys;		
		else foreach($keys AS $key => $keyValue) {		
			if(is_nan($key) ) 	
				$keyValues[$key] = $keyValue;			
			
			else if( is_array($keyValue) && array_key_exists('key', $keyValue)) 
				$keyValues[$keyValue['key']] = $keyValue;
			
		}
			
			
		
		return $keyValues;
	}
	
	
	
	function __destruct(){		
		self::$memcached->resetServerList();
	}
}

function pre_dump($val,$title=''){
// 	return false;
	echo '<br/>['.$title.']<br/><pre>';
	var_dump($val);
	echo '</pre><br/>------------------------------<br/>';
}

function not_in_array($keys,$dataKeys){
	$emptyKeys = array();
	if(!empty($keys) && false == is_array($keys) ) $keys = array($keys);
	else foreach ($keys AS $key) if(!array_key_exists($key, $dataKeys)) $emptyKeys[] = $key;
	
	return $emptyKeys;	
}


?>