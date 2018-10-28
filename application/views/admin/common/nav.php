<?php if(isset($nav)) : ?>
<ul class="nav nav-tabs" role="tablist">
	<?php foreach ($nav as $n) : ?>
		<?php if(isset($site_permission_hide_first[$controller_name]) && in_array($n['action'], $site_permission_hide_first[$controller_name]) && $method_name !== $n['action']) : ?>
		<?php else : //show ?>
			<li role="presentation" <?=($method_name == $n['action']) ? 'class="active"' : ''?>><a href="<?=site_url('admin/'.$controller_name.'/'.$n['action'])?>"><i class="fa fa-list fa-fw"></i> <?=$n['title']?></a></li>
		<?php endif;?>
	<?php endforeach;?>
</ul>
<?php endif;?>