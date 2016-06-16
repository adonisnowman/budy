<?php 
class AdminPurviewlist extends BaseModel
{
	public $account_index;
	
	public function initialize()
	{
		$this->belongsTo("controller_id", "AdminController", "id");
		$this->belongsTo("purview_id", "AdminPurview", "id");
		$this->belongsTo("create_account_index", "AdminAccount", "auto_index");
		$this->belongsTo("update_account_index", "AdminAccount", "auto_index");
		
	}
	
	public function setAccountIndex($index){
		$this->account_index = $index;	
	}
	
	public function beforeValidationOnCreate()
	{
		$this->onshelf = 0;
		$this->competence_id = 0;
		$this->action_type = 0;
		$this->create_time = date('Y-m-d H:i:s');
		$this->update_time = date('Y-m-d H:i:s');
		if($this->account_index){
			$this->create_account_index = $this->account_index;
			$this->update_account_index = $this->account_index;
		}
	
	}
	
	public function beforeValidationOnUpdate()
	{
		
		$this->update_time = date('Y-m-d H:i:s');
		if($this->account_index){
			$this->update_account_index = $this->account_index;
		}
	}
	
	public function get_AdminController_field($field = false)
	{
		if($field) $field = $this->AdminController->{$field};
		return $field;
	
	}
	
	public function get_AdminPurview_field($field = false)
	{
		if($field) $field = $this->AdminPurview->{$field};
		return $field;
	
	}
	
	public function get_AdminAccount_field($field = false)
	{
		if($field) $field = $this->AdminAccount->{$field};
		return $field;
	
	}
}

?>