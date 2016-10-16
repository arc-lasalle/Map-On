

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
    <div class="title">R2RML output file <span style="font-weight:bold;"><a href="<?php echo base_url();?>/<?php echo $filename; ?>">R2RML</a></span><br /> Edit manually a <span style="font-weight:bold;"><a href="<?php echo base_url();?>index.php/r2rml/edit/<?php echo $datasource_id; ?>">R2RML part</a></span> <br /></div><br />
	<form><textarea id="code" name="code" readonly>
	<?php 
	echo $r2rmlcode; 
	?> 
	</textarea></form>
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
