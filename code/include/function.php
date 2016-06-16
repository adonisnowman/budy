<?php
	include_once('include/memcached.php');

	function curPageName() {
		return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	}
	
	function curPageURL($serverName = false) {
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		
		$pageURL .= $_SERVER["SERVER_NAME"].(($_SERVER["SERVER_PORT"] != "80")?":".$_SERVER["SERVER_PORT"]:'');
		
		if($serverName) return $pageURL;
		 
		$pageURL .= $_SERVER["REQUEST_URI"];
		return $pageURL;		
	}
	
	
	function getDomain(){
		$server = false;
		if(preg_match('/(?P<domain>[0-9.\-A-Za-z]+).com/',$_SERVER ["SERVER_NAME"],$server) ){
			$server = $server['domain'];
		}
		return $server;
	}
	
	function Rebot(){
		
		if(preg_match("/(Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla)/i", $_SERVER['HTTP_USER_AGENT'])) 
			return true;
	
		return false;
	}
	function ArticleOnlineCkeck($ai,$url){
	
		
		$server = getDomain();		
		$sql = "SELECT 1 FROM  `news_article_state`  WHERE  `id` =$ai AND domain = '$server' AND  `online` =  '1' LIMIT 1";
		
		$result = mysql_query($sql);
		if($result && mysql_num_rows($result)) return 1;
		header("Location: $url");
		exit;
	}
	function CategoryOnlineCkeck($categoryID,$url){
		$server = getDomain();		
		$sql = "SELECT 1 FROM  `news_article_state` WHERE `online` =  '1' AND domain = '$server' AND `category` LIKE  '%\"$categoryID\"%' ";
		
		
	
		$result = mysql_query($sql);		
		if($result && mysql_num_rows($result)) return mysql_num_rows($result);		
		header("Location: $url");
		exit;
	}
	function TagOnlineCkeck($tagID,$url){
		$server = getDomain();
		$sql = "SELECT 1 FROM  `news_article_state` WHERE `online` =  '1' AND domain = '$server' AND `tag` LIKE  '%\"$tagID\"%' ";	
		$result = mysql_query($sql);
		if($result && mysql_num_rows($result)) return mysql_num_rows($result);
		header("Location: $url");
		exit;
	}
	function AuthorOnlineCkeck($authorID,$url){
		$server = getDomain();
		$sql = "SELECT 1 FROM  `news_article_state` WHERE `online` =  '1' AND domain = '$server' AND `author_id` = $authorID ";		
		$result = mysql_query($sql);
		if($result && mysql_num_rows($result)) return mysql_num_rows($result);
		header("Location: $url");
		exit;
	}
	function GetPrevArticle($id){
		$server = getDomain();
		$url = "CONCAT('http://$server.com/', `news_article_onshelf`.id,'/')";
		$img = "CONCAT('http://file.$server.com/n', `news_article_onshelf`.id, '/t_m.jpg')";
		
		//$sql = "SELECT $url AS url , $img AS img , id,title FROM `news_article_onshelf` WHERE id < $id ORDER BY id DESC LIMIT 1";
		$sql = "SELECT $url AS url , $img AS img , `news_article_onshelf`.id,`news_article_onshelf`.title FROM `news_article_onshelf`
		INNER JOIN (
			SELECT DISTINCT `id` ,`source_mode`
			FROM  `news_article_state`
			WHERE `online` =  '1' AND domain = '$server' AND id < $id ORDER BY id DESC LIMIT 1
		) AS nas ON nas.id =  `news_article_onshelf`.`id` ";
		
		$result = mysql_query($sql);
		if($result && mysql_num_rows($result)) return mysql_fetch_array($result);
		return false;
	}
	
	function GetNextArticle($id){
		$server = getDomain();
		$url = "CONCAT('http://$server.com/', `news_article_onshelf`.id,'/')";
		$img = "CONCAT('http://file.$server.com/n', `news_article_onshelf`.id, '/t_m.jpg')";
		
		//$sql = "SELECT $url AS url , $img AS img , id,title FROM `news_article_onshelf` WHERE id > $id ORDER BY id ASC LIMIT 1 ";
		$sql = "SELECT $url AS url , $img AS img , `news_article_onshelf`.id,`news_article_onshelf`.title FROM `news_article_onshelf`
		INNER JOIN (
			SELECT DISTINCT `id` 
			FROM  `news_article_state`
			WHERE `online` =  '1' AND domain = '$server'  AND id > $id ORDER BY id ASC LIMIT 1
		) AS nas ON nas.id =  `news_article_onshelf`.`id` ";
		
		$result = mysql_query($sql);
		if($result && mysql_num_rows($result)) return mysql_fetch_array($result);
		return false;
	}
	
	function GetArticleTotal(){
		$server = getDomain();
		$sql = "SELECT 1 FROM  `news_article_state` WHERE `online` =  '1' AND domain = '$server' ";
		
		$result = mysql_query($sql);
		if($result && mysql_num_rows($result)) return mysql_num_rows($result);
		return false;
	}
	
	function check_mobile(){
	
		$regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
		$regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
		$regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
		$regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
		$regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
		$regex_match.=")/i";
	
		$check = preg_match("/(ipad)/i ", strtolower($_SERVER['HTTP_USER_AGENT']));
		$check = ($check)?false:preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
	
		return $check;
	}
?>