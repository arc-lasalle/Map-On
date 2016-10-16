
<div class="ui modal">
	<div class="ui small blue header">Create new data source</div>
	
	<div class="content">

	<div class="ui form segment" >
	<?php 	echo form_open_multipart(base_url().'index.php/datasource/createnew_post');	?>
	
		<div class="field">
			<label>Name</label>
			<div class="ui input">
				<input type="text" id="input_name" name="input_name" placeholder="Name of the data source...">
			</div>
		</div>
		<div class="field">
			<label>SQL file</label>
			<input placeholder="SQL File..." id="input_sqlfile" name="input_sqlfile" type="text">
		</div>
		<div class="field">
			<label>String Connection</label>
			<input placeholder="String Connection..." id="input_stringconnection" name="input_stringconnection" type="text">
		</div>
		<div class="field">
			<label>XML file</label>
			<input placeholder="XML File..." id="input_xmlfile" name="input_xmlfile" type="text">
		</div>
		<div class="field">
			<label>Base URI</label>
			<input placeholder="http://base_uri/.../" id="input_basicuri" name="input_basicuri" type="text">
		</div>
		<div class="field">
			<label>Target ontology</label>
			<div class="ui selection dropdown select-language">
				<input name="input_ontology" type="hidden" value="fr-FR">
				<div class="text">Select ontology...</div>
				<i class="dropdown icon"></i>
				<div class="menu ui transition hidden">
					<?php 
						foreach($ontologies as $row){
						
							echo '<div class="item" data-value="'.$row->id.'">'.$row->name.'</div>';			
						}
					?>
					
				</div>
			</div>
		</div>
	</div>
	</div>
	
	<div class="actions">
		<div class="ui tiny deny button">Cancel</div> <input type="submit" value="Create" class="ui tiny button" />
	</div>
	 <?php echo form_close(); ?>
</div>

<script>

$('.ui.dropdown')
  .dropdown()
;

</script>