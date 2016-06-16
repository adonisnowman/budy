<?php 

	$SQL = "SELECT id FROM `product_category` WHERE parent_category = 1  ORDER BY sort Limit 1";
	$result = mysql_query($SQL);
	$commend = mysql_fetch_array($result);
	$commend_id = $commend['id'];
	
	
	$ogurl ="http://www.niusnews.com/upload/imgs/default/12JunC/babypics/4.jpg";
	
	
	$SQL = "SELECT id,name,code FROM `product_category` WHERE parent_category = 0 ORDER BY sort ASC";
	$result = mysql_query($SQL);
	
	while($row = mysql_fetch_array($result)){

		$all_cate[$row['id']] = $row;
		$temp_menu = $row;
			
		$SQL = "SELECT * FROM `product_category` WHERE id != {$row['id']} AND parent_category = {$row['id']} ORDER BY sort ASC";
		$child_result = mysql_query($SQL);
		while($item = mysql_fetch_array($child_result)){
			$temp_menu['child_menu'][$item['id']] = $item;
			$all_cate[$item['id']] = $item;
			if($item['id'] == $commend_id) $commend_cate = $item;
			if(isset($cate[0]) && $cate[0] == $item['code']) {
					$category_table = $row['code'];
					$cate[1] = $category_id = $item['id'];
					$main_title = $item['name'];
			}
		}
		
		if($row['code'] == $category_table ){
			 
			$category_name = $row['name'];
			$category = (!empty($temp_menu['child_menu']))?$temp_menu['child_menu']:array();
		}
		
		
		
		$temp_menu['menu_css'] = (!isset($temp_menu['child_menu']))?"":"menu-item-has-children";
		$main_menu[] = $temp_menu;
	}
	
	$menu['menu'][0]['main_menu'] = $main_menu;
	$menu['menu'][0]['topbar_menu'] = $main_menu;
	$data['main-head'] = tpl_get_html("./tpl/","menu.tpl.html",$menu);
	
	
	$data['mobile-menu'] = tpl_get_html("./tpl/","mobile-menu.tpl.html",$menu);
	
	$SQL = "SELECT id,title,content FROM `video` WHERE onshelf = 1 AND category_id = $commend_id ORDER BY id DESC Limit 3";
	$result = mysql_query($SQL);
	while($item = mysql_fetch_array($result)){
		$commend_code = $commend_cate['code'];
		$item['category_name'] = $commend_cate['name'];
		$item['category_href'] = "/$commend_code/";
		$item['href'] = "/$commend_code/{$item['id']}.html";
		$main_commend[] = $item;
	}
?>