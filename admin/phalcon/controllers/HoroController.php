<?php
class HoroController extends BaseController
{

	public $settings;
	public $DBconnect;
	
	public function initialize()
	{
	
		$connection = $this->settings['connection'] = array(
				"host" => "localhost",
				"username" => "ifunso_user",
				"password" => "AbcAbcAbc1985",
				"dbname" => "ifunso_horoscope",
				"charset" => 'utf8',
		);
		$this->DBconnect = $this->connectDB($connection);
	}
	
	public function indexAction()
	{
		$this->action = 'list';
		$this->listAction();
		return;
	}
	
	public function listAction($pg = 1,$viewType = false)
	{
		
		
		
		$ArticleFromIndex = ArticleFromIndex::find(array(" from_table = 'horoscope'"));
	
		if($ArticleFromIndex != false ) {
			$ArticleFromIndex= implode(',', array_column($ArticleFromIndex->toArray(), 'from_index'));
		}
		
		$sql = "SELECT pk_horoscope,title_horoscope,img_file FROM horoscope
				WHERE pk_horoscope not in ( ".$ArticleFromIndex." )  ";
		$total = $this->DBconnect->query($sql);
		$total_nums = $total->numRows();
		$page_one = 20;
		
		$sql = "SELECT pk_horoscope,title_horoscope,img_file FROM horoscope  
				WHERE pk_horoscope not in ( ".$ArticleFromIndex." ) 
				limit ".($pg-1)*$page_one.", ".$page_one;
		
		// Send a SQL statement to the database system
		$content = $this->DBconnect->fetchAll($sql);
		
		
		$page ['start'] = 1;
		$page ['end'] = ceil ( $total_nums / $page_one );
		$page ['url'] = "/".$this->controller.'/'.$this->action;
		$page ['page_num'] = $pg;
		$page ['url_end'] = '/' ;
		
		
		$p = new Pagetheme;
		$page = $p->page($page);
		
		
		$viewContent['page'] = $page;
		$viewContent['content'] = $content;
		
		$viewContent['ArticleGroup'] = ArticleGroup::find()->toArray();
		
		if($viewType == 'json') return json_encode($viewContent['content'],true);
		
		$view = new contentView('./app/views');
		$main_content = $view->getRender($this->controller, $this->action,$viewContent);
		
		
		$this->template = array(
			"main_content" => $main_content,
		);
		/*
		
		// Print each robot name
		while ($robot = $result->fetch()) {
			echo $robot["title_horoscope"];
		}
		/*
		// Get all rows in an array
		$robots = $connection->fetchAll($sql);
		foreach ($robots as $robot) {
			echo $robot["title_horoscope"];
		}
		
		// Get only the first row
		$robot = $connection->fetchOne($sql);
		/**/
		$this->view->disable();		
	}

	public function removeAction()
	{
		if($this->request->isPost()){
			
			$from_index = $this->request->getPost('horo');
			$from_table = 'horoscope';
			$horoArticle = new ArticleFromIndex;
			$horo_input = array('article_index'=> -1,'from_index'=>$from_index,'from_table'=>$from_table);
			
			if ($horoArticle->create($horo_input) == false) {
				foreach ($horoArticle->getMessages() as $message) {
							echo $message, "\n";
					}
			}
		}
	
	}	
	
	public function registerAction()
	{
	

	}
}
?>