<?php 
	$start = microtime(true);
	session_start();
	header("Content-type: text/html; charset=utf-8");

	include_once("./include/db.php");
	include_once("./include/htmltpl.php");
	include_once("./include/function.php");

	
	$table_name = 'series';
	
	$img = 'http://i.ytimg.com/vi/H-_pS2LIPNE/mqdefault.jpg';
	
	$SQL = "SELECT id,cover,video_id FROM $table_name WHERE onshelf = 1";
	$result = mysql_query($SQL);
	while($row = mysql_fetch_array($result) ){
	
		$id = $row['id'];
// 		$video_id = $row['video_id'];
		$url = "http://i.ytimg.com/vi/".$row['video_id']."/hqdefault.jpg";//$row['cover'];
		
		$filename = $table_name."_$id.jpg";
		echo $img_url ="/cover/$filename";
		echo $img = $_SERVER['DOCUMENT_ROOT']."/cover/$filename";
		
		if(get_url_photo($url,$img)){
				
			
			
			echo $SQL = "UPDATE $table_name SET cover = '$img_url' WHERE id = '$id'";
			
			mysql_query($SQL);
		}
	}
	
	
// 	//是否建立目錄
// 	if(false !== $newDir && false === is_dir( $newDir ) ) {
// 		mkdir($newDir);
// 		chmod($newDir, 0777);
// 	}
	
	
// 	get_url_photo( $img , $newDir."$key.jpg");
	
	
	
	
	
	
	
	
	function get_url_photo($url,$filename){
		if(is_url_exist($url)){
			echo $url."<br/>";
			file_put_contents($filename,file_get_contents($url));	
			return true;
			
		}else{
			echo "ERROR:".$url."<br/>";
		}
		return false;
	}
	
	
	
	
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
	
	//自動加浮水印
	function watermark_auto($filename, $position='bottom_right', $markfile='images/url-icon.png'){
		$src_temp = imagecreatefromjpeg($markfile);
		$imagesize_temp = getimagesize($markfile);
	
		if( is_file($filename) === true ) {
			chmod($filename, 0777);
			//製作縮圖
			$src_info = getimagesize($filename);
			if($src_info["mime"] == "image/png"){
				$src_im = imagecreatefrompng($filename);
			}else if($src_info["mime"] == "image/gif"){
				$src_im = imagecreatefromgif($filename);
			}else {
				$src_im = imagecreatefromjpeg($filename);
			}
			$img_type = $src_info["mime"];
			$alpha = 100;
	
			preg_match("/\/(?P<type>png|jpeg|gif)/", $src_info["mime"], $match);
			if ( empty($match['type']) ) return false;
			elseif ( $match['type'] == 'jpeg' ) $match['type'] = 'jpg';
	
			$a_width = $src_info[0];
			$a_height = $src_info[1];
				
			// unlink($filename);
			// $filename .= '.'. $match['type'];
				
			$resize = imagecreatetruecolor($a_width, $a_height) or die("error4!\n");
			imagecopyresampled($resize, $src_im, 0, 0, 0, 0, $a_width, $a_height, $src_info[0], $src_info[1]);
			switch ($position) {
				case 'top_right':
					imagecopyresampled($resize, $src_temp, $a_width-$imagesize_temp[0], 0, 0, 0, $imagesize_temp[0], $imagesize_temp[1], $imagesize_temp[0], $imagesize_temp[1]);
					break;
				case 'top_left':
					imagecopyresampled($resize, $src_temp, 0, 0, 0, 0, $imagesize_temp[0], $imagesize_temp[1], $imagesize_temp[0], $imagesize_temp[1]);
					break;
				case 'bottom_right':
					imagecopyresampled($resize, $src_temp, $a_width-$imagesize_temp[0], $a_height-$imagesize_temp[1], 0, 0, $imagesize_temp[0], $imagesize_temp[1], $imagesize_temp[0], $imagesize_temp[1]);
					break;
				case 'bottom_left':
					imagecopyresampled($resize, $src_temp, 0, $a_height-$imagesize_temp[1], 0, 0, $imagesize_temp[0], $imagesize_temp[1], $imagesize_temp[0], $imagesize_temp[1]);
					break;
				default:
					imagecopyresampled($resize, $src_temp, 0, $a_height-$imagesize_temp[1], 0, 0, $imagesize_temp[0], $imagesize_temp[1], $imagesize_temp[0], $imagesize_temp[1]);
			}
				
			imagejpeg($resize, $filename, $alpha);
		}
		else return false;
	}
?>