
<div class="ui green segment">
	<div class="ui two column divided grid">
		<div class="ui column">
			Data source: <span style="font-weight:bold;"><?php echo $datasource->name; ?></span><br />
			Ontology: <span style="font-weight:bold;"><?php echo $ontologyName; ?></span><br />
			Modified: <span style="font-weight:bold;"><?php echo $datasource->date; ?></span><br />
		</div>
		<div class="ui column">
			Import external <span style="font-weight:bold;"><a href="<?php echo base_url();?>index.php/r2rml/import/<?php echo $datasource_id; ?>">R2RML file</a></span> <br />
			Export to: <span style="font-weight:bold;"><a href="<?php echo base_url();?>index.php/r2rml/export/<?php echo $datasource_id; ?>">R2RML</a></span> <br />
		</div>
	</div>
</div>

<div class="ui form green segment">

	<?php 	echo form_open_multipart(base_url().'index.php/r2rml/import_post');	
			echo form_hidden('datasource_id', $datasource_id);
	?>
		<div class="field">
			<label><strong>Import an external R2RML file:</strong> </label> When a R2RML file is imported all the mappings already created in the data source are removed and replaced with the mappings obtained from the imported R2RMl file. A mapping space will be automatically created with the new mappings.<br/><br/>
			
			<div class="field">
				<div class="ui action input">
					<input type="text" id="_attachmentName" readonly>
					<label for="input_r2rmlfile" class="ui icon button btn-file">
						Select file&nbsp;
						 <input type="file" id="input_r2rmlfile" name="input_r2rmlfile" style="display: none">
					</label>
				</div>
			</div>
		</div>
			
		<div class="actions">
			<div class="ui tiny deny button">Cancel</div> <input type="submit" value="Import" class="ui tiny button" />
		</div>
	<br />
	<?php echo form_close(); ?>

			
</div>

<script>
	$('#input_r2rmlfile').change(function() {
		var filename = $('#input_r2rmlfile').val();
		$('#_attachmentName').val(filename);
	});
	$('#_attachmentName').val("");
</script>