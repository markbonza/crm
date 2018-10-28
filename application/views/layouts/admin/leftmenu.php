<?php defined('BASEPATH') OR exit('No direct script access allowed'); 

$photo = $user_session['photo'];
$gender = $user_session['gender'];

$gender_avatar = '';
switch($gender) :
	case 'male' :
		$gender_avatar = 'male.png';
		break;
	case 'female' :
		$gender_avatar = 'female.png';
		break;
	default:
		$gender_avatar = 'user1.png';
		break;
endswitch;
?>
<section>
	<!-- Left Sidebar -->
	<aside id="leftsidebar" class="sidebar">
		<!-- User Info -->
		<div class="user-info">
			<div class="image">
				<?php if($photo != '') : ?>
					<img src="<?=base_url('assets/uploads/users/'.$photo)?>" width="48" height="48" alt="User" />
				<?php else : ?>
					<img src="<?=base_url('assets/images/'.$gender_avatar)?>" width="48" height="48" alt="User" />				
				<?php endif;?>
			</div>
			<div class="info-container">
				<div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=strtoupper($user_session['username']); ?></div>
				<div class="email"><?=$user_session['email']; ?></div>
			</div>
		</div>
		<!-- #User Info -->
		<!-- Menu -->
		<div class="menu">
			<ul class="list">
				<li class="header">MAIN NAVIGATION</li>
				<li <?=($controller_name=='index') ? 'class="active"' : '' ?>>
					<a href="<?=site_url('admin/dashboard/');?>">
						<i class="material-icons">home</i>
						<span>Home</span>
					</a>
				</li>
				<?php foreach (unserialize(SITE_PERMISSIONS) as $_controller => $classes) : ?>
					<?php if($user_session['is_system_admin'] || isset($access_permissions[$_controller])) :?>
						<?php if(array_depth($classes) == 1) : ?>
        					<li <?=($controller_name==$_controller) ? 'class="active"' : '' ?>>
        						<a href="<?=site_url('admin/'.$_controller.'/');?>">
        							<?php if(isset($site_icons[$_controller])) : ?>
        								<i class="material-icons"><?=$site_icons[$_controller]?></i>
        							<?php endif;?>
        							<span><?=ucfirst(str_replace("_", " ", $_controller))?></span>
        						</a>
        					</li>
    					<?php else: ?>
        					<li class="<?=($controller_name==$_controller) ? 'active' : '' ?>" >
        						<a href="javascript:void(0);" class="menu-toggle">
        							<?php if(isset($site_icons[$_controller])) : ?>
        								<i class="material-icons"><?=$site_icons[$_controller]?></i>
        							<?php endif;?>
        							<span><?=ucfirst(str_replace("_", " ", $_controller))?></span>
        						</a>
        						<ul class="ml-menu">
                					<?php foreach ($classes as $_class => $ac) : ?>
                						<?php if($user_session['is_system_admin'] || isset($access_permissions[$_controller][$_class])) :?>
                                    		<?php if(isset($site_permission_hide_first[$_controller]) && in_array($_class, $site_permission_hide_first[$_controller]) && $method_name !== $_class) : ?>
                                    		<?php else : //show ?>
                        						<li class="<?=($controller_name==$_controller && $method_name == $_class) ? 'active' : '' ?>" >
                        							<a href="<?=site_url('admin/'.$_controller.'/'.$_class);?>">
                										<?=ucfirst(str_replace("_", " ", (isset($site_permission_titles[$_controller][$_class])) ? $site_permission_titles[$_controller][$_class] : $_class))?>
                        							</a>
                        						</li>
                							<?php endif;?>
                						<?php endif;?>
                					<?php endforeach;?>
        						</ul>
        					</li>
    					<?php endif;?>
					<?php endif;?>					
				<?php endforeach;?>
				<li>
					<a href="<?=site_url('index/logout');?>">
						<i class="material-icons">power_settings_new</i>
						<span>Logout</span>
					</a>
				</li>
			</ul>
		</div>
	</aside>
	<!-- #END# Left Sidebar -->
</section>