<?php  defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';
/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          sathish
 * @license         Openwave
 */

use Restserver\Libraries\REST_Controller;
class Users extends REST_Controller
{
    private $check;
    
	function __construct()
	{
	    parent::__construct();
	    error_reporting(-1);
	    ini_set('display_errors', 'On');
	    
	    $this->load->model('Api','api');
	    
	    $this->check = new stdClass();
	    
	    $u_token = $this->input->post('u_token');
	    if(!empty($u_token)){
	        $allowed = $this->api->check($u_token);
	        if($allowed){
	            $this->check->status = 1;
	        }else{
	            $this->check->status = 0;
	            $this->check->msg = "Invalid Token";
	        }
	    }else{
	        $this->check->status = 0;
	        $this->check->msg = "Invalid Access";
	    }
	}
	
	function get_post(){
	    if(!$this->check->status){
	        return $this->response(json_encode($this->check),400);
	    }
	    $user_id = $this->input->post('u_token');
	    $user_id = decrypt($user_id);
	    $limit = $this->input->post('length');
	    $offset = $this->input->post('start');
	    
	    $editor = $this->u_model->byid($user_id);
	    
	    $search = array(
	        'username'=>$this->input->post('username'),
	        'firstname'=>$this->input->post('firstname'),
	        'lastname'=>$this->input->post('lastname'),
	        'search_is_system_admin'=>$editor->is_system_admin,
	    );
	    $totalcount = $this->u_model->setCountOnly()->get($search);
	    $data = $this->u_model->setCountOnly(false)->setLimit($limit,$offset)->get($search);
	    
	    $this->load->model('users/Groups_model','g_model');
	    $groups = $this->g_model->get();
	    
	    $arr = array();
	    $count = $offset;
	    foreach ($data as $d){
	        $count++;
	        $_d = (array)$d;
	        $_d['index'] = $count;
	        $_d['group_name'] = (isset($groups[$d->group_id]) ? $groups[$d->group_id]->name : 'N/A');
	        $arr[] = $_d;
	    }
	    echo json_encode(
	        array(
	            'draw' => $this->input->post('draw'),
	            'recordsTotal' => $count - $offset,
	            'recordsFiltered' => $totalcount,
	            'data'=>$arr
	        )
        );
	}
	
	function add_modal_post(){
	    if(!$this->check->status){
	        return $this->response(json_encode($this->check),400);
	    }
	    $u_token = $this->input->post('u_token');
	    $user_id = decrypt($u_token);
	    
	    $obj = new stdClass();
	    $obj->status = 1;
	    
	    $editor = $this->u_model->byid($user_id);
	    
	    $this->load->model('users/Groups_model','g_model');
	    $groups = $this->g_model->bystatus(1);
	    
	    $data = new stdClass();
	    $data->user_id = $user_id;
	    $data->u_token = $u_token;
	    $data->mode = 'Add';
	    $data->groups = $groups;
	    $data->is_system_admin = $editor->is_system_admin;
	    
	    $html = $this->load->view('admin/users/common/user_modal',$data,true);
	    
	    $obj->view = $html;
	    
	    $this->response(json_encode($obj),200);
	}
	
	function save_post(){
	    if(!$this->check->status){
	        return $this->response(json_encode($this->check),400);
	    }
	    $user_id = $this->input->post('u_token');
	    $user_id = decrypt($user_id);
	    $id = $this->input->post('id');
	    $is_update = !empty($id);
	    
	    $obj = new stdClass();
	    $obj->status = 0;
	    $obj->msg = "Something went wrong";
	    
	    $data = $this->u_model->post($this->input->post(), $is_update);
	    
	    if($data){
	        if(!empty($id)){
	            //update
	            if(empty($data->password)){
	                unset($data->password);
	            }
	            $data->updated_by = $user_id;
	            $res = $this->u_model->update($id, $data, true);
	            if($res->status){
	                $obj->status = 1;
	                $obj->msg = "Successfully update user [".$data->username."]";
	            }else{
	                $obj = $res;
	            }
	        }else{
	            //create
	            $data->created_by = $user_id;
	            $res = $this->u_model->insert($data);
	            if($res->status){
	                $obj->status = 1;
	                $obj->msg = "Successfully saved user [".$data->username."]";
	            }else{
	                $obj = $res;
	            }
	        }
	    }else{
	        $errors = $this->u_model->getErrors();
	        $obj->msg = implode(PHP_EOL, $errors);
	    }
	    
	    $this->response(json_encode($obj),200);
	}
	function view_modal_post(){
	    if(!$this->check->status){
	        return $this->response(json_encode($this->check),400);
	    }
	    
	    $user_id = $this->input->post('u_token');
	    $user_id = decrypt($user_id);
	    $id = $this->input->post('id');
	    
	    $obj = new stdClass();
	    $obj->status = 1;
	    
	    $editor = $this->u_model->byid($user_id);
	    
	    $this->load->model('users/Groups_model','g_model');
	    $groups = $this->g_model->bystatus(1);
	    
	    $data = new stdClass();
	    
	    if(!empty($id)){
	        $data->mode = 'Update';
	        $details = $this->u_model->setFields(array('*'))->byid($id);
	        if(empty($details)){
	            $obj->status = 0;
	            $obj->msg = "User not found";
	            return $this->response(json_encode($obj),200);
	        }else{
	            $data->data = $details;
	            $data->id = $id;
	        }
	    }else{
	        $data->mode = 'Add';
	    }
	    $data->u_token = encrypt($user_id);
	    $data->groups = $groups;
	    $data->is_system_admin = $editor->is_system_admin;
	    
	    $html = $this->load->view('admin/users/common/user_modal',$data,true);
	    
	    $obj->view = $html;
	    $this->response(json_encode($obj),200);
	    
	}
	function add_groups_modal_post(){
	    if(!$this->check->status){
	        return $this->response(json_encode($this->check),400);
	    }
        $user_id = $this->input->post('u_token');
        $user_id = decrypt($user_id);
                
        $obj = new stdClass();
        $obj->status = 1;
        
        $data = new stdClass();
        $data->user_id = $user_id;
        $data->mode = 'Add';
        $data->u_token = encrypt($user_id);
        $data->site_permission_titles = unserialize(SITE_PERMISSIONS_TITLES);
        
        $html = $this->load->view('admin/users/common/group_modal',$data,true);
        
        $obj->view = $html;
        
        $this->response(json_encode($obj),200);
	}
	
