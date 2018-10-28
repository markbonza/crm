<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title><?=isset($page_title) ? $page_title : SITE_NAME ?> | <?=SITE_DESCRIPTION?></title>
    <!-- Favicon-->
    <link rel="icon" href="<?= site_url('assets/favicon.ico')?>" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="<?= site_url('assets/plugins/bootstrap/css/bootstrap.css')?>" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="<?= site_url('assets/plugins/node-waves/waves.css')?>" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="<?= site_url('assets/plugins/animate-css/animate.css')?>" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="<?= site_url('assets/css/style.css')?>" rel="stylesheet">
</head>

<body class="login-page">
    <div class="login-box">
        <div class="logo">
            <a href="javascript:void(0);"><b><?=SITE_NAME?></b></a>
            <small><?=SITE_DESCRIPTION?></small>
        </div>
        <?php if($this->session->flashdata('error')){
		  echo '<div class="alert alert-danger">';
			echo '<a class="close" data-dismiss="alert">×</a>';
			echo $this->session->flashdata('error');
		  echo '</div>'; 
		}else if($this->session->flashdata('success')){
		  echo '<div class="alert alert-success">';
			echo '<a class="close" data-dismiss="success">×</a>';
			echo $this->session->flashdata('success');
		  echo '</div>'; 
		}?>
        <div class="card">
            <div class="body">
                <?=(isset($the_view_content)) ? $the_view_content : ''?>
            </div>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="<?= site_url('assets/plugins/jquery/jquery.min.js')?>"></script>

    <!-- Bootstrap Core Js -->
    <script src="<?= site_url('assets/plugins/bootstrap/js/bootstrap.js')?>"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="<?= site_url('assets/plugins/node-waves/waves.js')?>"></script>

    <!-- Validation Plugin Js -->
    <script src="<?= site_url('assets/plugins/jquery-validation/jquery.validate.js')?>"></script>

    <!-- Custom Js -->
    <script src="<?= site_url('assets/js/admin.js')?>"></script>
    <script src="<?= site_url('assets/js/pages/examples/sign-in.js')?>"></script>
</body>

</html>