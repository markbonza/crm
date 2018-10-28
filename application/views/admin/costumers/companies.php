<div class="row clearfix">
	<?php $this->load->view('admin/common/nav')?>
	<div class="card">
		<div class="col-md-12 header">
			<form class="form-horizontal" id="search_form" action="<?=site_url('admin/costumers/companies')?>" method="POST">
				<div class="form-group">
					<div class="col-md-4 text-center">
						<label>Company Name</label>
						<div class="input-group">
							<div class="form-line">
								<input type="text" name="name" class="form-control text-center" placeholder="Search by company name" value="<?=(isset($query['name']) ? $query['name'] : '')?>">
							</div>
						</div>
					</div>
				</div>
				<br/>
				<div class="form-group text-center">
					<button type="submit" name="btnsubmit" class="btn btn-primary btn-lg">Search</button>
				</div>
			</form>
		</div>
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
							<th>Name</th>
							<th>Address</th>
							<th>Headquarters</th>
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
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
		</div>
	</div>
</div>

<script>
var stile = "mywin,left=50,top=40,width=1100,height=600,resizable=0";

function Popup(apri) {
	window.open(apri, "", stile);
}

$(document).ready(function($){

	$(document).on('click','#btn-add',function(){
    	$('#modal-details .modal-content').html(''); 
        $.blockUI();
        $.ajax({
			type:"POST",
			url:"<?php echo base_url('api/companies/add_modal');?>",
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
        pageLength:10,
        "length":10, 
        "start":0,            
		"bLengthChange": false,
		"autoWidth": true,
		"bAutoWidth": true,
        "iDisplayLength":10, 
        "iDisplayStart":0, 
        "sPaginationType": "full_numbers",           
		 ajax: {
			 url: "<?=site_url('api/companies/get')?>",
			 type : 'post',
			 data : function(d){
		         var dt = $("#search_form").serializeArray();
				 var obj = {};
		         $.each(dt, function(index, value) {
		        	 d[value.name] = value.value;
		         });
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
             { "data": "address" },
             { "data": "headquarters" },
             {
                 "data" : 'id',
                 'render' : function (data, type, row){
                 	var button_edit = '<button type="button" class="btn btn-success btn-xs waves-effect btn-edit" title="Edit" data-id="'+row.id+'"><i class="material-icons">mode_edit</i></button>';
                 	var button_delete = '<button type="button" class="btn btn-danger btn-xs waves-effect btn-delete" title="Delete" data-id="'+row.id+'"><i class="material-icons">delete</i></button>';
                 	var button_plan = '<a href="<?=site_url('admin/costumers/plan/')?>'+row.id+'" class="btn btn-danger btn-xs waves-effect" title="Account Plan" data-id="'+row.id+'"><i class="material-icons">folder_open</i></a>';

                 	var buttons = "";
                    <?php if(isset($access_permissions[$controller_name]['plan'])) : ?>
                 	buttons += ' '+ button_plan;
                    <?php endif;?>
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
        if(confirm("Are you sure you want to delete this company?")){
        	$.ajax({
    			type:"POST",
    			url:"<?php echo base_url('api/companies/delete');?>",
    			data:{id:id,u_token:'<?=get_user_token()?>'},
    			success:function(response)
    			{
    				//alert(response);
    				response = JSON.parse(response);
    				alert(response.msg);
    				if(response.status){
    			    	$('#table').DataTable().ajax.reload();
    				}
    			},
    			error: function(XMLHttpRequest, textStatus, errorThrown) {
    		    	console.log(XMLHttpRequest);
    		 	}
    		});
        }
    });

    $(document).on('click','.btn-edit',function(e){
        e.preventDefault();
        var id = $(this).attr('data-id');
        $.ajax({
			type:"POST",
			url:"<?php echo base_url('api/companies/view_modal');?>",
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

function remove_row(event){
	console.log(event);
}
</script>