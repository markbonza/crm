<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Index extends Guest_Controller
{    
    function __construct()
    {
        parent::__construct();
    }
    
    public function index(){
        $this->data['page_title'] = 'Sign In';
        
        $login_id = $this->session->userdata('id');
        if($login_id){
            $this->session->set_flashdata('success','Welcome '.$this->session->userdata('username'));
            redirect('admin/dashboard');
        }
        if($this->input->server('REQUEST_METHOD') == 'POST'){
            $user = $this->input->post('username');
            $pw = $this->input->post('password');
            
            $login = $this->u_model->login($user,$pw);
            if($login){
                redirect('admin/dashboard');
            }else{
                $this->session->set_flashdata('error','Invalid login');
            }
        }
        $this->render('login','login');
    }
    
    public function logout(){
        $this->u_model->logout();
        redirect('index');
    }
}