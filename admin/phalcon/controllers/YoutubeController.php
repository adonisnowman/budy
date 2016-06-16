<?php
class YoutubeController extends BaseController
{

	public $settings;
	public $facebook;
	public $purviewAction;
	
	public function initialize()
	{
	
		if ($this->request->isPost()) {
		
			$this->order_by = $this->request->getPost('order_by');
			
		}
	
	}
	
	
	public function notFoundAction()
	{
		
	}
	
	public function indexAction()
	{
		
		set_time_limit(0);
		
		$content = YoutubeTemp::find("category_id = 47 AND title LIKE '%一月%'")->toArray();
		
		foreach ($content AS $item){
					
			
// 			$this->getInfo_YoutubeTemp($item['video_id']);

// 			continue;
			
			$add_input['title'] = $item['title'];
			$add_input['memo']= $item['memo'];
			$add_input['video_id'] = $item['video_id'];
			$add_input['category_id'] = $item['category_id'];
			
			$content  = new Story();
				
			if ($content->create($add_input) == false) {
				foreach ($content->getMessages() as $message) {
					echo $message, "\n";
				}
			}
			
		}
		
	}
	
	function getInfo_YoutubeTemp($video_id){
		
		
		$url = "https://www.youtube.com/watch?v=$video_id";
			
		$sites_html = file_get_contents($url);
		$sites_html = mb_convert_encoding($sites_html, 'utf-8', mb_detect_encoding($sites_html));
		$sites_html = mb_convert_encoding($sites_html, 'html-entities', 'utf-8');
			
		$html = new DOMDocument();
		@$html->loadHTML($sites_html);
		$xpath = new DOMXPath($html);
			
		$tag = "span#eow-title";
		$query = $this->query_replace($tag);
		$description = $xpath->query($query);
		foreach($description as $content){
			$perview['title'] = trim($content->nodeValue);
		}
		$tag = "p#eow-description";
		$query = $this->query_replace($tag);
		$description = $xpath->query($query);
		foreach($description as $content){
			$perview['memo'] = trim($content->nodeValue);
		}
		
		$content = YoutubeTemp::findFirst(" video_id = '$video_id' ");
		
		foreach($perview AS $key => $value)
			$content->$key = $value;
			
		
		if ($content->save() == false) {
			foreach ($content->getMessages() as $message) {
				echo $message, "\n";
			}
		}
	}
	
	public function listAction($viewType = false){		
		
		$this->action = 'views';
		$this->viewsAction();
		return;
	
	}
	
	public function insertAction($viewType = false){
	
		if ($this->request->isPost()) {
		
			$info = $this->request->getPost('info');
			var_dump($info);
			$category_id = $info['category_id'];	
			foreach ($info['id'] as $id){
				if( YoutubeTemp::count(" video_id = '$id' ") > 0) continue;
				$add_input['video_id'] = $id;
				$add_input['category_id'] =$category_id;
				$content  = new YoutubeTemp();
				
				if ($content->create($add_input) == false) {
					foreach ($content->getMessages() as $message) {
						echo $message, "\n";
					}
				}	
			}
			
				
			
			
			
		}
	
	}
	
	public function searchAction($viewType = 'json'){
		
		
		$return = array();
		if ($this->request->isPost()) {
			
			$video_id = $this->request->getPost('id');
			$url = "https://www.youtube.com/watch?v=$video_id";
			$cover = "https://i.ytimg.com/vi/$video_id/mqdefault.jpg";
			$return['preview']['video_id'] = $video_id;
			$return['preview']['cover'] = $cover;
			$return['preview']['content'] = '<div class="embed-responsive embed-responsive-16by9"><iframe src="https://www.youtube.com/embed/'.$video_id.'" frameborder="0" allowfullscreen></iframe></div>';
			$return['preview_html'] = $return['preview']['content'];
			
			$sites_html = file_get_contents($url);
			$sites_html = mb_convert_encoding($sites_html, 'utf-8', mb_detect_encoding($sites_html));
			$sites_html = mb_convert_encoding($sites_html, 'html-entities', 'utf-8');
			
			$html = new DOMDocument();
			@$html->loadHTML($sites_html);
			$xpath = new DOMXPath($html);
			
			$tag = "span#eow-title";
			$query = $this->query_replace($tag);
			$description = $xpath->query($query);
			foreach($description as $content){
				$return['preview']['title'] = trim($content->nodeValue);
			}
			$tag = "p#eow-description";
			$query = $this->query_replace($tag);
			$description = $xpath->query($query);
			foreach($description as $content){
				$return['preview']['memo'] = trim($content->nodeValue);
			}
		}
		
		if($viewType == 'json') $return = json_encode($return,true);
		
		echo $return;
	}
	
