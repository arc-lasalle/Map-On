function mapping_graph () {
    var self = this;
    this.graph = Viva.Graph.graph();
    this.graphics = Viva.Graph.View.svgGraphics();
    this.geom = Viva.Graph.geom();



    var idealLength = 300;
    this.layout = Viva.Graph.Layout.forceDirected(this.graph, {
        springLength : idealLength,
        springCoeff : 0.0008,
        dragCoeff : 0.02,
        gravity : -10,

        springTransform: function (link, spring) {
            spring.length = idealLength * (1 - link.data.connectionStrength);
            
            if ( typeof link.data.coeff != 'undefined' ) {
                spring.coeff = link.data.coeff;
            }
            
        }
    });

    this.renderer = Viva.Graph.View.renderer(this.graph, {
        container  : document.getElementById('graphDiv'),
        graphics : this.graphics,
        layout : this.layout
    });


    this.graphics.node(  function ( node ) {
        return self.drawNode( self, node );
    });

    this.graphics.placeNode( function ( nodeUI, pos ) {
        return self.placeNode( self, nodeUI, pos );
    } );

    this.graphics.link( function ( link ) {
        return self.drawLink( self, link );
    } );

    this.graphics.placeLink( function ( linkUI, fromPos, toPos ) {
        return self.placeLink( self, linkUI, fromPos, toPos );
    });
}



mapping_graph.prototype.draw = function () {
    var self = this;
    var col, table, type, classNode,dpNode;

    var width = 900;
    var i = 0;
    var numTables = Object.keys(php_vars.mp_graph.tables).length;
    var offset = width/(numTables+1);

    // Tables and columns
    for ( var tableKey in php_vars.mp_graph.tables ) {

        table = php_vars.mp_graph.tables[tableKey];

        //if ( table['enabled'] == false ) continue;

        var posX = parseInt(table['layoutX']);
        var posY = parseInt(table['layoutY']);

        if ( posX == 0 && posY == 0 ) {
            posX = -width/2 + (i+1)*offset;
            posY = (i%2 == 0) ? -100 : 100;
        }

        this.addNode( {id: table['name'], type: 'table', text: table['name'], desc: '', x: posX, y: posY, visible: table['enabled']} );

        for ( var colKey in table.columns ) {

            col = table.columns[colKey];

            var nodeData = { parent: table['name'], id: table['name']+"_"+col['name'], type: 'column', text: col['name'], desc: col['type'], visible: table['enabled'] };

            if ( col['foreignkey'] != "" ) nodeData.type = 'columnWithFk';


            if ( typeof col['layoutX'] != 'undefined' && typeof col['layoutY'] != 'undefined' ) {
                nodeData.x = col['layoutX'];
                nodeData.y = col['layoutY'];
            }

            this.addNode( nodeData );

        }

        i++;
    }

    // Table FK's
    for ( var tableKey in php_vars.mp_graph.tables ) {
        table = php_vars.mp_graph.tables[tableKey];

        for ( var colKey in table.columns ) {
            col = table.columns[colKey];
            if ( col['foreigntable'] == "" ) continue;

            this.addLink( col['foreigntable']+"_"+col['foreignkey'],  table['name']+"_"+col['name'], 'column-column' );

        }
    }

    if ( typeof php_vars.mp_graph.classes !== 'undefined' ) {

        // Classes & ObjectProperties
        var numClasses = Object.keys(php_vars.mp_graph.classes).length;
        offset = (numClasses > 0) ? width / numClasses : 1;

        i = 0;

        for (var classKey in php_vars.mp_graph.classes) {
            classNode = php_vars.mp_graph.classes[classKey];

            var nodeData = {
                id: classNode['qname'],
                type: 'class',
                text: classNode['qname'],
                desc: classNode['description'],
                extra: { class_id: classNode['id'], uri: classNode['class'] }
            }



            if (typeof classNode['objectProperty'] != 'undefined') {
                nodeData.parent = classNode['objectProperty']['domainQname'];
                nodeData.linkDesc = classNode['objectProperty']['qname'];
            }

            if ( typeof classNode['layoutX'] != 'undefined' && typeof classNode['layoutY'] != 'undefined' ) {
                nodeData.x = parseInt(classNode['layoutX']);
                nodeData.y = parseInt(classNode['layoutY']);
            }

            this.addNode( nodeData );
            this.addLink( classNode['qname'], classNode['tableLink'], 'table-class', {pull: false} );

            i++;
        }

        // Data propierties
        for (var dpKey in php_vars.mp_graph.data_properties) {
            dpNode = php_vars.mp_graph.data_properties[dpKey];

            var nodeData = {
                parent: dpNode['domainQname'],
                id: dpNode['qname'],
                type: 'dataProperty',
                text: dpNode['qname'],
                desc: '',
                extra: {
                    class_id: dpNode['mappedclass_id'],
                    dataproperty_id: dpNode['id'],
                    uri: dpNode['dataproperty']
                }
            }

            if ( typeof dpNode['layoutX'] != 'undefined' && typeof dpNode['layoutY'] != 'undefined' ) {
                nodeData.x = parseInt(dpNode['layoutX']);
                nodeData.y = parseInt(dpNode['layoutY']);
            }

            this.addNode( nodeData );

            this.addLink(dpNode['qname'], dpNode['rangeTable'], 'table-class' );

        }

    }
    

    this.renderer.run();
    $('#ea_loader').addClass('hidden');

    var timer;
    timer = setTimeout(function(){
        self.renderer.pause();
        clearTimeout(timer);
    }, 10000);

};

