
module.exports = function ( graph, sidebar, options ) {

	var importOntology = {},
		languageTools = webvowl.util.languageTools(),
		statistics = webvowl.modules.statistics();


	importOntology.loadFromText = function ( jsonText ) {

		var filename;
		var alternativeFilename = "";

		//pauseMenu.reset();

		var data;
		if (jsonText) {
			data = JSON.parse(jsonText);

			if (!filename) {
				// First look if an ontology title exists, otherwise take the alternative filename
				var ontologyNames = data.header ? data.header.title : undefined;
				var ontologyName = languageTools.textInLanguage(ontologyNames);

				if (ontologyName) {
					filename = ontologyName;
				} else {
					filename = alternativeFilename;
				}
			}
		}

		//exportMenu.setJsonText(jsonText);

		options.data(data);
		graph.reload();
		sidebar.updateOntologyInformation(data, statistics);

		//exportMenu.setFilename(filename);
	};

	importOntology.loadFromUrl = function ( jsonUrl ) {
		/*var hashParameter = location.hash.slice(1);
		var urlKey = "url=";
		var jsonUrl;

		if (
				hashParameter.substr(0, urlKey.length) != urlKey ||
				(jsonUrl = decodeURIComponent(hashParameter.slice(urlKey.length))) == ""
		) {
			console.log("JSON url not defined.");
			jsonUrl = "http://localhost/mapon/upload/foaf.json";
			//return;
		}*/


		$.ajax({
			async: false,
			type: "GET",
			url: jsonUrl,
			success: function( data ) {
				this.loadFromText( JSON.stringify(data) );
			}
		});
	};



	return importOntology;
};
