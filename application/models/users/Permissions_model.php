<?php

class Permissions_model extends CI_Model
{
	public $table = 'users_groups_permissions';
	
	public $list = array();
	private $bygroup = array();
	
	function __construct() {
		parent::__construct();
	}
	
	public function post($permissions){
	    $perm = array();
	    foreach ($permissions as $resource_key => $resources){
	        foreach ($resources as $perm_key => $resource){
	            $perm[$resource_key][$perm_key] = json_encode($resource);
	        }
	    }
	    return $perm;
	}
	
	public function get($search = array(), $single = false){
	    $this->list = array();
	    $this->bygroup = array();
	    	    
        $this->db->select("*");
        
        $this->db->from($this->table);
		
		if(isset($search['group_id']) && !empty($search['group_id'])){
		    $this->db->where('group_id',$search['group_id']);
		}
		
		if(isset($search['id']) && !empty($search['id'])){
		    $this->db->where('id',$search['id']);
		}
		
		$query = $this->db->get();
		if($query->num_rows() > 0){
		    if(!$single){
		        $res = $query->result();
		        foreach($res as $r){
		            $r->permissions = json_decode($r->permissions);
		            $this->list[$r->id] = $r;
		            $this->bygroup[$r->group_id][$r->controller][$r->class] = $r->permissions;
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
	
	public function bygroup($id){
	    if(empty($this->bygroup)){
	        $this->get();
	    }
	    if(isset($this->bygroup[$id])){
	        return $this->bygroup[$id];
	    }
	    return null;
	}
	
	public function update($id,$data){
		$this->db->where('id',$id);		
		$this->db->update($this->table,$data);
		return true;
	}
	
	public function insert($data){
	    $this->db->insert($this->table,$data);
	    return $this->db->insert_id();
	}
	
	public function delete($id, $field = 'id'){
	    $this->db->where($field, $id);
	    $this->db->delete($this->table);
	    return true;
	}
	
	public function insertGroupPermissions($id, $permissions){
        $res = false;
        $perm_data = array(
            'group_id'=>$id,
        );
        $permissions = $this->post($permissions);
        if($permissions){
            $this->delete($id, 'group_id');
            foreach ($permissions as $controller => $classes){
                $perm_data['controller'] = $controller;
                foreach ($classes as $class => $controls){
                    $perm_data['class'] = $class;
                    $perm_data['permissions'] = $controls;
                    $this->insert($perm_data);
                }
            }
        }
        return $res;
	}
	
	public function check($group_id, $controller, $action, $section = 'admin'){
	    $free_access = unserialize(FREE_PERMISSION);
	    
	    $permissions = $this->bygroup($group_id);
	    
	    if(!isset($permissions[$controller][$action]) && !in_array($controller, $free_access)){
	        if(!isset($permissions[$controller])){
	            $this->session->set_flashdata('error','Please login');
	            redirect('index');
	            return false;
	        }else{
	            reset($permissions[$controller]);
	            $first_key = key($permissions[$controller]);
	            redirect($section.'/'.$controller.'/'.$first_key);
	        }
	    }
	    return $permissions; 
	}
}