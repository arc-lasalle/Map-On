<div class="ui green segment">
<table class="ui selectable basic small table">
  <thead>
    <tr>
	<th >Name</th>
	<th >SQL file</th>
	<th >URI</th>
	<th >Ontology</th>
	<th >Date</th>
	<th ></th>
  </tr></thead>
  <tbody>
    <?php foreach($datasources as $row): ?>
		<tr>
			<td><a href="<?php echo base_url();?>index.php/datasource/view/<?php echo $row->id; ?>"><?php echo $row->name; ?></a>	</td>
			<td><?php echo $row->sqlfile; ?> </td>
			<td><?php echo $row->basicuri; ?> </td>
			<td><?php echo $row->ontologyName; ?> </td>
			<td><?php echo $row->date; ?> </td>
			<td>
				<a href="#" onclick="openEditDialog(<?php echo $row->id; ?>, '<?php echo $row->name; ?>','<?php echo $row->sqlfile; ?>','<?php echo $row->basicuri; ?>',<?php echo $row->ontology_id; ?>,'<?php echo $row->ontologyName; ?>');">
					<i class="edit purple icon" title="Edit data source"></i>
				</a>
				<a href="<?php echo base_url();?>index.php/datasource/delete/<?php echo $row->id; ?>" onclick="return confirm('Are you sure?');">
					<i class="delete red icon"  title="Delete data source"></i>
				</a>
			</td>
		</tr>
	<?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr><th colspan="7"><br/>
      <div class="ui tiny button" onMouseUp="$('.ui.new.modal').modal('show');">Create new data source</div>
    </th>
  </tr></tfoot>
</table>
</div>

<!-- MODAL BOX -->



<!-- MODAL BOX -->

<div class="ui new modal">
	<div class="ui small blue header">Create new data source</div>
	
	<div class="content">



		<div class="ui pointing secondary demo menu">
			<a id="tab_sql" class="active red item" onclick="uploadType(0);">Sql file</a>
			<a id="tab_schema" class="blue item" onclick="uploadType(1);" >Schema file</a>
		</div>

	<div class="ui form segment" >
	<?php 	echo form_open_multipart(base_url().'index.php/datasource/createnew_post');	?>
	
		<div class="field">
			<label>Name</label>
			<div class="ui input">
				<input type="text" id="input_name" name="input_name" placeholder="Name of the data source...">
			</div>
		</div>

		<div class="field">
			<label id="file_label" >SQL file</label>
		
			<div class="field">
				<div class="ui action input">
					<input type="text" id="_attachmentName" readonly>
					<label for="input_attachment" class="ui icon button btn-file" style="max-width: 35px;">
						 Select file&nbsp;
						 <input type="file" id="input_attachment" name="input_attachment" style="display: none" onchange="showFileName();">
					</label>
				</div>
			</div>
		</div>
		<input type="hidden" id="form_file_type" name="file_type" value="sql">

		<div class="field">
			<label>Base URI</label>
			<input placeholder="http://base_uri/.../" id="input_basicuri" name="input_basicuri" type="text">
		</div>


		<div class="field">
			<label>Target ontology</label>
			<div class="ui selection dropdown select-language">
				<input name="input_ontology" id="input_ontology" type="hidden" value="fr-FR">
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
		<input id="download_schema_parser" value="Download schema creator tool" class="ui tiny left floated button" style="display: none; width: 180px;" onclick="window.open('<?php echo base_url(); ?>/public/tools/MaponSql.zip')" />
		<input type="submit" value="Create" class="ui tiny button" />
		<div class="ui tiny deny button">Cancel</div>
	</div>

	<?php echo form_close(); ?>
</div>


<div class="ui edit modal">
	<div class="ui small blue header">Edit mapping space</div>

	<div class="content">

	<div class="ui form segment" >
	<?php 	echo form_open_multipart(base_url().'index.php/datasource/edit_post');?>

		<input type="hidden" id="datasource_id" name="datasource_id" value="1223">
		<div class="field">
			<label>Name</label>
			<div class="ui input">
				<input type="text" id="input_edit_name" name="input_edit_name" placeholder="Name of the data source...">
			</div>
		</div>

		<div class="field">
			<label>SQL file</label>
			<input type="text" id="edit_attachmentName" readonly>
		</div>

		<div class="field">
			<label>Base URI</label>
			<input placeholder="http://base_uri/.../" id="input_edit_basicuri" name="input_edit_basicuri" type="text">
		</div>

		<div class="field">
			<label>Target ontology</label>
			<div class="ui selection dropdown edit-ontology">
				<input name="input_edit_ontology" id="input_edit_ontology" type="hidden" value="fr-FR">
				<div class="text" id="input_edit_ontology_name">Select ontology...</div>
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



	<div class="actions">
		<input type="submit" value="Edit data source" class="ui tiny button" /> <div class="ui tiny deny button">Cancel</div>
	</div>

	</div>
	<?php echo form_close(); ?>

</div>


<script>
	$('.ui.dropdown')
	  .dropdown()
	;

	function openEditDialog(id, name, sqlfile, basicuri, ontologyID, ontologyName) {

		document.getElementById('datasource_id').value = id;
		document.getElementById('input_edit_name').value = name;
		document.getElementById('edit_attachmentName').value = sqlfile;
		document.getElementById('input_edit_basicuri').value = basicuri;
		document.getElementById('input_edit_ontology').value = ontologyID;
		document.getElementById('input_edit_ontology_name').innerHTML = ontologyName;

		$('.ui.edit.modal').modal('show');
	}

	function showFileName() {
		var fil = document.getElementById("input_attachment");
		$('#_attachmentName').val(fil.value);
	}
	$('#_attachmentName').val("");

	function uploadType( type ) {
		$('#tab_sql, #tab_schema').removeClass('active');

		if ( type == 0 ) {
			// SQL
			$('#tab_sql').addClass('active');
			$('#file_label').text("SQL file");
			$('#form_file_type').val("sql");
			$('#download_schema_parser').css('display', 'none');
		} else {
			// Schema
			$('#tab_schema').addClass('active');
			$('#file_label').text("Schema file");
			$('#form_file_type').val("schema");
			$('#download_schema_parser').css('display', 'block');
		}
	}

</script>