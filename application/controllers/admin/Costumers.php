<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Costumers extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('companies/Companies_model','companies_model');
    }
    
    public function index(){        
        add_components('datatable');
        add_components('select');
        add_components('datetimepicker');
        
        $this->data['companies'] = $this->companies_model->setUser($this->session->userdata())->get();
        
        $this->data['page_title'] = 'Costumers';
        $this->render('admin/costumers/index'); 
    }
    
    public function companies(){
        add_components('datatable');
        add_components('select');
        
        $this->data['page_title'] = 'Companies';
        $this->render('admin/costumers/companies');
    }
    
    public function plan($company_id){        
        $company = $this->companies_model->setUser($this->session->userdata())->byid($company_id);
        if(empty($company)){
            $this->session->set_flashdata('error','Company not found');
            redirect('admin/dashboard');
        }
        $this->data['overview'] = $this->load->view('admin/costumers/plan/overview',array('company'=>$company),true);
        $this->data['company'] = $company;
        $this->data['page_title'] = 'Account plan';
        $this->render('admin/costumers/plan');
    }
}