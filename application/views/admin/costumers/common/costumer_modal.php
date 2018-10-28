<form id="costumer_form">
    <div class="modal-header">
    	<button type="button" class="close btn-close-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    	<h4 class="modal-title text-center" id="myModalLabel"><?=$mode?> User</h4>
    </div>
    <div class="modal-body">
    	<div class="container-fluid row">
    		<div class="row">
				<div class="col-md-6">
    				<label>Company</label>
    				<div class="form-group">
    					<div class="form-line">
    						<?php 
    						$companies_array = array();
    						foreach ($companies as $c){
    						    $companies_array[$c->id] = $c->name;
    						}
    						?>
    						<?=form_dropdown('company_id', $companies_array, (isset($data->company_id) ? $data->company_id : ''),' class="form-control text-center selectpicker" title="-- Select Company --" id="company_id" data-show-subtext="true" data-actions-box="true" data-live-search="true" data-selected-text-format="count > 3"');?>
    					</div>
    				</div>
				</div>
				<div class="col-md-6">
    				<label>Salutation</label>
    				<div class="form-group">
    					<div class="form-line">
    						<?=form_dropdown('prefix', $salutations, (isset($data->prefix) ? $data->prefix : ''),' class="form-control text-center selectpicker" title="-- Select Prefix --" id="prefix" data-show-subtext="true" data-actions-box="true" data-live-search="true" data-selected-text-format="count > 3"');?>
    					</div>
    				</div>
				</div>
    		</div>
    		<div class="row">
				<div class="col-md-6">
    				<label>First name</label>
    				<div class="form-group">
    					<div class="form-line">
    						<input type="text" name="firstname" class="form-control" placeholder="Type in first name" value="<?=(isset($data->firstname) ? $data->firstname : '')?>">
    					</div>
    				</div>
				</div>
				<div class="col-md-6">
    				<label>Last name</label>
    				<div class="form-group">
    					<div class="form-line">
    						<input type="text" name="lastname" class="form-control" placeholder="Type in last name" value="<?=(isset($data->lastname) ? $data->lastname : '')?>">
    					</div>
    				</div>
				</div>
			</div>
    		<div class="row">
				<div class="col-md-6">
    				<label>Phone</label>
    				<div class="form-group">
    					<div class="form-line">
    						<input type="text" name="phone" class="form-control" placeholder="Type in phone" value="<?=(isset($data->phone) ? $data->phone : '')?>">
    					</div>
    				</div>
				</div>
				<div class="col-md-6">
    				<label>Email</label>
    				<div class="form-group">
    					<div class="form-line">
    						<input type="email" name="email" class="form-control" placeholder="Type in email" value="<?=(isset($data->email) ? $data->email : '')?>">
    					</div>
    				</div>
				</div>
			</div>
    		<div class="row">
				<div class="col-md-6">
    				<label>Birthdate</label>
    				<div class="form-group">
    					<div class="form-line">
    						<input type="text" name="birthdate" id="birthdate" class="form-control datepicker" placeholder="Select birthdate" value="<?=(isset($data->birthdate) ? date('m/d/Y',$data->birthdate) : '')?>">
    					</div>
    				</div>
				</div>
				<div class="col-md-6">
    				<label>Head</label>
    				<div class="form-group">
    					<div class="form-line">
    						<?php 
    						$head_array = array();
    						foreach ($costumers as $c){
    						    $costumers[$c->id] = $c->fullname;
    						}
    						?>
    						<?=form_dropdown('head_id', $head_array, (isset($data->head_id) ? $data->head_id : ''),' class="form-control text-center selectpicker" title="-- Select Head --" id="head_id" data-show-subtext="true" data-actions-box="true" data-live-search="true" data-selected-text-format="count > 3"');?>
    					</div>
    				</div>
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
    $('.datepicker').bootstrapMaterialDatePicker({
        format: 'MM/DD/YYYY',
        clearButton: true,
        weekStart: 1,
        time: false
    });
	$('.selectpicker').selectpicker('refresh');
	$(document).off('submit','#costumer_form').on('submit','#costumer_form', function(e){
		e.preventDefault();
		var data = $(this).serializeArray();
		$.blockUI({baseZ: 2000});
        $.ajax({
			type:"POST",
			url:"<?php echo base_url('api/costumers/save');?>",
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