<?php

class Companies_users_model extends CI_Model
{
    public $table_companies_users = 'companies_users';
	
	public $bycompany_id = array();
	private $errors = array();
	public $byuser_id = array();
	
	function __construct() {
		parent::__construct();
	}
	
	public function get($search = array()){
	    $this->bycompany_id = array();
	    $this->byuser_id = array();
        $this->db->select("*");
        
        $this->db->from($this->table_companies_users);
		
		if(isset($search['user_id']) && !empty($search['user_id'])){
		    $this->db->where('user_id',$search['user_id']);
		}
		
		if(isset($search['company_id']) && !empty($search['company_id'])){
		    $this->db->where('company_id',$search['company_id']);
		}
		
		$query = $this->db->get();
		
		if($query->num_rows() > 0){
	        $resource = $query->result_id;
	        while($r = @mysqli_fetch_object($resource)) {
	            $this->bycompany_id[$r->company_id][$r->user_id] = $r;
	            $this->byuser_id[$r->user_id][$r->company_id] = $r;
	        }
		}
		return $this->bycompany_id;
	}
		
	public function bycompany_id($id){
		if(empty($this->bycompany_id)){
			$this->get();
		}
		if(isset($this->bycompany_id[$id])){
			return $this->bycompany_id[$id];
		}
		return array();
	}
	
	public function byuser_id($id){
	    if(empty($this->byuser_id)){
	        $this->get();
	    }
	    if(isset($this->byuser_id[$id])){
	        return $this->byuser_id[$id];
	    }
	    return array();
	}
	
	public function update($id,$data){
		$this->db->where('id',$id);		
		$this->db->update($this->table_companies_users,$data);
		return true;
	}
	
	public function insert($data){
	    $this->db->insert($this->table_companies_users,$data);
	    return $this->db->insert_id();
	}
		
	public function delete($id, $field = 'id'){
	    $this->db->where($field,$id);
	    $this->db->delete($this->table_companies_users);
	    return true;
	}
	
	public function getErrors(){
	    return $this->errors;
	}
}