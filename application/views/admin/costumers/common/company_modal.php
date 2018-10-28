<form id="company_form">
    <div class="modal-header">
    	<button type="button" class="close btn-close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    	<h4 class="modal-title text-center" id="myModalLabel"><?=$mode?> Company</h4>
    </div>
    <div class="modal-body">
    	<div class="container-fluid">
    		<div class="form-group">
				<label>Company Name</label>
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
				<label>Address</label>
				<div class="input-group">
					<div class="form-line">
						<textarea name="address" class="form-control" placeholder="Type in address"><?=(isset($data->address) ? $data->address : '')?></textarea>
					</div>
				</div>
			</div>
    		<div class="form-group">
				<label>Headquarters</label>
				<div class="input-group">
					<div class="form-line">
						<textarea name="headquarters" class="form-control" placeholder="Type in headquarters"><?=(isset($data->headquarters) ? $data->headquarters : '')?></textarea>
					</div>
				</div>
			</div>
			<hr>
			<h4>Permissions</h4>
			<div class="row">
				<?php $c = 0;foreach ($users as $_user_id => $user) : $c++;?>
					<div class="col-md-4">
    					<h5><?=$user->fullname?></h5>
    					<input type="checkbox" id="cb_<?=$_user_id?>" name="users[<?=$_user_id?>][user_id]" value="<?=$_user_id?>" class="filled-in" <?=(isset($company_users[$_user_id]) ? 'checked' : '')?>/>
                        <label for="cb_<?=$_user_id?>">Allow </label>
    					<input type="checkbox" id="cb_is_manager_<?=$_user_id?>" name="users[<?=$_user_id?>][is_manager]" value="1" class="filled-in" <?=(isset($company_users[$_user_id]) && $company_users[$_user_id]->is_manager ? 'checked' : '')?>/>
                        <label for="cb_is_manager_<?=$_user_id?>">Manager? </label>
					</div>
				<?php endforeach;?>
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
	$(document).off('submit','#company_form').on('submit','#company_form', function(e){
		e.preventDefault();
		var data = $(this).serializeArray();
		$.blockUI({baseZ: 2000});
        $.ajax({
			type:"POST",
			url:"<?php echo base_url('api/companies/save');?>",
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