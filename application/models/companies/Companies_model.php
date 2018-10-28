<?php

class Companies_model extends CI_Model
{
    public $table_companies = 'companies';
	
	public $list = array();
	private $errors = array();
	private $limit = null;
	private $offset = null;
	private $count_only = false;
	
	private $allowed_companies = null;
	
	function __construct() {
		parent::__construct();
	}
	
	public function post($post){
	    $allowed = array(
	        'id','name','description','address','headquarters'
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
	
	public function setUser($user){
	    $this->load->model('companies/Companies_users_model','companies_users_model');
	    if(!is_object($user)){
	        $user = (object)$user;
	    }
	    if(!$user->is_system_admin){
	        $allowed_companies = $this->companies_users_model->byuser_id($user->id);
	        if($allowed_companies){
	            $this->allowed_companies = array_keys($allowed_companies);
	        }
	    }else{
	        $this->allowed_companies = true;
	    }
	    
	    return $this;
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
	    
	    if(empty($this->allowed_companies)){
	        return array();
	    }
	    
        $this->db->select("*");
        
        $this->db->from($this->table_companies);
		
		if(isset($search['name']) && !empty($search['name'])){
		    $this->db->like('name',$search['name']);
		}
		if(isset($search['id']) && !empty($search['id'])){
		    $ids = $search['id'];
		    if(!is_array($ids)){
		        $ids = array($ids);
		    }
		    
		    $this->db->where("id in ('".implode("','", $ids)."')");
		}else{
		    if($this->allowed_companies !== true){
		        $this->db->where("id in ('".implode("','", $this->allowed_companies)."')");
		    }
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
	
	public function update($id,$data){
	    if(!isset($data->updated)){
	        $data->updated = time();
	    }
		$this->db->where('id',$id);		
		$this->db->update($this->table_companies,$data);
		return true;
	}
	
	public function insert($data){
	    if(!isset($data->created)){
	        $data->created = time();
	    }
	    $this->db->insert($this->table_companies,$data);
	    return $this->db->insert_id();
	}
	
	public function is($val, $field, $except_id = null){
	    $this->db->select('*');
	    $this->db->from($this->table_companies);
	    $this->db->where($field,$val);
	    if(!empty($except_id)){
	        $this->db->where('id !='.$except_id);
	    }
	    $query = $this->db->get();
	    return ($query->num_rows() > 0);
	}
	
	public function delete($id){
	    $this->db->where('id',$id);
	    $this->db->delete($this->table_companies);
	    return true;
	}
	
	public function getErrors(){
	    return $this->errors;
	}
}