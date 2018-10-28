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
class Costumers extends REST_Controller
{
    private $check;
    
	function __construct()
	{
	    parent::__construct();
	    error_reporting(-1);
	    ini_set('display_errors', 'On');
	    
	    $this->load->model('costumers/Costumers_model','costumers_model');
	    $this->load->model('companies/Companies_model','companies_model');
	    $this->load->model('companies/Companies_users_model','companies_users_model');
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
	    
	    $search = array(
	        'company_id'=>$this->input->post('company_id'),
	    );
	    
	    $editor = $this->u_model->byid($user_id);
	    
        $totalcount = $this->costumers_model->setUser($editor)->setCountOnly()->get($search);
        $data = $this->costumers_model->setUser($editor)->setCountOnly(false)->setLimit($limit,$offset)->get($search);
	    
        $companies = $this->companies_model->setUser($editor)->get();
	    
	    $arr = array();
	    $count = $offset;
	    foreach ($data as $d){
	        $count++;
	        $_d = (array)$d;
	        $_d['index'] = $count;
	        $_d['company_name'] = (isset($companies[$d->company_id]) ? $companies[$d->company_id]->name : 'N/A');
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
	    
	    $editor = $this->u_model->byid($user_id);
	    
	    $obj = new stdClass();
	    $obj->status = 1;
	    
	    $this->load->model('companies/Companies_model','companies_model');
	    
	    $data = new stdClass();
	    $data->user_id = $user_id;
	    $data->u_token = $u_token;
	    $data->mode = 'Add';
	    $data->costumers = array();
	    $data->salutations = unserialize(SITE_SALUTATIONS);
	    $data->companies = $this->companies_model->setUser($editor)->get();
	    
	    $html = $this->load->view('admin/costumers/common/costumer_modal',$data,true);
	    
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
	    
	    $obj = new stdClass();
	    $obj->status = 0;
	    $obj->msg = "Something went wrong";
	    
	    $data = $this->costumers_model->post($this->input->post());
	    
	    if($data){
	        if(!empty($id)){
	            //update
	            $data->updated_by = $user_id;
	            $res = $this->costumers_model->update($id, $data);
	            if($res){
	                $obj->status = 1;
	                $obj->msg = "Successfully update costumers [".$data->firstname."]";
	            }
	        }else{
	            //create
	            $data->created_by = $user_id;
	            $res = $this->costumers_model->insert($data);
	            if($res){
	                $obj->status = 1;
	                $obj->msg = "Successfully saved costumers [".$data->firstname."]";
	            }
	        }
	    }else{
	        $errors = $this->costumers_model->getErrors();
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
	    
	    $editor = $this->u_model->byid($user_id);
	    
	    $obj = new stdClass();
	    $obj->status = 1;
	    
	    $data = new stdClass();
	    
	    if(!empty($id)){
	        $data->mode = 'Update';
	        $details = $this->costumers_model->setUser($editor)->byid($id);
	        if(empty($details)){
	            $obj->status = 0;
	            $obj->msg = "Costumer not found";
	            return $this->response(json_encode($obj),200);
	        }else{
	            $data->data = $details;
	            $data->id = $id;
	        }
	    }else{
	        $data->mode = 'Add';
	    }
	    $data->u_token = encrypt($user_id);
	    $data->costumers = array();
	    $data->salutations = unserialize(SITE_SALUTATIONS);
	    $data->companies = $this->companies_model->setUser($editor)->get();
	    
	    $html = $this->load->view('admin/costumers/common/costumer_modal',$data,true);
	    
	    $obj->view = $html;
	    $this->response(json_encode($obj),200);
	    
	}
	
	function delete_post(){
	    if(!$this->check->status){
	        return $this->response(json_encode($this->check),400);
	    }
	    
	    $user_id = $this->input->post('u_token');
	    $user_id = decrypt($user_id);
	    $id = $this->input->post('id');
	    
	    $obj = new stdClass();
	    $obj->status = 0;
	    $obj->msg = "Something went wrong";
	   
	    if(!empty($id)){
	        $res = $this->costumers_model->delete($id);
	        if($res){
	            $obj->status = 1;
	            $obj->msg = "Successfully deleted company";
	            return $this->response(json_encode($obj),200);
	        }else{
	            $obj->msg = "Failed deleting company";
	        }
	    }else{
	        $obj->msg = "Please specify company to delete";
	    }
	    
	    $this->response(json_encode($obj),200);
	}
}