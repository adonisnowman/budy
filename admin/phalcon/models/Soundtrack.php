<?php 
// namespace Blog;
class Soundtrack extends BaseModel
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
		if(!$this->album) $this->album = $this->ProductCategory->name;
		$this->cover = "https://i.ytimg.com/vi/$video_id/mqdefault.jpg";	
		
		
		$video_id = "$video_id?start=".$this->start_time."&end=".$this->end_time;
			
		$this->content ='<div class="embed-responsive embed-responsive-16by9"><iframe src="https://www.youtube.com/embed/'.$video_id.'" frameborder="0" allowfullscreen></iframe></div>';
	}
	
	public function beforeValidationOnUpdate()
	{
		$video_id = $this->video_id;
		
		$this->cover = "https://i.ytimg.com/vi/$video_id/mqdefault.jpg";
		$this->update_time = date('Y-m-d H:i:s');
		
		
		$video_id = "$video_id?start=".$this->start_time."&end=".$this->end_time;	
		$this->content ='<div class="embed-responsive embed-responsive-16by9"><iframe src="https://www.youtube.com/embed/'.$video_id.'" frameborder="0" allowfullscreen></iframe></div>';
		
		
	}
}

?>