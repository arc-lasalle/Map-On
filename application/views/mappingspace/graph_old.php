
	<!-- CSS's -->
	<link REL=StyleSheet HREF="<?php echo base_url(); ?>/public/css/mappingspace/graph.css" TYPE="text/css" MEDIA=screen>
	<link REL=StyleSheet HREF="<?php echo base_url(); ?>/public/css/common/edition_area.css" TYPE="text/css" MEDIA=screen>

	<!-- WebVOWL CSS's -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/webvowl/webvowl.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/webvowl/webvowl.app.css" />

	<!-- Database Graph CSS's -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/public/css/external/dbgraph/wwwsqldesigner.css" />


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
			$tableCount = count($mapTables);
			$width=600;
			$offset = $width/($tableCount+1);
			$i = 0;
			
			foreach($tables as $row) {

				if(array_key_exists(strtolower($row->name), $mapTables)) {
					echo "nodeTable = graph.addNode('".strtolower($row->name)."', {text: '".$row->name."', type:2, isPinned: true});\n";
					
					if(array_key_exists(strtolower($row->name), $layout)) {
						$posx = $layout[strtolower($row->name)]["layoutX"];
						$posy = $layout[strtolower($row->name)]["layoutY"];
					} else {
						$posx = -$width/2 + ($i+1)*$offset;
						$posy = $i%2 == 0 ? 120 : 220;
					} 
					//Positioning the nodes
					echo "layout.setNodePosition(nodeTable, ".$posx.", ".$posy.");";
					
					$i++;
					
					foreach($columns[$row->id] as $col) {

						$type = ($col->foreignkey == "") ? 3: 5;
						
						if(array_key_exists(strtolower($row->name."_".$col->name), $layout)) {
							echo "nodeCol = graph.addNode('".strtolower($row->name."_".$col->name)."', {text: '".$col->name."', type:".$type.", description: '".$col->type."', isPinned: true});\n";
							$posx = $layout[strtolower($row->name."_".$col->name)]["layoutX"];
							$posy = $layout[strtolower($row->name."_".$col->name)]["layoutY"];
							echo "layout.setNodePosition(nodeCol, ".$posx.", ".$posy.");";
						} else {
							echo "nodeCol = graph.addNode('".strtolower($row->name."_".$col->name)."', {text: '".$col->name."', type:".$type.", description: '".$col->type."'});\n";
						} 
						
						//Positioning the nodes
						echo "graph.addLink('".strtolower($row->name)."', '".strtolower($row->name."_".$col->name)."', { connectionStrength: 0.9, type: 2, label:''});\n\n";
					}
				}
			}
			
			//foreign keys
			foreach($tables as $row) {

				if(array_key_exists(strtolower($row->name), $mapTables)) {
					foreach($columns[$row->id] as $col) {
						
						if($col->foreignkey != "" && $col->foreigntable != "") {
							if(array_key_exists(strtolower($col->foreigntable), $mapTables)) {
								echo "graph.addLink('".strtolower($col->foreigntable."_".$col->foreignkey)."', '".strtolower($row->name."_".$col->name)."', { connectionStrength: 0.7, type: 5, label:''});\n\n";
							}
						}
						
						//echo "graph.addLink('".$row->name."', '".$row->name."_".$col->name."', { connectionStrength: 0.9, type: 2});\n\n";

					}
				}
			}
			$i = 0;
			if(count($mclasses) > 0)
				$offset = $width/(count($mclasses));
			else
				$offset = 1;
			
			//Classes  
			$mapClasses = array();
			foreach($mclasses as $row) {
				if(!array_key_exists($row->class, $mapClasses)) {
					$mapClasses[$row->class] = 1;

					if(array_key_exists($row->class, $layout)) {
						echo "node = graph.addNode('".$row->class."', {text: '".$row->class."', type:1, isPinned: true, id:".$row->id.", uri:'".$row->uri."', description:'".str_replace("'", "",str_replace("\n", ' ', $mappedclassDescription[$row->id]))."'});\n";
						echo "layout.setNodePosition(node, ".$layout[$row->class]["layoutX"].", ".$layout[$row->class]["layoutY"].");";		
					} else {
						echo "node = graph.addNode('".$row->class."', {text: '".$row->class."', type:1, isPinned: true, id:".$row->id.", uri:'".$row->uri."', description:'".str_replace("'", "",str_replace("\n", ' ', $mappedclassDescription[$row->id]))."'});\n";
						$posx = -$width/2 + ($i+1)*$offset;
						$posy = $i%2 == 0 ? -120 : -220;
						echo "layout.setNodePosition(node, ".$posx.", ".$posy.");";
						//echo "layout.setNodePosition(node, 0, -200);";
					}
					$i++;
				}
			}

			//Data properties
			foreach($datapropertiesList as $key => $row) {
					
				if(array_key_exists($row['name'], $layout)) {
					echo "node = graph.addNode('".$row['name']."', {text: '".$row['name']."', type:4, isPinned: true, uri:'".$row['uri']."', mappedclass_id:".$mc_id[$key].", dataproperty_id:".$dp_id[$key]."});\n";
					$posx = $layout[$row['name']]["layoutX"];
					$posy = $layout[$row['name']]["layoutY"];

					echo "layout.setNodePosition(node, ".$posx.", ".$posy.");";
				} else {
					echo "node = graph.addNode('".$row['name']."', {text: '".$row['name']."', type:4, mappedclass_id:".$mc_id[$key].", uri:'".$row['uri']."', dataproperty_id:".$dp_id[$key]."});\n";
				} 
			}

			//Object & data & mappings properties		
			foreach($mclasses as $row) {

				//Object
				foreach($objectproperties[$row->id] as $objp) {
	            	echo "if (graph.getNode('".$objp->target."'))";
					echo "	graph.addLink('".$row->class."', '".$objp->target."', { connectionStrength: 0.65, type: 1, label:'".$objp->objectproperty."', uri:'".$objp->uri."'});\n\n";
				}
				
				//Data properties
				foreach($dataproperties[$row->id] as $objp) {
					echo "if (graph.getNode('".$objp->dataproperty."'))";
					echo "	graph.addLink('".$row->class."', '".$objp->dataproperty."', { connectionStrength: 0.7, type: 4, label:''});\n\n";
				}

				//Mappings
				if($mappings[$row->id] != "") {
					echo "if (graph.getNode('".strtolower($mappings[$row->id])."'))";
					echo "	graph.addLink('".$row->class."', '".strtolower($mappings[$row->id])."', { connectionStrength: 0.1 , type: 3, label:''});\n\n";
				}

			}
			
			foreach($mappingsDP as $row) {
				echo "if (graph.getNode('".strtolower($row[1])."'))";
				echo "	graph.addLink('".$row[0]."', '".strtolower($row[1])."', { connectionStrength: 0.1, type: 3, label:''});\n\n";
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
					.attr('font-size', '9')
					.attr('font-weight', 'normal')
				
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
					ui.append(svgTitle);
        
				} else if(node.data.type === 2) {
					//Table
					bckgr.attr('style', 'fill:rgb(89,79,138)');		//marron: 150,111,72
					svgText.attr('style', 'fill:rgb(256,256,256)');
					/*
					tableBox = Viva.Graph.svg('rect')
                     .attr('width', 10)
                     .attr('height', 10)
					 .attr('y', -2)
					 .attr('fill-opacity', 0.0)
					 .attr('stroke', 'rgb(156,156,156)')
					 .attr('stroke-width', 1)
                     .attr('stroke-dasharray', '2, 1')
					 .attr('id', 'tableBox'+node.id);
					 
					ui.append(tableBox);	
					ui.tableBox	= tableBox;*/
					
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
					bckgr.attr('style', 'fill:rgb(161,207,100); opacity:0.8')	//green: 41,166,121
						 .attr('rx', 7)
						 .attr('ry', 7)
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
				ui.nodeId = node.id;
				
				 ui.addEventListener('click', function (e) {
                        // toggle pinned mode
                        layout.pinNode(node, true);
                    });
				$(ui).mousedown( function(e) {
					showMenu(node, e, true);
				});
				$(ui).mouseup(function(e) {
					showMenu(node, e, true);
					handleMouseUp(e, node);
				}).hover(function() {
					
						if(node.data.type === 1){
								
							hoverLabelClass(node.id, '');
											
							offset = $('#viewbox_'+node.id.replace(":", "--")).position().top;

							contactTopPosition =offset +  $("#listMappedClasses").scrollTop() -60;
							$("#listMappedClasses").animate({scrollTop: contactTopPosition});
						}
                        handleMouseOverNode(node);
                    },
                    function(){
                    	if(node.data.type === 1){
                    		document.getElementById('viewbox_'+node.id.replace(":", "--")).style.backgroundColor = '#faf9fa';
                    	}
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

			var createMarker = function(id) {
                    return Viva.Graph.svg('marker')
                               .attr('id', id)
                               .attr('viewBox', "0 0 10 10")
                               .attr('refX', "8")
                               .attr('refY', "5")
                               .attr('markerUnits', "strokeWidth")
                               .attr('markerWidth', "8")
                               .attr('markerHeight', "4")
                               .attr('orient', "auto")
							   .attr('style', 'fill:rgb(240,89,64)');
                },

            marker = createMarker('Triangle');
            marker.append('path').attr('d', 'M 0 0 L 10 5 L 0 10 z');


			
            var geom = Viva.Graph.geom();

            graphics.link(function(link){
                // Notice the Triangle marker-end attribe:
				var uig = Viva.Graph.svg('g');
				
				uig.data = link.data; 
				uig.data.toId = link.toId;
				uig.data.fromId = link.fromId;
				
                var ui = Viva.Graph.svg('path')
       			    .attr('stroke-width', 1)
				    .attr('fill', 'none');
						   
				uig.append(ui);			
				uig.data.path = ui;
				
				if(link.data.type === 1 ) {
					//class class
					ui .attr('stroke', 'rgb(240,89,64)')		//blue 195,41,15
						.attr('stroke-width', 2)
						.attr('marker-end', 'url(#Triangle)');
						
					var label = Viva.Graph.svg('text').text(link.data.label).attr('font-size', '7').attr('font-weight', 'normal');
					uig.append(label);
					uig.data.label = label;
					
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

				$(uig).mousedown( function(e) {
					showMenu(link, e, false);
				});
				$(uig).mouseup(function(e) {
					showMenu(link, e, false);

				}).hover(function() {
                        handleMouseOverLink(link);
                    },
                    function(){
                        handleMouseLeaveLink(link);
                    });
				
				
				return uig;		   
            }).placeLink(function(linkUI, fromPos, toPos) {
                // Here we should take care about
                //  "Links should start/stop at node's bounding box, not at the node center."

                // For rectangular nodes Viva.Graph.geom() provides efficient way to find
                // an intersection point between segment and rectangle
                var toNodeSize = nodeSize,
                    fromNodeSize = nodeSize;

				
				var nodeUI = graphics.getNodeUI(linkUI.data.fromId);
				var bbox = nodeUI.bckgr.getBBox();
				var offset = 2;
				
                var from = geom.intersectRect(
                        // rectangle:
								fromPos.x - bbox.width / 2 , // left
                                fromPos.y - bbox.height / 2 , // top
                                fromPos.x + bbox.width / 2 , // right
                                fromPos.y + bbox.height / 2 , // bottom
						
                        // segment:
                                fromPos.x, fromPos.y, toPos.x, toPos.y)
                           || fromPos; // if no intersection found - return center of the node
				var nodeUI = graphics.getNodeUI(linkUI.data.toId);
				var bbox = nodeUI.bckgr.getBBox();
				
                var to = geom.intersectRect(
                        // rectangle:
                                toPos.x - bbox.width / 2 -offset, // left
                                toPos.y - bbox.height / 2 -offset, // top
                                toPos.x + bbox.width / 2 +offset*2, // right
                                toPos.y + bbox.height / 2 +offset, // bottom
								
								/*
								toPos.x - toNodeSize / 2, // left
                                toPos.y - toNodeSize / 2, // top
                                toPos.x + toNodeSize / 2, // right
                                toPos.y + toNodeSize / 2, // bottom
								*/
                        // segment:
                                toPos.x, toPos.y, fromPos.x, fromPos.y)
                            || toPos; // if no intersection found - return center of the node

		
				var ry = linkUI.data.type == 3 ? 0 : 0;
                //var data = 'M' + from.x + ',' + from.y +
                //           'L' + to.x + ',' + to.y;
				var	data = 'M' + from.x + ',' + from.y + 
                           ' A 100,' + ry + ',0,0,1,' + to.x + ',' + to.y;
                linkUI.data.path.attr("d", data);
				
				if(linkUI.data.type === 1 ) {
					posx = (from.x + to.x) / 2;
					posy = (from.y + to.y) / 2;
					
					linkUI.data.label.attr("x", posx);
                	linkUI.data.label.attr("y", posy);
					
					if (!linkUI.bbox) {
						linkUI.bbox = linkUI.data.label.getBBox();
					//	var bbox = linkUI.data.label.getBBox();
					
					}
				
					linkUI.data.label.attr('transform',	'translate(' +  ( -linkUI.bbox.width/2) + ',2)');
				}
            });

			renderer.run();
			$('#ea_loader').addClass('hidden'); //Hide mapping loader after render.

            // Marker should be defined only once in <defs> child element of root <svg> element:
            var defs = graphics.getSvgRoot().append('defs');
            defs.append(marker);
			
			setTimeout(function(){renderer.pause();}, 2000);
			
			handleMouseUp = function(e, node) {
				//alert("pined");
				if (e.shiftKey) {
					node.data.isPinned = true; //!node.data.isPinned;
				}
				layout.pinNode(node, !layout.isNodePinned(node));
				//A table node or class has been moved, so, we store the positions.
				//if(node.data.type === 2 || node.data.type === 1) {
					
					node.data.isPinned = true;
					
					var position = layout.getNodePosition(node.id);
					
					$.ajax({ url: '<?php echo base_url().'index.php/mappingspace/storepositions'; ?>',
							 data: {mappingspaceid: <?php echo $mappingspace->id; ?>, nodeid: node.id, layoutX: position.x, layoutY: position.y},
							 type: 'post',
							 success: function(output) {}
					});
				//}

				timer = setTimeout(function(){renderer.pause(); clearTimeout(timer);}, 2000);
			},
			
			highlightNode = function (nodeid, onoff, expand, main) {
				var nodeUI = graphics.getNodeUI(nodeid);

				nodeUI.attr('style', 'opacity:1.0'); //marron: 150,111,7
				
				if(onoff) {
					if(main){
						nodeUI.bckgr.attr('stroke', 'rgb(000,000,000)').attr('stroke-width', 2.5);						
					} else{
						nodeUI.bckgr.attr('stroke', 'rgb(50,50,50)').attr('stroke-width', 1.5);					
					}
				} else {
					nodeUI.bckgr.attr('stroke-width', 0);
				}
				
				if(expand) {
					graph.forEachLinkedNode(nodeid, function(nodeTo, link){
						var linkUI = graphics.getLinkUI(link.id);
						if (linkUI && link.data.type == 3) {
						
							
							highlightNode(link.toId, onoff, false, false);
							
							if(onoff) {
								linkUI.data.path.attr('stroke-width', 3);
								linkUI.data.path.attr('style', 'opacity:1.0');
							
							} else {
								linkUI.data.path.attr('stroke-width', 1);
							}
							
						}
					});
				}
				//showing the description (type of the column)
				if (nodeUI.svgDescription) {
					
					if(onoff) 	nodeUI.svgDescription.attr('visibility', 'visible');
					else 		nodeUI.svgDescription.attr('visibility', 'hidden');
					
				}
			}
				
			handleMouseOverNode = function(node) {
				
				HideAllNodes(true);
				
				handleMouseOverNodeId(node.id, node.data.type, true);
			},
			
			handleMouseOverNodeId = function(nodeid, type, main) {
			
				highlightNode(nodeid, true, true, main);
				
				graph.forEachLinkedNode(nodeid, function(nodeTo, link){
					var linkUI = graphics.getLinkUI(link.id);
					if (linkUI) {
					    linkUI.data.path.attr('stroke-width', 3);
					    linkUI.data.path.attr('style', 'opacity:1.0');

						if(linkUI.data.label)
							linkUI.data.label.attr('font-weight', 'bold').attr('font-size', '8');

						if(link.toId != nodeid)
							highlightNode(link.toId, true, true, false);
						if(link.fromId != nodeid)
							highlightNode(link.fromId, true, true, false);
						
						if(type === 2) {
							if(link.toId != nodeid)
								handleMouseOverNodeId(nodeTo.id, nodeTo.data.type, false);
						}
					}
				});
			},

			HideAllNodes = function(onoff) {
			
				graph.forEachNode(function(node){
					var nodeUI = graphics.getNodeUI(node.id);
					if(onoff) {
						nodeUI.attr('style', 'opacity:0.5'); //marron: 150,111,7
							
					} else {
						nodeUI.attr('style', 'opacity:1.0'); //marron: 150,111,7
					}
				});
				
				graph.forEachLink(function(link){
					var linkUI = graphics.getLinkUI(link.id);
					if(onoff) {
						linkUI.data.path.attr('style', 'opacity:0.5'); //marron: 150,111,7
							
					} else {
						linkUI.data.path.attr('style', 'opacity:1.0'); //marron: 150,111,7
					}
				});
			},
				
			handleMouseLeaveNode = function(node) {
				
				HideAllNodes(false);
				
				highlightNode(node.id, false, true, false);
				
				graph.forEachLinkedNode(node.id, function(nodeTo, link){
					var linkUI = graphics.getLinkUI(link.id);
					if (linkUI) {
						if(link.data.type === 1 || link.data.type === 2 || link.data.type === 4) 
							linkUI.data.path.attr('stroke-width', 2);
						else 
							linkUI.data.path.attr('stroke-width', 1);
					
						if(linkUI.data.label)
							linkUI.data.label.attr('font-weight', 'normal').attr('font-size', '7');
							
						highlightNode(link.toId, false, true, false);
						highlightNode(link.fromId, false, true, false);
						
						if(node.data.type === 2) {
							if(link.toId != node.id)
								handleMouseLeaveNode(nodeTo);						
						}
					}
				});
			},
		   
			handleMouseOverLink = function(link) {
			   
				var linkUI = graphics.getLinkUI(link.id);
				linkUI.data.path.attr('stroke-width', 3);
				if(linkUI.data.label)
					linkUI.data.label.attr('font-weight', 'bold').attr('font-size', '8');
							
				highlightNode(link.toId, true, true, false);
				highlightNode(link.fromId, true, true, false);

			},

			handleMouseLeaveLink = function(link) {
				var linkUI = graphics.getLinkUI(link.id);
				if(link.data.type === 1 || link.data.type === 2 || link.data.type === 4) 
					linkUI.data.path.attr('stroke-width', 2);
				else
					linkUI.data.path.attr('stroke-width', 1);
				
				if(linkUI.data.label)
					linkUI.data.label.attr('font-weight', 'normal').attr('font-size', '7');
							
				highlightNode(link.toId, false, true, false);
				highlightNode(link.fromId, false, true, false);

			};
			
			$('#highlightForm').submit(function(e) {
				e.preventDefault();
				var action = $('#action').val();
				var nodeId = $('#nodeid').val();
				
				var table = $('#table').val();
				
		
				if(action === "highlight") {
					handleMouseOverNode(graph.getNode(nodeId));
				} else {
					handleMouseLeaveNode(graph.getNode(nodeId));
				}
				
			});
        };
	
    // or to execute some function
    window.onload = main; //notice no parenthesis
    </script>

<!--
	<div class="ui green segment">
		<div class="ui two column divided grid">
			<div class="ui column">
				Data source: <span style="font-weight:bold;"><a href="<?php echo base_url();?>index.php/datasource/view/<?php echo $datasource->id; ?>"><?php echo $datasource->name; ?></a></span><br />
				Ontology: <span style="font-weight:bold;"><?php echo $ontologyName; ?></span><br />
				Modified: <span style="font-weight:bold;"><?php echo $datasource->date; ?></span><br />
			</div>
			<div class="ui column">
				Mapping space: <span style="font-weight:bold;"><a href="<?php echo base_url();?>index.php/mappingspace/graph/<?php echo $datasource->id; ?>/<?php echo $mappingspace->id; ?>"><?php echo $mappingspace->name; ?></a></span> <br />
			</div>
		</div>
	</div>
-->
	<div class="ui stackable grid">
		<div class="five wide column">

			<div class="ui green segment g_left_col">

				<!-- Mapping Options -->
				<div id="options_1_mapping">
					<div id="content">
						<table width="100%"><tr><td>
								Mappings:  <a href="<?php echo base_url();?>index.php/mappedclass/createnew/<?php echo $datasource_id."/".$mappedspace_id; ?>"><i class="circular add purple link icon" data-content="Click it for creating a new mapping between a class and a table "></i></a>
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
		<div class="eleven wide column">

			<div class="ui green segment g_right_col">
				<div class="ui small header">
					<table width="100%"><tr><td>
					<div class="right_colum_title"><strong>Graph representation of the mappings</strong></div>
					</td><td style="text-align: right">		
						<i class="circular history purple link icon" onclick="sidebar();" data-position="top left" data-html="click to too see the history log"></i>
					</td></tr></table>
				</div>
				<div class="content field">

					<div id="ea_loader" class="hidden"></div>

					<!-- Mapping Graph -->
					<div id="graph_1_mapping" style="overflow: hidden">
						<div id="graphDiv" class="g_right_col_graph avoid_right_click" style="background-image: URL(<?php echo base_url()?>public/img/lightbg.png); height:650px "></div>
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
	
	<div style="display: none;"> 
		<form id='highlightForm'>
        <input type='text' id='action' value=""/>
		<input type='text' id='nodeid' value= ""/>
		<input type='text' id='table' value="" />
		<input type='submit' value='center'/>
        </form>
	</div>
	
<script>
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

			$("#suggest_Mapping").load('<?php echo site_url("mappingspace/searchmapping"); ?>', { string: document.getElementById('input_mapping').value, mappingspace_id: <?php echo $mappingspace->id; ?>,datasource_id: <?php echo $datasource_id; ?>  } );

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
		document.getElementById('action').value = "highlight";
		document.getElementById('nodeid').value = string_uri;
		document.getElementById('table').value = "";

		hoverLabelClass(string_uri, '');
		//document.getElementById('viewbox_'+string_uri.replace(":", "--")).style.backgroundColor = 'rgb(252,221,216)';
		
		$( "#highlightForm" ).submit();

    }
	/*
	///////////////////////////////////////////////////
	// JS functions for Table selection
	//
	function suggestTable(idTable) {
		document.getElementById('hidden_search_inputtext_table').value = "";
	
		//alert(idTable);
		if(document.getElementById(idTable).value != ""){   

			$("#suggest_Table").load('<?php echo site_url("mapping/suggesttable"); ?>', { string: document.getElementById(idTable).value, datasource_id: <?php echo $datasource_id; ?>, target: idTable } );

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
		$("#input_sql").load('<?php echo site_url("mapping/generateSQL"); ?>', { input_table: string_uri, datasource_id: <?php echo $datasource_id; ?> } );
		//To autocomplete the URI section
		$("#input_uri").load('<?php echo site_url("mapping/generateURI"); ?>', { input_class: document.getElementById('input_class').value, input_table: document.getElementById(idTable).value, datasource_id: <?php echo $datasource_id; ?> } );
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
		
		document.getElementById('action').value = "highlight";
		document.getElementById('nodeid').value = mappedclass_id;
		document.getElementById('table').value = "";

		document.getElementById('viewbox_'+mappedclass_id.replace(":", "--")).style.backgroundColor = 'rgb(253,230,226)';//#e0eee0';
		
		$( "#highlightForm" ).submit();
		
	}
	
	function outLabelClass(mappedclass_id, table) {
		
		document.getElementById('action').value = "nohighlight";
		document.getElementById('nodeid').value = mappedclass_id;
		document.getElementById('table').value = "";

		document.getElementById('viewbox_'+mappedclass_id.replace(":", "--")).style.backgroundColor = '#faf9fa';

		$( "#highlightForm" ).submit();

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
	<script type="text/javascript">
		var php_vars = JSON.parse(unescape('<?php echo addslashes( json_encode($_ci_data['_ci_vars']) ); ?>'));
		php_vars.base_url = '<?php echo base_url(); ?>';
		php_vars.db_tables = '<?php echo json_encode($tables); ?>';
		php_vars.db_columns = '<?php echo json_encode($columns); ?>';
		php_vars.user_technician = <?php echo ($this->ion_auth->in_group("technician")==1) ? "true" : "false"  ?>;
	</script>

	<script src="<?php echo base_url(); ?>/public/js/common/edition_area.js"></script>
	<script src="<?php echo base_url(); ?>/public/js/mappingspace/graph.js" language="javascript" type="text/javascript" ></script>

	<!-- WebVowl JS's -->
	<script src="<?php echo base_url(); ?>/public/js/external/webvowl/d3.min.js"></script>
	<script src="<?php echo base_url(); ?>/public/js/external/webvowl/webvowl.js"></script>
	<script src="<?php echo base_url(); ?>/public/js/external/webvowl/webvowl.app.js"></script>

	<!-- Database Graph JS's -->
	<script src="<?php echo base_url(); ?>/public/js/external/dbgraph/wwwsqldesigner.min.js"></script>