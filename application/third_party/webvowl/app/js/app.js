module.exports = function () {

	var container_selector = "#graph";

	var app = {},
		graph = webvowl.graph(),
		options = graph.graphOptions();

	// New modules
	var sidebar = require("./modules/sidebar")(graph),
		importOntology,
		showElement,
		placeNodes;

	// Graph modules
	var statistics = webvowl.modules.statistics(),
		languageTools = webvowl.util.languageTools(),
		focuser = webvowl.modules.focuser(),
		selectionDetailDisplayer = webvowl.modules.selectionDetailsDisplayer(sidebar.updateSelectionInformation),
		datatypeFilter = webvowl.modules.datatypeFilter(),
		subclassFilter = webvowl.modules.subclassFilter(),
		disjointFilter = webvowl.modules.disjointFilter(),
		nodeDegreeFilter = webvowl.modules.nodeDegreeFilter(),
		setOperatorFilter = webvowl.modules.setOperatorFilter(),
		nodeScalingSwitch = webvowl.modules.nodeScalingSwitch(graph),
		compactNotationSwitch = webvowl.modules.compactNotationSwitch(graph),
		pickAndPin = webvowl.modules.pickAndPin();


	function initialize_default_options () {
		options.graphContainerSelector( container_selector );
		options.selectionModules().push( focuser );
		options.selectionModules().push(selectionDetailDisplayer);
		options.selectionModules().push( pickAndPin );
		options.filterModules().push(statistics);
		options.filterModules().push(datatypeFilter);
		options.filterModules().push(subclassFilter);
		options.filterModules().push(disjointFilter);
		options.filterModules().push(setOperatorFilter);
		options.filterModules().push(nodeScalingSwitch);
		options.filterModules().push(nodeDegreeFilter);
		options.filterModules().push(compactNotationSwitch);
		nodeDegreeFilter.setDegreeQueryFunction(function () { return 0; });
	}

	// ===================================================================================
	// External functions.
	// ===================================================================================

	app.initialize = function ( ) {

		initialize_default_options();

		importOntology = require("./modules/importOntology")(graph, sidebar, options);
		placeNodes = require("./modules/placeNodes")(graph, pickAndPin );
		showElement = require("./modules/showElement")(graph, pickAndPin, placeNodes, options);


		options.selectionModules().push( placeNodes );
		options.filterModules().push( placeNodes );



		graph.start();

		adjustSize();

	};

	// Sets the data and repaint the canvas.
	app.loadVowlFile = function ( jsonText ) {
		importOntology.loadFromText( jsonText );
	}

	// If we want a layout must be called before loadVowlFile.
	app.setOntologyLayout = function ( ontology_layout ) {
		placeNodes.setOntologyLayout( ontology_layout );
	}

	// Center the canvas to specific node.
	app.centerNode = function ( iri ) {
		showElement.selectNode( iri );
	}

	// Center the canvas to specific property.
	app.centerProperty = function ( iri ) {
		showElement.selectProperty( iri );
	}

	// Nedded for sidebar messages.
	app.setSelectedDomain = function ( iri ) {
		sidebar.setSelectedDomain( iri );
	}


	// ===================================================================================
	// Callbacks.
	// ===================================================================================


	// When we move or unpin a node, this callback is callback is called.
	app.setSaveOntologyLayoutCallback = function ( cbk ) {
		placeNodes.setSaveOntologyLayoutCallback( cbk );
	}

	// Returns an array with the ontology info.
	app.setOntologyInfoCallback = function ( cbk ) {
		sidebar.setOntologyInfoCallback( cbk );
	}

	// Returns an array with the clicked element info.
	app.setElementInfoCallback = function ( cbk ) {
		sidebar.setElementInfoCallback( cbk );
	}




	function adjustSize() {

		var graphContainer = d3.select( container_selector ),
			svg = graphContainer.select("svg");

		graphContainer.style("height", "100%");
		svg.attr("width", "100%")
		   .attr("height", "100%");
		graph.updateStyle();

		/*var height = window.innerHeight - 40,
			width = window.innerWidth - (window.innerWidth * 0.22);
		graphContainer.style("height", height + "px");
		svg.attr("width", width)
			.attr("height", height);
		options.width(width)
			.height(height);
		graph.updateStyle();*/
	}

	return app;
};