// Node data
// {id:'', type:'', text:'', desc:''}
// Optional params {x:123, y:123}, {parent:''}, {linkDesc: ''} {visible: ''} {extra: {} }
mapping_graph.prototype.addNode = function ( nodeData ) {
    nodeData.x = parseInt(nodeData.x);
    nodeData.y = parseInt(nodeData.y);

    if ( typeof nodeData.id != 'string' || typeof nodeData.text != 'string' || typeof nodeData.type != 'string' || typeof nodeData.desc != 'string' ) {
        console.error("Error adding the node, invalid param type: ", nodeData );
        return false;
    }


    if ( typeof this.nodeList == 'undefined' ) this.nodeList = [];
    if ( this.nodeList.indexOf(nodeData.id) != -1 && nodeData.id != 'tempnode' ){
        console.log("Adding node (", nodeData.id, ") twice.");
        return;
    }
    this.nodeList.push(nodeData.id);


    /*
    var node = this.graph.getNode(nodeData.id);
    if ( typeof node != 'undefined' ) {
        console.error("Adding node (", nodeData.id, ") twice.");
        return;
    }*/

    var data = {text:nodeData.text, type:nodeData.type, description: nodeData.desc, visible: true };

    if ( typeof nodeData.extra != 'undefined' ) {
        data.extra = nodeData.extra;
    }

    if ( typeof nodeData.visible != 'undefined' ) {
        data.visible = nodeData.visible;
    }

    var node = this.graph.addNode( nodeData.id.toLowerCase(), data);


    if ( !isNaN(nodeData.x) && !isNaN(nodeData.y) ) {
        this.layout.pinNode( node, true );
        this.layout.setNodePosition( node, nodeData.x, nodeData.y );
    }


    if ( typeof nodeData.parent != 'undefined' ) {

        var linkData = { connectionStrength: 0.9 }

        switch ( nodeData.type ) {
            case 'class': linkData.type = 'class-class'; break;
            case 'dataProperty': linkData.type = 'class-dataProperty'; break;
            case 'column': linkData.type = 'table-column'; break;
            default: linkData.type = 'table-column';
        }

        if ( typeof nodeData.linkDesc !== 'undefined' ) linkData.label = nodeData.linkDesc;

        this.graph.addLink( nodeData.parent.toLowerCase(), nodeData.id.toLowerCase(), linkData );
    }

    return true;
};

// data: {desc: 'foo', pull: true}
mapping_graph.prototype.addLink = function( nodeA, nodeB, type, data ) {

    nodeA = nodeA.toLowerCase();
    nodeB = nodeB.toLowerCase();

    var node = this.graph.getNode(nodeA);
    if ( typeof node == 'undefined' ) {
        console.error("MappingGraph: Node with id (", nodeA ,") not found."); return;
    }

    node = this.graph.getNode(nodeB);
    if ( typeof node == 'undefined' ) {
        console.error("MappingGraph: Node with id (", nodeB ,") not found."); return;
    }



    var linkData = { connectionStrength: 0.8, type: type};

    if ( typeof data !== 'undefined' ) {
        if ( data.pull == false ) linkData.coeff = 0.000000001;
        if ( typeof data.desc !== 'undefined' ) linkData.label = data.desc;
    }
    

    this.graph.addLink( nodeA,  nodeB, linkData );
};


