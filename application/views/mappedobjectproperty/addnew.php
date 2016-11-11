<!-- CSS's -->
<link REL=StyleSheet HREF="<?php echo base_url(); ?>/public/css/common/edition_area.css" TYPE="text/css" MEDIA=screen>

<!-- WebVOWL CSS's -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/webvowl/webvowl.css" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/webvowl/webvowl.app.css" />

<!-- Database Graph CSS's -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/dbgraph/wwwsqldesigner.css" />


<div class="ui stackable grid">
	<div id="left_grid" class="five wide column">
		<div class="ui green segment g_left_col" >

			<!-- Mapping Options -->
			<div id="options_1_mapping">
				<div class="g_left_col_scroll" style="padding: 5px; padding-right: 10px; overflow-y: scroll;">


						<div class="ui small header">
							<strong>Add object property to <?php echo $class; ?></strong>
						</div>

						<div class="ui form secondary accordion fluid segment" >
						<?php 	echo form_open_multipart(base_url().'index.php/mappedobjectproperty/addnew_post');
								echo form_hidden('mappingspace_id', $routes['mappingspace_id']);
								echo form_hidden('datasource_id', $routes['datasource_id']);
								echo form_hidden('mappedclassdomain_id', $mappedclassdomain_id);
						?>


								<div class="field">

									<label>Source class</label>
									<div class="ui icon input">
										<input type="text" class="gl_clear_default" value="<?php echo $class; ?>" disabled="true" style="color: black;">
									</div>
								</div>

								<div class="field">
									<label>Object property</label>
									<div class="ui icon input" data-content="Type for searching a data property of the ontology">
										<input type="text" id="input_objectproperty" name="input_objectproperty" class="gl_clear_default" autocomplete="off" value="Object property name..." onkeyup="chk_suggestObjectproperty();" onclick="chk_suggestObjectproperty(0);">
										<i class="search icon"></i>
									</div>
									<input id="hidden_search_inputtext_objectproperty" name="hidden_search_inputtext_objectproperty" type="hidden" value = "">
									<div id="suggest_Objectproperty" class="search_box_objectproperty gl_clickOut_hide avoid_overflow" style="position: fixed;">
										<div class="ui inverted active dimmer">
											<div class="ui mini text loader">Searching....</div>
										</div><br/><br/>
									</div>


								</div>
								<div class="field">
									<label>Target class</label>
									<div class="ui selection dropdown" data-content="Type for searching a data property of the ontology">
										<input type="hidden" name="input_target">
										<div id="selected_target" class="text">Select a target...</div>
										<i class="dropdown icon"></i>
										<div class="menu" id="input_target">
										</div>
									</div>
								</div>
								<div class="field">
									<label>Target Table/Column</label>
									<div class="ui icon input" data-content="Type for searching a column of the data source" >
										<input type="text" id="input_table" name="input_table" class="gl_clear_default" autocomplete="off" value="<?php echo $mappedtablecolumn; ?>" onkeyup="chk_suggestTable();" onclick="chk_suggestTable(0);">
										<i class="search icon"></i>
									</div>
									<input id="hidden_search_inputtext_table" name="hidden_search_inputtext_table" type="hidden" value = "">
									<div id="suggest_Table" class="search_box_table gl_clickOut_hide" style="position: fixed;">
										<div class="ui inverted active dimmer">
											<div class="ui mini text loader">Searching....</div>
										</div><br/><br/>
									</div>
								</div>


							<div class="field">
								<div class="title">
									<i class="icon dropdown"></i>
									URI details
								</div>
								<div class="content field">
									<div class="field">
										<label>Value</label>
										<div class="ui input" data-content="Write the value for the mapping. When a column of the data source and the target class are selected the value is automatically set">
											<textarea type="text" id="input_uri" rows="2"  name="input_uri" ></textarea>
										</div>

									</div>
								</div>
							</div>

							<div class="actions">
								<input type="submit" value="Add" class="ui tiny button" /> <div class="ui tiny button" style= "right: 11px; position: absolute;"  onmouseup="window.location.href = '<?php echo base_url();?>index.php/mappingspace/graph/<?php echo $routes['datasource_id']."/".$routes['mappingspace_id']; ?>';">Cancel</div>
							</div>
							<div class="ui error message"></div>
						</div>

						<?php echo form_close(); ?>

						<div class="ui small header">
							<strong>Filtering tables of the data source</strong>
						</div>
					
						<div id="table_buttons"></div>

				</div>
			</div>

			<!-- Ontology Options -->
			<div id="options_2_ontology">
				<div style="height: 100%;">
					<br />
					<h1 style="margin: 0px; font-weight: bold; text-align: justify;">Select the object property</h1>
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

								<div id="sidebar_ontology_objprop_selector" class="ui segment secondary hidden">
									<p style="font-size: 15px; text-decoration: underline;"><b>Selection:</b></p>
									<p style="padding-left: 5px; margin-top: -10px;">
										<b>Domain:</b><br>
										&nbsp;&nbsp;<?php echo $class; ?><br>
										<b>Object property:</b><br>
										&nbsp;&nbsp;<span id="sidebar_ontology_obj_prop">Select.</span><br>
										<b>Range:</b><br>
										&nbsp;&nbsp;<span id="sidebar_ontology_dest_class">Select.</span><br>
									</p>

									<input id="sidebar_ontology_btn_selectobjprop" type="submit" value="Select" class="ui primary tiny button"/>
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

	//URI details
	$('.ui.icon.input').popup({transition: 'scale'  });
	$('.ui.input').popup({transition: 'scale'  });
	$('.selection.dropdown').popup({transition: 'scale'  });

	$('.ui.accordion').accordion();
	$('.ui.checkbox').checkbox();
	$('.table.checkbox').checkbox('attach events', '.check.button', 'check');
	$('.table.checkbox').checkbox('attach events', '.uncheck.button', 'uncheck');

	var settings = {
        fields: {
            input_objectproperty: {
                identifier  : 'input_objectproperty',
                rules: [
                    { type: 'check_object_property', prompt: 'Please enter a valid object property' },
                    { type   : 'empty', prompt : 'Please enter the object property for creating the mapping'},
                    { type   : 'not[Object property name...]', prompt : 'Please enter the object property for creating the mapping'	}
                ]
            },
            input_table: {
                identifier  : 'input_table',
                rules: [
                    { type: 'check_table', prompt: 'Please enter a valid table/column' },
                    { type   : 'empty', prompt : 'Please enter the column of a table for creating the mapping'}
                ]
            },
            input_target: {
                identifier  : 'input_target',
                rules: [
                    { type   : 'empty', prompt : 'Please select a target class for the object property'}
                ]
            },
            input_uri: {
                identifier  : 'input_uri',
                rules: [
                    { type   : 'empty', prompt : 'Please enter the URI for creating the mapping'}
                ]
            }
        },
		rules: {
			check_object_property: function () {
				var found = false;

				$.ajax({
					async: false,
					type: "POST",
					url: "<?php echo site_url("mapping/checkObjectProperty"); ?>",
					data: { datasource_id: <?php echo $datasource_id; ?>, op_name: document.getElementById('input_objectproperty').value },
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

	$('.dropdown').dropdown({
		onChange: function() { 
			var value = $('.dropdown').dropdown('get value');
			if(value == "---,,") {
			
				$('.dropdown').dropdown('set selected', '1');
			}
						
		}
	});

	$('.ui.checkbox').checkbox();
	

	
	
	//Toggle table nodes in the graph
	function chk_toggleTable(element, status) {
		element = element.toLowerCase()
		console.log("table: " + document.getElementById('input_table').value+ " Element: " + element + " status: "+ status);
		
		//searching if the element is the mapped table.
		if(document.getElementById('input_table').value.toLowerCase().indexOf(element) <= -1) {
			
			toggleTable(element, status);
			
		} else {

			$("#table_id_"+element).checkbox('setting', 'onChange', function () {});
			if(status){
				$("#table_id_"+element).checkbox('check');
			} else {
				$("#table_id_"+element).checkbox('check');
			}
			
			$("#table_id_"+element).checkbox('setting', 'onChange', function () {chk_toggleTable(element, document.getElementById("table_"+element).checked );});
		}
	}
	
	
	//Toggle table nodes in the graph
	function toggleTable(element, status) {
			
		if(status){
			document.getElementById('action').value = "filtertableon";
		} else {
			document.getElementById('action').value = "filtertableoff";
		}

		document.getElementById('nodeid').value = "";	
		document.getElementById('table').value = element;

		$( "#centerForm" ).submit();
		
		$.ajax({ url: '<?php echo base_url().'index.php/mappedclass/storetableson'; ?>',
				 data: {mappedclass_id: php_vars.routes.mapped_class_id, tableid: element, onoff: status},
				 type: 'post',
				 success: function(output) {}
		});
	}
	
	function chk_suggestObjectproperty(time){
		if ( time === undefined ) time = 500;
        clearTimeout(timer);
        timer=setTimeout("suggestObjectproperty(0)",time);
    }
	
	function suggestObjectproperty() {
		document.getElementById('hidden_search_inputtext_objectproperty').value = "";

		// Remove error of previous form validation
		$("#suggest_Objectproperty").parent().removeClass("error");

		// Show loader until receiving load() data.
		$("#suggest_Objectproperty").html('<div class="ui inverted active dimmer"><div class="ui mini text loader">Searching....</div></div><br/><br/>');

		$("#suggest_Objectproperty").load(php_vars.routes.base_url+'index.php/mapping/suggestobjectproperty', { string: document.getElementById('input_objectproperty').value, class: '<?php echo $uriMappedClass; ?>', datasource_id: <?php echo $datasource_id; ?> } );

		//var position = $("#input_objectproperty").position();
		//document.getElementById('suggest_Objectproperty').style.top = position.top + 66 + "px";
		//document.getElementById('suggest_Objectproperty').style.left = position.left + 12 + "px";
		if( $("#suggest_Objectproperty").is(":hidden") ) $("#suggest_Objectproperty").fadeIn();

	}
	
	function add_search_box_Objectproperty(string_uri){

		// Remove error of previous form validation
		$("#input_target").parent().parent().removeClass("error");

		document.getElementById('input_objectproperty').value = string_uri;
		document.getElementById('hidden_search_inputtext_objectproperty').value = string_uri;
		$("#suggest_Objectproperty").fadeOut();
		
		//To autocomplete the VALUE section
		$.post(php_vars.routes.base_url+"index.php/mapping/generateObjectpropertyURI",  { input_object: string_uri, datasource_id: <?php echo $datasource_id; ?>, input_table :  document.getElementById('input_table').value}, function(data) {
				//alert(data);
				$('#input_uri').val(data);
			});
		
		$("#input_target").load(php_vars.routes.base_url+"index.php/mapping/generateObjectpropertyTarget", { input_object: string_uri, mappingspace_id: php_vars.routes.mappingspace_id, datasource_id: php_vars.routes.datasource_id }, function() {
				//alert("loaded"); 
				$('.dropdown').dropdown({
					onChange: function() { 
						var value = $('.dropdown').dropdown('get value');
						
						//alert(value);
						if(value == "---,,") {
						
							$('.dropdown').dropdown('set selected', '1');
						}
						
						$.post(
                            php_vars.routes.base_url+"index.php/mapping/generateObjectpropertyURI",
                            { input_object: string_uri, datasource_id: php_vars.routes.datasource_id, input_table :  document.getElementById('input_table').value },
                            function(data) {
									//alert(data);
									$('#input_uri').val(data);
                            });


                        var nodeName = $('#selected_target').text();
                        var linklabel = $('#input_objectproperty').val();
                        var table = $('#input_table').val().replace("->", "_").toLowerCase();

                        mappingGraph.drawTempNode( nodeName, table, 'class' );
                        mappingGraph.addLink( php_vars.mp_graph.classes[0].qname, 'tempnode', 'class-class', linklabel );



					}
				});
	
			} );


        var nodeName = $('#selected_target').text();
        var linklabel = $('#input_objectproperty').val();
        var table = $('#input_table').val().replace("->", "_").toLowerCase();

        mappingGraph.drawTempNode( nodeName, table, 'class' );
        mappingGraph.addLink( php_vars.mp_graph.classes[0].qname, 'tempnode', 'class-class', linklabel );
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
		$("#suggest_Table").parent().removeClass("error");

		// Show loader until receiving load() data.
		$("#suggest_Table").html('<div class="ui inverted active dimmer"><div class="ui mini text loader">Searching....</div></div><br/><br/>');

		$("#suggest_Table").load(php_vars.routes.base_url+"index.php/mapping/suggesttable", { string: document.getElementById('input_table').value, datasource_id: php_vars.routes.datasource_id } );

		//var position = $("#suggest_Table").position();
		//alert("Div: "+position.top+" "+ position.left);
		//var position = $("#input_table").offset();
		//alert("Table: "+position.top+" "+ position.left);

		//document.getElementById('suggest_Table').style.top = position.top-137 + "px";
		//document.getElementById('suggest_Table').style.left = position.left -24 + "px";
		if( $("#suggest_Table").is(":hidden") ) $("#suggest_Table").fadeIn();

	}
	
	function add_search_box_Table(string_uri){

		if (typeof dbgraph_add_search_box_Table == 'function') {
			if (dbgraph_add_search_box_Table(string_uri)) return;
		}

		// Remove error of previous form validation
		$("#suggest_Table").parent().removeClass("error");

		document.getElementById('input_table').value = string_uri;
		document.getElementById('hidden_search_inputtext_table').value = string_uri;
		$("#suggest_Table").fadeOut();
		
		//To autocomplete the URI section
		$.post('<?php echo site_url("mapping/generateObjectpropertyURI"); ?>', { input_object: document.getElementById('input_objectproperty').value , datasource_id: <?php echo $datasource_id; ?>, input_table :  document.getElementById('input_table').value }, function(data) {
				//alert(data);
				$('#input_uri').val(data);
			});


        var nodeName = $('#selected_target').text();
        var linklabel = $('#input_objectproperty').val();
        var table = $('#input_table').val().replace("->", "_").toLowerCase();

        mappingGraph.drawTempNode( nodeName, table, 'class' );
        mappingGraph.addLink( php_vars.mp_graph.classes[0].qname, 'tempnode', 'class-class', linklabel );

        var table = $('#input_table').val().toLowerCase().split("->");
        mappingGraph.toggleTable( table[0], true );

	
	}
	


	function add_search_box_Class(string_uri){
		if ( typeof ont_add_search_box_Class == 'function' ) {
			if( ont_add_search_box_Class( string_uri ) ) return;
		}
		// ...
	}
	function add_search_box_Dataproperty(string_uri) {

		if (typeof ont_add_search_box_Class == 'function') {
			if (ont_add_search_box_Dataproperty(string_uri)) return;
		}
		// ...
	}

</script>

<!-- JS's -->
<script type="text/javascript">
	php_vars.base_url = '<?php echo base_url(); ?>';
</script>

<script src="<?php echo base_url(); ?>/public/js/common/edition_area.js"></script>
<script src="<?php echo base_url(); ?>/public/js/mappedobjectproperty/addnew.js" language="javascript" type="text/javascript" ></script>

<!-- WebVowl JS's -->
<script src="<?php echo base_url(); ?>/public/js/external/webvowl/d3.min.js"></script>
<script src="<?php echo base_url(); ?>/public/js/external/webvowl/webvowl.js"></script>
<script src="<?php echo base_url(); ?>/public/js/external/webvowl/webvowl.app.js"></script>

<!-- Database Graph JS's -->
<script src="<?php echo base_url(); ?>/public/js/external/dbgraph/wwwsqldesigner.min.js"></script>