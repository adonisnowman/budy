<?php 
	$start = microtime(true);
	
	header("Content-type: text/html; charset=utf-8");

	include_once("./include/db.php");
	include_once("./include/htmltpl.php");
	include_once("./include/function.php");

	
	$get = $_REQUEST['get'];
	$id = $_REQUEST['id'];
	$cate = explode("/", $get);
	$category_table = $cate[0];
	
	$data = array();
	
	include("menu.php");
	
	
	$next_href = '';
	$first_href = '';
	$posts_grid = false;
	$SQL = "SELECT * FROM $category_table WHERE id > $id AND onshelf = 1 ";
	if(!empty($category_id)) $SQL .= "AND category_id = $category_id ";
	$SQL .="ORDER BY id ASC Limit 12 ";
	$result = mysql_query($SQL);
	
	while($item = mysql_fetch_array($result)){	

		
		$category_code = $all_cate[$item['category_id']]['code'];
		$item['category_name'] = $all_cate[$item['category_id']]['name'];		
		$item['category_href'] = "/$category_code/";		
		$item['href'] = "/$category_code/{$item['id']}.html";
		if(!$next_href) $next_href = $item['href'];
		$list[] = $item;
	}
	
	if(!empty($list)) $posts_grid['article']= $list;
	$posts['main'][] = $posts_grid;
	$data['posts-grid'] = tpl_get_html("./tpl/","posts-grid.tpl.html",$posts);
	
		
	
	include("aside.php");
	
	
	if(isset($category_id) ){
		$category_code = $all_cate[$category_id]['code'];
		$SQL = "SELECT id FROM $category_table WHERE category_id = $category_id AND onshelf = 1 ORDER BY id ASC Limit 1";
		$result = mysql_query($SQL);
		$item = mysql_fetch_array($result);
		$first_href = "/$category_code/{$item['id']}.html";
	}
	
	if($category_table == 'Bouns'){
		
		$SQL = "SELECT id FROM Bouns WHERE onshelf = 1 ORDER BY id ASC Limit 1";
		$result = mysql_query($SQL);
		$item = mysql_fetch_array($result);
		$first_href = "/Bouns/{$item['id']}.html";
	}
	
	$SQL = "SELECT * FROM $category_table WHERE id= $id Limit 1";
	$result = mysql_query($SQL);
	
	if(mysql_num_rows($result)){
		
		$SQL = "UPDATE $category_table SET views = views+1 WHERE id= $id Limit 1 ";
		mysql_query($SQL);
		
		$content = mysql_fetch_array($result);
		$content['category_name'] = $category_name;
		$content['slides'] = $main_commend;
		
		if($first_href) $content['pagination'][0]['first'][]['href'] = $first_href;
		if($next_href) $content['pagination'][0]['next'][]['href'] = $next_href;
		
		
		$ogimage = $content['cover']; 
		$main_title=$content['title'];
		$main_desc=$content['memo'];
		
		$article['main'][] = $content;
		
		if($category_table == 'article')
			$data['article'] = tpl_get_html("./tpl/","article.tpl.html",$article);
		else {		
			$data['main-slider'] = tpl_get_html("./tpl/","main-slider.tpl.html",$article);
		}	
		
		
	}
	
	
		
	
	
		
	$footer['footer'][] = array();
	$data['footer'] = tpl_get_html("./tpl/","footer.tpl.html",$footer);
	$data['main-title'] = $main_title;
	$data['main-desc'] = $main_title;
	$data['ogurl'] = curPageURL();
	$data['ogimage'] = "http://budy.tw/".$ogimage;
	
	$HTML['HTML'][] = $data;
	
	echo tpl_get_html("./tpl/","main.tpl.html",$HTML);
	
?>