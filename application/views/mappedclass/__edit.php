<div id="content">
		
	<div id="title">Edit mapped class</div>
	
	<?php 
		echo form_open_multipart(base_url().'index.php/mappedclass/edit_post');
		echo form_hidden('mappedclass_id', $mappedclass_id);
		echo form_hidden('datasource_id', $datasource_id);
		echo form_hidden('mappingspace_id', $mappingspace_id);
	?>
	        <table>
            <tr>
                <td>
                    <?php echo form_label('Class'); ?>
                </td>
                <td>
                    <?php 
                    echo form_input(array(
								'name'  => 'input_class',
								'id'    => 'input_class',
								'autocomplete' => 'off',
								'size'	=> '100',
								'value'    => "$class",
								'onkeyup' => "chk_suggestClass();",
								)); 
                    ?>
					<input id="hidden_search_inputtext_class" name="hidden_search_inputtext_class" type="hidden" value = "">
					<div id="suggest_Class" class="search_box_class"> </div>
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
								'value'    => "$table",
								)); 
                    ?>
					<input id="hidden_search_inputtext_table" name="hidden_search_inputtext_table" type="hidden" value = "">
					<div id="suggest_Table" class="search_box_table"> </div>
                </td>
            </tr>
			<tr>
                <td>
                    <?php echo form_label('SQL'); ?>
                </td>
                <td>
                    <?php 
                    echo form_textarea(array(
								'name'  => 'input_sql',
								'id'    => 'input_sql',
								'autocomplete' => 'off',
								'rows'    => '3',
								'cols'    => '75',
								'value'    => "$sql",
								)); 
                    ?>
                </td>
            </tr>
			<tr>
                <td>
                    <?php echo form_label('URI'); ?>
                </td>
                <td>
                    <?php 
                    echo form_textarea(array(
								'name'  => 'input_uri',
								'id'    => 'input_uri',
								'autocomplete' => 'off',
								'rows'    => '2',
								'cols'    => '75',
								'value'    => "$uri",
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
	
	function chk_suggestClass(){
        clearTimeout(timer);
        timer=setTimeout("suggestClass(0)",1000);
    }
	
	function suggestClass() {
		document.getElementById('hidden_search_inputtext_class').value = "";
	
		if(document.getElementById('input_class').value != ""){   

			$("#suggest_Class").load('<?php echo site_url("mapping/suggestclass"); ?>', { string: document.getElementById('input_class').value, datasource_id: <?php echo $datasource_id; ?> } );

			var position = $("#input_class").position();
			document.getElementById('suggest_Class').style.top = position.top+22 + "px";
			document.getElementById('suggest_Class').style.left = position.left + "px";
			if( $("#suggest_Class").is(":hidden") ) $("#suggest_Class").fadeIn();
		}else{
			$("#suggest_Class").fadeOut();
		}
	}
	
	function add_search_box_Class(string_uri){
		document.getElementById('input_class').value = string_uri;
		document.getElementById('hidden_search_inputtext_class').value = string_uri;
		$("#suggest_Class").fadeOut();
		
		//To autocomplete the URI section
		$("#input_uri").load('<?php echo site_url("mapping/generateURI"); ?>', { input_class: document.getElementById('input_class').value, input_table: document.getElementById('input_table').value, datasource_id: <?php echo $datasource_id; ?> } );
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

			$("#suggest_Table").load('<?php echo site_url("mapping/suggesttable"); ?>', { string: document.getElementById('input_table').value, datasource_id: <?php echo $datasource_id; ?> } );

			var position = $("#input_table").position();
			document.getElementById('suggest_Table').style.top = position.top+22 + "px";
			document.getElementById('suggest_Table').style.left = position.left + "px";
			if( $("#suggest_Table").is(":hidden") ) $("#suggest_Table").fadeIn();
		}else{
			$("#suggest_Table").fadeOut();
		}
	}
	
	function add_search_box_Table(target, string_uri){

		document.getElementById('input_table').value = string_uri;
		document.getElementById('hidden_search_inputtext_table').value = string_uri;
		$("#suggest_Table").fadeOut();
		
		//To autocomplete the SQL section
		$("#input_sql").load('<?php echo site_url("mapping/generateSQL"); ?>', { input_table: string_uri, datasource_id: <?php echo $datasource_id; ?> } );
		//To autocomplete the URI section
		$("#input_uri").load('<?php echo site_url("mapping/generateURI"); ?>', { input_class: document.getElementById('input_class').value, input_table: document.getElementById('input_table').value, datasource_id: <?php echo $datasource_id; ?> } );
    }
	
</script>