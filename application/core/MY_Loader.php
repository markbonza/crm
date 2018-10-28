<?php

class MY_Loader extends CI_Loader {
	/**
	 * Database Loader
	 *
	 * @access    public
	 * @param    string    the DB credentials
	 * @param    bool    whether to return the DB object
	 * @param    bool    whether to enable active record (this allows us to override the config setting)
	 * @return    object
	 */
	/*function database($params = '', $return = FALSE, $active_record = FALSE)
	{
		// Do we even need to load the database class?
		if (class_exists('CI_DB') AND $return == FALSE AND $active_record == FALSE)
		{
			return FALSE;
		}
	
		require_once(BASEPATH.'database/DB'.'.php');
	
		// Load the DB class
		$db =& DB($params,true, $active_record);
	
		$my_driver = config_item('subclass_prefix').'DB_'.$db->dbdriver.'_driver';
		$my_driver_file = APPPATH.'libraries/'.$my_driver.'.php';
	
		if (file_exists($my_driver_file))
		{
			require_once($my_driver_file);
			$db_obj = new $my_driver(get_object_vars($db));
			$db=& $db_obj;
	
	
		}
	
		if ($return === TRUE)
		{
			return $db;
		}
		// Grab the super object
		$CI =& get_instance();
	
		// Initialize the db variable.  Needed to prevent
		// reference errors with some configurations
		$CI->db = '';
		$CI->db = $db;
		//var_dump($CI);die;
		// Assign the DB object to any existing models
		//$this->_ci_assign_to_models();
	}*/
}
      
?>