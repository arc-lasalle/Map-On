<!-- CSS's -->
<link REL=StyleSheet HREF="<?php echo base_url(); ?>/public/css/common/edition_area.css" TYPE="text/css" MEDIA=screen>

<!-- WebVOWL CSS's -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/webvowl/webvowl.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/webvowl/webvowl.app.css" />

<!-- Database Graph CSS's -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/dbgraph/wwwsqldesigner.css" />


<div class="ui stackable grid">
	<div id="left_grid" class="five wide column">
		<div class="ui green segment">

			<!-- Mapping Options -->
			<div id="options_1_mapping">
				<div class="ui small header">
					<strong><?php echo $dataproperty_id == 0 ? "Add data property to \"".$class."\"" : "Edit \"".$class."\" data property"; ?></strong>
				</div>
				<div class="ui form secondary accordion fluid segment" >
					<?php 	echo form_open_multipart(base_url().'index.php/mappeddataproperty/addnew_post');
							echo form_hidden('mappingspace_id', $mappingspace_id);
							echo form_hidden('datasource_id', $datasource_id);
							echo form_hidden('mappedclass_id', $mappedclass_id);
							echo form_hidden('mappedclass', $class);
							echo form_hidden('dataproperty_id', $dataproperty_id);
					?>

					<div class="field">
						<label>Data property</label>
						<div class="ui icon input" data-content="Type for searching a data property of the ontology">
							<input type="text" id="input_dataproperty" name="input_dataproperty" class="gl_clear_default" autocomplete="off" value="<?php echo $dataproperty_id == 0 ? "Data property name...": $dataproperty ; ?>" onkeyup="chk_suggestDataproperty();" onclick="chk_suggestDataproperty(0);">
							<i class="search icon"></i>
						</div>
						<input id="hidden_search_inputtext_dataproperty" name="hidden_search_inputtext_dataproperty" type="hidden" value = "">
						<div id="suggest_Dataproperty" class="search_box_dataproperty gl_clickOut_hide">
							<div class="ui inverted active dimmer">
								<div class="ui mini text loader">Searching....</div>
							</div><br/><br/>
						</div>
					</div>
					<div class="field">
						<label>Table/Column source</label>
						<div class="ui icon input" data-content="Type for searching a column of the data source" >
							<input type="text" id="input_table" name="input_table" class="gl_clear_default" autocomplete="off" value="<?php echo $table ; ?>" onkeyup="chk_suggestTable();" onclick="chk_suggestTable(0);">
							<i class="search icon"></i>
						</div>
						<input id="hidden_search_inputtext_table" name="hidden_search_inputtext_table" type="hidden" value = "">
						<div id="suggest_Table" class="search_box_table gl_clickOut_hide">
							<div class="ui inverted active dimmer">
								<div class="ui mini text loader">Searching....</div>
							</div><br/><br/>
						</div>
					</div>


					<div class="field">
						<div class="title">
							<i class="icon dropdown"></i>
							Value and Type details
						</div>
						<div class="content field">
							<div class="field">
								<label>Value</label>
								<div class="ui  input" data-content="Write the value for the mapping. When a column of the data source is selected the value  is automatically set">
									<textarea type="text" id="input_value" rows="2"  name="input_value" ><?php echo $value ; ?></textarea>
								</div>

							</div>
							<div class="field">
								<label>type</label>
								<div class="ui input" data-content="Write the type of the data property. When a column of the data source is selected the type is is automatically derived from the type of the column">
									<textarea type="text" id="input_type" name="input_type"><?php echo $type ; ?></textarea>
								</div>
							</div>
						</div>
					</div>

					<div class="actions">
						 <input type="submit" value="<?php echo $dataproperty_id == 0 ? "Add": "Edit"; ?>" class="ui tiny button" /> <div class="ui tiny button" style= "right: 11px; position: absolute;"  onmouseup="window.location.href = '<?php echo base_url();?>index.php/mappingspace/graph/<?php echo $datasource_id."/".$mappingspace_id; ?>';">Cancel</div>
					</div>
					<div class="ui error message"></div>
				</div>

				<?php echo form_close(); ?>

				<div class="ui small header">
					<strong>Filtering tables of the data source</strong>
				</div>

				<div id="table_buttons"></div>
			</div>

			<!-- Ontology Options -->
			<div id="options_2_ontology">
				<div style="height: 100%;">
					<br />
					<h1 style="margin: 0px; font-weight: bold; text-align: justify;">Select the data property</h1>
					<!--h1 class="webvowl_title" style="margin: 0px; font-weight: bold; text-align: justify;">Ontology title</h1-->
					<br />
					<div class="ui styled accordion" style="margin: 2px; width: 95%;">
						<div class="title" style="background: #faf9fa;"><i class="dropdown icon"></i>Vocabulary info</div>
						<div id="sidebar_ontology_info" class="content">
							<!-- Filled by the function cbk_showOntologyInfo from addnew.js -->
						</div>

						<div id="tab_node_info" class="title active" style="background: #faf9fa;"><i class="dropdown icon"></i>Selected item</div>
						<div class="content active">
							<div id="sidebar_ontology_element_info">
								<div id="selection-details">
									<div id="classSelectionInformation" class="hidden">
										<p><b>Name: </b><span id="name"></span></p>
										<p><b>Type: </b><span id="typeNode"></span></p>
										<p><b>Equiv.: </b><span id="classEquivUri"></span></p>
										<p><b>Disjoint: </b><span id="disjointNodes"></span></p>
										<p><b>Charac.: </b><span id="classAttributes"></span></p>
										<p><b>Individuals: </b><span id="individuals"></span></p>
										<p><b>Description: </b><span id="nodeDescription"></span></p>
										<p><b>Comment: </b><span id="nodeComment"></span></p>
									</div>
									<div id="propertySelectionInformation" class="hidden">
										<p><b>Name: </b><span id="propname"></span></p>
										<p><b>Type: </b><span id="typeProp"></span></p>
										<p id="inverse"><b>Inverse: </b><span></span></p>
										<p><b>Domain: </b><span id="domain"></span></p>
										<p><b>Range: </b><span id="range"></span></p>
										<p><b>Subprop.: </b><span id="subproperties"></span></p>
										<p><b>Superprop.: </b><span id="superproperties"></span></p>
										<p><b>Equiv.: </b><span id="propEquivUri"></span></p>
										<p id="infoCardinality"><b>Cardinality: </b><span></span></p>
										<p id="minCardinality"><b>Min. cardinality: </b><span></span></p>
										<p id="maxCardinality"><b>Max. cardinality: </b><span></span></p>
										<p><b>Charac.: </b><span id="propAttributes"></span></p>
										<p><b>Description: </b><span id="propDescription"></span></p>
										<p><b>Comment: </b><span id="propComment"></span></p>
									</div>
									<div id="noSelectionInformation">
										<p><span>Select an element in the visualization.</span></p>
									</div>
								</div>
							</div>

							<div id="sidebar_ontology_element_buttons" style="margin-top: 20px;">

								<div style="text-align: center;">
									<button id="sidebar_ontology_btn" class="ui primary button hidden">
										<b>Select data property</b>
									</button>
								</div>

								<div id="sidebar_ontology_warning" class="ui red message hidden">
									<i class="warning sign icon"></i>
									<span id="sidebar_ontology_warning_msg"></span>
								</div>

							</div>

						</div>

						<div class="title" style="background: #faf9fa;"><i class="dropdown icon"></i>Search</div>
						<div class="content">
							<div class="ui form secondary fluid" >

								<div class="field">
									<label>Class</label>
									<div class="ui icon input" data-content="Type for searching a class of the ontology" >
										<input type="text" class="ea_class_search_box gl_clear_default gl_clickOut_disable" autocomplete="off" value="<?php echo empty($class) ? "Class name...": $class ; ?>">
										<i class="search icon gl_clickOut_disable"></i>
									</div>
									<div class="ea_class_search_results ea_search_box gl_clickOut_hide"></div>
								</div>

								<div class="field">
									<label>Data property</label>
									<div class="ui icon input" data-content="Type for searching a data property of the ontology">
										<input type="text" class="ea_dataproperty_search_box gl_clear_default gl_clickOut_disable" autocomplete="off" value="<?php echo empty($dataproperty) ? "Data property name...": $dataproperty ; ?>">
										<i class="search icon gl_clickOut_disable"></i>
									</div>
									<div class="ea_dataproperty_search_results ea_search_box gl_clickOut_hide"></div>
								</div>

							</div>
						</div>

					</div>
				</div>
			</div>

			<!-- Database Options -->
			<div id="options_3_database">
				<div style="height: 100%;">
					<br />
					<h1 style="margin: 0px; font-weight: bold; text-align: justify;">Select a row</h1>
					<br />
					<div class="ui styled accordion" style="margin: 2px; width: 95%;">

						<div id="tab_row_info" class="title active" style="background: #faf9fa;"><i class="dropdown icon"></i>Selected item</div>
						<div class="content active" >
							<div id="sidebar_dbgraph_info">
								<!-- Filled by the function cbk_dbgraph_showElementInfo from addnew.js -->
								<br>Select a row.<br>
							</div>
							<br>
							<div  style="text-align: center;">
								<button id="sidebar_dbgraph_btn_select" class="ui primary button hidden">
									<b>Select row</b>
								</button>
							</div>
							<div id="sidebar_dbgraph_warning" class="ui red message hidden">
								<i class="warning sign icon"></i> <span id="sidebar_dbgraph_warning_msg"></span>
							</div>
						</div>

						<div class="title" style="background: #faf9fa;"><i class="dropdown icon"></i>Search</div>
						<div class="content">
							<div class="ui form secondary fluid" >

								<div class="field">
									<label>Table/Column source</label>
									<div class="ui icon input" data-content="Type for searching a column of the data source" >
										<input type="text" class="ea_table_search_box gl_clear_default gl_clickOut_disable" autocomplete="off" value="Column name...">
										<i class="search icon gl_clickOut_disable"></i>
									</div>
									<div class="ea_table_search_results ea_search_box gl_clickOut_hide"></div> <!-- search_box_table-->
								</div>

							</div>
						</div>

					</div>
				</div>
			</div>

		</div>
	</div>
	<div id="right_grid" class="eleven wide column">
		<div class="ui green segment g_right_col">
			<div class="ui small header">
				<table width="100%"><tr><td>
							<i id="horizontal_collapse" style="margin-left: -30px; position: fixed; color: gray;" class="caret left icon link"></i>
					<div class="right_colum_title"><strong>Data source graph representation:</strong> <i>click on a column item to map it to a class</i></div>
					</td><td style="text-align: right">		
						<i class="circular history purple link icon" onclick="sidebar();" data-position="top left" data-html="click to too see the history log"></i>
					</td></tr></table>
			</div>

			<div class="content field">

				<div id="ea_loader" class="hidden"></div>

				<!-- Mapping Graph -->
				<div id="graph_1_mapping" style="overflow: hidden">
					<?php echo $graph; ?>
				</div>

				<!-- Ontology Graph - WebVOWL -->
				<div id="graph_2_ontology" style="overflow: hidden">

					<section id="canvasArea" class="g_right_col_graph">
						<div id="graph"></div>
					</section>

				</div>

				<!-- Database Graph -->
				<div id="graph_3_database" style="overflow: hidden; background-color: #e8e8e8">
					<div id="container" class="g_right_col_graph" style="position: relative; overflow: hidden;">
						<div id="area" class="dbgraph"></div>
					</div>
				</div>

				<!-- Buttons - Mappings/Ontology/Database -->
				<div style="margin-top: 5px;">
					<div style="float: left;">
						<a class="item" onclick="$('.ui.modal').modal('show');" style="cursor: pointer;">Legend</a>
					</div>
					<div style="float: right;">
						<div class="ui secondary menu g_view_buttons">
							<a id="btn_show_mapping" class="active item">Mappings</a>
							<a id="btn_show_ontology" class="item">Ontology</a>
							<a id="btn_show_database" class="item">Database</a>
						</div>
					</div>
				</div>

			</div>

		</div>
	</div>
