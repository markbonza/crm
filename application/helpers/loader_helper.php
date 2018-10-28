<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('add_js')){
	function add_js($file='')
	{
		$str = '';
		$ci = &get_instance();
		$header_js  = $ci->config->item('header_js');

		if(empty($file)){
			return;
		}

		if(is_array($file)){
			if(!is_array($file) && count($file) <= 0){
				return;
			}
			foreach($file AS $item){
				$url = parse_url($item);
				if(in_array("http", $url)) {
					//contains http
					$item = $item;
				}else{
					$item = base_url($item);
				}
				$header_js[] = $item;
			}
			$ci->config->set_item('header_js',$header_js);
		}else{
			$str = $file;
			$url = parse_url($str);
			if(in_array("http", $url)) {
				//contains http
				$str = $str;
			}else{
				$str = base_url($str);
			}
			$header_js[] = $str;
			$ci->config->set_item('header_js',$header_js);
		}
	}
}

//Dynamically add CSS files to header page
if(!function_exists('add_css')){
	function add_css($file='')
	{
		$str = '';
		$ci = &get_instance();
		$header_css = $ci->config->item('header_css');

		if(empty($file)){
			return;
		}

		if(is_array($file)){
			if(!is_array($file) && count($file) <= 0){
				return;
			}
			foreach($file AS $item){
				$header_css[] = $item;
			}
			$ci->config->set_item('header_css',$header_css);
		}else{
			$str = $file;
			$header_css[] = $str;
			$ci->config->set_item('header_css',$header_css);
		}
	}
}

if(!function_exists('put_css')){
	function put_css()
	{
		$str = '';
		$ci = &get_instance();
		$header_css = $ci->config->item('header_css');

		if($header_css){
			foreach($header_css AS $item){
				if(strpos($item, "http://") !==false || strpos($item, "https://") !==false) {
					$str .= '<link rel="stylesheet" href="'.$item.'" type="text/css" />'."\n";
				} else {
					$str .= '<link rel="stylesheet" href="'.base_url($item).'" type="text/css" />'."\n";
				}
			}
		}

		return $str;
	}
}

if(!function_exists('put_js')){
	function put_js()
	{
		$str = '';
		$ci = &get_instance();
		$header_js  = $ci->config->item('header_js');

		if($header_js){
			foreach($header_js AS $item){
				if(strpos($item, "http://") !==false || strpos($item, "https://") !==false) {
					$str .= '<script type="text/javascript" src="'.$item.'"></script>'."\n";
				} else {
					$str .= '<script type="text/javascript" src="'.base_url().'js/'.$item.'"></script>'."\n";
				}
			}
		}

		return $str;
	}
}

