<pre>
<?php 
	$start = microtime(true);
	
	header("Content-type: text/html; charset=utf-8");

	include_once("./include/db.php");
	include_once("./include/htmltpl.php");
	include_once("./include/function.php");
	
	
	
	
	
	//----------------------/
	
	//$href['rules'] = "(?P<val>\/node[0-9]+)";
	$href["name"] = "href";
	$href["tags"]= "div#article-content";//播放清單
	$href["img_prefix"]= "";
	$href["img_attr"]= "src";
	$href["remove_tags"]= "script,div.adsDiv";
	$href["remove_attr"]= "";
	$href["type"]= "html";
	
	//$href["val_prefix"]= "http://ww.daliulian.net/cat62";
	
	$url = "http://ww.daliulian.net/cat62/node1132610"; //播放清單
	
	
	$content = resolveHTML_rules($href,$url);
	var_dump($content);
	get_page();
	exit;
	
	
	
	function get_page(){
			$href['rules'] = "\/cat48\/62(?P<val>\?page=[0-9]+)";
			$href["name"] = "href";
			$href["tags"]= "ul#yw2 li.page";//播放清單
			$href["img_prefix"]= "";
			$href["img_attr"]= "src";
			$href["remove_tags"]= "script,style";
			$href["remove_attr"]= "style";
			$href["type"]= "href";
			
			$href["val_prefix"]= "http://ww.daliulian.net/cat48/62";
			
			$url = "http://ww.daliulian.net/cat48/62"; 
			
			$content = resolveHTML_rules($href,$url);
			var_dump($content);
			//----------------------get list
			$href['rules'] = "(?P<val>\/node[0-9]+)";
			$href["name"] = "href";
			$href["tags"]= "div.summary";//播放清單
			$href["img_prefix"]= "";
			$href["img_attr"]= "src";
			$href["remove_tags"]= "script,style";
			$href["remove_attr"]= "style";
			$href["type"]= "href";
			
			$href["val_prefix"]= "http://ww.daliulian.net/cat62";
			
			$url = $content['href'][0];
			
			
			$content = resolveHTML_rules($href,$url);
			var_dump($content);
			
		
	}
	
	
	
	
	function resolveHTML_rules($rules,$url = ''){
	
	
		$html_reg = "/^http[s]?\:\/\/([a-z0-9A-Z-_\/.]*)(\/[a-z0-9A-Z-_\/.]*)?/";
		$return =false;
		$tag = (!empty($rules['tags']))?$rules['tags']:'*';		
		
		$tag = query_replace($tag);
		$tags = explode(" ", $tag);
		
		$query = "//".implode("/", $tags);
	
	
		$reg = "/".$rules['rules']."/";
		$attr = $rules['type'];
		$name = $rules['name'];
		$img_prefix = $rules['img_prefix'];
		$val_prefix = $rules['val_prefix'];
		$img_attr = $rules['img_attr'];
	
	
		if($url == '') $url = $rules['url'];
	
	
		$sites_html = file_get_contents($url);
		$sites_html = mb_convert_encoding($sites_html, 'utf-8', mb_detect_encoding($sites_html));
		$sites_html = mb_convert_encoding($sites_html, 'html-entities', 'utf-8');
	
		$html = new DOMDocument();
		@$html->loadHTML($sites_html);
		$xpath = new DOMXPath($html);
	
	
		$remove_tag = explode(",", $rules['remove_tags']);
	
		foreach ($remove_tag as $i){
				
			$remove_query = "//".implode("/", explode(" ", query_replace($i)));
				
			foreach($xpath->query($query.$remove_query) as $content){
				$content->parentNode->removeChild($content);
			}
		}
	
		$remove_attr = explode(",", $rules['remove_attr']);
		foreach ($remove_attr as $i){
			foreach($xpath->query($query."//*[@$i]") as $content){
				$content->removeAttributeNode($content->getAttributeNode( $i ));
			}
		}
	
		foreach($xpath->query($query."//img") as $content){
			$src = $content->getAttribute($img_attr);
			$src = ((!preg_match('/^http[s]?/',$src)&& $img_prefix)?$img_prefix:'').$src;
			$content->setAttribute('src', $src);
			$real_src[] = $src;
		}
	
	
		foreach($xpath->query($query."//iframe[@src]") as $content){
	
	
			$element = $html->createElement('div');
			$element->setAttribute('class','embed-responsive embed-responsive-16by9');
			$element->appendChild($content->cloneNode(false));
	
			$content->parentNode->replaceChild($element, $content);
	
		}
	
	
			
			
	
		switch($rules['type']){
			case 'href':
				$query .= "//a";
				$attr = "href";
				break;
			case 'src':
				$query .= "//img";
				$attr = $img_attr;
				break;
			case 'iframe':
				$query .= "//iframe[@src]";
				$attr = 'src';
			case 'video':
				$query .= "//video[@data-href]";
				$attr = 'data-href';
		}
	
		$video = array('iframe');
		echo $query;
		
		$description = $xpath->query($query);
		foreach($description as $content){
			
			
			
			
			$tag_name = '';
			if($rules['type'] == 'video' || $rules['type'] == 'iframe') {
	
				if( in_array($content->tagName,$video) ) $tag_name = $content->tagName ;//$content->getAttribute($attr);
				if(preg_match('/youtube/',$content->getAttribute($attr))) $tag_name = 'youtube';
				if( in_array($content->tagName,$video) ) $tag_name = $content->tagName ;
			}
				
			
			
			
			
			if($attr == 'html') $value = $html->saveHTML($content) ;
			else if($attr == 'text') $value = $content->nodeValue ;
			else  $value = $content->getAttribute($attr);
			
			
			
			if($rules['rules'] && preg_match($reg, $value,$temp) ){
				$value = $temp["val"];
			}else if($rules['rules'] && !preg_match($reg, $value)){
				continue;
			}
			
	
			
			if(is_array($return) && in_array($value,$return[$name])) continue; 
			if($val_prefix) $value = $val_prefix.$value;
			$return[$name][] = $value;
				
	
		}
	
		return $return;
	
	}
	
	function query_replace($tag){
	
		$tag = css_replace($tag);
		$tag = id_replace($tag);
		return $tag;
	}
	
	function css_replace($tag){
		preg_match_all("/(?P<css>\.[\w\-]+)/",$tag,$result);
	
		if(!empty($result) && is_array($result))
			foreach($result['css'] as $css){
			$tag = str_replace($css,"[contains(@class,'".substr($css, 1)."')]",$tag);
		}
		return $tag;
	}
	
	function id_replace($tag){
		preg_match_all("/(?P<id>#[\w\-]+)/",$tag,$result);
	
		if(!empty($result) && is_array($result))
			foreach($result['id'] as $id){
			$tag = str_replace($id,"[@id='".substr($id, 1)."']",$tag);
		}
		return $tag;
	}
	
?>