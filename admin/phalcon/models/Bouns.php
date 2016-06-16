<?php 
// namespace Blog;
class Bouns extends BaseModel
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
		$this->category_id = 34;
		$this->album = '本月特輯';
		$this->cover = "https://i.ytimg.com/vi/$video_id/mqdefault.jpg";	
		
		
		$video_id = "$video_id?start=".$this->start_time."&end=".$this->end_time;
			
		$this->content ='<div class="embed-responsive embed-responsive-16by9"><iframe src="https://www.youtube.com/embed/'.$video_id.'&autoplay=1" frameborder="0" allowfullscreen></iframe></div>';
	}
	
	public function beforeValidationOnUpdate()
	{
		$video_id = $this->video_id;
		
		$this->cover = "https://i.ytimg.com/vi/$video_id/mqdefault.jpg";
		$this->update_time = date('Y-m-d H:i:s');
		
		
		$video_id = "$video_id?start=".$this->start_time."&end=".$this->end_time;	
		$this->content ='<div class="embed-responsive embed-responsive-16by9"><iframe src="https://www.youtube.com/embed/'.$video_id.'&autoplay=1" frameborder="0" allowfullscreen></iframe></div>';
		
		
	}
}

?>