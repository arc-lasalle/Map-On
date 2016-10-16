<div id="content">
		
	<div id="title">Edit object property</div>
	
	<?php 
		echo form_open_multipart(base_url().'index.php/mappedobjectproperty/edit_post');
		echo form_hidden('objectproperty_id', $objectproperty_id);
		echo form_hidden('datasource_id', $datasource_id);
		echo form_hidden('mappingspace_id', $mappingspace_id);
		echo form_hidden('mappedclass_id', $mappedclass_id);
	?>
	    <table>
            <tr>
                <td>
                    <?php echo form_label('Object property'); ?>
                </td>
                <td>
                    <?php 
                    echo form_input(array(
								'name'  => 'input_objectproperty',
								'id'    => 'input_objectproperty',
								'size'	=> '100',
								'autocomplete' => 'off',
								'onkeyup' => "chk_suggestObjectproperty();",
								'value' => "$objectproperty",
								)); 
                    ?>
					<input id="hidden_search_inputtext_objectproperty" name="hidden_search_inputtext_objectproperty" type="hidden" value = "">
					<div id="suggest_Objectproperty" class="search_box_objectproperty"> </div>
                </td>
            </tr>
			<tr>
                <td>
                    <?php echo form_label('Uri'); ?>
                </td>
                <td>
                    <?php 
                    echo form_textarea(array(
								'name'  => 'input_uri',
								'id'    => 'input_uri',
								'rows'    => '2',
								'cols'    => '75',
								'value' => "$uri",
								)); 
                    ?>
                </td>
            </tr>
			<tr>
                <td>
					<br/>	
					<div class="buttons"><button type="submit" class="positive" name="create">Edit object property</button></div>
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
	
	function chk_suggestObjectproperty(){
        clearTimeout(timer);
        timer=setTimeout("suggestObjectproperty(0)",1000);
    }
	
	function suggestObjectproperty() {
		document.getElementById('hidden_search_inputtext_objectproperty').value = "";
	
		if(document.getElementById('input_objectproperty').value != ""){   

			$("#suggest_Objectproperty").load('<?php echo site_url("mapping/suggestobjectproperty"); ?>', { string: document.getElementById('input_objectproperty').value, class: '<?php echo $uriMappedClass; ?>', datasource_id: <?php echo $datasource_id; ?> } );

			var position = $("#input_objectproperty").position();
			document.getElementById('suggest_Objectproperty').style.top = position.top+22 + "px";
			document.getElementById('suggest_Objectproperty').style.left = position.left + "px";
			if( $("#suggest_Objectproperty").is(":hidden") ) $("#suggest_Objectproperty").fadeIn();
		}else{
			$("#suggest_Objectproperty").fadeOut();
		}
	}
	
	function add_search_box_Objectproperty(string_uri){
		document.getElementById('input_objectproperty').value = string_uri;
		document.getElementById('hidden_search_inputtext_objectproperty').value = string_uri;
		$("#suggest_Objectproperty").fadeOut();
		
		//To autocomplete the VALUE section
		$("#input_uri").load('<?php echo site_url("mapping/generateObjectpropertyURI"); ?>', { input_object: string_uri, datasource_id: <?php echo $datasource_id; ?> } );
    }
	
	
		
	
	
</script>