
<div id="content">
	<?php echo $name; ?>
	<div id="title">
		<table ><tr><td>Mapped classes:</td><td><a href="<?php echo base_url();?>index.php/mappedclass/createnew/<?php echo $datasource_id."/".$mappedspace_id; ?>">Create new</a></td></tr></table>
	</div>
	<!--
	<table id="box-table-a">
	<thead>
		<tr>
			<th scope="col">class</th>
			<th scope="col">sql</th>
			<th scope="col">uris</th>
			<th scope="col">Date</th>
			<th scope="col"></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo $row->class; ?></td>
			<td><?php echo $row->sql; ?></td>
			<td><?php echo $row->uri; ?></td>
			<td><?php echo $row->date; ?> </td>
			<td><a href="<?php echo base_url();?>index.php/mappedclass/edit/<?php echo $datasource_id."/".$id."/".$row->id; ?>"><img src="./public/img/modify.png" title="edit mapped class"></a> <a href="<?php echo base_url();?>index.php/mappedclass/delete/<?php echo $datasource_id."/".$id."/".$row->id; ?>" onclick="return confirm('Are you sure?');"><img src="./public/img/delete.png" title="delete mapped class"></a></td>
		</tr>
		</tbody>
	</table> 
	-->
	<?php foreach($mclasses as $row): ?>
		<div><p><span><?php echo $row->class; ?></span>
		
		</p></div>
	<?php endforeach; ?>
	
</div>
