<form id="group_form">
    <div class="modal-header">
    	<button type="button" class="close btn-close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    	<h4 class="modal-title text-center" id="myModalLabel"><?=$mode?> Group</h4>
    </div>
    <div class="modal-body">
    	<div class="container-fluid">
    		<div class="form-group">
				<label>Group Name</label>
				<div class="input-group">
					<div class="form-line">
						<input type="text" name="name" class="form-control" placeholder="Type in name" value="<?=(isset($data->name) ? $data->name : '')?>">
					</div>
				</div>
			</div>
    		<div class="form-group">
				<label>Description</label>
				<div class="input-group">
					<div class="form-line">
						<textarea name="description" class="form-control" placeholder="Type in description"><?=(isset($data->description) ? $data->description : '')?></textarea>
					</div>
				</div>
			</div>
    		<div class="form-group">
				<input type="checkbox" id="cb_status" name="status" class="filled-in" <?=(isset($data->status) && $data->status > 0 ? 'checked' : '')?>/>
                <label for="cb_status">Status</label>
			</div>
			<hr>
			<h4>Permissions</h4>
			<div class="row">
            	<ul class="nav nav-tabs tab-nav-right" role="tablist">
    				<?php $c = 0;foreach (unserialize(SITE_PERMISSIONS) as $resource_key => $resources) : $c++;?>
    					<li role="presentation" class="<?=($c==1) ? 'active' : ''?>"><a href="#<?=$resource_key?>" data-toggle="tab"><?=ucfirst($resource_key)?></a></li>
    				<?php endforeach;?>
				</ul>
				<div class="tab-content">
    				<?php $c = 0;foreach (unserialize(SITE_PERMISSIONS) as $resource_key => $resources) : $c++;?>
    					<div role="tabpanel" class="tab-pane fade in <?=($c==1) ? 'active' : ''?>" id="<?=$resource_key?>">
            				<?php foreach ($resources as $perm_key => $resource) : ?>
    							<div class="col-md-6">
        							<h6><?=ucfirst(str_replace("_", " ", (isset($site_permission_titles[$resource_key][$perm_key])) ? $site_permission_titles[$resource_key][$perm_key] : $perm_key))?></h6>
                					<?php foreach ($resource as $a) : $name = 'permissions['.$resource_key.']['.$perm_key.']['.$a.']';?>
                        				<input type="checkbox" id="cb_<?=$resource_key.'_'.$perm_key.'_'.$a?>" name="<?=$name?>" class="filled-in" <?=(isset($permissions[$resource_key][$perm_key]->$a) ? 'checked' : '')?>/>
                                        <label for="cb_<?=$resource_key.'_'.$perm_key.'_'.$a?>"><?=ucfirst($a)?> </label>
                					<?php endforeach;?>
    							</div>
            				<?php endforeach;?>
    						<div class="clearfix"></div>
						</div>
    				<?php endforeach;?>
				</div>
			</div>
    	</div>
    </div>
    <div class="modal-footer">
		<input type="hidden" name="u_token" value="<?=$u_token?>">
    	<?php if(isset($id)) : ?>
    		<input type="hidden" name="id" value="<?=$id?>">
    		<button type="submit" class="btn btn-primary">Update</button>
    	<?php else : ?>
    		<button type="submit" class="btn btn-primary">Save</button>
    	<?php endif;?>
    	<button type="button" class="btn btn-default btn-close-modal" data-dismiss="modal" >Close</button>
    </div>
</form>

<script>
	$(document).off('submit','#group_form').on('submit','#group_form', function(e){
		e.preventDefault();
		var data = $(this).serializeArray();
		$.blockUI({baseZ: 2000});
        $.ajax({
			type:"POST",
			url:"<?php echo base_url('api/users/save_group');?>",
			data:data,
			success:function(response)
			{
				response = JSON.parse(response);
				alert(response.msg);
				if(response.status){
					$('#modal-details').modal('hide');
				}
		    	$.unblockUI();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
		    	console.log(XMLHttpRequest);
		    	$.unblockUI();
		 	}
		});
	});
</script>