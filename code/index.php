<?php 
	$start = microtime(true);
	
	header("Content-type: text/html; charset=utf-8");

	include_once("./include/db.php");
	include_once("./include/htmltpl.php");
	include_once("./include/function.php");

	$data = array();
	
	$category_table = 'video';
	
	
	include("menu.php");
	
	$SQL = "SELECT * FROM `video` WHERE onshelf = 1 and category_id = $commend_id ORDER BY id DESC Limit 1";
	$result = mysql_query($SQL);
	$main_slider = mysql_fetch_array($result);
	
     

	
	
	$main_title=$main_slider['title'];
	$main_desc=$main_slider['memo'];
	
	
	
	
	
	$category_code = $all_cate[$commend_id]['code'];
	
	$main_slider['slides'] = $main_commend;
	$slider['main'][] = $main_slider;
	
	$data['main-slider'] = tpl_get_html("./tpl/","main-slider.tpl.html",$slider);
	
	
	$SQL = "SELECT * FROM `article` ORDER BY RAND() Limit 1";
	$result = mysql_query($SQL);
	$command = mysql_fetch_array($result);
	
	$command['href'] = "/article/{$command['id']}.html";
	
	$main['main'][] = $command;
	$data['main-grid'] = tpl_get_html("./tpl/","main-grid.tpl.html",$main);
	
	$posts_grid = false;
	$SQL = "SELECT * FROM video as list
			INNER JOIN (
				SELECT id as cid FROM product_category WHERE parent_category = 1 AND views > 0 Limit 6 
				) as cate on cate.cid =  list.category_id 			
			GROUP BY category_id ORDER BY list.id DESC ";

	
	$result = mysql_query($SQL);
	
	while($item = mysql_fetch_array($result)){
		$category_code = $all_cate[$item['category_id']]['code'];
		$item['category_name'] = $all_cate[$item['category_id']]['name'];
		$item['category_href'] = "/$category_code/";
		$item['href'] = "/$category_code/{$item['id']}.html";
		$list[] = $item;		
	}
		
	if(!empty($list)) $posts_grid['article']= $list;
	$posts['main'][] = $posts_grid;
	$data['posts-grid'] = tpl_get_html("./tpl/","posts-grid.tpl.html",$posts);

	
	include("aside.php");
	
	$footer['footer'][] = array();
	$data['footer'] = tpl_get_html("./tpl/","footer.tpl.html",$footer);
	$data['main-desc'] = $main_title;
	$data['main-title'] = $main_title;
	$data['ogurl'] = '/images/maxresdefault.jpg';
	$HTML['HTML'][] = $data;
	
	echo tpl_get_html("./tpl/","main.tpl.html",$HTML);
	
?>