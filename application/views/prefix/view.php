<div class="ui green segment">
	Namespaces:<br/><br/>
		
	<table class="ui basic small table">
	<thead>
		<tr>
			<th scope="col">Prefix</th>
			<th scope="col">IRI</th>
			<th scope="col"></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach($prefixes as $row): ?>
		<tr>
			<td><?php echo $row->prefix; ?></td>
			<td><?php echo $row->iri; ?> </td>
			<td><a href="#" onclick="openEditDialog(<?php echo $row->id; ?>, '<?php echo $row->prefix; ?>', '<?php echo $row->iri; ?>');"><img src="<?php echo base_url();?>/public/img/modify.png" title="edit namespace"></a> <a href="<?php echo base_url();?>index.php/prefix/delete/<?php echo $ontology_id; ?>/<?php echo $row->id; ?>" onclick="return confirm('Are you sure?');"><img src="<?php echo base_url();?>/public/img/delete.png" title="delete namespace"></a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>

	<tfoot>
		<tr><th colspan="3"><br/>
			<div class="ui tiny button" onMouseUp="$('.ui.new.modal').modal('show');">add new namespace</div></th>
		</tr>
	</tfoot> 
	</table> 
</div>


<script>

	function openEditDialog(id, prefix, iri) {
	
		document.getElementById('prefix_id').value = id;
		document.getElementById('input_edit_prefix').value = prefix;
		document.getElementById('input_edit_iri').value = iri;
		
		$('.ui.edit.modal').modal('show');
	}
</script>
<!-- MODAL BOX -->

<div class="ui new modal">
	<div class="ui small blue header">Create new namespace</div>
	
	<div class="content">
		
	<div class="ui form segment" >
	<?php 	echo form_open_multipart(base_url().'index.php/prefix/createnew_post');?>
	
		<input type="hidden" id="ontology_id" name="ontology_id" value="<?php echo $ontology_id; ?>">
		<div class="field">
			<label>Prefix</label>
			<div class="ui input">
				<input type="text" id="input_prefix" name="input_prefix" placeholder="Prefix of the namespace...">
			</div>
		</div>
		
		<div class="field">
			<label>IRI</label>
			<div class="ui input">
				<input type="text" id="input_iri" name="input_iri" placeholder="IRI of the namespace...">
			</div>
		</div>
	</div>
	</div>
	
	
	<div class="actions">
		<input type="submit" value="Add new namespace" class="ui tiny button" /> <div class="ui tiny deny button">Cancel</div>
	</div>
	
	<?php echo form_close(); ?>

</div>


<div class="ui edit modal">
	<div class="ui small blue header">Edit namespace</div>
	
	<div class="content">
		
	<div class="ui form segment" >
	<?php 	echo form_open_multipart(base_url().'index.php/prefix/edit_post');?>
	
		<input type="hidden" id="ontology_id" name="ontology_id" value="<?php echo $ontology_id; ?>">
		<input type="hidden" id="prefix_id" name="prefix_id" value="1234">
		<div class="field">
			<label>Prefix</label>
			<div class="ui input">
				<input type="text" id="input_edit_prefix" name="input_edit_prefix" value="valuesss">
			</div>
		</div>
		
		<div class="field">
			<label>IRI</label>
			<div class="ui input">
				<input type="text" id="input_edit_iri" name="input_edit_iri" value="valueiri">
			</div>
		</div>
	</div>
	</div>
	
	
	<div class="actions">
		<input type="submit" value="Edit namespace" class="ui tiny button" /> <div class="ui tiny deny button">Cancel</div>
	</div>
	
	<?php echo form_close(); ?>

</div>
