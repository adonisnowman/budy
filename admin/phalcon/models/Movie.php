<?php 
// namespace Blog;
class Movie extends BaseModel
{
	
	
	public function initialize()
	{		
		$this->belongsTo("category_id", "ProductCategory", "id");
	}
	
	public function beforeValidationOnCreate()
	{
		$video_id = $this->video_id;	
		$this->onshelf = 1;
		$this->update_time = date('Y-m-d H:i:s');
		$this->views = 0;
		$this->cover = "https://i.ytimg.com/vi/$video_id/mqdefault.jpg";		
		$this->content ='<div class="embed-responsive embed-responsive-16by9"><iframe src="https://www.youtube.com/embed/'.$video_id.'?autoplay=1" frameborder="0" allowfullscreen></iframe></div>';
	}
	
	public function beforeValidationOnUpdate()
	{
		$video_id = $this->video_id;
		
		$this->update_time = date('Y-m-d H:i:s');
		$this->cover = "https://i.ytimg.com/vi/$video_id/mqdefault.jpg";
		$this->content ='<div class="embed-responsive embed-responsive-16by9"><iframe src="https://www.youtube.com/embed/'.$video_id.'?autoplay=1" frameborder="0" allowfullscreen></iframe></div>';
		
	}
}

?>