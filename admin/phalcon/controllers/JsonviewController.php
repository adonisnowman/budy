<?php
class JsonviewController extends BaseController
{

	public $settings;
	public $facebook;
	
	public function initialize()
	{
		
	
	}
	
	
	public function notFoundAction()
	{
		
		
	}
	
	public function seriescheckAction($viewType = 'json')
	{
	
		$content = false;
		$return['css'] = 'primary';
		$return['check'] = 0;
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');
			if(isset($info['video_id'])) $add_content = Series::count(" video_id = '{$info['video_id']}' ");
	
			if(isset($info['id'])) $content = Series::findFirst(" id = '{$info['id']}' ");
		}
	
		if(false !== $content){
			$return['preview'] = $content->toArray();
			$return['preview_html'] = $return['preview']['content'];
		}
		else if(empty($info['video_id']) ){
			$return['message'] = "Type a name to create account";
		}else if($add_content) {
			$return['message'] = "Name has Used";
			$return['css'] = 'danger';
		}
		else {
			$return['message'] = "Enjoy it";
			$return['css'] = 'success';
			$return['check'] = 1;
	
			$video_id = $info['video_id'];//K6lOZMc8dlU
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
	
	public function bounscheckAction($viewType = 'json')
	{
	
		$content = false;
		$return['css'] = 'primary';
		$return['check'] = 0;
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');
			if(isset($info['video_id'])) $add_content = Bouns::count(" video_id = '{$info['video_id']}' ");
				
			if(isset($info['id'])) $content = Bouns::findFirst(" id = '{$info['id']}' ");
		}
	
		if(false !== $content){
			$return['preview'] = $content->toArray();
			$return['preview_html'] = $return['preview']['content'];
		}
		else if(empty($info['video_id']) ){
			$return['message'] = "Type a name to create account";
		}else if($add_content) {
			$return['message'] = "Name has Used";
			$return['css'] = 'danger';
		}
		else {
			$return['message'] = "Enjoy it";
			$return['css'] = 'success';
			$return['check'] = 1;
				
			$video_id = $info['video_id'];//K6lOZMc8dlU
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
	
	public function articlecheckAction($viewType = 'json')
	{
		
		$content = false;
		
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');			
			$content = Article::findFirst(" id = '{$info['id']}' ");
		}
		
		if(false !== $content){
			$return['preview'] = $content->toArray();			
			$return['preview_html'] = $return['preview']['content'];
		}
		
		if($viewType == 'json') $return = json_encode($return,true);
		
		echo $return;
	}
	
	
	public function soundtrackcheckAction($viewType = 'json')
	{
	
		$content = false;
		$return['css'] = 'primary';
		$return['check'] = 0;
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');
			if(isset($info['video_id'])) $add_content = Soundtrack::count(" video_id = '{$info['video_id']}' ");
				
			if(isset($info['id'])) $content = Soundtrack::findFirst(" id = '{$info['id']}' ");
		}
	
		if(false !== $content){
			$return['preview'] = $content->toArray();
			$return['preview_html'] = $return['preview']['content'];
		}
		else if(empty($info['video_id']) ){
			$return['message'] = "Type a name to create account";
		}else if($add_content) {
			$return['message'] = "Name has Used";
			$return['css'] = 'danger';
		}
		else {
			$return['message'] = "Enjoy it";
			$return['css'] = 'success';
			$return['check'] = 1;
				
			$video_id = $info['video_id'];//K6lOZMc8dlU
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
	
	public function moviecheckAction($viewType = 'json')
	{
	
		$content = false;
		$return['css'] = 'primary';
		$return['check'] = 0;
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');
			if(isset($info['video_id'])) $add_content = Movie::count(" video_id = '{$info['video_id']}' ");
			
			if(isset($info['id'])) $content = Movie::findFirst(" id = '{$info['id']}' ");
		}
		
		if(false !== $content){
			$return['preview'] = $content->toArray();
			$return['preview_html'] = $return['preview']['content'];
		}
		else if(empty($info['video_id']) ){
			$return['message'] = "Type a name to create account";
		}else if($content) {
			$return['message'] = "Name has Used";
			$return['css'] = 'danger';
		}
		else {
			$return['message'] = "Enjoy it";
			$return['css'] = 'success';
			$return['check'] = 1;
				
			$video_id = $info['video_id'];//K6lOZMc8dlU
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
	
	public function storycheckAction($viewType = 'json')
	{
	
		$content = false;
		$return['css'] = 'primary';
		$return['check'] = 0;
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');
			if(isset($info['video_id'])) $add_content = Story::count(" video_id = '{$info['video_id']}' ");
				
			if(isset($info['id'])) $content = Story::findFirst(" id = '{$info['id']}' ");
		}
	
		if(false !== $content){
			$return['preview'] = $content->toArray();
			$return['preview_html'] = $return['preview']['content'];
		}
		else if(empty($info['video_id']) ){
			$return['message'] = "Type a name to create account";
		}else if($add_content) {
			$return['message'] = "Name has Used";
			$return['css'] = 'danger';
		}
		else {
			$return['message'] = "Enjoy it";
			$return['css'] = 'success';
			$return['check'] = 1;
				
			$video_id = $info['video_id'];//K6lOZMc8dlU
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
	
	public function videocheckAction($viewType = 'json')
	{
	
		$content = false;
		$return['css'] = 'primary';
		$return['check'] = 0;
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');
			if(isset($info['video_id'])) $add_content = Video::count(" video_id = '{$info['video_id']}' ");
			
			if(isset($info['id'])) $content = Video::findFirst(" id = '{$info['id']}' ");
		}
		
		if(false !== $content){
			$return['preview'] = $content->toArray();
			$return['preview_html'] = $return['preview']['content'];
		}
		else if(empty($info['video_id']) ){
			$return['message'] = "Type a name to create account";
		}else if($add_content) {
			$return['message'] = "Name has Used";
			$return['css'] = 'danger';
		}
		else {
			$return['message'] = "Enjoy it";
			$return['css'] = 'success';
			$return['check'] = 1;
			
			$video_id = $info['video_id'];//K6lOZMc8dlU
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
	
	public function categorycheckAction($viewType = 'json')
	{
	
		$content = false;
		$return['css'] = 'primary';
		$return['check'] = 0;
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');
			$content = ProductCategory::count(" name = '{$info['name']}' ");
		}
		if(empty($info['name']) ){
			$return['message'] = "Type a name to create category";
		}else if($content) {
			$return['message'] = "Category Name has Used";
			$return['css'] = 'danger';
		}
		else {
			$return['message'] = "Enjoy it";
			$return['css'] = 'success';
			$return['check'] = 1;
		}
	
		if($viewType == 'json') $return = json_encode($return,true);
	
		echo $return;
	}
	
	public function accountcheckAction($viewType = 'json')
	{
		
		$content = false;
		$return['css'] = 'primary';
		$return['check'] = 0;
		if ($this->request->isPost()) {
			$info = $this->request->getPost('info');				
			$content = AdminUseraccount::count(" account = '{$info['account']}' ");
		}
		if(empty($info['account']) ){			
			$return['message'] = "Type a name to create account";
		}else if($content) {
			$return['message'] = "Name has Used";
			$return['css'] = 'danger';
		}
		else {
			$return['message'] = "Enjoy it";
			$return['css'] = 'success';
			$return['check'] = 1;
		}
		
		if($viewType == 'json') $return = json_encode($return,true);
		
		echo $return;
	}
	
	public function fbfansAction($viewType = false)
	{
		
		if ($this->request->isPost()) {
			$id = $this->request->getPost('info');
		
		}
		$info = $this->facebook->graph_api('/'.$id)->asArray();
		
		
		$jsoninfo['html']['id'] = $info['id'];		
		$jsoninfo['html']['name'] = $info['name'];
		$jsoninfo['html']['likes'] = $info['likes'];
		$jsoninfo['html']['talking_about_count'] = $info['talking_about_count'];
		
		$jsoninfo['attr']['src'] = "http://graph.facebook.com/{$info['id']}/picture?width=96&height=96";
		
		$jsoninfo['info'] = $info;
		
		echo json_encode($jsoninfo);
	}
	
	public function purviewAction($viewType = false)
	{
		$main_content = false;
		$where = '';
		if ($this->request->isPost()) {
			$id = $this->request->getPost('id');
				
		}
		
		
		$content = AdminPurview::find(" id = $id ");
		
		if($content != false) $content = $content->toArray();
		
		$viewContent['content'] = $content;
		if($viewType == 'json') $main_content = json_encode($content,true);
		else{
			$view = new contentView();
			$main_content = $view->getRender($this->controller, $this->action,$viewContent);
		}
		
		echo $main_content;
	}
	
	
}
?>