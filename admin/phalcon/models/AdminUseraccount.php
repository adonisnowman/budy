<?php 
class AdminUseraccount extends BaseModel
{
	
	public $purviewlist;
	public function initialize()
	{
		$this->belongsTo("usergroup", "AdminUsergroup", "id");
		
	}
	
	public function beforeSave()
    {
        if(is_array($this->purviewlist)) 
        	$this->purviewlist = JSON_encode( $this->relativePurview($this->AdminUsergroup->purviewlist,$this->purviewlist) );	
    }
	
	public function afterFetch()
	{
		$purview = json_decode($this->purviewlist);
		if(!is_array($purview)) $purview = array();
		$this->purviewlist = $this->relativePurview($this->AdminUsergroup->purviewlist,$purview);
	}
	
	
	
	function relativePurview($groupPurview,$purview){
		return $fullDiff = array_merge(array_diff($groupPurview, $purview), array_diff($purview, $groupPurview));
	
	}
}

?>