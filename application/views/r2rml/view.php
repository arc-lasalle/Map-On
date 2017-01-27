

<div class="ui green segment">

	Data source: <span style="font-weight:bold;"><?php echo $datasource->name; ?></span><br />
	Ontology: <span style="font-weight:bold;"><?php echo $ontologyName; ?></span><br />
	Modified: <span style="font-weight:bold;"><?php echo $datasource->date; ?></span><br />

</div>

<div class="ui green segment">
	<h3 class="ui header">Export options</h3>
	<div class="ui form">
		<div class="grouped fields">
			<div class="field">
				<div class="ui checkbox" id="checkbox_short_alias">
					<input type="checkbox" name="example"> <label>Short alias (Oracle allows max 30char alias)</label>
				</div>
			</div>
			<div class="field">
				<div class="ui checkbox">
					<input type="checkbox" name="example" disabled> <label>Save mappingspace names and node positions.</label>
				</div>
			</div>
		</div>
	</div>
	<br/>
	<button class="ui small button" onclick="location.href='<?php echo base_url();?>index.php/r2rml/edit/<?php echo $datasource_id; ?>'">
		Edit manually a R2RML part
	</button>
	<button class="ui small button" onclick="location.href='<?php echo base_url();?>/<?php echo $filename; ?>'">
		Download file
	</button>
    <button class="ui small button" id="btn_copy_clipoard">
        Copy to clipboard
    </button>
</div>


<div class="ui green segment">

	<div id="theCode"></div>
    <textarea id="clipboardCode" hidden></textarea>

</div>

<script>

    var php_vars = {};
    php_vars.base_url = '<?php echo base_url(); ?>';
    php_vars.datasource_id = '<?php echo $datasource->id; ?>';


    $('#checkbox_short_alias').checkbox({
        onChecked: function() { getCode( true ); },
        onUnchecked: function() { getCode( false ); }
    });

    function getCode( shortAlias ) {
        $('#theCode').html('Loading...');

        var url = php_vars.base_url + 'index.php/r2rml/exportR2RML/'+php_vars.datasource_id+'/1/0';
        if ( shortAlias ) url = php_vars.base_url + 'index.php/r2rml/exportR2RML/'+php_vars.datasource_id+'/1/1';

        $.ajax({ type: "GET", url: url, success: setCode });
    }

    function setCode( code ) {

        $('#theCode').html('<textarea id="code" name="code" readonly></textarea>');
        $('#code').text(code);
        $('#clipboardCode').text(code);

        var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
            mode: "text/turtle",
            matchBrackets: true,
            lineNumbers: true,
            lineWrapping: true,
            readOnly: 'nocursor',
            theme: 'neat'
        });
        editor.setSize("100%", "100%");
    }


    document.querySelector('#btn_copy_clipoard').addEventListener('click', function(event) {
        $('#clipboardCode').show();

        try {
            document.querySelector('#clipboardCode').select();
            document.execCommand('copy');
        } catch (err) {
            console.log('Oops, unable to copy');
        }

        $('#clipboardCode').hide();
    });

    getCode( false );
</script>


