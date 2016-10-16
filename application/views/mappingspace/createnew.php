<div class="ui modal">
	<div class="ui small blue header">Create new mapping space</div>
	
	<div class="content">
	
<div id="content">
		
	<div id="title">New mapping space </div>
	
	
	<div class="ui form segment" >
	<?php 	echo form_open_multipart(base_url().'index.php/mappingspace/createnew_post');	
			echo form_hidden('datasource_id', $datasource_id);
	?>
	
		<div class="field">
			<label>Name</label>
			<div class="ui input">
				<input type="text" id="input_name" name="input_name" placeholder="Name of the data source...">
			</div>
		</div>
		
		
		<div class="actions">
			<div class="ui tiny deny button">Cancel</div> <input type="submit" value="Create" class="ui tiny button" />
		</div>
	<?php echo form_close(); ?>
	</div>
	
      

</div>

</div>
