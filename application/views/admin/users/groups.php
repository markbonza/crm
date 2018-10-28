<div class="row clearfix">
	<?php $this->load->view('admin/common/nav')?>
	<div class="card">
		<div class="col-md-12 body">
			<?php if(isset($can->add)) : ?>
    			<button type="button" id="btn-add" class="btn btn-success">
    				<i class="material-icons">add</i>
    			</button>
			<?php endif;?>
			<div class="table-responsive">
				<table id="table" class="table table-bordered table-hover dataTable display text-center" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>#</th>
							<th>Roles</th>
							<th>Description</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>

<div class="modal fade" id="modal-details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		</div>
	</div>
</div>

<script>
$(document).ready(function($){

	$(document).on('click','#btn-add',function(){
    	$('#modal-details .modal-content').html(''); 
        $.blockUI();
        $.ajax({
			type:"POST",
			url:"<?php echo base_url('api/users/add_groups_modal');?>",
			data:{u_token : '<?=get_user_token()?>'},
			success:function(response)
			{
				//alert(response);
				response = JSON.parse(response);
				if(response.status){
					$('#modal-details .modal-content').html(response.view);
					$('#modal-details').modal('show');
				}else{
					console.log(response);
				    alert(response.msg);
				}
		    	$.unblockUI();
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
		    	console.log(XMLHttpRequest);
		    	$.unblockUI();
		 	}
		});
	});
	
	var table = $('#table').dataTable({
		displayStart: 1,
		searching: false,
        pageLength:3,
        "length":3, 
        "start":0,            
		"bLengthChange": false,
		"autoWidth": true,
		"bAutoWidth": true,
        "iDisplayLength":10, 
        "iDisplayStart":0, 
        "sPaginationType": "full_numbers",           
		 ajax: {
			 url: "<?=site_url('api/users/get_groups')?>",
			 type : 'post',
			 data : function(d){
	        	 d['u_token'] = '<?=get_user_token()?>';
    	         return d;
	         },
			 error: function (xhr, error, thrown) {
				 console.log(xhr);
			 }
		 },
         "bProcessing": true,
         "bServerSide": true,
         "bStateSave": false,
		 columns: [
             { "data": "index" },
             { "data": "name" },
             { "data": "description" },
             { 
                 "data" : 'status',
                 'render' : function (data, type, row){
                     var state = "Active";
                     if(row.status <= 0){
                         state = "Inactive";
                     }
                	 return state;
                 }	
             },
             {
                 "data" : 'id',
                 'render' : function (data, type, row){
                     var button_edit = '<button type="button" class="btn btn-success btn-xs waves-effect btn-assignx btn-edit" title="Edit" data-id="'+row.id+'"><i class="material-icons">mode_edit</i></button>';
                     var button_delete = '<button type="button" class="btn btn-danger btn-xs waves-effect btn-assignx btn-delete" title="Delete" data-id="'+row.id+'"><i class="material-icons">delete</i></button>';

                     var buttons = "";
                      <?php if(isset($can->edit)) : ?>
                    	buttons += ' '+ button_edit;
                      <?php endif;?>
                      <?php if(isset($can->delete)) : ?>
                    	buttons += ' '+ button_delete;
                      <?php endif;?>
                	 return buttons;
                 }
             }
         ]	 
	});

    $(document).on('click','.btn-delete',function(e){
        var id = $(this).data('id');
        $.ajax({
			type:"POST",
			url:"<?php echo base_url('api/users/delete_group_modal');?>",
			data:{id:id,u_token:'<?=get_user_token()?>'},
			success:function(response)
			{
				//alert(response);
				response = JSON.parse(response);
				if(response.status){
					$('#modal-details .modal-content').html(response.view);
			        $('#modal-details').modal('show');
				}else{
				    alert("No records were returned");
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
		    	console.log(XMLHttpRequest);
		 	}
		});
    });
    
    $(document).on('click','.btn-edit',function(e){
        e.preventDefault();
        var id = $(this).attr('data-id');
        $.ajax({
			type:"POST",
			url:"<?php echo base_url('api/users/view_group_modal');?>",
			data:{id:id,u_token:'<?=get_user_token()?>'},
			success:function(response)
			{
				//alert(response);
				response = JSON.parse(response);
				if(response.status){
					$('#modal-details .modal-content').html(response.view);
			        $('#modal-details').modal('show');
				}else{
				    alert("No records were returned");
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
		    	console.log(XMLHttpRequest);
		 	}
		});
    });
    
    $('#modal-details').on('hidden.bs.modal', function () {
    	$('#table').DataTable().ajax.reload(null, false);
	})
});
</script>