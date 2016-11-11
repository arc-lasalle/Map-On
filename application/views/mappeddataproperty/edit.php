<div id="content">
		
	<div id="title">Edit data property</div>
	
	<?php 
		echo form_open_multipart(base_url().'index.php/mappeddataproperty/edit_post');
		echo form_hidden('dataproperty_id', $dataproperty_id);
		echo form_hidden('datasource_id', $datasource_id);
		echo form_hidden('mappingspace_id', $mappingspace_id);
		echo form_hidden('mappedclass_id', $mappedclass_id);
	?>
	        <table>
            <tr>
                <td>
                    <?php echo form_label('Dataproperty'); ?>
                </td>
                <td>
                    <?php 
                    echo form_input(array(
								'name'  => 'input_dataproperty',
								'id'    => 'input_dataproperty',
								'size'	=> '100',
								'value'    => "$dataproperty",
								'autocomplete' => 'off',
								'onkeyup' => "chk_suggestDataproperty();",
								)); 
                    ?>
					<input id="hidden_search_inputtext_dataproperty" name="hidden_search_inputtext_dataproperty" type="hidden" value = "">
					<div id="suggest_Dataproperty" class="search_box_dataproperty"> </div>
                </td>
            </tr>
			<tr>
                <td>
                    <?php echo form_label('Table/Column source'); ?>
                </td>
                <td>
                    <?php 
                    echo form_input(array(
								'name'  => 'input_table',
								'id'    => 'input_table',
								'size'	=> '100',
								'autocomplete' => 'off',
								'onkeyup' => "chk_suggestTable();",
								'value' => $mappedtablecolumn,
								)); 
                    ?>
					<input id="hidden_search_inputtext_table" name="hidden_search_inputtext_table" type="hidden" value = "">
					<div id="suggest_Table" class="search_box_table"> </div>
                </td>
            </tr>
			<tr>
                <td>
                    <?php echo form_label('Value'); ?>
                </td>
                <td>
                    <?php 
                    echo form_textarea(array(
								'name'  => 'input_value',
								'id'    => 'input_value',
								'rows'    => '1',
								'cols'    => '75',
								'value'    => "$value",
								)); 
                    ?>
                </td>
            </tr>
			<tr>
                <td>
                    <?php echo form_label('type'); ?>
                </td>
                <td>
                    <?php 
                    echo form_input(array(
								'name'  => 'input_type',
								'id'    => 'input_type',
								'size'	=> '100',
								'value'    => "$type",
							)); 
                    ?>
                </td>
            </tr>
			<tr>
                <td>
					<br/>	
					<div class="buttons"><button type="submit" class="positive" name="create">Edit mapped class</button></div>
                </td>
				<td><br/>
					<div class="buttons"><a href="<?php echo base_url();?>index.php/mappingspace/graph/<?php echo $datasource_id."/".$mappingspace_id; ?>" class="negative">Cancel</a></div>
				</td>
            </tr>
        </table>
        <?php echo form_close(); ?>
</div>

<script>
	///////////////////////////////////////////////////
	// JS functions for Datatype selection
	//
	var timer;
	
	function chk_suggestDataproperty(){
        clearTimeout(timer);
        timer=setTimeout("suggestDataproperty(0)",1000);
    }
	
	function suggestDataproperty() {
		document.getElementById('hidden_search_inputtext_dataproperty').value = "";
	
		if(document.getElementById('input_dataproperty').value != ""){   

			$("#suggest_Dataproperty").load('<?php echo site_url("mapping/suggestdataproperty"); ?>', { string: document.getElementById('input_dataproperty').value, class: '<?php echo $uriMappedClass; ?>' } );

			var position = $("#input_dataproperty").position();
			document.getElementById('suggest_Dataproperty').style.top = position.top+22 + "px";
			document.getElementById('suggest_Dataproperty').style.left = position.left + "px";
			if( $("#suggest_Dataproperty").is(":hidden") ) $("#suggest_Dataproperty").fadeIn();
		}else{
			$("#suggest_Dataproperty").fadeOut();
		}
	}
	
	function add_search_box_Dataproperty(string_uri){
		document.getElementById('input_dataproperty').value = string_uri;
		document.getElementById('hidden_search_inputtext_dataproperty').value = string_uri;
		$("#suggest_Dataproperty").fadeOut();
    }
	
	///////////////////////////////////////////////////
	// JS functions for Table selection
	//
	function chk_suggestTable(){
        clearTimeout(timer);
        timer=setTimeout("suggestTable(0)",1000);
    }
	
	function suggestTable() {
		document.getElementById('hidden_search_inputtext_table').value = "";
	
		if(document.getElementById('input_table').value != ""){   

			$("#suggest_Table").load('<?php echo site_url("mapping/suggestcolumn"); ?>', { string: document.getElementById('input_table').value, sourcetable_id: <?php echo $sourcetable_id; ?>, sourcetable_name: '<?php echo $sourcetable_name; ?>', datasource_id: <?php echo $datasource_id; ?> } );

			var position = $("#input_table").position();
			document.getElementById('suggest_Table').style.top = position.top+22 + "px";
			document.getElementById('suggest_Table').style.left = position.left + "px";
			if( $("#suggest_Table").is(":hidden") ) $("#suggest_Table").fadeIn();
		}else{
			$("#suggest_Table").fadeOut();
		}
	}
	
	function add_search_box_Table(string_uri){
		document.getElementById('input_table').value = string_uri;
		document.getElementById('hidden_search_inputtext_table').value = string_uri;
		$("#suggest_Table").fadeOut();
		
		//To autocomplete the VALUE section
		$("#input_value").load('<?php echo site_url("mapping/generateDatapropertyValue"); ?>', { input_table: string_uri, datasource_id: <?php echo $datasource_id; ?> } );
    }
</script>