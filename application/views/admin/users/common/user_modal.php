<form id="user_form">
    <div class="modal-header">
    	<button type="button" class="close btn-close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    	<h4 class="modal-title text-center" id="myModalLabel"><?=$mode?> User</h4>
    </div>
    <div class="modal-body">
    	<div class="container-fluid row">
    		<div class="row">
				<div class="col-md-6">
    				<label>Username</label>
    				<div class="input-group">
    					<div class="form-line">
    						<input type="text" name="username" class="form-control" placeholder="Type in username" value="<?=(isset($data->username) ? $data->username : '')?>">
    					</div>
    				</div>
				</div>
				<div class="col-md-6">
    				<label>Password</label>
    				<div class="input-group">
    					<div class="form-line">
    						<input type="password" name="password" class="form-control" placeholder="Type in password" value="">
    					</div>
    				</div>
				</div>
			</div>
    		<div class="row">
				<div class="col-md-6">
    				<label>First name</label>
    				<div class="input-group">
    					<div class="form-line">
    						<input type="text" name="firstname" class="form-control" placeholder="Type in first name" value="<?=(isset($data->firstname) ? $data->firstname : '')?>">
    					</div>
    				</div>
				</div>
				<div class="col-md-6">
    				<label>Last name</label>
    				<div class="input-group">
    					<div class="form-line">
    						<input type="text" name="lastname" class="form-control" placeholder="Type in last name" value="<?=(isset($data->lastname) ? $data->lastname : '')?>">
    					</div>
    				</div>
				</div>
			</div>
    		<div class="row">
				<div class="col-md-6">
    				<label>Email</label>
    				<div class="input-group">
    					<div class="form-line">
    						<input type="email" name="email" class="form-control" placeholder="Type in email" value="<?=(isset($data->email) ? $data->email : '')?>">
    					</div>
    				</div>
				</div>
				<div class="col-md-6">
    				<label>Group</label>
    				<div class="input-group">
    					<div class="form-line">
    						<?php 
    						$groups_array = array();
    						foreach ($groups as $g){
    						    $groups_array[$g->id] = $g->name;
    						}
    						?>
    						<?=form_dropdown('group_id', $groups_array, (isset($data->group_id) ? $data->group_id : ''),' class="form-control text-center selectpicker" title="-- Select Group --" id="group_id" data-show-subtext="true" data-actions-box="true" data-live-search="true" data-selected-text-format="count > 3"');?>
    					</div>
    				</div>
				</div>
			</div>
    		<div class="row">
				<div class="col-md-6">
    				<label>Phone</label>
    				<div class="input-group">
    					<div class="form-line">
    						<input type="text" name="phone" class="form-control" placeholder="Type in phone" value="<?=(isset($data->phone) ? $data->phone : '')?>">
    					</div>
    				</div>
				</div>
				<div class="col-md-6">
    				<label>Gender</label>
    				<div class="input-group">
    					<div class="form-line">
            				<input type="checkbox" id="cb_male" name="gender" value="0" class="with-gap" <?=(isset($data->gender) && $data->gender == 0 ? 'checked' : '')?>/>
                            <label for="cb_male">Male</label>
            				<input type="checkbox" id="cb_female" name="gender" value="1" class="with-gap" <?=(isset($data->gender) && $data->gender == 1 ? 'checked' : '')?>/>
                            <label for="cb_female">Female</label>
    					</div>
    				</div>
				</div>
			</div>
    		<div class="form-group">
				<label>Address</label>
				<div class="">
					<div class="form-line">
						<textarea name="description" class="form-control no-resize" placeholder="Type in address"><?=(isset($data->address) ? $data->address : '')?></textarea>
					</div>
				</div>
			</div>
    		<div class="row">
    			<?php if($is_system_admin) : ?>
    				<div class="col-md-6">
        				<input type="checkbox" id="cb_is_system_admin" name="is_system_admin" class="filled-in" <?=(isset($data->is_system_admin) && $data->is_system_admin > 0 ? 'checked' : '')?>/>
                        <label for="cb_is_system_admin">Is System Admin</label>
    				</div>
				<?php endif;?>
				<div class="col-md-6">
    				<input type="checkbox" id="cb_status" name="status" class="filled-in" <?=(isset($data->status) && $data->status > 0 ? 'checked' : '')?>/>
                    <label for="cb_status">Status</label>
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
	$('.selectpicker').selectpicker('refresh');
	$(document).off('submit','#user_form').on('submit','#user_form', function(e){
		e.preventDefault();
		var data = $(this).serializeArray();
		$.blockUI({baseZ: 2000});
        $.ajax({
			type:"POST",
			url:"<?php echo base_url('api/users/save');?>",
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