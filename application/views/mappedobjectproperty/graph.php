
	<div id="graphDiv" class="g_right_col_graph avoid_right_click unselectable" style="background-image: URL(<?php echo base_url()?>public/img/lightbg.png); height: 650px"> </div>

	<script type="text/javascript">
		php_vars = [];
		php_vars.routes = JSON.parse('<?php echo json_encode($routes); ?>');
		php_vars.mp_graph = JSON.parse(unescape('<?php echo addslashes( json_encode($mapping_graph) ); ?>'));

		var mappingGraph;

		window.onload = function() {
			mappingGraph = new mapping_graph();
			mappingGraph.draw();



			mappingGraph.showTableButtons("table_buttons");

			//var nodeName = $('.dropdown').dropdown('get value')[0].replace("New:#:", " ");
			var nodeName = $('#selected_target').text();
			var linklabel = $('#input_objectproperty').val();
			var table = $('#input_table').val().replace("->", "_").toLowerCase();

			mappingGraph.drawTempNode( nodeName, table, 'class' );
			mappingGraph.addLink( php_vars.mp_graph.classes[0].qname, 'tempnode', 'class-class', linklabel );




			var table = $('#input_table').val().toLowerCase().split("->");
			mappingGraph.toggleTable( table[0], true );
			
			
			
		};



	</script>


	<script src="<?php echo base_url(); ?>/public/js/common/mapping_graph.js" language="javascript" type="text/javascript" ></script>
