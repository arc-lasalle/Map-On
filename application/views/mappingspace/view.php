
<div id="content">
	<?php echo $name; ?>
	<div id="title">
		<table ><tr><td>Mapped classes:</td><td><a href="<?php echo base_url();?>index.php/mappedclass/createnew/<?php echo $datasource_id."/".$mappedspace_id; ?>">Create new class</a></td></tr></table>
	</div>
	
	<?php foreach($mclasses as $row): ?>
		<div id="header_mappedclass"><span style="font-weight:bold"><?php echo $row->class; ?> <a href="<?php echo base_url();?>index.php/mappedclass/edit/<?php echo $datasource_id."/".$mappedspace_id."/".$row->id; ?>"><img src="./public/img/modify.png" title="edit mapped class"></a> <a href="<?php echo base_url();?>index.php/mappedclass/delete/<?php echo $datasource_id."/".$mappedspace_id."/".$row->id; ?>" onclick="return confirm('Are you sure?');"><img src="./public/img/delete.png" title="delete mapped class"></a></span>
		<!--<br />
		<span style="font-style:italic;"><?php echo $row->sql; ?></span><br />
		<span><?php echo $row->uri; ?></span><br />
		-->
		</div>
		<div id="header_mappeddataproperties"><a style=" text-align=left;" href="<?php echo base_url();?>index.php/mappeddataproperty/addnew/<?php echo $datasource_id."/".$mappedspace_id."/".$row->id; ?>">add new</a><br />
		<?php foreach($dataproperties[$row->id] as $datarow): ?>
			<span style="font-weight:bold;"><?php echo $datarow->dataproperty; ?></span> <span><?php echo $datarow->value; ?></span> <span><?php echo $datarow->type; ?></span> <a href="<?php echo base_url();?>index.php/mappeddataproperty/edit/<?php echo $datasource_id."/".$mappedspace_id."/".$row->id."/".$datarow->id; ?>"><img src="./public/img/modify.png" title="edit data property"></a> <a href="<?php echo base_url();?>index.php/mappeddataproperty/delete/<?php echo $datasource_id."/".$mappedspace_id."/".$datarow->id; ?>" onclick="return confirm('Are you sure?');"><img src="./public/img/delete.png" title="delete data property"></a><br />			
		<?php endforeach; ?>
		</div>
		<div id="header_mappedobjectproperties"><a style=" text-align=left;" href="<?php echo base_url();?>index.php/mappedobjectproperty/addnew/<?php echo $datasource_id."/".$mappedspace_id."/".$row->id; ?>">add new</a><br />
		<?php foreach($objectproperties[$row->id] as $objrow): ?>
			<span style="font-weight:bold;"><?php echo $objrow->objectproperty; ?></span> <span><?php echo $objrow->target; ?></span> <span><?php echo $objrow->uri; ?></span> <a href="<?php echo base_url();?>index.php/mappedobjectproperty/edit/<?php echo $datasource_id."/".$mappedspace_id."/".$row->id."/".$objrow->id; ?>"><img src="./public/img/modify.png" title="edit data property"></a> <a href="<?php echo base_url();?>index.php/mappedobjectproperty/delete/<?php echo $datasource_id."/".$mappedspace_id."/".$objrow->id; ?>" onclick="return confirm('Are you sure?');"><img src="./public/img/delete.png" title="delete data property"></a><br />			
		<?php endforeach; ?>
		</div>
	<?php endforeach; ?>
</div>
