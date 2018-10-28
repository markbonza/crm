<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
$this->load->view('layouts/admin/header');
$this->load->view('layouts/admin/leftmenu');
?>
	<div class="content-wrapper">
		<!-- Content Wrapper. Contains page content -->
			<?php echo $the_view_content; ?>
	</div><!-- /.content-wrapper -->
<?php 
$this->load->view('layouts/admin/footer');  ?>