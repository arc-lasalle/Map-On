
<div class="ui modal">
	<div class="ui small blue header">Add new ontology module</div>
	
	<div class="content">
		
	<div class="ui form segment" >
	
	<?php 	echo form_open_multipart(base_url().'index.php/ontology/addnewmodule_post');
			echo form_hidden('ontology_id', $ontology_id);
	?>
	
		<div class="field">
			<label>Name</label>
			<div class="ui input">
				<input type="text" id="input_name" name="input_name" placeholder="Name of the module...">
			</div>
		</div>
		<div class="field">
			<label>Prefix</label>
			<div class="ui input">
				<input type="text" id="input_prefix" name="input_prefix" placeholder="Prefix of the ontology...">
			</div>
		</div><!--
		<div class="field">
			<label>URL</label>
			<div class="ui input">
				<input type="text" id="input_url" name="input_url" placeholder="URL of the ontology...">
			</div>
		</div> -->
		<div class="field">
			<label>File</label>
		
			<div class="field">
				<div class="ui action input">
					<input type="text" id="_attachmentName" readonly>
					<label for="input_attachment" class="ui icon button btn-file">
						Select file&nbsp;
                        <input type="file" id="input_attachment" name="input_attachment" style="display: none">
					</label>
				</div>
			</div>
		</div>
		
		
		</div>
		</div>
		
		<div class="actions">
		<input type="submit" value="Add new ontology module" class="ui tiny button" /> <div class="ui tiny deny button">Cancel</div>
		</div>
	
        <?php echo form_close(); ?>

</div>


<script>



$('#input_attachment').change(function() {
    var filename = $('#input_attachment').val();
    $('#_attachmentName').val(filename);
});
$('#_attachmentName').val("");

</script>