mapping_graph.prototype.drawNode = function( self, node ) {
    var nodeSize = 10;
    var ui = Viva.Graph.svg('g');

    if ( typeof node.data == 'undefined' ) {
        console.error("Mapping graph: Node data is undefined.");
        return;
    }

    if ( !node.data.visible ) ui.attr('visibility', 'hidden' );

    var svgText = Viva.Graph.svg('text')
            .attr('y', 8).attr('x', 5)
            .attr('cursor', 'pointer')
            .attr('class', 'text-link')
            .attr('font-size', '11');

    var  bckgr = Viva.Graph.svg('rect')
            .attr('width', nodeSize)
            .attr('height', nodeSize+4)
            .attr('y', -2)
            .attr('id', 'background'+node.id);

    var svgDescription = Viva.Graph.svg('text')
        //.attr('onclick', 'alert("ssd");')
        .attr('y', 25).attr('x', 2)
        .attr('class', 'text-link')
        .attr('font-size', '12')
        .attr('visibility', 'hidden');


    svgText.text(node.data.text);




    if(node.data.type === 'class' ) { // Class
        bckgr.attr('style', 'fill:rgb(240,89,64)').attr('rx', 7).attr('ry', 7)
        svgText.attr('style', 'fill:rgb(256,256,256)');

    } else if(node.data.type === 'dataProperty') { // Data property
        bckgr.attr('style', 'fill:rgb(161,207,100); opacity:0.8').attr('rx', 7).attr('ry', 7);
        svgText.attr('style', 'fill:rgb(256,256,256)');

    } else if( node.data.type === 'table' ) { // Table
        bckgr.attr('style', 'fill:rgb(89,79,138)');
        svgText.attr('style', 'fill:rgb(256,256,256)');

    } else if(node.data.type === 'column') { // Table Column
        bckgr.attr('style', 'fill:rgb(89,79,138); opacity:0.8');
        svgText.attr('style', 'fill:rgb(256,256,256)');

    } else if(node.data.type === 'columnWithFk') { // Table column with a foreign key
        bckgr.attr('style', 'fill:rgb(89,79,138); opacity:0.8')
            .attr('stroke', 'rgb(256,40,40)')
            .attr('stroke-width', 0.1);
        svgText.attr('style', 'fill:rgb(256,256,256)');
    }

    ui.data = node.data;


    if ( typeof node.data.description != 'undefined' ) {
        svgDescription.text(node.data.description);
    }
    if ( node.data.type != 'class' ) {
        ui.append(svgDescription);
        ui.svgDescription = svgDescription;
    }



    ui.bckgr = bckgr;
    ui.append(bckgr);

    ui.svgText = svgText;
    ui.append(svgText);


    var pin = Viva.Graph.svg('circle')
        .attr('r', 4)
        .attr('stroke-width', 1)
        .attr('fill', 'red')
        .attr('stroke', 'black')
        .attr('visibility', 'hidden')
        .attr('class', 'pin');

    ui.pin = pin;
    ui.append(pin);


    ui.addEventListener('click', function (e) {
        self.mouseNode( self, node, 'click', e );
    });
    ui.addEventListener('mouseover', function (e) {
        self.mouseNode( self, node, 'over', e );
    });
    ui.addEventListener('mouseleave', function (e) {
        self.mouseNode( self, node, 'leave', e );
    });
    ui.addEventListener('mousedown', function (e) {
        self.mouseNode( self, node, 'down', e );
    });



    
    return ui;
};

mapping_graph.prototype.placeNode = function( self, nodeUI, pos ) {
    var nodeSize = 10;
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
};

mapping_graph.prototype.onMouseNode = function ( node_id, action ) {
    var node = this.graph.getNode(node_id.toLowerCase());
    if ( typeof node == 'undefined' ) {
        console.error("MappingGraph: Node with id (", node_id ,") not found.");
        return;
    }
    this.mouseNode( this, node, action, null );
}

