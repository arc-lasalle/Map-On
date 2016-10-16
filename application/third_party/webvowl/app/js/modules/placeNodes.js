module.exports = function ( graph, pickAndPin ) {
    var placeNodes = {},
        elementTools = webvowl.util.elementTools(),
        enabled,
        processedNodes,
        processedProperties,
        ontology_layout,
        save_layout_callback;




    placeNodes.setup = function() {
        pickAndPin.enabled(true);
        enabled = true;
        ontology_layout = [];
    }
    placeNodes.setup();



    // When a node is clicked
    placeNodes.handle = function ( selected_node ) {
        //console.log("Click: ", selected_node );

        if ( save_layout_callback !== undefined && enabled) {
            if ( getType(selected_node) === "node" ) {
                save_layout_callback( selected_node.iri, true, selected_node.x, selected_node.y );
            }
        }

    }

    function getType( element ) {

        if ( element === undefined ) return "none";

        if ( elementTools.isLabel(element) ) return "label";
        if ( elementTools.isDatatype(element) ) return "datatype";
        if ( elementTools.isObjectProperty(element) ) return "object_property";
        if ( elementTools.isDatatypeProperty(element) ) return "datatype_property";
        if ( elementTools.isRdfsSubClassOf(element) ) return "rdfs_subclass_of";
        if ( elementTools.isNode(element) ) return "node";
        if ( elementTools.isProperty(element) ) return "property";
        return "none";
    }


    // When loads the data specify the position of the nodes
    placeNodes.filter = function ( preprocessedNodes, preprocessedProperties ) {

        processedNodes = preprocessedNodes;
        processedProperties = preprocessedProperties;

        processedNodes.forEach(function (node) {

            for ( var i = 0; i < ontology_layout.length; i++ ) {

                // Set the initial position
                if ( ontology_layout[i]['nodeid'] === node.iri() ) {
                    node.px = parseInt( ontology_layout[i]['layoutX'] + "",10);
                    node.py = parseInt( ontology_layout[i]['layoutY'] + "",10);
                    node.x = node.px;
                    node.y = node.py;
                    node.pinned( true );
                    break;
                }

            }

            // Set the callback when the pin is clicked.
            node.unpin = function () {
                if ( save_layout_callback !== undefined ) {
                    node.frozen( false ); // Lo congelamos en selectNode
                    save_layout_callback(node.iri, false, -1, -1);
                }
            };

        });
    }


    // We receive an array with nodeid, layoutX, layoutY
    // where the nodeid is the iri.
    placeNodes.setOntologyLayout = function ( layout ) {
        ontology_layout = layout;
    }

    // When we click a node or their pin, this callback is called.
    placeNodes.setSaveOntologyLayoutCallback = function ( cbk ) {
        save_layout_callback = cbk;
    }


    // Functions a filter must have
    placeNodes.filteredNodes = function () {
        return processedNodes;
    };

    placeNodes.filteredProperties = function () {
        return processedProperties;
    };

    placeNodes.enabled = function (p) {
        if (!arguments.length) return enabled;
        enabled = p;
        return placeNodes;
    };

    return placeNodes;
};