	public function viewsAction($viewType='')
	{
		
		$href['rules'] = "\/watch\?v=(?P<val>[0-9a-zA-Z_\-]+)";
		$href["name"] = "href";
		// 	$href["tags"]= "ol#playlist-autoscroll-list";//播放清單
// 		$href["tags"]= "ol.section-list"; //搜尋清單
		$href["img_prefix"]= "";
		$href["img_attr"]= "src";
		$href["remove_tags"]= "script,style";
		$href["remove_attr"]= "style";
		$href["type"]= "href";
		
// 		
		
		
		
		$return = false;
		$Youtube = array();
		$purviewAction = $this->purviewlist(1,1);
		
				
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');			
			$url = $info['youtube_url'];
			$href['tags'] = $info['resolve_tag'];
			$content = $this->resolveHTML_rules($href,$url);	
			
			foreach ($content['href'] AS $id){
				if( YoutubeTemp::count(" video_id = '$id' ") > 0) continue;
				$Item['purviewAction'] = $purviewAction;
				$Item['video_id'] = $id;
				$Item['id'] = $id;
				$Youtube[] = $Item;
			}			
		}else if(YoutubeTemp::count()){
			
			$result = YoutubeTemp::find();
			$result->rewind();
			
			while ($result->valid()) {
					
				$row = $result->current();
				$Item = $row->toArray();
				$Item['ProductCategory'] = $row->ProductCategory->name;
				$Item['onshelf_css'] = ($Item['onshelf'])?'onshelf':'offshelf';
					
				$Item['purviewAction'] = $purviewAction;
				$Youtube[] = $Item;
				$result->next();
			}
		}
		
		
		
		$ProductCategory = ProductCategory::find(array('parent_category != 0 AND onshelf = 1 ',"order" => "parent_category ASC , id DESC"));
		
		$viewContent['ProductCategory'] = (false !== $ProductCategory)?$ProductCategory->toArray():array();
		$viewContent['Youtube'] = $Youtube;
		
		
		$view = new contentView();
		$viewContent = $view->getRender($this->controller, $this->controller.$this->action,$viewContent);
		if($viewType == 'json') $return =  json_encode($content,true);
		if($viewType == 'viewContent') $return =  $viewContent;
	
		
		
		$main_content['viewContent'] = $viewContent;
		
		if($return) { echo $return; exit; }
		else{
			$main_content = $view->getRender($this->controller, $this->action,$main_content);
			
			$this->template = array(
					"main_content" => $main_content,
			);
		}
		
	}
	
	
	function resolveHTML_rules($rules,$url = ''){
	
	
		$html_reg = "/^http[s]?\:\/\/([a-z0-9A-Z-_\/.]*)(\/[a-z0-9A-Z-_\/.]*)?/";
		$return =false;
		$tag = (!empty($rules['tags']))?$rules['tags']:'*';
	
		$tag = $this->query_replace($tag);
		$tags = explode(" ", $tag);
	
		$query = "//".implode("/", $tags);
	
	
		$reg = "/".$rules['rules']."/";
		$attr = $rules['type'];
		$name = $rules['name'];
		$prefix = $rules['img_prefix'];
		$img_attr = $rules['img_attr'];
	
	
		if($url == '') $url = $rules['url'];
	
	
		$sites_html = file_get_contents($url);
		$sites_html = mb_convert_encoding($sites_html, 'utf-8', mb_detect_encoding($sites_html));
		$sites_html = mb_convert_encoding($sites_html, 'html-entities', 'utf-8');
	
		$html = new DOMDocument();
		@$html->loadHTML($sites_html);
		$xpath = new DOMXPath($html);
	
	
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
	
	
		$description = $xpath->query($query);
		foreach($description as $content){
				
			$value = $content->getAttribute($attr);
				
			if($rules['rules'] && preg_match($reg, $value,$temp) ){
				$value = $temp["val"];
			}else if($rules['rules'] && !preg_match($reg, $value)){
				continue;
			}
				
	
				
			if(is_array($return) && in_array($value,$return[$name])) continue;
				
			$return[$name][] = $value;
	
	
		}
	
		return $return;
	
	}
}
?>