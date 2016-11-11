

<div class="ui green segment">

	Data source: <span style="font-weight:bold;"><?php echo $datasource->name; ?></span><br />
	Ontology: <span style="font-weight:bold;"><?php echo $ontologyName; ?></span><br />
	Modified: <span style="font-weight:bold;"><?php echo $datasource->date; ?></span><br />

</div>

<!--div class="ui green segment">
	<h3 class="ui header">Export options</h3>
	<div class="ui form">
		<div class="grouped fields">
			<div class="field">
				<div class="ui checkbox">
					<input type="checkbox" name="example" disabled> <label>Short alias (Oracle allows max 30char alias)</label>
				</div>
			</div>
			<div class="field">
				<div class="ui checkbox">
					<input type="checkbox" name="example" disabled> <label>Save mappingspaces names and node positions.</label>
				</div>
			</div>
		</div>
	</div>
	<br/>
	<button class="ui small button">
		Generate R2RML
	</button>
	<button class="ui small button" onclick="location.href='<?php echo base_url();?>index.php/r2rml/edit/<?php echo $datasource_id; ?>'">
		Edit manually
	</button>
	<button class="ui small button" onclick="location.href='<?php echo base_url();?>/<?php echo $filename; ?>'">
		Get file
	</button>
</div-->

<div class="ui green segment">
	<div class="title">
		R2RML output file <span style="font-weight:bold;"><a href="<?php echo base_url();?>/<?php echo $filename; ?>">R2RML</a></span>
		<br />
		Edit manually a <span style="font-weight:bold;"><a href="<?php echo base_url();?>index.php/r2rml/edit/<?php echo $datasource_id; ?>">R2RML part</a></span>
		<br />
	</div>
	<br />

	<textarea id="code" name="code" readonly>
		<?php echo $r2rmlcode; ?>
	</textarea>

	<script>
		var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
			mode: "text/turtle",
			matchBrackets: true,
			lineNumbers: true,
			lineWrapping: true,
			readOnly: 'nocursor',
			theme: 'neat'
		});
		editor.setSize("100%", "100%");
	</script>

</div>
