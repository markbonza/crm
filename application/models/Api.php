<?php

class Api extends CI_Model
{    
    function __construct() {
        parent::__construct();
        error_reporting(-1);
        ini_set('display_errors', 'On');
    }
    
    public function check($token){
        $allowed = false;
        
        $token = decrypt($token);
        
        $user = $this->u_model->byid($token);
        if($user){
            $allowed = true;
            return $allowed;
        }
        
        return $allowed;
    }
}