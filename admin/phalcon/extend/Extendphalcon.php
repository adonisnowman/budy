<?php
use \Phalcon\Config\Adapter\Ini as PhConfig;
use \Phalcon\Mvc\Controller as PhController;
use \Phalcon\Db\Adapter\Pdo\Mysql  as PhMysql;
use \Phalcon\Mvc\View as PhView;
use \Phalcon\Mvc\Model as PhModel;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;

class BaseController extends PhController
{
	public $template;
	public $order_by;
	public $topSearch;
	public $controller;
	public $action;

	static $accountMemo = 'Guest';
	static $title = '';
	static $menu = array();
	static $breadcrumbs = array();
	static $topbar_control = array();
	static $select_result =  array();


	public function beforeDispatch($dispatcher){



	}
	public function beforeExecuteRoute($dispatcher)
	{

		$this->controller = $dispatcher->getControllerName();
		$this->action = $dispatcher->getActionName();


	}



	public function afterExecuteRoute($dispatcher)
	{
// 		$menu[] = array('name' => '權限管理','href' => 'purview/views');
		self::$menu = $this->menulist();
		
		if($template = $this->template){
			
			if($this->session->has('userInfo')) {
				$Login = $this->session->get('userInfo');
				$template['accountMemo'] = $Login['name'];
			}
			
			$Menugroup = AdminMenugroup::find()->toArray();
			
			
			foreach($Menugroup AS $k => $v){
				$purview_arr = json_decode( $v['purviewlist']);
				if(!is_array($purview_arr) ) continue;
				foreach ( self::$menu AS $menu ){
					if(in_array($menu['id'], $purview_arr)) $Menugroup[$k]['child_menu'][] = $menu;
				}	
				
			}
			
						
			$template['Menugroup'] = $Menugroup;
			$template['menu'] = self::$menu;
			$template['title'] = self::$title;
			$template['breadcrumbs'] = self::$breadcrumbs;
			$template['topbar_control'] = self::$topbar_control;
			$template['select_result'] = self::$select_result;
			echo $this->view->getRender('', 'template',$template,
					function($view) {	$view->setRenderLevel(PhView::LEVEL_LAYOUT);	}
			);
		}
	}


	public function onConstruct()
	{


	}

	protected function menulist(){
	
		$Login = $this->session->get('userInfo');
		$purviewlist = $Login['purviewlist'];
		
		$listUrl = "CONCAT(admin_controller.code, '/', admin_purview.code)";
		
		$sql = "SELECT admin_controller.id as id,admin_controller.name as name, $listUrl as href FROM admin_purviewlist
		INNER JOIN admin_controller ON admin_controller.id = admin_purviewlist.controller_id
		INNER JOIN admin_purview ON admin_purview.id = admin_purviewlist.purview_id
		WHERE admin_purviewlist.onshelf = 1 and admin_purviewlist.purview_id  = 7 " ;
		if(is_array($purviewlist)) 
			$sql .= " and admin_purviewlist.id in (".implode(',',$purviewlist).")";
	
		
		$builder = new BaseModel();
		return $builder->CustomQuery($sql)->toArray();
	}
	protected function relativePurview($groupPurview,$purview){
		return $fullDiff = array_merge(array_diff($groupPurview, $purview), array_diff($purview, $groupPurview));
	
	}
	protected function purviewlist($actionType = false,$onshelf = false,$controller = false){
		
		
		if(false === $controller) $controller = $this->controller;
		
		$sql = "SELECT * FROM admin_purviewlist
		INNER JOIN admin_controller ON admin_controller.id = admin_purviewlist.controller_id
		INNER JOIN admin_purview ON admin_purview.id = admin_purviewlist.purview_id
		WHERE admin_controller.code = '$controller' ";
		
		if (false !== $actionType ) $sql = $sql." and admin_purviewlist.action_type  = $actionType ";
		
		if (false !== $onshelf ) $sql = $sql." and admin_purviewlist.onshelf  = $onshelf ";
		
		$builder = new BaseModel();
		return $builder->CustomQuery($sql)->toArray();
	}
	
	
	
	protected function getControllerTitle()
	{
		$url = $this->controller;
		if($this->action != 'index') $url .= "/".$this->action;
	
		$title = AdminMenu::findFirst(array(
				"url = :url:",
				"bind" => array('url' => $url ),
		));
	
		if($title != false)	{
			$title = $title->toArray();
			return $title['child_name'];
		}
		else return $url;
	}
	
	protected function topSearchSql($field = array(),$topSearch = FALSE)
	{
		if(!$topSearch) $topSearch = $this->topSearch;
		if($topSearch && count($field)) {
				
			$where = implode(' LIKE :topSearch: OR ', $field)." LIKE :topSearch: ";
				
			$topSearch = array($where,
	
					"bind" => array('topSearch' => '%' . $topSearch . '%')
			);
		}
		return $topSearch;
	}

	protected function connectDB($connection){
		return $connection = new PhMysql($connection);


	}
	
	function query_replace($tag){
	
		$tag = $this->css_replace($tag);
		$tag = $this->id_replace($tag);
		
		$tags = explode(" ", $tag);
			
		return "//".implode("/", $tags);
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
}

class IndexController extends BaseController
{

	public function indexAction()
	{
		
		$this->response->redirect('account');
	}


}

class contentView extends PhView
{
	public function __Construct($ViewsDir = false)
	{
		$configFile = CONFIGFILE_PATH;
		$config = new PhConfig($configFile);
		$this->setViewsDir(($ViewsDir)?$ViewsDir:$config->application->viewsDir);
	}

}

class BaseModel extends PhModel
{
	public static function CustomQuery($sql)
    {
        // Base model
        $robot = new BaseModel();

        // Execute the query
        return new Resultset(null, $robot, $robot->getReadConnection()->query($sql));
    }
}
