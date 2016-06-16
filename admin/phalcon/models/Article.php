<?php 
// namespace Blog;
class Article extends BaseModel
{
	
	
	public function initialize()
	{		
		$this->belongsTo("category_id", "ProductCategory", "id");
	}
	
	public function beforeValidationOnCreate()
	{
			
		$this->onshelf = 1;
		$this->update_time = date('Y-m-d H:i:s');
		$this->views = 0;
		$this->category_id = 2;
	}
	
	
	public function beforeValidationOnUpdate()
	{	
		$this->update_time = date('Y-m-d H:i:s');
	}
}

?>