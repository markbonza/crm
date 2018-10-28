<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Users extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function index(){        
        add_components('datatable');
        add_components('select');
        
        $this->data['page_title'] = 'Users';
        $this->render('admin/users/index'); 
    }
    
    public function groups(){
        
        add_components('datatable');
        
        $this->data['page_title'] = 'User Groups';
        $this->render('admin/users/groups');
    }
}