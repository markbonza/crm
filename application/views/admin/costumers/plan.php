<div class="row clearfix">
	<?php $this->load->view('admin/common/nav')?>
	<div class="card table-responsive">
		<div class="header">
        	<table class="table table-bordered table-condensed table-stripped">
        		<thead>
            		<tr>
            			<th colspan="4" class="text-center">DETAILS</th>
            		</tr>
        		</thead>
        		<tbody>
        			<tr>
        				<th class="fit">Costumer</th>
        				<td><?=$company->name?></td>
        				<th class="fit">Location</th>
        				<td><?=$company->address?></td>
        			</tr>
        			<tr>
        				<th class="fit">Account Manager</th>
        				<td></td>
        				<th class="fit">Headquarters</th>
        				<td><?=$company->headquarters?></td>
        			</tr>
        			<tr>
        				<th class="fit">Last Updated</th>
        				<td></td>
        				<th class="fit">Reviewed On</th>
        				<td></td>
        			</tr>
        		</tbody>
        	</table>
		</div>
		<div class="body">
        	<?=$overview?>
		</div>
	</div>
</div>