
	<!-- CSS's -->
	<link REL=StyleSheet HREF="<?php echo base_url(); ?>/public/css/mappingspace/graph.css" TYPE="text/css" MEDIA=screen>
	<link REL=StyleSheet HREF="<?php echo base_url(); ?>/public/css/common/edition_area.css" TYPE="text/css" MEDIA=screen>

	<!-- WebVOWL CSS's -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/webvowl/webvowl.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/webvowl/webvowl.app.css" />

	<!-- Database Graph CSS's -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/dbgraph/wwwsqldesigner.css" />


	<div class="ui stackable grid">
		<div id="left_grid" class="five wide column">

			<div class="ui green segment g_left_col">

				<!-- Mapping Options -->
				<div id="options_1_mapping">
					<div id="content">
						<table width="100%"><tr><td>
								Mappings:  <a href="<?php echo base_url();?>index.php/mappedclass/createnew/<?php echo $this->maponrouting->getDatasourceId() ."/".$this->maponrouting->getMappingSpaceId(); ?>"><i class="circular add purple link icon" data-content="Click it for creating a new mapping between a class and a table "></i></a>
						</td><td style="text-align: right">
							<div class="ui icon input" data-content="Search a mapping by name of the class of the table to highlight it" >
								<input type="text" id="input_mapping" size="20" name="input_mapping" class="gl_clear_default" autocomplete="off" value="search..." onkeyup="chk_searchmapping();" onclick="chk_searchmapping(0);" >
									<i class="search icon"></i>
							</div>
							<input id="hidden_search_inputtext_mapping" name="hidden_search_inputtext_mapping" type="hidden" value = "">
							<div id="suggest_Mapping" class="search_box_mapping gl_clickOut_hide">
								<div class="ui inverted active dimmer">
									<div class="ui mini text loader">Searching....</div>
								</div><br/><br/>
							</div>
						</td></tr></table>
						<br />
						<div id="listMappedClasses" style="/*height:610px;*/ overflow-y: scroll" class="g_left_col_scroll">
							<div style="margin: 4px">
							<?php 	foreach($mappedclass as $text) {
										echo $text;
									}
							?>
							</div>
						</div>
					</div>
				</div>


				<!-- Ontology Options -->
				<div id="options_2_ontology">
					<div style="height: 100%; overflow: scroll;">
						<br />
						<h1 class="webvowl_title" style="margin: 0px; font-weight: bold; text-align: justify;">Ontology</h1>
						<!--h1 class="webvowl_title" style="margin: 0px; font-weight: bold; text-align: justify;">Ontology title</h1-->
						<br />
						<div class="ui styled accordion" style="margin: 2px; width: 95%;">
							<div class="title" style="background: #faf9fa;"><i class="dropdown icon"></i>Vocabulary info</div>
							<div id="sidebar_ontology_info" class="content">
								<!-- Filled by the function cbk_showOntologyInfo from graph.js -->
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
									<!--button id="sidebar_dbgraph_btn_select" class="ui primary button hidden">
										<b>Select row</b>
									</button-->
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
					<div class="right_colum_title"><strong>Graph representation of the mappings</strong></div>
					</td><td style="text-align: right">		
						<i class="circular history purple link icon" onclick="sidebar();" data-position="top left" data-html="click to too see the history log"></i>
					</td></tr></table>
				</div>
				<div class="content field">

					<div id="ea_loader" class="hidden"></div>

					<!-- Mapping Graph -->
					<div id="graph_1_mapping" style="overflow: hidden">
						<div id="graphDiv" class="g_right_col_graph avoid_right_click unselectable" style="background-image: URL(<?php echo base_url()?>public/img/lightbg.png); height:650px "></div>
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

	<!-- Viewbox -->
	<div id="mapping_dropdown" class="ui top pointing dropdown" style="position: absolute; top: 100px; left: 100px;">

		<div class="menu">
			<div style="position: absolute; height: 50px; width: 100%; margin-top: -40px;"></div> <!-- Necesary for hissing when mouse leave -->

			<a class="item url1"><i class="edit purple icon"></i>Edit mapping</a>

			<a class="item url2" style=" text-align=left;"><i class="add circle purple  icon" ></i>Create data property</a>

			<a class="item url3" style=" text-align=left;"><i class="resize horizontal purple  icon" ></i>Create object property</a>

			<a class="item url4" style=" text-align=left;" title="Expand class"><i class="asterisk purple icon" ></i>Expand mapping</a>

			<a class="item url5" style=" text-align=left;" title="Expand class"><i class="edit purple icon" ></i>Edit data property</a>

			<a class="item url7"><i class="external share purple icon" ></i>Move to<div class="menu suburl7"></div></a>

			<a class="item url8" data-content="The URI" onclick="copyToClipboard( this.getAttribute('data-content') );"><i class="external copy purple icon" ></i>Copy uri</a>

			<a class="item url6" onclick="return confirm('Are you sure?');"><i class="delete red icon" ></i>Delete</a>


		</div>
	</div>

	<script type="text/javascript">
		var php_vars = JSON.parse(unescape('<?php echo addslashes( json_encode($_ci_data['_ci_vars']) ); ?>'));
		php_vars.base_url = '<?php echo base_url(); ?>';
		php_vars.user_technician = <?php echo ($this->ion_auth->in_group("technician")==1) ? "true" : "false"  ?>;

		php_vars.routes = JSON.parse('<?php echo json_encode($routes); ?>');
		php_vars.mp_graph = JSON.parse(unescape('<?php echo addslashes( json_encode($mapping_graph) ); ?>'));
	</script>

