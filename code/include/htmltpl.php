<?php

function tpl_create($tpl, $data){

	if(!isset($data) || !is_array($data)) return false;
	foreach ($data AS $focus => $set_Value)	
		$tpl = preg_replace('/(\{'.$focus.'\})/', $set_Value, $tpl);
	
	return $tpl;
}

function tpl_block_array($tpl, $block)
{
	if( !is_array( $block) ) return $tpl;
	
	
	if(count($block) > 0)
	foreach($block AS $keys => $value)
	{
		
		//取得value keys
		if(!is_array($value) ) $data[$keys] = $value;	
		else
		{
			//取代BLOCK
			
			
			$temp_reg = '/<!--{'.$keys.'}-->(?P<'.$keys.'>[\s\S]+?)<!--{\/'.$keys.'}-->/';
			if(!is_numeric($keys)) preg_match_all($temp_reg,$tpl,$temp_tpl);
			
			if(isset($temp_tpl[$keys]))
			foreach ($temp_tpl[$keys] AS $key_tpl){
				$new_tpl = '';
				foreach($value AS $new_block)
					$new_tpl .= tpl_block_array($key_tpl, $new_block);
				
				$reg_v ='<!--{'.$keys.'}-->'.$key_tpl.'<!--{/'.$keys.'}-->';
				$tpl = str_replace( $reg_v,$new_tpl,$tpl);
			}
				
			
		}
		
	}
	//取代value
	if(isset($data) && is_array($data)) $tpl = tpl_create($tpl, $data);

	// 	清除空BLOCK
	preg_match_all('/<!--{[\/](?P<block_name>[\S]+)}-->/',$tpl,$temp_block);
	foreach($temp_block['block_name'] AS $keys)
	{
		$reg_v =  '/<!--{'.$keys.'}-->(?P<'.$keys.'>[\s\S]+?)<!--{\/'.$keys.'}-->/';
		$tpl = preg_replace($reg_v, '', $tpl);
	}
	
	return $tpl;
}


function memcached_fileget($filename){
	
	$memcache = false;
	
	if(class_exists("Memcached")) 	$memcache = new Memcached();
	else					      	$memcache = new Memcache();	
	
	
	$memcache_key = md5($filename);
	$memcache->addServer('localhost', 11211);
	$memcache->delete($memcache_key);
	
	
	if($memcache && $file_content = $memcache->get($memcache_key)){
		return $file_content;
	}
	$file_content = file_get_contents($filename);
	if($memcache) $memcache->set( $memcache_key, $file_content, 0 , 3600);
	
	return $file_content;
}
function tpl_get_html($tpl_dir, $tpl_file, $block)
{
	
	$filename = $tpl_dir.$tpl_file;
	$tpl = file_get_contents($filename);
	
	
	$tpl = tpl_block_array($tpl, $block);
	
	
// 	清除空值
	$tpl = preg_replace('/\{([\S]+)\}/','',$tpl);
	return $tpl;
}

?>