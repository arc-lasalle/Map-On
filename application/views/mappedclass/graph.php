

<script src="<?php echo base_url(); ?>/public/js/common/mapping_graph.js" language="javascript" type="text/javascript" ></script>

    <script type="text/javascript">
		var php_vars = [];
		php_vars.base_url = '<?php echo base_url(); ?>';
		php_vars.mp_graph = JSON.parse(unescape('<?php echo addslashes( json_encode($mapping_graph) ); ?>'));
        //var mappingGraph;
        
        window.onload = function() {
            mappingGraph = new mapping_graph();
            mappingGraph.draw();

            mappingGraph.showTableButtons("table_buttons");



            var nodeName = $('#input_class').val()
            var table = $('#input_table').val().replace("->", "_").toLowerCase();
            mappingGraph.drawTempNode( nodeName, table, 'class' );

            
            var table = $('#input_table').val().toLowerCase().split("->");
            mappingGraph.toggleTable( table[0], true );
        };

	</script>

	<div id="graphDiv" class="g_right_col_graph avoid_right_click unselectable" style="background-image: URL(<?php echo base_url()?>public/img/lightbg.png); height: 680px"> </div>