	function get_groups_post(){
	    if(!$this->check->status){
	        return $this->response(json_encode($this->check),400);
	    }
	    $user_id = $this->input->post('u_token');
	    $user_id = decrypt($user_id);
	    $limit = $this->input->post('length');
	    $offset = $this->input->post('start');
	    
	    $this->load->model('users/Groups_model','g_model');
	    $totalcount = $this->g_model->setCountOnly()->get();
	    $data = $this->g_model->setCountOnly(false)->setLimit($limit,$offset)->get();
	    
	    $arr = array();
	    $count = $offset;
	    foreach ($data as $d){
	        $count++;
	        $_d = (array)$d;
	        $_d['index'] = $count;
	        $arr[] = $_d;
	    }
	    echo json_encode(
	        array(
	            'draw' => $this->input->post('draw'),
	            'recordsTotal' => $count - $offset,
	            'recordsFiltered' => $totalcount,
	            'data'=>$arr
	        )
        );
	}
	
	function save_group_post(){
	    if(!$this->check->status){
	        return $this->response(json_encode($this->check),400);
	    }
	    $user_id = $this->input->post('u_token');
	    $user_id = decrypt($user_id);
	    $id = $this->input->post('id');
	    $permissions = $this->input->post('permissions');
	    
	    $obj = new stdClass();
	    $obj->status = 0;
	    $obj->msg = "Something went wrong";
	    
	    $this->load->model('users/Groups_model','g_model');
	    $data = $this->g_model->post($this->input->post());
	    
	    if($data){	        
	        
	        if(!empty($id)){
	            //update
	            $this->g_model->update($id, $data);
	            $obj->status = 1;
	            $obj->msg = "Successfully update group [".$data->name."]";
	        }else{
	            //create
	            $id = $this->g_model->insert($data);
	            $obj->status = 1;
	            $obj->msg = "Successfully saved group [".$data->name."]";
	        }
	        $this->permissions_model->insertGroupPermissions($id, $permissions);
	    }else{
	        $errors = $this->g_model->getErrors();
	        $obj->msg = implode(PHP_EOL, $errors);
	    }
	    
	    $this->response(json_encode($obj),200);
	}
	
	function view_group_modal_post(){
	    if(!$this->check->status){
	        return $this->response(json_encode($this->check),400);
	    }
	    
	    $user_id = $this->input->post('u_token');
	    $user_id = decrypt($user_id);
	    $id = $this->input->post('id');
	    
	    $obj = new stdClass();
	    $obj->status = 1;
	    
	    $data = new stdClass();
	    
	    if(!empty($id)){
	        $data->mode = 'Update';
	        $this->load->model('users/Groups_model','g_model');
	        
	        $details = $this->g_model->byid($id);
	        
	        if(empty($details)){
	            $obj->status = 0;
	            $obj->msg = "Group not found";
	            return $this->response(json_encode($obj),200);
	        }else{
	            $permissions = $this->permissions_model->bygroup($id);
	            
	            $data->data = $details;
	            $data->permissions = $permissions;
	            $data->id = $id;
	        }
	    }else{
	        $data->mode = 'Add';
	    }
	    
	    $data->site_permission_titles = unserialize(SITE_PERMISSIONS_TITLES);
	    $data->u_token = encrypt($user_id);
	    
	    $html = $this->load->view('admin/users/common/group_modal',$data,true);
	    
	    $obj->view = $html;
	    $this->response(json_encode($obj),200);
	    
	}
}