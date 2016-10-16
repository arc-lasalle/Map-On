
module.exports = function ( graph, pickAndPin, placeNodes, options ) {
    var showElement = {};

    showElement.selectNode = function ( iri ) {

        var node = graph.getNode( iri );

        if ( node === undefined ) return false;

        // Froze the node in the actual position.
        node.frozen( true );

        // Move the graph to the node.
        var pos_x = (options.width()/2) - node.x;
        var pos_y = (options.height()/2) - node.y;

        graph.move( pos_x, pos_y );


        // Execute all node functions modules. (Shows the sidebar information, etc.)
        var pickPinDefault = pickAndPin.enabled();
        pickAndPin.enabled( false );
        placeNodes.enabled( false ); // Avoid save position to BD

        options.selectionModules().forEach(function (module) {
            module.handle( node );
        });

        pickAndPin.enabled(pickPinDefault);
        placeNodes.enabled( true );


        graph.updateStyle();

        return true;
    }

    showElement.selectProperty = function ( iri ) {

        var prop = graph.getProperty( iri );

        if ( prop === undefined ) return false;

        var node_domain = prop.domain();
        var node_range = prop.range();

        // Froze the node in the actual position.
        node_domain.frozen( true );
        node_range.frozen( true );

        var pos_x = Math.min(node_domain.x, node_range.x) + (Math.abs(node_domain.x - node_range.x)/2);
        var pos_y = Math.min(node_domain.y, node_range.y) + (Math.abs(node_domain.y - node_range.y)/2);

        // Move the graph to the node.
        var pos_x = (options.width()/2) - pos_x;
        var pos_y = (options.height()/2) - pos_y;

        graph.move( pos_x, pos_y );


        // Execute all node functions modules. (Shows the sidebar information, etc.)

        placeNodes.enabled( false ); // Avoid save position to BD

        options.selectionModules().forEach(function (module) {
            module.handle( prop );
        });

        placeNodes.enabled( true );


        graph.updateStyle();

        return true;
    }

    return showElement;
};