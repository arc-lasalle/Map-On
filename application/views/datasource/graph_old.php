
	<script type="text/javascript">
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

				echo "nodeTable = graph.addNode('".$row->name."', {text: '".$row->name."', type:2, isPinned: true});\n";
				
				$posx = $row->layoutX;
				$posy = $row->layoutY;
				
				if($row->layoutX == 0 && $row->layoutY == 0) {
					$posx = -$width/2 + ($i+1)*$offset;
					$posy = $i%2 == 0 ? -100 : 100;
					//Positioning the nodes
				} 
				echo "layout.setNodePosition(nodeTable, ".$posx.", ".$posy.");";

				$i++;
				
				foreach($columns[$row->id] as $col) {

					$type = ($col->foreignkey == "") ? 3: 5;
					echo "nodeCol = graph.addNode('".$row->name."_".$col->name."', {text: '".$col->name."', type:".$type.", description: '".$col->type."'});\n";
					echo "layout.setNodePosition(nodeCol, ".$posx.", ".$posy.");";

					//Positioning the nodes
					echo "graph.addLink('".$row->name."', '".$row->name."_".$col->name."', { connectionStrength: 0.9, type: 2});\n\n";
				}
				
			}
			
			//foreign keys
			foreach($tables as $row) {
			
				foreach($columns[$row->id] as $col) {
					
					if($col->foreignkey != "" && $col->foreigntable != "") {
						echo "graph.addLink('".$col->foreigntable."_".$col->foreignkey."', '".$row->name."_".$col->name."', { connectionStrength: 0.8, type: 5});\n\n";
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
					.attr('cursor', 'pointer')
					.attr('class', 'text-link')
					.attr('font-size', '11'),
									
				bckgr = Viva.Graph.svg('rect')
                     .attr('width', nodeSize)
                     .attr('height', nodeSize+4)
					 .attr('y', -2)
					 .attr('id', 'background'+node.id);

				if(typeof node.data !== 'undefined')
					svgText.text(node.data.text)
				else
					alert(node.id);
				
					
 					 
				if(node.data.type === 1) {
					//Class
					bckgr.attr('style', 'fill:rgb(240,89,64)');		//naranja 204,159,42
					svgText.attr('style', 'fill:rgb(256,256,256)');
				} else if(node.data.type === 2) {
					//Table
					bckgr.attr('style', 'fill:rgb(89,79,138)');		//marron: 150,111,72
					svgText.attr('style', 'fill:rgb(256,256,256)');
				} else if(node.data.type === 3) {
					//Table Column
					bckgr.attr('style', 'fill:rgb(89,79,138); opacity:0.8'); //marron: 150,111,7
					svgText.attr('style', 'fill:rgb(256,256,256)');
					
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
					svgText.attr('style', 'fill:rgb(256,256,256)');
					
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
					nodeUI.bckgr.attr('width', nodeUI.bbox.width+6);
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
					ui.attr('stroke', 'rgb(140,101,62)')	///red 256,0,0
                    .attr('stroke-dasharray', '5, 1')
					.attr('stroke-width', 1);
				} else if(link.data.type === 4 ) {
					//Table-column
					ui.attr('stroke', 'rgb(41,166,121)')
					.attr('stroke-width', 1)
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
			
			var timer;
			timer = setTimeout(function(){renderer.pause(); clearTimeout(timer);}, 1000);
			
			
			handleMouseUp = function(e, node) {
				//alert("pined");
				if (e.shiftKey) {
					node.data.isPinned = true; //!node.data.isPinned;
				}
				

				
				//A table node has been moved, so, we store the positions.
				if(node.data.type === 2) {
				var position = layout.getNodePosition(node.id);
					
					$.ajax({ url: '<?php echo base_url().'index.php/datasource/storepositions'; ?>',
							 data: {datasourceid: <?php echo $datasource_id; ?>, nodeid: node.id, layoutX: position.x, layoutY: position.y},
							 type: 'post',
							 success: function(output) {}
					});
				}
				
				timer = setTimeout(function(){renderer.pause(); clearTimeout(timer);}, 1000);
			},
			
			highlightNode = function (nodeid, onoff) {
				var nodeUI = graphics.getNodeUI(nodeid);

				if(onoff) {
					nodeUI.bckgr.attr('stroke', 'rgb(119,88,57)')
									.attr('stroke-width', 3);
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
				
				graph.forEachLinkedNode(node.id, function(node, link){
					var linkUI = graphics.getLinkUI(link.id);
					if (linkUI) {
					   linkUI.attr('stroke-width', 2);
					    
						highlightNode(link.toId, true);
						highlightNode(link.fromId, true);

					}
				});
			},

			
			handleMouseLeaveNode = function(node) {
				
				highlightNode(node.id, false);
				
				graph.forEachLinkedNode(node.id, function(node, link){
					var linkUI = graphics.getLinkUI(link.id);
					if (linkUI) {
						linkUI.attr('stroke-width', 1);
					
						highlightNode(link.toId, false);
						highlightNode(link.fromId, false);
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
				linkUI.attr('stroke-width', 1);
				
				highlightNode(link.toId, false);
				highlightNode(link.fromId, false);

			};
        };
	
    // or to execute some function
    window.onload = main; //notice no parenthesis
    </script>
	
	
	<div id="graphDiv" style="background-image: URL(<?php echo base_url()?>public/img/lightbg.png); height: 350px; padding:10px;"> </div>
	<br />
	<img src="<?php echo base_url()?>public/img/legend.png" width="300px">
	

