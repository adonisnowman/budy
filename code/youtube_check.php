<pre>
<?php 
	$start = microtime(true);
	set_time_limit(0);
	header("Content-type: text/html; charset=utf-8");

	include_once("./include/db.php");

	$img = 'http://i.ytimg.com/vi/H-_pS2LIPNE/mqdefault.jpg';
	
	
	$tableSQL = " SELECT code FROM product_category WHERE parent_category = 0 AND id != 2";
	$tableresult = mysql_query($tableSQL);
	while($table = mysql_fetch_array($tableresult) ){
	
		$SQL = "SELECT video_id FROM {$table['code']} WHERE onshelf = 1 ";
		$result = mysql_query($SQL);
		while($row = mysql_fetch_array($result) ){
			
			$id= $row['video_id'];
			$url = "http://i.ytimg.com/vi/$id/mqdefault.jpg";
			if(false === is_url_exist($url)){
				
				$SQL = "UPDATE video SET onshelf = 0 WHERE video_id = '$id'";
				echo $id."<br/>";
				mysql_query($SQL);
			}
		}
	
	}
	exit;
	
	
	
function is_url_exist($url){
	
	
	
    $ch = curl_init($url);    
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if($code == 200){
       $status = true;
    }else{
      $status = false;
    }
    curl_close($ch);
   
   return $status;
}
	
?>