</div>

<!-- Modal for the legend -->
<div class="ui modal small">
	<div class="header">Legend</div>
	<div class="image content"><img src="<?php echo base_url() ?>/public/img/legend.png"></div>
	<div class="actions"></div>
</div>


<script>
	///////////////////////////////////////////////////
	// JS functions for Datatype selection
	//
	var timer;
	
	$('.ui.icon.input').popup({transition: 'scale'  });
	$('.ui.input').popup({transition: 'scale'  });

	$('.ui.accordion').accordion();
	$('.ui.checkbox').checkbox();
	
	function sidebar() {
		 $('.sidebar').sidebar('toggle');
	}


	var settings = {
		fields: {
			input_dataproperty: {
				identifier: 'input_dataproperty',
				rules: [
					{ type: 'check_data_property', prompt: 'Please enter a valid data property' },
					{ type: 'empty', prompt : 'Please enter the data property for creating the mapping'},
					{ type: 'not[Data property name...]', prompt : 'Please enter the data property for creating the mapping'	}
				]
			},
			input_table: {
				identifier: 'input_table',
				rules: [
					{ type: 'check_table', prompt: 'Please enter a valid table/column' },
					{ type: 'empty', prompt : 'Please enter the column of a table for creating the mapping'}
				]
			},
			input_value: {
				identifier: 'input_value',
				rules: [
					{ type: 'empty', prompt : 'Please enter the value for creating the mapping'}
				]
			},
			input_type: {
				identifier: 'input_type',
				rules: [
					{ type: 'empty', prompt : 'Please enter the type of the data property'},
					{ type: 'not[URI pattern...]', prompt : 'Please enter the type of the data property'}
				]
			}
		},
		rules: {
			check_data_property: function () {
				var found = false;

				$.ajax({
					async: false,
					type: "POST",
					url: "<?php echo site_url("mapping/checkDataProperty"); ?>",
					data: { datasource_id: <?php echo $datasource_id; ?>, dp_name: document.getElementById('input_dataproperty').value },
					success: function( data ) {
						if ( data == "true" ) found = true;
					}
				});
				return found;
			},
			check_table: function () {
				var found = false;

				$.ajax({
					async: false,
					type: "POST",
					url: "<?php echo site_url("mapping/checkTable"); ?>",
					data: { datasource_id: <?php echo $datasource_id; ?>, table: document.getElementById('input_table').value },
					success: function( data ) {
						if ( data == "true" ) found = true;
					}
				});
				return found;
			}
		}
	};


	$('.ui.form').form(settings);
	
	
	function chk_suggestDataproperty(time){
		if ( time === undefined ) time = 500;
        clearTimeout(timer);
        timer=setTimeout("suggestDataproperty(0)",time);
    }
	
	function suggestDataproperty() {
		document.getElementById('hidden_search_inputtext_dataproperty').value = "";

		// Remove error of previous form validation
		$("#suggest_Dataproperty").parent().removeClass("error");

		// Show loader until receiving load() data.
		$("#suggest_Dataproperty").html('<div class="ui inverted active dimmer"><div class="ui mini text loader">Searching....</div></div><br/><br/>');

		$("#suggest_Dataproperty").load('<?php echo site_url("mapping/suggestdataproperty"); ?>', { string: document.getElementById('input_dataproperty').value, class: '<?php echo $uriMappedClass; ?>', datasource_id: <?php echo $datasource_id; ?> } );

		var position = $("#input_dataproperty").position();
		document.getElementById('suggest_Dataproperty').style.top = position.top+66 + "px";
		document.getElementById('suggest_Dataproperty').style.left = position.left+12 + "px";
		if( $("#suggest_Dataproperty").is(":hidden") ) $("#suggest_Dataproperty").fadeIn();

	}
	
	function add_search_box_Dataproperty(string_uri){

		if ( typeof ont_add_search_box_Class == 'function' ) {
			if( ont_add_search_box_Dataproperty( string_uri ) ) return;
		}

		document.getElementById('input_dataproperty').value = string_uri;
		document.getElementById('hidden_search_inputtext_dataproperty').value = string_uri;
		$("#suggest_Dataproperty").fadeOut();


		var nodeName = $('#input_dataproperty').val();
		var table = $('#input_table').val().replace("->", "_").toLowerCase();

		mappingGraph.drawTempNode( nodeName, table, 'dataProperty' );
		mappingGraph.addLink( php_vars.mp_graph.classes[0].qname, 'tempnode', 'class-dataProperty' );

	}
	
	///////////////////////////////////////////////////
	// JS functions for Table selection
	//
	function chk_suggestTable(time){
		if ( time === undefined ) time = 500;
        clearTimeout(timer);
        timer=setTimeout("suggestTable(0)",time);
    }
	
	function suggestTable() {
		document.getElementById('hidden_search_inputtext_table').value = "";

		// Remove error of previous form validation
		$("#suggest_Dataproperty").parent().removeClass("error");

		// Show loader until receiving load() data.
		$("#suggest_Table").html('<div class="ui inverted active dimmer"><div class="ui mini text loader">Searching....</div></div><br/><br/>');

		$("#suggest_Table").load('<?php echo site_url("mapping/suggestcolumn"); ?>', { string: document.getElementById('input_table').value, sourcetable_id: <?php echo $sourcetable_id; ?>, sourcetable_name: '<?php echo $sourcetable_name; ?>', datasource_id: <?php echo $datasource_id; ?> } );

		var position = $("#input_table").offset();
		document.getElementById('suggest_Table').style.top = position.top -137 + "px";
		document.getElementById('suggest_Table').style.left = position.left -24 + "px";
		if( $("#suggest_Table").is(":hidden") ) $("#suggest_Table").fadeIn();

	}
	
	function add_search_box_Table(string_uri){

		if (typeof dbgraph_add_search_box_Table == 'function') {
			if (dbgraph_add_search_box_Table(string_uri)) return;
		}

		document.getElementById('input_table').value = string_uri;
		document.getElementById('hidden_search_inputtext_table').value = string_uri;
		$("#suggest_Table").fadeOut();
		
		//To autocomplete the VALUE section
		$.post('<?php echo site_url("mapping/generateDatapropertyValue"); ?>', { input_table: string_uri, datasource_id: <?php echo $datasource_id; ?> }, function(data) {
			$('#input_value').val(data);
		});

		$.post('<?php echo site_url("mapping/getColumnType"); ?>', { input_table: string_uri, datasource_id: <?php echo $datasource_id; ?> }, function(data) {
			$('#input_type').val(data);
		});


		var nodeName = $('#input_dataproperty').val();
		var table = $('#input_table').val().replace("->", "_").toLowerCase();

		mappingGraph.drawTempNode( nodeName, table, 'dataProperty' );
		mappingGraph.addLink( php_vars.mp_graph.classes[0].qname, 'tempnode', 'class-dataProperty' );


		var table = $('#input_table').val().toLowerCase().split("->");
		mappingGraph.toggleTable( table[0], true );
    }
	


	function add_search_box_Class(string_uri){
		if ( typeof ont_add_search_box_Class == 'function' ) {
			if( ont_add_search_box_Class( string_uri ) ) return;
		}
		// ...
	}
</script>

<!-- JS's -->
<script type="text/javascript">
	//var php_vars = JSON.parse(unescape('<?php echo addslashes( json_encode($_ci_data['_ci_vars']) ); ?>'));
	php_vars.base_url = '<?php echo base_url(); ?>';

</script>

<script src="<?php echo base_url(); ?>/public/js/common/edition_area.js"></script>
<script src="<?php echo base_url(); ?>/public/js/mappeddataproperty/addnew.js" language="javascript" type="text/javascript" ></script>

<!-- WebVowl JS's -->
<script src="<?php echo base_url(); ?>/public/js/external/webvowl/d3.min.js"></script>
<script src="<?php echo base_url(); ?>/public/js/external/webvowl/webvowl.js"></script>
<script src="<?php echo base_url(); ?>/public/js/external/webvowl/webvowl.app.js"></script>

<!-- Database Graph JS's -->
<script src="<?php echo base_url(); ?>/public/js/external/dbgraph/wwwsqldesigner.min.js"></script>