<script>
	var mappingGraph;

	window.onload = function() {
		mappingGraph = new mapping_graph();
		mappingGraph.draw();
		mappingGraph.restoreLocalPositions( php_vars.datasource.id, php_vars.mappingspace.id );
	};

	window.addEventListener("beforeunload", function (e) {
		mappingGraph.storeLocalPositions( php_vars.datasource.id, php_vars.mappingspace.id );
		return null;
	});

	///////////////////////////////////////////////////
	// JS functions for Datatype selection
	//

	function copyToClipboard(text) {
		var $temp = $("<input>");
		$("body").append($temp);
		$temp.val(text).select();
		document.execCommand("copy");
		$temp.remove();
	}

	$('#mapping_dropdown').dropdown({
			on: 'hover'
	});

	var timer;

	$('.ui.popup').popup();

	//popups of the help insights
	
	$('.ui.icon.input').popup({transition: 'scale'  });
	$('.purple.icon').popup({transition: 'scale'  });
	$('.red.icon').popup({transition: 'scale'  });
	
	$('.ui.small.red.circular.image.label').popup({hoverable:true,setFluidWidth:true, maxSearchDepth:20, position : 'right center'  });
	
	function sidebar() {
		 $('.sidebar').sidebar('toggle');
	}

	function chk_searchmapping( time ){

		if ( time === undefined ) time = 500;

		clearTimeout(timer);
		timer=setTimeout("searchmapping()",time);
	}

	function searchmapping() {
		document.getElementById('hidden_search_inputtext_mapping').value = "";
	
		//if(document.getElementById('input_mapping').value != ""){  //If "" show all mappings

			// Show loader until receiving load() data.
			$("#suggest_Mapping").html('<div class="ui inverted active dimmer"><div class="ui mini text loader">Searching....</div></div><br/><br/>');

			$("#suggest_Mapping").load('<?php echo site_url("mappingspace/searchmapping"); ?>', { string: document.getElementById('input_mapping').value, mappingspace_id: <?php echo $mappingspace->id; ?>,datasource_id: <?php echo $this->maponrouting->getDatasourceId(); ?>  } );

			var position = $("#input_mapping").position();
			document.getElementById('suggest_Mapping').style.top = position.top+50 + "px";
			document.getElementById('suggest_Mapping').style.left = position.left +37+ "px";
			if( $("#suggest_Mapping").is(":hidden") ) $("#suggest_Mapping").fadeIn();
		//}else{
		//	$("#suggest_Mapping").fadeOut();
		//}
	}
	

		
	function add_search_box_Mapping(string_uri){
		document.getElementById('input_mapping').value = string_uri;
		document.getElementById('hidden_search_inputtext_mapping').value = string_uri;
		$("#suggest_Mapping").fadeOut();
		
		offset = $('#viewbox_'+string_uri.replace(":", "--")).position().top;

		contactTopPosition =offset +  $("#listMappedClasses").scrollTop() -60;
		$("#listMappedClasses").animate({scrollTop: contactTopPosition});
		

		//highligth
		//handleMouseOverNode( graph.getNode(string_uri) );
		mappingGraph.onMouseNode( string_uri, 'over' );

		hoverLabelClass(string_uri, '');
		//document.getElementById('viewbox_'+string_uri.replace(":", "--")).style.backgroundColor = 'rgb(252,221,216)';






    }
	/*
	///////////////////////////////////////////////////
	// JS functions for Table selection
	//
	function suggestTable(idTable) {
		document.getElementById('hidden_search_inputtext_table').value = "";
	
		//alert(idTable);
		if(document.getElementById(idTable).value != ""){   

			$("#suggest_Table").load('<?php echo site_url("mapping/suggesttable"); ?>', { string: document.getElementById(idTable).value, datasource_id: <?php echo $this->maponrouting->getDatasourceId(); ?>, target: idTable } );

			var position = $("#"+idTable).position();
			var width = document.getElementById('suggest_Table').style.width;
			document.getElementById('suggest_Table').style.top = position.top+22 + "px";
			document.getElementById('suggest_Table').style.left = (position.left -200)+ "px";
			if( $("#suggest_Table").is(":hidden") ) $("#suggest_Table").fadeIn();
		}else{
			$("#suggest_Table").fadeOut();
		}
	}
	
	function add_search_box_Table(idTable, string_uri){
		document.getElementById(idTable).value = string_uri;
		document.getElementById('hidden_search_inputtext_table').value = string_uri;
		$("#suggest_Table").fadeOut();
		
		//To autocomplete the SQL section
		$("#input_sql").load('<?php echo site_url("mapping/generateSQL"); ?>', { input_table: string_uri, datasource_id: <?php echo $this->maponrouting->getDatasourceId(); ?> } );
		//To autocomplete the URI section
		$("#input_uri").load('<?php echo site_url("mapping/generateURI"); ?>', { input_class: document.getElementById('input_class').value, input_table: document.getElementById(idTable).value, datasource_id: <?php echo $this->maponrouting->getDatasourceId(); ?> } );
    }*/
	function add_search_box_Table(string_uri) {

		if (typeof dbgraph_add_search_box_Table == 'function') {
			if (dbgraph_add_search_box_Table(string_uri)) return;
		}

		// ...
	}

	
	//////////////////////////////////////////////
	// Viewboxes:
	function toogleLabelClass(mappedclass_id, table) {
		$('#details_'+mappedclass_id).toggle();
		$('#buttons_'+mappedclass_id).toggle();
		
		/*
		if($('#buttons_'+mappedclass_id).is(':visible')) {
			$('#tableBox_'+mappedclass_id).html('<input type="text" class="tableBox" id="tableBox'+mappedclass_id+'" name="tableBox'+mappedclass_id+'" value="'+table+'" size="40" autocomplete="off" onkeyup="suggestTable(\'tableBox'+mappedclass_id+'\');"/><input id="hidden_search_inputtext_table" name="hidden_search_inputtext_table" type="hidden" value = ""> <div id="suggest_Table" class="search_box_table">');
		} else {
			var value = $('#tableBox'+mappedclass_id).val();

			$('#tableBox_'+mappedclass_id).html('<span Id="labelboxTableColumn">'+value+'</span>');
		}*/
	}
		
	//////////////////////////////////////////////

	function hoverLabelClass(mappedclass_id, table) {


		document.getElementById('viewbox_'+mappedclass_id.replace(":", "--")).style.backgroundColor = 'rgb(253,230,226)';//#e0eee0';

		// Higlihgt
		//handleMouseOverNode(graph.getNode(mappedclass_id));
		mappingGraph.onMouseNode( mappedclass_id, 'over' );

		
	}
	
	function outLabelClass(mappedclass_id, table) {

		document.getElementById('viewbox_'+mappedclass_id.replace(":", "--")).style.backgroundColor = '#faf9fa';

		// nohighlight
		//handleMouseLeaveNode(graph.getNode(mappedclass_id));
		mappingGraph.onMouseNode( mappedclass_id, 'leave' );

	}

	function add_search_box_Class(string_uri){
		if ( typeof ont_add_search_box_Class == 'function' ) {
			if( ont_add_search_box_Class( string_uri ) ) return;
		}
		// ...
	}
	function add_search_box_Dataproperty(string_uri) {

		if (typeof ont_add_search_box_Dataproperty == 'function') {
			if (ont_add_search_box_Dataproperty(string_uri)) return;
		}
		// ...
	}
</script>


	<!-- JS's -->


	<script src="<?php echo base_url(); ?>/public/js/common/mapping_graph.js" language="javascript" type="text/javascript" ></script>


	<script src="<?php echo base_url(); ?>/public/js/common/edition_area.js"></script>
	<script src="<?php echo base_url(); ?>/public/js/mappingspace/graph.js" language="javascript" type="text/javascript" ></script>

	<!-- WebVowl JS's -->
	<script src="<?php echo base_url(); ?>/public/js/external/webvowl/d3.min.js"></script>
	<script src="<?php echo base_url(); ?>/public/js/external/webvowl/webvowl.js"></script>
	<script src="<?php echo base_url(); ?>/public/js/external/webvowl/webvowl.app.js"></script>

	<!-- Database Graph JS's -->
	<script src="<?php echo base_url(); ?>/public/js/external/dbgraph/wwwsqldesigner.min.js"></script>