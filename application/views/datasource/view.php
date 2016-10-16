
<div class="ui green segment">
	<div class="ui two column divided grid">
		<div class="ui column">
			Data source: <span style="font-weight:bold;"><?php echo $name; ?></span><br />
			Ontology: <span style="font-weight:bold;"><?php echo $ontologyName; ?></span><br />
			Modified: <span style="font-weight:bold;"><?php echo $date; ?></span><br />
		</div>
		<div class="ui column">
			Import external <span style="font-weight:bold;"><a href="<?php echo base_url();?>index.php/r2rml/import/<?php echo $datasource_id; ?>">R2RML file</a></span> <br />
			Export to: <span style="font-weight:bold;"><a href="<?php echo base_url();?>index.php/r2rml/export/<?php echo $datasource_id; ?>">R2RML</a></span> <br />
		</div>
	</div>
</div>

<div class="ui green segment">
Mapping spaces:<br /><br />
<table class="ui selectable basic small table">
  <thead>
    <tr>
	<th >Name</th>
	<th >Mappings</th>
	<th >Tables</th>
	<th >Date</th>
	<th ></th>
  </tr></thead>
  <tbody>
  <?php foreach($mspaces as $row): ?>
		<tr>
			<td><a href="<?php echo base_url();?>index.php/mappingspace/graph/<?php echo $datasource_id."/".$row->id; ?>"><?php echo $row->name; ?></a>	</td>
			<td style="max-width:300px"><?php echo $mappings[$row->id]; ?> </td>
			<td style="max-width:300px"><?php echo $tables[$row->id]; ?> </td>
			<td><?php echo $row->date; ?> </td>
			<td><a href="#" onclick="openEditDialog(<?php echo $row->id; ?>, '<?php echo $row->name; ?>');"><i class="edit purple icon" title="edit mapping space"></i></a> <a href="<?php echo base_url();?>index.php/mappingspace/delete/<?php echo $datasource_id."/".$row->id; ?>" onclick="return confirm('Are you sure?');"><i class="delete red icon"  title="delete mapping space"></i></a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	<tfoot>
    <tr><th colspan="7"><br/>
      <div class="ui tiny button" onMouseUp="$('.ui.new.modal').modal('show');">Create new mapping space</div>
    </th>
  </tr></tfoot>
</table> 
</div>

<div class="ui green segment">
    <div class="title">Graph view</div>
 	<?php echo $graph; ?> 
</div>

<?php //echo $createnewMapping; ?>




<script>

	function openEditDialog(id, name) {
	
		document.getElementById('mappingspace_id').value = id;
		document.getElementById('input_edit_name').value = name;
				
		$('.ui.edit.modal').modal('show');
	}
</script>
<!-- MODAL BOX -->

<div class="ui new modal">
	<div class="ui small blue header">Create new mapping space</div>
	
	<div class="content">
		
	<div class="ui form segment" >
	<?php 	echo form_open_multipart(base_url().'index.php/mappingspace/createnew_post');?>
	
		<input type="hidden" id="datasource_id" name="datasource_id" value="<?php echo $datasource_id; ?>">
		<div class="field">
			<label>Name</label>
			<div class="ui input">
				<input type="text" id="input_name" name="input_name" placeholder="name of the mapping space...">
			</div>
		</div>
		
	</div>
	</div>
	
	
	<div class="actions">
		<input type="submit" value="Create" class="ui tiny button" /> <div class="ui tiny deny button">Cancel</div>
	</div>
	
	<?php echo form_close(); ?>

</div>


<div class="ui edit modal">
	<div class="ui small blue header">Edit mapping space</div>
	
	<div class="content">
		
	<div class="ui form segment" >
	<?php 	echo form_open_multipart(base_url().'index.php/mappingspace/edit_post');?>
	
		<input type="hidden" id="datasource_id" name="datasource_id" value="<?php echo $datasource_id; ?>">
		<input type="hidden" id="mappingspace_id" name="mappingspace_id" value="1234">
		<div class="field">
			<label>Name</label>
			<div class="ui input">
				<input type="text" id="input_edit_name" name="input_edit_name" value="">
			</div>
		</div>
		
	</div>
	</div>
	
	
	<div class="actions">
		<input type="submit" value="Edit mapping space" class="ui tiny button" /> <div class="ui tiny deny button">Cancel</div>
	</div>
	
	<?php echo form_close(); ?>

</div>