if(!function_exists('add_components')){
	function add_components($component_name){
	    switch(strtolower($component_name)) {
	        case "jquery-ui":
	            add_js("assets/admin/js/jquery-ui.js");
	            break;
	        case "jquery-ui-css":
	            add_css("assets/admin/plugins/jquery-ui/jquery-ui.min.css");
	            break;
		    case "signature":		        
		        add_js("assets/admin/js/jSignature.js");
		        break;
		    case "inputmask":
		        add_js("assets/admin/js/jquery.inputmask.bundle.js");		        
		        break;
		    case "moment-range":
		        add_js("assets/admin/js/moment.js");
		        add_js("assets/admin/js/moment-range.js");
		        add_js("assets/admin/js/extend-moment-range.js");
		        break;
		    case "slider-range":
		        add_css("assets/admin/css/jquery-ui-slider-ext.css");
		        
		        add_js("assets/admin/js/jquery-ui-slider-ext.js");
		        break;
		    case "moment-range-only":
		        add_js("assets/admin/js/moment-range.js");
		        add_js("assets/admin/js/extend-moment-range.js");
		        break;
		    case "extend-moment-range":
		        add_js("assets/admin/js/extend-moment-range.js");
		        break;
			case "dialog":
				add_css("assets/admin/plugins/bootstrap-dialog/css/bootstrap-dialog.min.css");

				add_js("assets/admin/plugins/bootstrap-dialog/js/bootstrap-dialog.min.js");
				break;
			case "datatable":
				add_css("assets/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.min.css");

				add_js("assets/plugins/jquery-datatable/jquery.dataTables.js");
				add_js("assets/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.min.js");
				add_js("assets/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js");
				add_js("assets/plugins/jquery-datatable/extensions/export/buttons.print.min.js");
				break;
			case "tablesorter":
			    add_css("assets/admin/css/tablesorter.theme.dropbox.css");
			    
			    add_js("assets/admin/js/jquery.tablesorter.js");
			    add_js("assets/admin/js/jquery.tablesorter.widgets.js");
			    break;
			case "tablesorter-stickyheader":
			    add_js("assets/admin/js/widgets/widget-cssStickyHeaders.js");
			    break;
			case "tablesorter-grouping":
			    add_css("assets/admin/css/widget.grouping.css");
			    
			    add_js("assets/admin/js/widgets/widget-grouping.js");
			    break;
			case "datatable.fixedheader":
			    add_css("assets/mail/css/fixedHeader.bootstrap.min.css");
			    
			    add_js("assets/mail/js/dataTables.fixedHeader.min.js");
			    break;
			case "datatable.responsive":
			    add_css("assets/mail/css/responsive.bootstrap.min.css");
			    
			    add_js("assets/mail/js/dataTables.responsive.min.js");
			    break;
			case 'timepicker':
			    add_css("assets/admin/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css");
			    
			    add_js("assets/admin/plugins/bootstrap-daterangepicker/moment.min.js");
			    add_js("assets/admin/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js");
			    break;
			case 'datetimepicker': 
				add_css("assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css");
				
				add_js("assets/plugins/momentjs/moment.js");
				add_js("assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js");
				break;		
			case 'select': 
			    add_css("assets/plugins/bootstrap-select/css/bootstrap-select.min.css");
			    add_js("assets/plugins/bootstrap-select/js/bootstrap-select.min.js");
			    break;
			case 'select2':
			    add_css("assets/admin/css/select2.min.css");
			    add_js("assets/admin/js/select2.min.js");
			    break;	
			case 'chart':
				add_js("assets/qc/dashboard/js/Chart.bundle.min.js");	
				add_js("assets/qc/dashboard/js/Chart.PieceLabel.min.js");
				break;	
			case 'count':
				add_js("assets/qc/dashboard/js/jquery.countTo.js");
				break;	
			case 'dropzone': 
				add_css("assets/qc/dashboard/css/dropzone.css");

				add_js("assets/qc/dashboard/js/dropzone.js");
				break;
			case 'prettyphoto': 
				add_css("assets/qc/dashboard/css/prettyPhoto.css");

				add_js("assets/qc/dashboard/js/jquery.prettyPhoto.js");
				break;
			case 'jquery.download':
				add_js('assets/qc/dashboard/js/jquery.fileDownload.js');
				break;
			case 'dark':
				add_css("assets/qc/dashboard/css/dark.css");
				break;
			case 'light':
				add_css("assets/qc/dashboard/css/light.css");
				break;
			case 'blockui':
			    add_js("assets/admin/plugins/jquery.blockui.min.js");
			    break;
			case 'listgroup':
			    add_js("assets/map/js/listgroup.min.js");
			    break;
			case 'map':
			    add_css("assets/map/css/map.css");
			    break;
			case 'fix':
			    add_css("assets/admin/css/fix.css");
			    break;
			case 'fix.modal':
			    add_css("assets/admin/css/modal_fix.css");
			    break;
			case 'popup_layout':
			    add_css("assets/wide/css/popup_fix.css");
			    break;
			case 'editor':
			    add_css("assets/admin/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css");
			    
			    add_js("assets/admin/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js");
			    add_js("assets/admin/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js");
			    break;
			case 'job_events':
			    add_js("assets/events/job.js");
			    break;
			default :
				break;
		}
	}
}