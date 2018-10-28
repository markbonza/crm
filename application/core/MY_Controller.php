<?php defined('BASEPATH') OR exit('No direct script access allowed');
class MY_Controller extends CI_Controller
{
  protected $data = array();
  function __construct()
  {
    parent::__construct();
    $this->data['page_title'] = 'CRM App';
  }
  protected function render($the_view = NULL, $template = 'main')
  {
    if($template == 'json' )
    {
      //header('Content-Type: application/json');
      echo json_encode($this->data);
    }else if($template == 'ajax' )
	{
			$this->data['the_view_content'] = (is_null($the_view)) ? '' : $this->load->view($the_view,$this->data, TRUE);
			$this->load->view('web/jobs/_ajaxsearch', $this->data);
	}
    else
    {
      $this->data['the_view_content'] = (is_null($the_view)) ? '' : $this->load->view($the_view,$this->data, TRUE);
      //$this->load->view('templates/'.$template.'_view', $this->data);
	  $this->load->view('layouts/web/'.$template, $this->data);
    }
  }
}

class Admin_Controller extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->data['user_session'] = $this->session->userdata();
        $this->getSession('id');
                
        $controller	= $this->router->fetch_class();
        $action		= $this->router->fetch_method();
        
        $this->data['controller_name'] = $controller;
        $this->data['method_name'] = $action;
        
        $permissions = $this->permissions_model->check($this->getSession('group_id'), $controller, $action);
        $this->data['access_permissions'] = $permissions;
        
        $site_permission_titles = unserialize(SITE_PERMISSIONS_TITLES);
        $this->data['site_permission_titles'] = $site_permission_titles;
        
        $site_permission_hide_first = unserialize(SITE_PERMISSIONS_HIDE_FIRST);
        $this->data['site_permission_hide_first'] = $site_permission_hide_first;
        
        $site_icons = unserialize(SITE_ICONS);
        $this->data['site_icons'] = $site_icons;
        
        $nav = array();
        $can = new stdClass();
        if(isset($permissions[$controller][$action])){
            foreach ($permissions[$controller][$action] as $key => $val){
                $can->$key = true;
            }
            
            foreach ($permissions[$controller] as $_class => $class){
                $nav[] = array(
                    'action'=>$_class,
                    'title'=>ucfirst(str_replace("_", " ", (isset($site_permission_titles[$controller][$_class])) ? $site_permission_titles[$controller][$_class] : $_class))
                );
            }
        }
        $this->data['nav'] = $nav;
        
        $this->data['can'] = $can;
        
        $this->data['query'] = $this->input->post();
        
        if($this->getSession('username') == 'admin'){
            error_reporting(-1);
            ini_set('display_errors', 'On');
            if (!$this->input->is_ajax_request()) {
                $this->output->enable_profiler(TRUE);
            }
        }
    }
    protected function render($the_view = NULL, $template = 'main')
    {
        if($template == 'json' )
        {
            header('Content-Type: application/json');
            echo json_encode($this->data);
        }
        else
        {
            $this->data['the_view_content'] = (is_null($the_view)) ? '' : $this->load->view($the_view,$this->data, TRUE);
            $this->load->view('layouts/admin/'.$template, $this->data);
        }
    }
    
    public function getSession($field){
        if(isset($this->data['user_session'][$field])){
            return $this->data['user_session'][$field];
        }else{
            //log them out
            $this->session->set_flashdata('error','Please login');
            redirect('index');
        }
    }
}

class Guest_Controller extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	protected function render($the_view = NULL, $template = 'main')
	{
		if($template == 'json' )
		{
			header('Content-Type: application/json');
			echo json_encode($this->data);
		}
		else if($template == 'login')
		{
			$this->data['the_view_content'] = (is_null($the_view)) ? '' : $this->load->view($the_view,$this->data, TRUE);
			$this->load->view('layouts/login/main', $this->data);
		}
		else
		{
			$this->data['the_view_content'] = (is_null($the_view)) ? '' : $this->load->view($the_view,$this->data, TRUE);
			$this->load->view('layouts/guest/'.$template, $this->data);
		}
	}
}