mapping_graph.prototype.mouseNode = function ( self, node, action, e ) {
    switch ( action ) {
        case 'over':
            self.showPin( self, node, true );
            self.highlightNode(node.id, true);

            self.graph.forEachLinkedNode(node.id, function(node, link){
                var linkUI = self.graphics.getLinkUI( link.id );
                if (linkUI) {
                    linkUI.attr('stroke-width', 2);

                    self.highlightNode(link.toId, true);
                    self.highlightNode(link.fromId, true);

                }
            });

            break;
        case 'leave':
            self.showPin( self, node, false );
            self.highlightNode(node.id, false);

            self.graph.forEachLinkedNode(node.id, function(node, link){
                var linkUI = self.graphics.getLinkUI(link.id);
                if (linkUI) {
                    linkUI.attr('stroke-width', 1);

                    self.highlightNode(link.toId, false);
                    self.highlightNode(link.fromId, false);
                }
            });
            break;

        case 'down':
            this.cursorX = e.clientX;
            this.cursorY = e.clientY;
            break;

        case 'click':
            self.layout.pinNode(node, true);



            if( node.data.type === 'class' || node.data.type === 'dataProperty' || node.data.type === 'table' || node.data.type === 'column' ) {
                var position = self.layout.getNodePosition(node.id);

                var url;
                var data = { nodeid: node.id, layoutX: position.x, layoutY: position.y };

                if ( typeof php_vars.routes.mapped_class_id != 'undefined' ) {
                    url = php_vars.base_url + 'index.php/mappedclass/storepositions';
                    data.mappedclass_id = php_vars.routes.mapped_class_id;

                } else if ( typeof php_vars.routes.mappingspace_id != 'undefined' ) {
                    url = php_vars.base_url + 'index.php/mappingspace/storepositions';
                    data.mappingspaceid = php_vars.routes.mappingspace_id;

                } else {
                    url = php_vars.base_url + 'index.php/datasource/storepositions';
                    data.datasourceid = php_vars.routes.datasource_id;

                }

                if ( e.target.getAttribute("class") == 'pin' ) {
                    data.unpin = true;
                    self.layout.pinNode(node, false);
                } else {
                    self.showMenu(node, e, true);


                }

                $.ajax({ url: url, data: data, type: 'post', success: function(output) {} });

            } else {
                console.log("Moving ", node.data.type);
            }

            break;
    }
}


mapping_graph.prototype.drawLink = function( self, link ) {
    var uig = Viva.Graph.svg('g');

    uig.data = link.data;
    uig.data.toId = link.toId;
    uig.data.fromId = link.fromId;

    var ui = Viva.Graph.svg('path')
        .attr('stroke-width', 1)
        .attr('fill', 'none');

    uig.append(ui);
    uig.data.path = ui;

    if ( typeof link.data.label != 'undefined' ) {
        var label = Viva.Graph.svg('text').text(link.data.label).attr('font-size', '7').attr('font-weight', 'normal');
        uig.append(label);
        uig.data.label = label;

    }

    if(link.data.type === 'class-class' ) {
        ui .attr('stroke', 'rgb(240,89,64)')
            .attr('stroke-width', 2)
            .attr('marker-end', 'url(#Triangle)');

    } else if(link.data.type === 'table-column' ) {
        ui.attr('stroke', 'rgb(89,79,138)')
            .attr('stroke-width', 2);

    } else if(link.data.type === 'table-class' ) {
        ui.attr('stroke', 'rgb(0,159,218)')
            .attr('stroke-dasharray', '5, 1')
            .attr('stroke-width', 1);

    } else if(link.data.type === 'class-dataProperty' ) {
        ui.attr('stroke', 'rgb(41,166,121)')
            .attr('stroke-width', 2);

    } else if(link.data.type === 'column-column' ) { //FK
        ui.attr('stroke', 'rgb(89,79,138)')
            .attr('stroke-width', 1)
            .attr('stroke-dasharray', '5, 2');

    }

    ui.data = link.data;

    var visible = self.graphics.getNodeUI( link.data.toId ).data.visible;
    if ( visible ) {
        ui.attr('visibility', 'visible' );
    } else {
        ui.attr('visibility', 'hidden' );
    }

    uig.addEventListener('click', function (e) {
        self.mouseLink( self, link, 'click', e );
    });
    uig.addEventListener('mouseover', function (e) {
        self.mouseLink( self, link, 'over', e );
    });
    uig.addEventListener('mouseleave', function (e) {
        self.mouseLink( self, link, 'leave', e );
    });

    return uig;
};

