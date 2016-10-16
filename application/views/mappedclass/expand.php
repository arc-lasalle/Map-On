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
			
			var width = document.getElementById('graphDiv').getBoundingClientRect().width;
			<?php
			
			//root
			echo "node = graph.addNode('".$root."', {text: '".$root."', type:1, isPinned: true, description:''});\n";
			echo "layout.setNodePosition(node, -width/3, 0);"
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
				} else if(node.data.type === 6) {
					//Class
					bckgr.attr('style', 'fill:rgb(239,65,91)')		//naranja 204,159,42
						 .attr('rx', 7)
						 .attr('ry', 7)
					svgText.attr('style', 'fill:rgb(256,256,256)');
					
					svgTitle = Viva.Graph.svg('title')
						.text(node.data.description);
					ui.append(svgTitle);
				}
			
				ui.data = node.data;
				ui.append(bckgr);
				ui.append(svgText);
				ui.bckgr = bckgr;
				ui.svgText = svgText;
				ui.nodeId = node.id;
				
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

			var createMarker = function(id, color) {
                    return Viva.Graph.svg('marker')
                               .attr('id', id)
                               .attr('viewBox', "0 0 10 10")
                               .attr('refX', "8")
                               .attr('refY', "5")
                               .attr('markerUnits', "strokeWidth")
                               .attr('markerWidth', "8")
                               .attr('markerHeight', "4")
                               .attr('orient', "auto")
							   .attr('style', 'fill:rgb('+color+')');
                },

            markerClass = createMarker('markerClass', '240,89,64');
            markerClass.append('path').attr('d', 'M 0 0 L 10 5 L 0 10 z');

			markerSubClass = createMarker('markerSubClass', '239,65,139');
            markerSubClass.append('path').attr('d', 'M 0 0 L 10 5 L 0 10 z');
			
			markerSelectedClass = createMarker('markerSelectedClass', '0,159,218');
            markerSelectedClass.append('path').attr('d', 'M 0 0 L 10 5 L 0 10 z');
			
            var geom = Viva.Graph.geom();

            graphics.link(function(link){
                // Notice the Triangle marker-end attribe:
                var ui = Viva.Graph.svg('path')
       			    .attr('stroke-width', 2)
				    .attr('fill', 'none');
						   
				if(link.data.type === 1 ) {
					//class class
					ui .attr('stroke', 'rgb(240,89,64)')		//blue 195,41,15
						.attr('stroke-width', 2)
						.attr('marker-end', 'url(#markerClass)');;
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
				} else if(link.data.type === 6 ) {
					//class subclass
					ui .attr('stroke', 'rgb(239,65,139)')		//blue 195,41,15
						.attr('stroke-width', 2)
						.attr('marker-end', 'url(#markerSubClass)');
				}
				ui.data = link.data; 
				ui.data.toId = link.toId;
				ui.data.fromId = link.fromId;
				
				
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

            // Marker should be defined only once in <defs> child element of root <svg> element:
            var defs = graphics.getSvgRoot().append('defs');
            defs.append(markerClass);
			defs.append(markerSubClass);
			defs.append(markerSelectedClass);
			
			setTimeout(function(){renderer.pause();}, 1000);
			
			handleMouseUp = function(e, node) {
				//alert("pined");
				//if (e.shiftKey) {
				
				var wasPinned = layout.isNodePinned(node);

				console.log ("Pinned  "+node.id+"  ? " +wasPinned);
				console.log ("Pinned  "+node.id+"  ? " +node.data.isPinned);
				//node.data.isPinned = true;
				//node.isPinned = true;
				//layout.pinNode(node, 1); //!node.data.isPinned;
				
				layout.pinNode(node, !layout.isNodePinned(node));

				//}
				
				if(node.data.opened !== 1)  {
					node.data.opened = 1;
					//alert ("opened");
					
					$.ajax({ url: '<?php echo base_url().'index.php/ontology/loadclass'; ?>',
					data: {ontologyid: <?php echo $ontology_id; ?>, class: node.id},
					type: 'post',
					dataType: 'json',
					success: function(data) {
							loadClasses(graph, node.id, data);
						}
					});
				}
				
				
				selectPath(node.id);
			}
			highlightNode = function (nodeid, onoff) {
				var nodeUI = graphics.getNodeUI(nodeid);

				if(onoff) {
					nodeUI.bckgr.attr('stroke', 'rgb(000,000,000)')
									.attr('stroke-width', 2);
				} else {
					nodeUI.bckgr.attr('stroke-width', 0);
				}
				//showing the description (type of the column)
				if (nodeUI.svgDescription) {
					if(onoff) 	nodeUI.svgDescription.attr('visibility', 'visible');
					else 		nodeUI.svgDescription.attr('visibility', 'hidden');
					
				}
				
			}
			
			selectPath = function (nodeid) {

				var visited = new Array();
				
				var pathHtml = '';
				var outCompletePath =  { text :  ''};
				
				
				pathHtml = recursivePathHTMLDFS (nodeid, visited, graph, 1, outCompletePath);

				//console.log(visited);
			    $('#select_path').html(pathHtml);
//				$('#input_select_path').value = pathHtml;
				
				document.getElementById('input_select_path').value = outCompletePath.text;
			}
			
				
			recursivePathHTMLDFS = function (nodeid, visited, graph, backlinktype, outCompletePath) {
				visited[nodeid] = 1;
				var ret = "";
				
				if(nodeid == '<?php echo $root; ?>') {
					console.log("found: " + nodeid);
					outCompletePath.text = nodeid;
					return  '<div class="ui small orange circular image label" style="cursor:pointer;" >'+nodeid+'</div>';
				}
				
				graph.forEachLinkedNode(nodeid, function(nodeTo, link){
					if(nodeTo.id in visited && ret == "") {
					} else {
						retRec = recursivePathHTMLDFS(nodeTo.id, visited, graph, link.data.type, outCompletePath);

						if( retRec != "") {
							console.log("retRec: " + retRec);
							console.log("nodeid: " + nodeid);
							console.log("nodeTo.id: " + nodeTo.id);
							console.log("link.data.type: " + link.data.type);
							console.log("link.data.objprop: " + link.data.objprop);
							console.log("backlinktype: " + backlinktype);
							console.log("----------- " );
							//The link is a subclassof?
							if(backlinktype != 6) {
								ret = retRec + '<i class="long arrow right icon"></i> <div class="ui small orange circular image label" style="cursor:pointer;" >'+nodeid+'</div> ';
								
								if(link.data.type == 6)
									outCompletePath.text = outCompletePath.text + '|' + nodeid;
								else 
									outCompletePath.text = outCompletePath.text + '|' + link.data.objprop + '|' + nodeid;
							} else {
								ret = retRec;
								
								outCompletePath.text = outCompletePath.text + '|' + link.data.objprop;
							}
						}
					}
				});
				
				return ret;
			}
			
			
			selectPathNode = function (nodeid, onoff) {
				
				selectNode(nodeid, onoff);

				var visited = new Array();
				
				recursiveDFS (nodeid, visited, graph, onoff);
			}
			
			selectNode  = function (nodeid, onoff) {
				var nodeUI = graphics.getNodeUI(nodeid);

				if(onoff) {
					nodeUI.bckgr.attr('stroke', 'rgb(0,159,218)')
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
			
			selectLink = function (link, onoff) {
				var linkUI = graphics.getLinkUI(link.id);
				if (linkUI) {
					if(onoff) {
						linkUI  .attr('stroke', 'rgb(0,159,218)')
								.attr('stroke-width', 3)
								.attr('marker-end', 'url(#markerSelectedClass)');
						
					} else {
						if(link.data.type === 1 ) {
							linkUI  .attr('stroke', 'rgb(240,89,64)')		//blue 195,41,15
									.attr('stroke-width', 2)
									.attr('marker-end', 'url(#markerClass)');
						} else if(link.data.type === 6 ) {
							//class subclass
							linkUI  .attr('stroke', 'rgb(239,65,139)')		//blue 195,41,15
									.attr('stroke-width', 2)
									.attr('marker-end', 'url(#markerSubClass)');
						}
					}
				}
			}
			
			recursiveDFS = function (nodeid, visited, graph, onoff) {
				visited[nodeid] = 1;
				var ret = 0;
				
				if(nodeid == '<?php echo $root; ?>') {
					selectNode(nodeid, onoff);
					return 1;
				}
				
				graph.forEachLinkedNode(nodeid, function(nodeTo, link){
					if(nodeTo.id in visited) {
					} else {
						if(recursiveDFS(nodeTo.id, visited, graph, onoff)) {
							selectLink(link, onoff);
							ret = 1;
						}
					}
				});
				
				if(ret) {
					selectNode(nodeid, onoff);
				}
				
				return ret;
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
				
				
				selectPathNode(node.id, true);
			},
			
			handleMouseLeaveNode = function(node) {
				
				highlightNode(node.id, false);
				
				graph.forEachLinkedNode(node.id, function(nodeTo, link){
					var linkUI = graphics.getLinkUI(link.id);
					if (linkUI) {
						if(link.data.type === 1 || link.data.type === 2 || link.data.type === 4 || link.data.type === 6) 
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
				
				selectPathNode(node.id, false);
			};
		   
			handleMouseOverLink = function(link) {
			   
				var linkUI = graphics.getLinkUI(link.id);
				linkUI.attr('stroke-width', 3);
				
				highlightNode(link.toId, true);
				highlightNode(link.fromId, true);

			},

			handleMouseLeaveLink = function(link) {
				var linkUI = graphics.getLinkUI(link.id);
				if(link.data.type === 1 || link.data.type === 2 || link.data.type === 4 || link.data.type === 6) 
					linkUI.attr('stroke-width', 2);
				else
					linkUI.attr('stroke-width', 1);
				
				highlightNode(link.toId, false);
				highlightNode(link.fromId, false);

			};
			
			
			//We are loading the root element
			$.ajax({ url: '<?php echo base_url().'index.php/ontology/loadclass'; ?>',
			data: {ontologyid: <?php echo $ontology_id; ?>, class: '<?php echo $root; ?>'},
			type: 'post',
			dataType: 'json',
			success: function(data) {
				
				loadClasses(graph, '<?php echo $root; ?>', data) ;
					
				}
			});
			
			
			loadClasses = function(graph, source, data) 
			{
				var pos = layout.getNodePosition(source);
//				console.log(pos);
				for (n in data) {
				/*
					if(data[n] == 1) {
						node1 = graph.addNode(n, {text: n, type:1, description:''});
						layout.setNodePosition(node1, pos.x+10, pos.y+10);
						graph.addLink(source, n, { connectionStrength: 0.65, type: 1});
					} else if(data[n] == 2) {
						node2 = graph.addNode(n, {text: n, type:6, description:''});
						layout.setNodePosition(node2, pos.x, pos.y);
						graph.addLink(n, source, { connectionStrength: 0.65, type: 6});
					} else if(data[n] == -1) {
						node2 = graph.addNode(n, {text: n, type:6, description:''});
						layout.setNodePosition(node2, pos.x, pos.y);
						graph.addLink(n, source, { connectionStrength: 0.65, type: 1});
					}
					*/
					if(data[n] == "http://www.w3.org/2000/01/rdf-schema#subClassOf") {
						node2 = graph.addNode(n, {text: n, type:6, description:''});
						layout.setNodePosition(node2, pos.x + Math.floor((Math.random() * 100) - 50), pos.y + Math.floor((Math.random() * 100) - 50));
						graph.addLink(n, source, { connectionStrength: 0.65, type: 6, objprop: data[n]});
					} else {
						node1 = graph.addNode(n, {text: n, type:1, description:''});
						layout.setNodePosition(node1, pos.x+Math.floor((Math.random() * 100) - 50), pos.y+Math.floor((Math.random() * 100) - 50));
						graph.addLink(source, n, { connectionStrength: 0.65, type: 1, objprop: data[n]});
					}
				}
			}
			
			$('.pause').click(function () {
                    renderer.pause()
                });
			$('.reset').click(function () {
                    
					graph.clear ();
					<?php 	echo "node = graph.addNode('".$root."', {text: '".$root."', type:1, isPinned: true, description:''});\n";
							echo "layout.setNodePosition(node, -width/3, 0);"
					?>
					
					$.ajax({ url: '<?php echo base_url().'index.php/ontology/loadclass'; ?>',
						data: {ontologyid: <?php echo $ontology_id; ?>, class: '<?php echo $root; ?>'},
						type: 'post',
						dataType: 'json',
						success: function(data) {
							
							loadClasses(graph, '<?php echo $root; ?>', data) ;
								
							}
						});
                });
        };
	
	
		
					
    // or to execute some function
    window.onload = main; //notice no parenthesis
	

	
	
    </script>
	
	<div class="ui segment ">
		<div class="actions"><div class="ui mini reset button" >Reset graph</div>  <div class="ui mini pause button" >Pause graph rendering</div>  <div class="ui mini save button">Save path</div>
			<div class="ui icon mini input">
				<input type="text" id="input_class" name="input_class" autocomplete="off" value="Class name..." onkeyup="chk_suggestClass();">
				<i class="search icon"></i>
			</div>
			<input id="hidden_search_inputtext_class" name="hidden_search_inputtext_class" type="hidden" value = "">
			<div id="suggest_Class" class="search_box_class"> </div>
		</div>
	</div>
	
	<div class="ui segment ">
		<div class="ui small header">
			<strong>Expand mapping</strong> 
		</div>	
		
		<div class="ui form secondary accordion fluid segment" >
		<?php 	echo form_open_multipart(base_url().'index.php/mappedclass/expand_post');
				echo form_hidden('mappingspace_id', $mappingspace_id); 
				echo form_hidden('datasource_id', $datasource_id);
				echo form_hidden('mappedclass_id', $mappedclass_id);
		?>
			<input id="input_select_path" name="input_select_path" type="hidden" value = "">
			<span>Path: <span id="select_path"><div class="ui small orange circular image label" style="cursor:pointer;" ><?php echo $root;?></div>
			<i class="long arrow right icon"></i></span>
		</span>
		</div>
		<div class="actions">
			<div class="ui tiny button" onmouseup="window.location.href = '<?php echo base_url();?>index.php/mappingspace/graph/<?php echo $datasource_id."/".$mappingspace_id; ?>';">Cancel</div> <input type="submit" value="Save path" class="ui tiny button" />
		</div>
		<?php echo form_close(); ?>
	</div>	
	<div class="ui segment ">
		<div id="graphDiv" style="background-image: URL(<?php echo base_url()?>public/img/lightbg.png); height:600px "></div>
	</div>

		
