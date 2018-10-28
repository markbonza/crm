<?php

class Groups_model extends CI_Model
{
	public $table_groups = 'users_groups';
	
	public $list = array();
	public $bystatus = array();
	private $errors = array();
	private $data = array();
	private $limit = null;
	private $offset = null;
	private $count_only = false;
	
	function __construct() {
		parent::__construct();
	}
	
	public function post($post){
	    $allowed = array(
	        'id','name','status'
	    );
	    $required = array(
	        'name'
	    );
	    $default_fields = array(
	        'status'=>0
	    );
	    foreach ($default_fields as $key => $val){
	        if(!array_key_exists($key, $post)){
	            $post[$key] = $val;
	        }
	    }
	    $cleanData = new stdClass();
	    foreach($post as $key => $val)
	    {
	        if (! in_array($key, $allowed))
	        {
	            continue;
	        }
	        if ($key == 'status')
	        {
	            $val = !empty($val);
	        }
	        $cleanData->{$key} = mysqli_real_escape_string($this->db->conn_id,$val);
	    }
	    foreach($required as $key)
	    {
	        if (empty($cleanData->{$key}))
	        {
	            $this->errors[] = 'Field ' . ucfirst($key) . ' is required!';
	        }
	    }
	    if (! empty($this->errors))
	    {
	        return false;
	    }
	    return $cleanData;
	}
	
	public function setLimit($limit, $offset = 0){
	    $this->limit = $limit;
	    $this->offset = $offset;
	    return $this;
	}
	
	public function setCountOnly($bool = true){
	    $this->count_only = $bool;
	    return $this;
	}
	
	public function get($search = array(), $single = false){
	    $this->list = array();
	    $this->bystatus = array();
        $this->db->select("*");
        
        $this->db->from($this->table_groups);
		
		if(isset($search['name']) && !empty($search['name'])){
		    $this->db->where('name',$search['name']);
		}
		
		if(isset($search['id']) && !empty($search['id'])){
		    $this->db->where('id',$search['id']);
		}
		if($this->limit){
		    $this->db->limit($this->limit,$this->offset);
		}
		$query = $this->db->get();
		
		if($this->count_only){
		    return $query->num_rows();
		}
		if($query->num_rows() > 0){
		    if(!$single){
		        $resource = $query->result_id;
		        while($r = @mysqli_fetch_object($resource)) {
		            $this->list[$r->id] = $r;
		            $this->bystatus[$r->status][$r->id] = $r;
		        }
		    }else{
		        return $query->row();
		    }
			
		}
		return $this->list;
	}
		
	public function byid($id){
		if(empty($this->list)){
			$this->get();
		}
		if(isset($this->list[$id])){
			return (object)$this->list[$id];
		}
		return null;
	}
	
	public function bystatus($status){
	    if(empty($this->bystatus)){
	        $this->get();
	    }
	    if(isset($this->bystatus[$status])){
	        return $this->bystatus[$status];
	    }
	    return null;
	}
	
	public function update($id,$data){
		$this->db->where('id',$id);		
		$this->db->update($this->table_groups,$data);
		return true;
	}
	
	public function insert($data){
	    $this->db->insert($this->table_groups,$data);
	    return $this->db->insert_id();
	}
	
	public function is($val, $field, $except_id = null){
	    $this->db->select('*');
	    $this->db->from($this->table_groups);
	    $this->db->where($field,$val);
	    if(!empty($except_id)){
	        $this->db->where('id !='.$except_id);
	    }
	    $query = $this->db->get();
	    return ($query->num_rows() > 0);
	}
	
	public function getErrors(){
	    return $this->errors;
	}
}