<?php 
	$start = microtime(true);
	
	header("Content-type: text/html; charset=utf-8");

	include_once("./include/db.php");
	include_once("./include/htmltpl.php");
	include_once("./include/function.php");

	$data = array();
	$get = $_REQUEST['get'];

	$cate = explode("/", $get);
	$category_table = $cate[0];
	$main_title = ""; 
	
	
	include("menu.php");
	
	if(!$main_title) $main_title = $category_name;
	
	
	if(isset($category_id)){
		$date = date('Y-m-d H:i:s');
		$SQL = "UPDATE`product_category` SET views = views+1 ,update_time = '$date' WHERE id = $category_id Limit 1 ";
		mysql_query($SQL);
	}
	
	$posts_grid = false;
	$SQL = "SELECT list.* FROM $category_table as list 
				LEFT JOIN product_category on list.category_id =  product_category.id
				WHERE list.onshelf = 1 AND product_category.code != '' ";
	if(!empty($category_id)) $SQL .= "AND list.category_id = $category_id ";
	if($category_table != 'Bouns') $SQL .="ORDER BY list.views ASC , list.id DESC Limit 12 "; 
	$result = mysql_query($SQL);

	if($category_table == 'Bouns'){
		$bouns['main'][] = mysql_fetch_array($result);
		$data['main-slider']=tpl_get_html("./tpl/","main-slider.tpl.html",$bouns);
	}
	while($item = mysql_fetch_array($result)){
		
		
		$item['category_name'] = (empty($category))?$category_name:$category[$item['category_id']]['name'];
		$category_code = (empty($category))?$category_table:$category[$item['category_id']]['code'];
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
	$data['main-title'] = $main_title;
	$data['main-desc'] = $main_title;
	$data['ogurl'] = $ogurl;
	$HTML['HTML'][] = $data;
	
	echo tpl_get_html("./tpl/","main.tpl.html",$HTML);
	
?>