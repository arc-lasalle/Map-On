
<div id="content">
	
	<div id="title">
		<table ><tr><td>Mapping spaces:</td><td><a href="<?php echo base_url();?>index.php/mappingspace/createnew/<?php echo $datasource_id; ?>">Create new</a></td></tr></table>
	</div>
	<table id="box-table-a">
	<thead>
		<tr>
			<th scope="col">Name</th>
			<th scope="col">Date</th>
			<th scope="col"></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($mpsaces as $row): ?>
		<tr>
			<td><a href="<?php echo base_url();?>index.php/mappingspace/view/<?php echo $datasource_id."/".$row->id; ?>"><?php echo $row->name; ?></a>	</td>
			<td><?php echo $row->date; ?> </td>
			<td><a href="<?php echo base_url();?>index.php/mappingspace/edit/<?php echo $datasource_id."/".$row->id; ?>"><img src="./public/img/modify.png" title="edit data source"></a> <a href="<?php echo base_url();?>index.php/mappingspace/delete/<?php echo $datasource_id."/".$row->id; ?>" onclick="return confirm('Are you sure?');"><img src="./public/img/delete.png" title="delete data source"></a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	</table> 
</div>
