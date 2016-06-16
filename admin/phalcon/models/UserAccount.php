<?php 
// namespace Blog;
class UserAccount extends BaseModel
{
	
	
	public function initialize()
	{		
		$this->setSource('user_account');
        $this->setSchema('pocool');
	}
	
	
}
/*
namespace Ms168;
class Test extends BaseModel
{


	public function initialize()
	{


	}
}

/***/
?>