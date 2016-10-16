    <script type="text/javascript">
		$('#ea_loader').removeClass('hidden');
		$('#ea_loader').html("Loading mappings...<br>&#8635;");

        function main () {
            // This demo shows how to create a directional arrow in SVG renderer.
            // Though it might seem wordy it's due to SVG specific operations.
            // The library has minimal SVG manipulation support.
            // Maybe in future some of the following technniques will become part
            // of the library itself...
            var graph = Viva.Graph.graph();
			//
			/////////////////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////////////
			
			var idealLength = 300;
            var graphics = Viva.Graph.View.svgGraphics(),
                nodeSize = 10;
			var layout = Viva.Graph.Layout.forceDirected(graph, {
				/*springLength : 100,
				springCoeff : 0.0005,
				dragCoeff : 0.02,
				gravity : -2.2
				*/
				springLength : idealLength,
				springCoeff : 0.0008,
				dragCoeff : 0.02,
				gravity : -10,
				
				springTransform: function (link, spring) {
                    spring.length = idealLength * (1 - link.data.connectionStrength);
                  }
			});
			
			<?php
			
			//Tables and attributes
			$tableCount = count($tables);
			$width=900;
			$offset = $width/($tableCount+1);
			$i = 0;
			
			foreach($tables as $row) {

				echo "nodeTable = graph.addNode('".strtolower($row->name)."', {text: '".$row->name."', type:2, isPinned: true});\n";
				$posx = $row->layoutX;
				$posy = $row->layoutY;
					
				if(array_key_exists(strtolower($row->name), $layout)) {
					$posx = $layout[strtolower($row->name)]["layoutX"];
					$posy = $layout[strtolower($row->name)]["layoutY"];
				} else {
				
				
					if($posx == 0 && $posy == 0) {
						$posx = -$width/2 + ($i+1)*$offset;
						$posy = $i%2 == 0 ? -rand(50, 100) : rand(50, 150);
					} 
				}							
			
				//Positioning the nodes
				echo "layout.setNodePosition(nodeTable, ".$posx.", ".$posy.");";
				$i++;
				
				foreach($columns[$row->id] as $col) {

					$type = ($col->foreignkey == "") ? 3: 5;
					
					
					if(array_key_exists(strtolower($row->name."_".$col->name), $layout)) {
						echo "nodeCol = graph.addNode('".strtolower($row->name."_".$col->name)."', {text: '".$col->name."', type:".$type.", description: '".$col->type."', tablecolumn: '".$row->name."->".$col->name."', isPinned: true});\n";
						$posx = $layout[strtolower($row->name."_".$col->name)]["layoutX"];
						$posy = $layout[strtolower($row->name."_".$col->name)]["layoutY"];
						echo "layout.setNodePosition(nodeCol, ".$posx.", ".$posy.");";
					} else {
						echo "nodeCol = graph.addNode('".strtolower($row->name."_".$col->name)."', {text: '".$col->name."', type:".$type.", description: '".$col->type."', tablecolumn: '".$row->name."->".$col->name."'});\n";
					}
					

					//Positioning the nodes
					echo "graph.addLink('".strtolower($row->name)."', '".strtolower($row->name."_".$col->name)."', { connectionStrength: 0.9, type: 2});\n\n";
				}
			}
			
			//foreign keys
			foreach($tables as $row) {
				foreach($columns[$row->id] as $col) {
					if($col->foreignkey != "" && $col->foreigntable != "") {
						echo "graph.addLink('".strtolower($col->foreigntable."_".$col->foreignkey)."', '".strtolower($row->name."_".$col->name)."', { connectionStrength: 0.8, type: 5});\n\n";
					}
				}				
			}
			?>
			
			

            // In this example we fire off renderer before anything is added to
            // the graph:
            var renderer = Viva.Graph.View.renderer(graph, {
                     container  : document.getElementById('graphDiv'),
					 graphics : graphics, layout : layout
                });
           
            graphics.node(function(node) {
				// This time it's a group of elements: http://www.w3.org/TR/SVG/struct.html#Groups
			    var ui = Viva.Graph.svg('g'),
                // Create SVG text element with user id as content
               
				svgText = Viva.Graph.svg('text')
					//.attr('onclick', 'alert("ssd");')
					.attr('y', 8)
					.attr('x', 5)
					.attr('class', 'text-link')
					.attr('font-size', '9')
					.text(node.data.text),					
				bckgr = Viva.Graph.svg('rect')
                     .attr('width', nodeSize)
                     .attr('height', nodeSize+4)
					 .attr('y', -2)
					 .attr('id', 'background'+node.id);

				if(node.data.type === 1) {
					//Class
					bckgr.attr('style', 'fill:rgb(240,89,64)')		//naranja 204,159,42
						 .attr('rx', 7)
						 .attr('ry', 7)
					svgText.attr('style', 'fill:rgb(256,256,256)');
					
					svgTitle = Viva.Graph.svg('title')
						.text(node.data.description);
					//ui.append(svgTitle);
				} else if(node.data.type === 2) {
					//Table
					bckgr.attr('style', 'fill:rgb(89,79,138)');		//marron: 150,111,72
					svgText.attr('style', 'fill:rgb(256,256,256)');
				} else if(node.data.type === 3) {
					//Table Column
					bckgr.attr('style', 'fill:rgb(89,79,138); opacity:0.8'); //marron: 150,111,7
					svgText.attr('style', 'fill:rgb(256,256,256)')
						   .attr('cursor', 'pointer');
					svgDescription = Viva.Graph.svg('text')
					//.attr('onclick', 'alert("ssd");')
					.attr('y', 25)
					.attr('x', 2)
					.attr('class', 'text-link')
					.attr('font-size', '12')
					.attr('visibility', 'hidden')
					.text(node.data.description);
					ui.append(svgDescription);
					ui.svgDescription = svgDescription;
					
				} else if(node.data.type === 4) {
					//data property
					bckgr.attr('style', 'fill:rgb(161,207,100); opacity:0.8');	//green: 41,166,121
					svgText.attr('style', 'fill:rgb(256,256,256)');
				} else if(node.data.type === 5) {
					//table column with a foreign key
					bckgr.attr('style', 'fill:rgb(89,79,138); opacity:0.8') //marron: 150,111,7
						.attr('stroke', 'rgb(256,40,40)')
						.attr('stroke-width', 0.1);
					svgText.attr('style', 'fill:rgb(256,256,256)')
						   .attr('cursor', 'pointer');
					
					svgDescription = Viva.Graph.svg('text')
					//.attr('onclick', 'alert("ssd");')
					.attr('y', 25)
					.attr('x', 2)
					.attr('class', 'text-link')
					.attr('font-size', '12')
					.attr('visibility', 'hidden')
					.text(node.data.description);
					ui.append(svgDescription);
					ui.svgDescription = svgDescription;
				} 
			
				ui.data = node.data;
				ui.append(bckgr);
				ui.append(svgText);
			
				
				ui.bckgr = bckgr;
				ui.svgText = svgText;
				
				ui.addEventListener('click', function () {
                        // toggle pinned mode
                        layout.pinNode(node, true);
                    });
				$(ui).mouseup(function(e) {
					handleMouseUp(e, node);
				}).hover(function() {
                    handleMouseOverNode(node);
				},
				function(){
					handleMouseLeaveNode(node);
				});
					
	            return ui;
				
            }).placeNode(function(nodeUI, pos) {
                 // 'g' element doesn't have convenient (x,y) attributes, instead
                // we have to deal with transforms: http://www.w3.org/TR/SVG/coords.html#SVGGlobalTransformAttribute
				if (!nodeUI.bbox) {
					nodeUI.bbox = nodeUI.getBBox();
					var bbox = nodeUI.svgText.getBBox();
					nodeUI.bckgr.attr('width', bbox.width+12);
					nodeUI.attr('x', 105);
				}

				//nodeUI.bckgr.attr('width', nodeSize*2);
				nodeUI.attr('transform',
                            'translate(' +
                                  (pos.x -  nodeUI.bbox.width/2) + ',' + (pos.y - nodeSize/2) +
                            ')');
            });


            var geom = Viva.Graph.geom();

            graphics.link(function(link){
                // Notice the Triangle marker-end attribe:
                var ui = Viva.Graph.svg('path')
       			    .attr('stroke-width', 1)
				    .attr('fill', 'none');
						   
				if(link.data.type === 1 ) {
					//class class
					ui .attr('stroke', 'rgb(195,41,15)')		//blue 118,157,204
						.attr('stroke-width', 2);
				} else if(link.data.type === 2 ) {
					//Table-column
					ui.attr('stroke', 'rgb(89,79,138)')
						.attr('stroke-width', 2);
				} else if(link.data.type === 3 ) {
					//Mapping between table and class
					ui.attr('stroke', 'rgb(0,159,218)')	///red 140,101,62
                    .attr('stroke-dasharray', '5, 1')
					.attr('stroke-width', 1);
				} else if(link.data.type === 4 ) {
					//Class-datapropr
					ui.attr('stroke', 'rgb(41,166,121)')
					.attr('stroke-width', 2)
				} else if(link.data.type === 5 ) {
					//Column-column (foreign key
					ui.attr('stroke', 'rgb(89,79,138)')
					.attr('stroke-width', 1)
					.attr('stroke-dasharray', '5, 2')
				}
				ui.data = link.data; 
				
				$(ui).hover(function() {
                        handleMouseOverLink(link);
                    },
                    function(){
                        handleMouseLeaveLink(link);
                    });
					
				return ui;		   
            }).placeLink(function(linkUI, fromPos, toPos) {
                // Here we should take care about
                //  "Links should start/stop at node's bounding box, not at the node center."

                // For rectangular nodes Viva.Graph.geom() provides efficient way to find
                // an intersection point between segment and rectangle
                var toNodeSize = nodeSize,
                    fromNodeSize = nodeSize;

                var from = geom.intersectRect(
                        // rectangle:
                                fromPos.x - fromNodeSize / 2, // left
                                fromPos.y - fromNodeSize / 2, // top
                                fromPos.x + fromNodeSize / 2, // right
                                fromPos.y + fromNodeSize / 2, // bottom
                        // segment:
                                fromPos.x, fromPos.y, toPos.x, toPos.y)
                           || fromPos; // if no intersection found - return center of the node

                var to = geom.intersectRect(
                        // rectangle:
                                toPos.x - toNodeSize / 2, // left
                                toPos.y - toNodeSize / 2, // top
                                toPos.x + toNodeSize / 2, // right
                                toPos.y + toNodeSize / 2, // bottom
                        // segment:
                                toPos.x, toPos.y, fromPos.x, fromPos.y)
                            || toPos; // if no intersection found - return center of the node

		
				var ry = linkUI.data.type == 3 ? 0 : 0;
                //var data = 'M' + from.x + ',' + from.y +
                //           'L' + to.x + ',' + to.y;
				var	data = 'M' + from.x + ',' + from.y + 
                           ' A 100,' + ry + ',0,0,1,' + to.x + ',' + to.y;
                linkUI.attr("d", data);
            });
	
			renderer.run();
			$('#ea_loader').addClass('hidden');
			
			<?php
			
			foreach($tables as $row) {

				if(array_key_exists(strtolower($row->name), $tableson) ) { 
					echo "var status = 'visible';";
				} else {
					echo "var status = 'hidden';";
				}
			
				//hidden table if it is needed
			?>	
				
				if(document.getElementById('input_table').value.indexOf('<?php echo strtolower($row->name); ?>') > -1) {
					status = 'visible';
					$("#table_id_<?php echo strtolower($row->name); ?>").checkbox('check');
				}
					
				var nodeUI = graphics.getNodeUI('<?php echo strtolower($row->name); ?>');
				nodeUI.attr('visibility', status);

				graph.forEachLinkedNode('<?php echo strtolower($row->name); ?>', function(linkedNode, link){
					
					
					var nodeUI = graphics.getNodeUI(linkedNode.id);
					nodeUI.attr('visibility', status);
					var linkUI = graphics.getLinkUI(link.id);
					linkUI.attr('visibility', status);
					
					graph.forEachLinkedNode(linkedNode.id, function(linkedNode2, link2){
						if(link2.data.type === 5) {
							var linkUI = graphics.getLinkUI(link2.id);
							linkUI.attr('visibility', status);
						}									
						});
					});
			<?php
			}
				
			?>
		
			var timer;
			timer = setTimeout(function(){renderer.pause(); clearTimeout(timer);}, 1000);
			
			
			handleMouseUp = function(e, node) {
				//alert("pined");
				if (e.shiftKey) {
					node.data.isPinned = true; //!node.data.isPinned;
				}
				
				layout.pinNode(node, true);
				//A table node has been moved, so, we store the positions.
				node.data.isPinned = true;
				
				var position = layout.getNodePosition(node.id);
				
				$.ajax({ url: '<?php echo base_url().'index.php/mappedclass/storepositions'; ?>',
						 data: {mappedclass_id: <?php echo $mappedclass_id; ?>, nodeid: node.id, layoutX: position.x, layoutY: position.y},
						 type: 'post',
						 success: function(output) {}
				});
				
			
				if(node.data.type === 3 || node.data.type === 5) {
				
					//columns selected
					
					add_search_box_Table( node.data.tablecolumn);
				}
				
				timer = setTimeout(function(){renderer.pause(); clearTimeout(timer);}, 2000);
			},
			
			highlightNode = function (nodeid, onoff) {
				var nodeUI = graphics.getNodeUI(nodeid);

				if(onoff) {
					//We want to select columns, so type 3 and 5
					if(nodeUI.data.type === 3 || nodeUI.data.type === 5) {
						nodeUI.bckgr.attr('stroke', 'rgb(41,166,121)')
									.attr('stroke-width', 2);
					} else {
						nodeUI.bckgr.attr('stroke', 'rgb(0,0,0)')
									.attr('stroke-width', 2);
					}
				} else {
					nodeUI.bckgr.attr('stroke-width', 0);
				}
				//showing the description (type of the column)
				if (nodeUI.svgDescription) {
					
					if(onoff) 	nodeUI.svgDescription.attr('visibility', 'visible');
					else 		nodeUI.svgDescription.attr('visibility', 'hidden');
					
				}
				
			}
			
			handleMouseOverNode = function(node) {
				
				highlightNode(node.id, true);
				
				graph.forEachLinkedNode(node.id, function(nodeTo, link){
					var linkUI = graphics.getLinkUI(link.id);
					if (linkUI) {
					   linkUI.attr('stroke-width', 3);
					    
						highlightNode(link.toId, true);
						highlightNode(link.fromId, true);

						if(node.data.type === 2) {
							if(link.toId != node.id)
								handleMouseOverNode(nodeTo);						
						}
					}
				});
			},

			
			handleMouseLeaveNode = function(node) {
				
				highlightNode(node.id, false);
				
				graph.forEachLinkedNode(node.id, function(nodeTo, link){
					var linkUI = graphics.getLinkUI(link.id);
					if (linkUI) {
						if(link.data.type === 1 || link.data.type === 2 || link.data.type === 4) 
							linkUI.attr('stroke-width', 2);
						else 
							linkUI.attr('stroke-width', 1);
					
						highlightNode(link.toId, false);
						highlightNode(link.fromId, false);
						
						if(node.data.type === 2) {
							if(link.toId != node.id)
								handleMouseLeaveNode(nodeTo);						
						}
					}
				});
			};
		   
			handleMouseOverLink = function(link) {
			   
				var linkUI = graphics.getLinkUI(link.id);
				linkUI.attr('stroke-width', 3);
				
				highlightNode(link.toId, true);
				highlightNode(link.fromId, true);

			},

			handleMouseLeaveLink = function(link) {
				var linkUI = graphics.getLinkUI(link.id);
				if(link.data.type === 1 || link.data.type === 2 || link.data.type === 4) 
					linkUI.attr('stroke-width', 2);
				else
					linkUI.attr('stroke-width', 1);
				
				highlightNode(link.toId, false);
				highlightNode(link.fromId, false);

			};
			
			
			$('#centerForm').submit(function(e) {
				e.preventDefault();
				var action = $('#action').val();
				var nodeId = $('#nodeid').val();
				
				var table = $('#table').val().toLowerCase();
				
				
				if(action === "edit") {
					var position = layout.getNodePosition(':newClass:');
					graph.removeNode(':newClass:');
					//node = graph.addNode(':newClass:', {text: document.getElementById('nodeid').value, type: 1, isPinned: true});	
					node = graph.addNode(':newClass:', {text: document.getElementById('nodeid').value, type:1, isPinned: true, description: ''});	

					layout.setNodePosition(node, position.x, position.y);
					if(graphics.getNodeUI(table)) 
						graph.addLink(table, ':newClass:', { connectionStrength: 0.1, type: 3});
						
				} else if(action === "modify") {
					
					if(graphics.getNodeUI(table)) {
						var removed= false;
						graph.forEachLinkedNode(':newClass:', function(linkedNode, link){
							
							if(link.data.type === 3) {
								graph.removeLink(link); 
								removed= true;
							}
						});
											
						graph.addLink(table, ':newClass:', { connectionStrength: 0.1, type: 3});
					} 
				} else if(action === "filtertableoff") {
					
					nodeid = document.getElementById('table').value.toLowerCase();
					
					var nodeUI = graphics.getNodeUI(nodeid);
					nodeUI.attr('visibility', 'hidden');

					graph.forEachLinkedNode(nodeid, function(linkedNode, link){
						
						
						var nodeUI = graphics.getNodeUI(linkedNode.id);
						nodeUI.attr('visibility', 'hidden');
						var linkUI = graphics.getLinkUI(link.id);
						linkUI.attr('visibility', 'hidden');
						
						graph.forEachLinkedNode(linkedNode.id, function(linkedNode2, link2){
							if(link2.data.type === 5) {
								var linkUI = graphics.getLinkUI(link2.id);
								linkUI.attr('visibility', 'hidden');
							}									
							});
							
						});
					
				} else if(action === "filtertableon") {
					//asks for the table and their columns
					
					nodeid = document.getElementById('table').value.toLowerCase();
					console.log(nodeid);
					
					var nodeUI = graphics.getNodeUI(nodeid);
					nodeUI.attr('visibility', 'visible');

					graph.forEachLinkedNode(nodeid, function(linkedNode, link){
						
						
						var nodeUI = graphics.getNodeUI(linkedNode.id);
						nodeUI.attr('visibility', 'visible');
						var linkUI = graphics.getLinkUI(link.id);
						linkUI.attr('visibility', 'visible');
						
						graph.forEachLinkedNode(linkedNode.id, function(linkedNode2, link2){
							if(link2.data.type === 5) {
								var linkUI = graphics.getLinkUI(link2.id);
								linkUI.attr('visibility', 'visible');
							}									
							});
						});
				}
				
			});
			
			
			//Creation of a new class to be set by the user.
		
			node = graph.addNode(':newClass:', {text: document.getElementById('nodeid').value, type:1, isPinned: true, description: ''});	

			<?php 
				if(array_key_exists(':newClass:', $layout)) {
					$posx = $layout[':newClass:']["layoutX"];
					$posy = $layout[':newClass:']["layoutY"];
					echo "layout.setNodePosition(node, ".$posx.", ".$posy.");";
				} 
			?>
			
			//If the table node exists then we add the link
			if(layout.getNodePosition(document.getElementById('table').value.toLowerCase())) {
				graph.addLink(document.getElementById('table').value.toLowerCase(), ':newClass:', { connectionStrength: 0.1, type: 3});
			}
			//graph.addLink(':newClass:', document.getElementById('table').value, { connectionStrength: 0.1 , type: 3});
			
			var pos = layout.getNodePosition(':newClass:');
			//renderer.moveTo(pos.x, pos.y);
        };
	
    // or to execute some function
    window.onload = main; //notice no parenthesis
    </script>
	
	
	<div id="graphDiv" class="g_right_col_graph" style="background-image: URL(<?php echo base_url()?>public/img/lightbg.png); height: 680px"> </div>
	<!--br />
	<img src="<?php echo base_url()?>public/img/legend.png" width="300px"-->
	
	
	<div style="display: none;"> 
		<form id='centerForm'>
        <input type='text' id='action' value=""/>
		<input type='text' id='nodeid' value= ""/>
		<input type='text' id='table' value="" />
		<input type='submit' value='center'/>
        </form>
	</div>

