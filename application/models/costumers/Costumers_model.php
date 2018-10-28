<?php

class Costumers_model extends CI_Model
{
    public $table_costumers = 'costumers';
	
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
	        'id','company_id','prefix','firstname','lastname','phone','email','salutation','birthdate','head_id'
	    );
	    $required = array(
	        'company_id','firstname','phone','email','prefix'
	    );
	    $default_fields = array(
	        'status'=>0
	    );
	    $allow_0 = array(
	        'prefix'
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
	        if (empty($cleanData->{$key}) && !in_array($key, $allow_0))
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
        
        $this->db->from($this->table_costumers);
		
		if(isset($search['company_id']) && !empty($search['company_id'])){
		    $company_ids = $search['company_id'];
		    if(!is_array($company_ids)){
		        $company_ids = array($company_ids);
		    }
		    
		    $this->db->where("company_id in ('".implode("','", $company_ids)."')");
		}else{
		    if($this->allowed_companies !== true){
		        $this->db->where("company_id in ('".implode("','", $this->allowed_companies)."')");
		    }
		}
		if(isset($search['firstname']) && !empty($search['firstname'])){
		    $this->db->like('firstname',$search['firstname']);
		}
		if(isset($search['lastname']) && !empty($search['lastname'])){
		    $this->db->like('lastname',$search['lastname']);
		}
		if(isset($search['id']) && !empty($search['id'])){
		    $this->db->where('id',$search['id']);
		}
		if($this->limit){
		    $this->db->limit($this->limit,$this->offset);
		}
		$query = $this->db->get();
		
		//echo $this->db->last_query();die;
		if($this->count_only){
		    return $query->num_rows();
		}
		if($query->num_rows() > 0){
		    if(!$single){
		        $salutations = unserialize(SITE_SALUTATIONS);
		        $resource = $query->result_id;
		        while($r = @mysqli_fetch_object($resource)) {
		            $salutation = isset($salutations[$r->prefix]) ? $salutations[$r->prefix].' ' : '';
		            $r->fullname = $salutation.$r->firstname.' '.$r->lastname;
		            $r->birthday = ($r->birthdate) ? date('m/d/Y',$r->birthdate) : '';
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
	    if(isset($data->birthdate) && !empty($data->birthdate)){
	        $data->birthdate = strtotime($data->birthdate);
	    }
	    if(!isset($data->updated)){
	        $data->updated = time();
	    }
		$this->db->where('id',$id);		
		$this->db->update($this->table_costumers,$data);
		return true;
	}
	
	public function insert($data){
	    if(isset($data->birthdate) && !empty($data->birthdate)){
	        $data->birthdate = strtotime($data->birthdate);
	    }
	    if(!isset($data->created)){
	        $data->created = time();
	    }
	    $this->db->insert($this->table_costumers,$data);
	    return $this->db->insert_id();
	}
		
	public function delete($id){
	    $this->db->where('id',$id);
	    $this->db->delete($this->table_costumers);
	    return true;
	}
	
	public function is($val, $field, $except_id = null){
	    $this->db->select('*');
	    $this->db->from($this->table_costumers);
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