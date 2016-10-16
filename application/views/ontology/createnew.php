
<div class="ui modal">
	<div class="ui small blue header">Create new ontology</div>
	
	<div class="content">
		
	<div class="ui form segment" >
	<?php 	echo form_open_multipart(base_url().'index.php/ontology/createnew_post');?>
	
		<div class="field">
			<label>Name</label>
			<div class="ui input">
				<input type="text" id="input_name" name="input_name" placeholder="Name of the ontology...">
			</div>
		</div>
	</div>
	</div>
	
	
	<div class="actions">
		<input type="submit" value="Add new ontology" class="ui tiny button" /> <div class="ui tiny deny button">Cancel</div>
	</div>
	
	<?php echo form_close(); ?>

</div>


<script>

$('.ui.dropdown')
  .dropdown()
;

</script>
