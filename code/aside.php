<?php 

	$video_table ="soundtrack";
	
	$SQL = "SELECT title,content,category_id FROM `$video_table` ORDER BY RAND() Limit 1";
	$result = mysql_query($SQL);
	$main_video = mysql_fetch_array($result);
	$main_video['video_href'] = "/".$all_cate[$main_video['category_id']]['code'];
	
	$SQL = "SELECT name,content FROM `quote` ORDER BY RAND() Limit 1";
	$result = mysql_query($SQL);
	$quote = mysql_fetch_array($result);
	
	$SQL = "SELECT * FROM `article` ORDER BY RAND() Limit 1";
	$result = mysql_query($SQL);
	$command = mysql_fetch_array($result);
	
	$command['href'] = "/article/{$command['id']}.html";
	
	$aside_list = false;
	
	
	$SQL = "SELECT * FROM $category_table WHERE onshelf = 1 ORDER BY RAND() Limit 6";

	$result = mysql_query($SQL);
	if(mysql_num_rows($result));
	while($item = mysql_fetch_array($result)){
		
		$category_code = $all_cate[$item['category_id']]['code'];
		$item['category_name'] = $all_cate[$item['category_id']]['name'];		
		$item['category_href'] = "/$category_code/";
		$item['href'] = "/$category_code/{$item['id']}.html";

		$aside_list[] = $item;
	}

		
	
	$aside['aside'][0]['quote'][] = $quote;
	$aside['aside'][0]['main_video'][] = $main_video;
	$aside['aside'][0]['command'][] = $command;
	
	
	$aside['aside'][0]['list'] = $aside_list;
	$aside['aside'][0]['category'] = $category;
	
	
	$data['aside'] = tpl_get_html("./tpl/","aside.tpl.html",$aside);
	
?>