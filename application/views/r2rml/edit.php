
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

<div class="ui green segment">
    <div class="title">R2RML output file <span style="font-weight:bold;"><a href="<?php echo base_url();?>/<?php echo $filename; ?>">R2RML</a></span></div>
	
	<div id="content">
			
		
		<div class="ui form segment" >
		<label><strong>R2RML Prefixes:</strong></label>

		<form><textarea id="prefixes" name="prefixes" style="height: 200px;">
		<?php echo $r2rmlprefixes; ?> 
		</textarea></form>

		<?php 	echo form_open_multipart(base_url().'index.php/r2rml/edit_post');	
				echo form_hidden('datasource_id', $datasource_id);
		?>
			<div class="field">
				<label><strong>R2RML part:</strong></label>
				<div class="ui input" >
					<div style="border: 1px solid rgb(200,200,200);">

					<textarea type="text" id="input_r2rmlpart" rows="2"  name="input_r2rmlpart" ><?php echo $r2rmlpart ; ?></textarea>
					</div>
				</div>
			</div>
			
			<div class="actions">
				<div class="ui labeled tiny deny button">Cancel</div> <input type="submit" value="Edit" class="ui labeled tiny button" />
			</div>
		<br />
		<label><strong>R2RML automatically generated:</strong></label>
		<?php echo form_close(); ?>
		<form ><textarea id="code" name="code" rows="3">
		<?php echo $r2rmlcode; ?> 
		</textarea></form>
		
		</div>
	</div>
</div>




	<script>
		var editor = CodeMirror.fromTextArea(document.getElementById("prefixes"), {
			mode: "text/turtle",
			matchBrackets: true,
			readOnly: 'nocursor',
			theme: 'neat'
			});
			
		editor.setSize("100%", 100);
		
		var editor = CodeMirror.fromTextArea(document.getElementById("input_r2rmlpart"), {
			mode: "text/turtle",
			matchBrackets: true,
			lineNumbers: true,
			theme: 'neat'

			});
			
		editor.setSize("100%", "100%");


		var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
			mode: "text/turtle",
			matchBrackets: true,
			readOnly: 'nocursor',
			theme: 'neat'
			});	
		
		editor.setSize("100%", 600);
    </script>
 	
