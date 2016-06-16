<?php 
// namespace Blog;
class ProductCategory extends BaseModel
{
	
	
	public function initialize()
	{		
		
	}
	
	public function beforeValidationOnCreate()
	{
		$this->onshelf = 1;
		$this->views = 1;
		$this->update_time = date('Y-m-d H:i:s');
		if(!$this->icon) $this->icon = 'space.png';
	}
	
	public function beforeValidationOnUpdate()
	{
		$this->update_time = date('Y-m-d H:i:s');
		
	}
}

?>