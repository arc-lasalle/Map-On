
<div id="graphDiv" class="avoid_right_click unselectable" style="background-image: URL(<?php echo base_url()?>public/img/lightbg.png); height: 350px; padding:10px;"> </div>

<br />

<img src="<?php echo base_url()?>public/img/legend.png" width="300px">


<script type="text/javascript">
    php_vars = [];
    php_vars.base_url = '<?php echo base_url(); ?>';
    php_vars.routes = JSON.parse('<?php echo json_encode($routes); ?>');
    php_vars.mp_graph = JSON.parse('<?php echo json_encode($mapping_graph); ?>');

    var mappingGraph;

    window.onload = function() {
        mappingGraph = new mapping_graph();
        mappingGraph.draw();
    };

</script>


<script src="<?php echo base_url(); ?>/public/js/common/mapping_graph.js" language="javascript" type="text/javascript" ></script>




	

