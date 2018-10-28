<?php

class Users_model extends CI_Model
{
	public $table_users = 'users';
	
	public $list = array();
	public $byemail = array();
	public $fields = array();
	private $errors = array();
	
	private $limit = null;
	private $offset = null;
	private $count_only = false;

	function __construct() {
		parent::__construct();
		
		if(empty($this->fields)){
		    $this->setFields();
		}
	}

	public function init(){
	    $sql = "INSERT INTO `users` (`id`, `status`, `username`, `password`, `group_id`, `firstname`, `lastname`, `phone`, `email`, `address`, `last_ip`, `last_login`, `session_id`, `gender`, `photo`, `start_date`, `created`, `updated`) VALUES (NULL, '1', 'admin', '2fee7701c36745253c29e8936df02f5a', '1', 'Mark', 'Bonza', NULL, 'markraymondbonza@gmail.com', 'Somewhere', NULL, '', NULL, NULL, NULL, NULL, '', '')";
	}
	
	public function post($post, $is_update = false){
	    $allowed = array(
	        'id','username','password','firstname','lastname','email','group_id','phone','gender','address','is_system_admin','status'
	    );
	    $required = array(
	        'username','password','firstname','email','group_id'
	    );
	    $default_fields = array(
	        'status'=>0,
	        'is_system_admin'=>0,
	        'gender'=>0
	    );
	    
	    if($is_update){
	        if (($key = array_search('password', $required)) !== false) {
	            unset($required[$key]);
	        }
	    }
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
	        if ($key == 'status' || $key == 'is_system_admin' )
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
	
	public function setFields($fields = array('id','username','firstname','lastname','photo','gender','email','is_system_admin','group_id')){
	    if(!empty($fields)){
	        $this->fields = implode(",", $fields);
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
	    $this->byemail = array();
	    	    
        $this->db->select($this->fields);
        
		$this->db->from($this->table_users);
		
		if(isset($search['username']) && !empty($search['username'])){
		    $this->db->where('username',$search['username']);
		}
		if(isset($search['firstname']) && !empty($search['firstname'])){
		    $this->db->like('firstname',$search['firstname']);
		}
		if(isset($search['lastname']) && !empty($search['lastname'])){
		    $this->db->like('lastname',$search['lastname']);
		}
		if(isset($search['password']) && !empty($search['password'])){
		    $this->db->where('password',$search['password']);
		}
		if(isset($search['status']) && !empty($search['status'])){
		    $this->db->where('status',$search['status']);
		}
		if(array_key_exists('search_is_system_admin', $search)){
		    if(empty($search['search_is_system_admin'])){
		        $this->db->where('is_system_admin',0);
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
		            $r->fullname = $r->firstname.' '.$r->lastname;
		            $this->list[$r->id] = $r;
		            $this->byemail[$r->email] = $r;
		        }
		    }else{
		        return $query->row();
		    }
			
		}
		return $this->list;
	}
	
	public function byid($id){
	    $this->get();
		/*if(empty($this->list)){
			$this->get();
		}*/
		if(isset($this->list[$id])){
			return (object)$this->list[$id];
		}
		return null;
	}
	
	public function byemail($email){
	    $this->get();
	    /*if(empty($this->list)){
	        $this->get();
	    }*/
	    if(isset($this->byemail[$email])){
	        return (object)$this->byemail[$email];
	    }
	    return null;
	}
	
	public function is($val, $field, $except_id = null){
	    $this->db->select('*');
	    $this->db->from($this->table_users);
	    $this->db->where($field,$val);
	    if(!empty($except_id)){
	        $this->db->where('id !='.$except_id);
	    }
	    $query = $this->db->get();
	    return ($query->num_rows() > 0);
	}
	
	public function insert($data){
	    $obj = new stdClass();
	    $obj->status = 0;
	    if($this->is($data->username, 'username')){
	        $obj->msg = "Username already exist!";
	        return $obj;
	    }
	    if($this->is($data->email, 'email')){
	        $obj->msg = "Email already exist!";
	        return $obj;
	    }
	    if(isset($data->password)){
	        $data->password = md5($data->password);
	    }
	    if(!isset($data->created)){
	        $data->created = time();
	    }
	    if(!isset($data->updated)){
	        $data->updated = time();
	    }
	    $this->db->insert($this->table_users,$data);
	    $obj->status = 1;
	    $obj->id = $this->db->insert_id();
	    return $obj;
	}
	
	public function update($id, $data, $check_duplicate = false){
	    $obj = new stdClass();
	    $obj->status = 0;
	    if(!$check_duplicate){
	        if($this->is($data->username, 'username', $id)){
	            $obj->msg = "Username already exist!";
	            return $obj;
	        }
	        if($this->is($data->email, 'email', $id)){
	            $obj->msg = "Email already exist!";
	            return $obj;
	        }
	    }
	    if(isset($data->password)){
	        $data->password = md5($data->password);
	    }
	    if(!isset($data->updated)){
	        $data->updated = time();
	    }
	    
		$this->db->where('id',$id);		
		$this->db->update($this->table_users,$data);
		
		$obj->status = 1;
		$obj->id = $id;
		return $obj;
	}
	
	public function login($username, $password){
	    $password = md5($password);
	    
	    $user = $this->setFields(array('*'))->get(array('username'=>$username, 'password'=>$password, 'status'=>1), true);
	    if(!empty($user)){
	        $sess_array = array(
	            'id'		=> $user->id,
	            'username'=> $user->username,
	            'firstname'=> $user->firstname,
	            'lastname'=> $user->lastname,
	            'group_id'=> $user->group_id,
	            'photo'=>$user->photo,
	            'gender'=>$user->gender,
	            'email'=> $user->email,
	            'session_id'=> session_id(),
	            'is_system_admin'=> $user->is_system_admin,
	            'last_activity_update'	=> strtotime(date('Y-m-d H:i:s')),
	        );
	        $this->session->set_userdata($sess_array);
	        
	        $this->update($user->id, array('session_id'=>$sess_array['session_id']));       
	        return $user;
	    }
	    return false;
	}
	
	public function logout(){
	    $id = $this->session->userdata('id');
	    if(!empty($id)){
	        $this->db->where('id', $id);
	        $this->db->update('users', array( 'session_id' => '' ));
	        
	        $this->session->sess_destroy();
	        
	        return $this->db->affected_rows();
	    }else{
	        return false;
	    }
	}

	public function getErrors(){
	    return $this->errors;
	}
}
