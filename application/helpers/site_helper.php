<?php 

if(!function_exists('get_user_token')){
    function get_user_token(){
        $ci =& get_instance();
        return encrypt($ci->session->userdata('id'));
    }
}

if(!function_exists('safe_b64encode')){
    function safe_b64encode($string) {
        
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }
}

if(!function_exists('safe_b64decode')){
    function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}

if(!function_exists('encrypt')){
    function encrypt($q) {
        $cryptKey  = 'qSAtJB0rGtsatIn5UBdsk1xG03efDSkyCp';
        $qEncoded      = safe_b64encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
        return( $qEncoded );
    }
}

if(!function_exists('decrypt')){
    function decrypt($q) {
        $cryptKey  = 'qSAtJB0rGtsatIn5UBdsk1xG03efDSkyCp';
        $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), safe_b64decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
        return( $qDecoded );
    }
}

if(!function_exists('array_depth')){
    function array_depth(array $array) {
        $max_depth = 1;
        
        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = array_depth($value) + 1;
                
                if ($depth > $max_depth) {
                    $max_depth = $depth;
                }
            }
        }
        
        return $max_depth;
    }
}
?>