<?php defined('BASEPATH') OR exit('No direct script access allowed');
class System extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function index(){
        $this->render('admin/system'); 
    }
    
}