mapping_graph.prototype.placeLink = function( self, linkUI, fromPos, toPos ) {
    var nodeSize = 10;
    // Here we should take care about
    //  "Links should start/stop at node's bounding box, not at the node center."

    // For rectangular nodes Viva.Graph.geom() provides efficient way to find
    // an intersection point between segment and rectangle
    var toNodeSize = nodeSize,
        fromNodeSize = nodeSize;



    var nodeUI = self.graphics.getNodeUI(linkUI.data.fromId);
    var bbox = nodeUI.bckgr.getBBox();
    var offset = 2;

    var from = self.geom.intersectRect(
            // rectangle:
            fromPos.x - bbox.width / 2 , // left
            fromPos.y - bbox.height / 2 , // top
            fromPos.x + bbox.width / 2 , // right
            fromPos.y + bbox.height / 2 , // bottom

            // segment:
            fromPos.x, fromPos.y, toPos.x, toPos.y)
        || fromPos; // if no intersection found - return center of the node
    var nodeUI = self.graphics.getNodeUI(linkUI.data.toId);
    var bbox = nodeUI.bckgr.getBBox();

    var to = self.geom.intersectRect(
            // rectangle:
            toPos.x - bbox.width / 2 -offset, // left
            toPos.y - bbox.height / 2 -offset, // top
            toPos.x + bbox.width / 2 +offset*2, // right
            toPos.y + bbox.height / 2 +offset, // bottom
            toPos.x, toPos.y, fromPos.x, fromPos.y)
        || toPos; // if no intersection found - return center of the node


    var ry = linkUI.data.type == 3 ? 0 : 0;
    //var data = 'M' + from.x + ',' + from.y +
    //           'L' + to.x + ',' + to.y;
    var	data = 'M' + from.x + ',' + from.y + ' A 100,' + ry + ',0,0,1,' + to.x + ',' + to.y;
    linkUI.data.path.attr("d", data);

    if( typeof linkUI.data.label !== 'undefined' ) {
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


};

mapping_graph.prototype.mouseLink = function ( self, link, action, e ) {
    switch ( action ) {
        case 'over':
            var linkUI = self.graphics.getLinkUI(link.id);
            linkUI.data.path.attr('stroke-width', 3);
            if(linkUI.data.label)
                linkUI.data.label.attr('font-weight', 'bold').attr('font-size', '8');

            self.highlightNode(link.toId, true, true, false);
            self.highlightNode(link.fromId, true, true, false);
            
            break;
        case 'leave':
            var linkUI = self.graphics.getLinkUI(link.id);
            
            if(link.data.type === 'class-class' || link.data.type === 'table-column' || link.data.type === 'class-dataProperty')
                linkUI.data.path.attr('stroke-width', 2);
            else
                linkUI.data.path.attr('stroke-width', 1);

            if(linkUI.data.label)
                linkUI.data.label.attr('font-weight', 'normal').attr('font-size', '7');

            self.highlightNode(link.toId, false, true, false);
            self.highlightNode(link.fromId, false, true, false);

            break;
        case 'click':
            self.showMenu(link, e, false);
            break;
    }
}


mapping_graph.prototype.highlightNode = function ( nodeid, onoff ) {
    var nodeUI = this.graphics.getNodeUI( nodeid );

    //if ( onoff && !nodeUI.data.visible ) return;

    if(onoff) {
        nodeUI.bckgr.attr('stroke', 'rgb(119,88,57)')
            .attr('stroke-width', 3);
    } else {
        nodeUI.bckgr.attr('stroke-width', 0);
    }
    //showing the description (type of the column)
    if ( nodeUI.svgDescription ) {

        if(onoff && nodeUI.data.visible ) 	nodeUI.svgDescription.attr('visibility', 'visible');
        else 		nodeUI.svgDescription.attr('visibility', 'hidden');

    }

};

mapping_graph.prototype.showPin = function ( self, node, on ) {
    var nodeUI = self.graphics.getNodeUI( node.id );
    var isPinned = self.layout.isNodePinned(node);

    if ( on && isPinned ) {
        nodeUI.pin.attr('visibility', 'visible');
    } else {
        nodeUI.pin.attr('visibility', 'hidden');
    }

};


mapping_graph.prototype.showMenu = function ( nodeOrLink, e, isNode ){
    var show = false;
    if ( e.clientX == this.cursorX && e.clientY == this.cursorY ) show = true; // Click and not dragged.

    this.cursorX = e.clientX;
    this.cursorY = e.clientY;

    if ( !show ) return;

    if ( typeof php_vars.routes.mapped_class_id != 'undefined' ) return;

    if ( typeof nodeOrLink.data.extra === 'undefined' ) nodeOrLink.data.extra = {};

    var datasource_id = php_vars.routes.datasource_id;
    var mappingSpace_id = php_vars.routes.mappingspace_id;
    var class_id = nodeOrLink.data.extra.class_id;
    var dataProperty_id = nodeOrLink.data.extra.dataproperty_id;
    var uri = nodeOrLink.data.extra.uri;


    $('#mapping_dropdown').css('left', (e.clientX-90)+'px');
    $('#mapping_dropdown').css('top', (e.clientY-85)+'px');


    $('#mapping_dropdown .url1').attr("href", php_vars.base_url + "index.php/mappedclass/createnew/" + datasource_id + "/" + mappingSpace_id  + "/" + class_id );
    $('#mapping_dropdown .url2').attr("href", php_vars.base_url + "index.php/mappeddataproperty/addnew/" + datasource_id + "/" + mappingSpace_id  + "/" + class_id );
    $('#mapping_dropdown .url3').attr("href", php_vars.base_url + "index.php/mappedobjectproperty/addnew/" + datasource_id + "/" + mappingSpace_id  + "/" + class_id );
    $('#mapping_dropdown .url4').attr("href", php_vars.base_url + "index.php/mappedclass/expand/" + datasource_id + "/" + mappingSpace_id  + "/" + class_id );
    $('#mapping_dropdown .url5').attr("href", php_vars.base_url + "index.php/mappeddataproperty/addnew/" + datasource_id + "/" + mappingSpace_id  + "/" + class_id + "/" + dataProperty_id ); //Edit data property
    $('#mapping_dropdown .url6').attr("href", php_vars.base_url + "index.php/mappedclass/delete/" + datasource_id + "/" + mappingSpace_id  + "/" + class_id );
    $('#mapping_dropdown .url8').attr("data-content", uri );
    $('#mapping_dropdown .url8').popup({inline: true});


    var btnUrl, mappingspaces_buttons = "";

    // Lista mappinsgaces para mover.
    for ( i = 0; i < php_vars.mappingspaces.length; i++ ) {

        if( php_vars.mappingspaces[i].id == php_vars.mappingspace.id ) {
            mappingspaces_buttons += '<div class="item disabled">' + php_vars.mappingspaces[i].name + '</div>';
        } else {

            btnUrl = php_vars.base_url + "index.php/mappedclass/move/" + class_id + "/" + php_vars.mappingspaces[i].id;

            mappingspaces_buttons += '<div class="item" onclick="window.location = \' ' + btnUrl + ' \'">';
            mappingspaces_buttons += php_vars.mappingspaces[i].name;
            mappingspaces_buttons += '</div>';
        }

    }

    $(".suburl7").html(mappingspaces_buttons);

    $('#mapping_dropdown').removeClass('hidden');

    if ( isNode ) {
        if ( nodeOrLink.data.type === 'class' ) { // Object property
            $('#mapping_dropdown .item').removeClass('hidden');
            $('#mapping_dropdown .url5').addClass('hidden');
            if ( php_vars.user_technician != true ) $('#mapping_dropdown .url8').addClass('hidden');

        } else if ( nodeOrLink.data.type === 'dataProperty' ) { // Data property
            $('#mapping_dropdown .item').addClass('hidden');
            $('#mapping_dropdown .url5').removeClass('hidden');
            $('#mapping_dropdown .url6').removeClass('hidden');
            if ( php_vars.user_technician == true ) $('#mapping_dropdown .url8').removeClass('hidden');

        } else {
            $('#mapping_dropdown').addClass('hidden');
            console.log("Menu: ", nodeOrLink.data.type);
        }
    } else {
        // Is link
        $('#mapping_dropdown .item').addClass('hidden');
        if ( php_vars.user_technician == true ) {
            $('#mapping_dropdown .url8').removeClass('hidden');
        } else {
            $('#mapping_dropdown').addClass('hidden');
        }

    }


    $('#mapping_dropdown').dropdown('show');
}

mapping_graph.prototype.drawTempNode = function( name, linkNodeId, type ) {
    this.removeTempNode();
    this.addNode( { id: 'tempnode', type: type, text: name, desc: '', x:0, y:-300 } );
    this.addLink( 'tempnode', linkNodeId, 'table-class' );
};

mapping_graph.prototype.removeTempNode = function() {
    this.graph.removeNode( 'tempnode' );
};


mapping_graph.prototype.showTableButtons = function( container_id ) {
    var container = document.getElementById( container_id );
    var checked, buttons = '';
    var self = this;

    for ( var tableKey in php_vars.mp_graph.tables ) {
        var table = php_vars.mp_graph.tables[tableKey];

        checked = ( table['enabled'] == true ) ? 'checked="checked"' : '';

        buttons += '\
        <div class="field">\
            <div class="ui toggle checkbox" id= "table_id_' + table['name'].toLowerCase() + '">\
                    <input type="checkbox" name="public" id="table_' + table['name'].toLowerCase() + '" '+ checked +' >\
                <label>' + table['name'].toLowerCase() + '</label>\
            </div>\
        </div>\
        \
        ';
    }

    container.innerHTML = '\
        <div class="ui form secondary accordion fluid segment" >\
            <div class="two fields">\
                <div class="field"><label>List of tables:</label></div>\
                <div class="field">\
                    <div class="ui mini left attached compact positive check button">Check all</div> \
                    <div class="ui mini right attached compact negative uncheck button">Uncheck all</div>\
                </div>\
            </div>\
            ' + buttons + '\
        </div>\
    ';

    for ( var tableKey in php_vars.mp_graph.tables ) {
        var table = php_vars.mp_graph.tables[tableKey];

        document.getElementById( 'table_' + table['name'].toLowerCase() ).onclick=function( e ){
            var table = e.target.id;
            table = table.substring(6); //Remove 'table_'

            self.toggleTable( table );
        };

    }

};

mapping_graph.prototype.toggleTable = function( table_id, show ) {
    //console.log("Toggle: ", table_id );
    var self = this;
    var nodes = [];

    nodes.push( table_id );
    for ( var tableKey in php_vars.mp_graph.tables ) {
        var table = php_vars.mp_graph.tables[tableKey];
        if ( table.name.toLowerCase() == table_id ) {
            for ( var colKey in table.columns ) {
                nodes.push( table.columns[colKey].name.toLowerCase() );
            }
            break;
        }
    }



    var nodeUI = this.graphics.getNodeUI( nodes[0] );


    if ( typeof show == 'undefined' ) {
        show = ( nodeUI.attr('visibility') == 'hidden' ) ? true : false; //Toggle
    }

    var visible;
    if ( show == true ) {
        visible = "visible";
        $("#table_"+table_id).attr('checked','checked');
    } else {
        visible = "hidden";
        $("#table_"+table_id).removeAttr('checked');
    }

    
    nodeUI.attr('visibility', visible);
    nodeUI.data.visible = show;

    for ( var i = 1; i < nodes.length; i++ ) {
        nodeUI = this.graphics.getNodeUI( nodes[0] + "_" + nodes[i] );
        nodeUI.attr('visibility', visible);
        nodeUI.data.visible = show;
        //console.log(nodeUI.svgDescription);
    }


    var linkUI;
    this.graph.forEachLinkedNode( nodes[0], function(linkedNode, link) {

        //linkUI = self.graphics.getLinkUI(link.id);
        //linkUI.data.path.attr('visibility', visible);

        for ( var i = 0; i < linkedNode.links.length; i++ ) {
            linkUI = self.graphics.getLinkUI(linkedNode.links[i].id);

            nodeUI = self.graphics.getNodeUI( linkUI.data.toId );

            if ( nodeUI.data.visible ) {
                linkUI.data.path.attr('visibility', 'visible');
            } else {
                linkUI.data.path.attr('visibility', 'hidden');
            }
        }
    });





};