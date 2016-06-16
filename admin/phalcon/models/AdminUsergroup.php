<?php 
class AdminUsergroup extends BaseModel
{
	
	
	public function initialize()
	{
		$this->hasMany("id", "AdminUseraccount", "usergroup");
	}
	
	public function afterFetch()
	{
		$this->purviewlist = json_decode($this->purviewlist);
	}
	
	public function beforeValidationOnCreate()
	{
		if(is_array($this->purviewlist)) $this->purviewlist = JSON_encode($this->purviewlist);	
	}
	
	public function beforeValidationOnUpdate()
	{		
		
		if(is_array($this->purviewlist)) $this->purviewlist = JSON_encode($this->purviewlist);
	}
	
	
}

?>