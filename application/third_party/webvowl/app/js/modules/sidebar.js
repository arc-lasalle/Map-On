/**
 * Contains the logic for the sidebar.
 * @param graph the graph that belongs to these controls
 * @returns {{}}
 */
module.exports = function (graph) {

	var sidebar = {},
		languageTools = webvowl.util.languageTools(),
		elementTools = webvowl.util.elementTools(),
	// Required for reloading when the language changes
		ontologyInfo,
		lastSelectedElement,
		ontologyInfoCallback,
		elementInfoCallback;

	var selected_domain;

	/**
	 * Setup the menu bar.
	 */
	sidebar.setup = function () {
		//setupCollapsing();
	};

	sidebar.setSelectedDomain = function ( domain_iri ) {
		selected_domain = domain_iri;
	};

	sidebar.setOntologyInfoCallback = function ( cbk ) {
		ontologyInfoCallback = cbk;
	}

	sidebar.setElementInfoCallback = function ( cbk ) {
		elementInfoCallback = cbk;
	}

	/**
	 * Updates the information of the passed ontology.
	 * @param data the graph data
	 * @param statistics the statistics module
	 */
	sidebar.updateOntologyInformation = function (data, statistics) {
		data = data || {};
		ontologyInfo = data.header || {};

        showOntologyInfo( data.metrics, statistics, ontologyInfo.other );



		// Reset the sidebar selection
		sidebar.updateSelectionInformation(undefined);

		setLanguages( ontologyInfo.languages );
	};

	/**
	 * Update the information of the selected node.
	 * @param selectedElement the selection or null if nothing is selected
	 */
	sidebar.updateSelectionInformation = function (selectedElement) {
		lastSelectedElement = selectedElement;

		// Click event was prevented when dragging
		//if (d3.event && d3.event.defaultPrevented) {
		//	return;
		//}

		// INIT TO DELETE
		var tab_node_info = document.getElementById('tab_node_info');
		if ( tab_node_info !== null && ((tab_node_info.className).indexOf('active') < 0) ) tab_node_info.click();

		setSelectionInformationVisibility(false, false, true);

		if ( elementTools.isProperty(selectedElement) ) {

			displayPropertyInformation(selectedElement);

		} else if (elementTools.isNode(selectedElement)) {

			displayNodeInformation(selectedElement);

		}
		// FIN TO DELETE

		showElementInfo( selectedElement );
	};


	function showOntologyInfo( deliveredMetrics, statistics, metadata ) {

		var ontInfo = [];

		ontInfo['title'] = languageTools.textInLanguage(ontologyInfo.title, graph.language());
		ontInfo['about'] = ontologyInfo.iri;
		ontInfo['about']['url'] = ontologyInfo.iri;
		ontInfo['description'] = languageTools.textInLanguage(ontologyInfo.description, graph.language());
		ontInfo['version'] = ontologyInfo.version;

		var authors = ontologyInfo.author;
		if ( authors instanceof Array ) {
			authors = authors.join(", ");
		} else if ( typeof authors !== "string" ) {
			authors = "--";
		}

		ontInfo['authors'] = authors;

		deliveredMetrics = deliveredMetrics || {}; // Metrics are optional and may be undefined

		ontInfo['classCount'] = deliveredMetrics.classCount || statistics.classCount();
		ontInfo['objectPropertyCount'] = deliveredMetrics.objectPropertyCount || statistics.objectPropertyCount();
		ontInfo['datatypePropertyCount'] = deliveredMetrics.datatypePropertyCount || statistics.datatypePropertyCount();
		ontInfo['individualCount'] = deliveredMetrics.totalIndividualCount || statistics.totalIndividualCount();
		ontInfo['nodeCount'] = statistics.nodeCount();
		ontInfo['edgeCount'] = statistics.edgeCount();

		ontInfo['metadata'] = metadata || {};

		if ( ontologyInfoCallback !== undefined ) {
			ontologyInfoCallback( ontInfo );
		}

	}


	function showElementInfo ( selectedElement ) {

		//console.log("Vowl: Selected element: ", selectedElement);

		var info = getElementInfo( selectedElement, 0 );

		if ( elementInfoCallback !== undefined ) {
			elementInfoCallback( info );
		}
	}

	function getElementInfo ( selectedElement, level ) {
		var info = [];

		info['element'] = getType(selectedElement);

		if ( elementTools.isProperty(selectedElement) || elementTools.isNode(selectedElement) ) {

			info['type'] = selectedElement.type(); //"owl:DatatypeProperty"

			info['iri'] = selectedElement.iri();
			info['label'] = selectedElement.labelForCurrentLanguage();
			info['attributes'] = selectedElement.attributes();
			info['description'] = selectedElement.descriptionForCurrentLanguage();
			info['comment'] = selectedElement.commentForCurrentLanguage();
			info['annotations'] = selectedElement.annotations();

			info['equivalents'] = selectedElement.equivalents();


			if ( elementTools.isProperty(selectedElement) ) {

				if ( level == 0 ) {
					info['domain'] = getElementInfo( selectedElement.domain(), 1 );
					info['range'] = getElementInfo( selectedElement.range(), 1 );
				}

				if (selectedElement.inverse() !== undefined) {
					info['inverse']['label'] = selectedElement.inverse().labelForCurrentLanguage();
					info['inverse']['iri'] = selectedElement.inverse().iri();
				}


				info['subproperties'] = selectedElement.subproperties();
				info['superproperties'] = selectedElement.superproperties();

				info['cardinality'] = selectedElement.cardinality();
				info['minCardinality'] = selectedElement.minCardinality();
				info['maxCardinality'] = selectedElement.maxCardinality();


			} else if ( elementTools.isNode(selectedElement) ) {

				info['individuals'] = selectedElement.individuals();
				info['disjointWith'] = selectedElement.disjointWith();

			}

		}

		return info;

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


/*
	function cleanArray( arr ) {
		arr_cleaned = [];
		console.log("Arr: ",arr);
		for (var key in arr) {
			console.log("Key: ", key );
			arr_cleaned[key] = arr[key][0];
		}
		return arr_cleaned;
	}
*/







	function setText( selector, text, default_text, link) {

        var content = text || default_text || "";

		d3.selectAll(selector).each( function(d, i) {

			if ( link !== undefined ) {
				d3.select(this).append("a").attr("href", link).attr("target", "_blank").text( content );
			} else {
                d3.select(this).text( content );
            }

            d3.select(this).attr("value", content);
		});
	}

	function hideElement( selector, hide ) {
		d3.selectAll(selector).each( function(d, i) {
				d3.select(this).classed( "hidden", hide );
		});
	}










	function setSelectionInformationVisibility(showClasses, showProperties, showAdvice) {
		d3.select("#classSelectionInformation").classed("hidden", !showClasses);
		d3.select("#propertySelectionInformation").classed("hidden", !showProperties);
		d3.select("#noSelectionInformation").classed("hidden", !showAdvice);
	}

	// Borrar
	function displayPropertyInformation(property) {
		var isDatatypeProperty = (property.type() === "owl:DatatypeProperty");
		var isFromCurrentDomain = (property.domain().iri() === selected_domain);
		isFromCurrentDomain |= (property.domain().labelForCurrentLanguage() == "Thing" );
		//console.log(property.domain().labelForCurrentLanguage());

        setSelectionInformationVisibility(false, true, false);

		setIriLabel(d3.select("#propname"), property.labelForCurrentLanguage(), property.iri());

		setText( "#typeProp", property.type() );

		hideElement( '#btn_select_datatype_property', !isDatatypeProperty );

		hideElement( '#webvowl_warning', (isFromCurrentDomain || !isDatatypeProperty) );
		setText( "#webvowl_warning span", 'This datatype is from another domain.' );


		if (property.inverse() !== undefined) {
			d3.select("#inverse").classed("hidden", false);
			setIriLabel(d3.select("#inverse span"), property.inverse().labelForCurrentLanguage(), property.inverse().iri());
		} else {
			d3.select("#inverse").classed("hidden", true);
		}

		var equivalentIriSpan = d3.select("#propEquivUri");
		listNodeArray(equivalentIriSpan, property.equivalents());

		listNodeArray(d3.select("#subproperties"), property.subproperties());
		listNodeArray(d3.select("#superproperties"), property.superproperties());

		if (property.minCardinality() !== undefined) {
			d3.select("#infoCardinality").classed("hidden", true);
			d3.select("#minCardinality").classed("hidden", false);
			d3.select("#minCardinality span").text(property.minCardinality());
			d3.select("#maxCardinality").classed("hidden", false);

			if (property.maxCardinality() !== undefined) {
				d3.select("#maxCardinality span").text(property.maxCardinality());
			} else {
				d3.select("#maxCardinality span").text("*");
			}

		} else if (property.cardinality() !== undefined) {
			d3.select("#minCardinality").classed("hidden", true);
			d3.select("#maxCardinality").classed("hidden", true);
			d3.select("#infoCardinality").classed("hidden", false);
			d3.select("#infoCardinality span").text(property.cardinality());
		} else {
			d3.select("#infoCardinality").classed("hidden", true);
			d3.select("#minCardinality").classed("hidden", true);
			d3.select("#maxCardinality").classed("hidden", true);
		}

		setIriLabel(d3.select("#domain"), property.domain().labelForCurrentLanguage(), property.domain().iri());
		setIriLabel(d3.select("#range"), property.range().labelForCurrentLanguage(), property.range().iri());

		displayAttributes(property.attributes(), d3.select("#propAttributes"));

		setTextAndVisibility(d3.select("#propDescription"), property.descriptionForCurrentLanguage());
		setTextAndVisibility(d3.select("#propComment"), property.commentForCurrentLanguage());

		listAnnotations(d3.select("#propertySelectionInformation"), property.annotations());
	}



	function setIriLabel(element, name, iri) {
		element.selectAll("*").remove();
		appendIriLabel(element, name, iri);
	}

	function appendIriLabel(element, name, iri) {
		var tag;

		if (iri) {
			tag = element.append("a")
				.attr("href", iri)
				.attr("title", iri)
				.attr("target", "_blank");
		} else {
			tag = element.append("span");
		}
		tag.text(name);
	}

	function displayAttributes(attributes, textSpan) {
		var spanParent = d3.select(textSpan.node().parentNode);

		if (attributes && attributes.length > 0) {
			// Remove redundant redundant attributes for sidebar
			removeElementFromArray("object", attributes);
			removeElementFromArray("datatype", attributes);
			removeElementFromArray("rdf", attributes);
		}

		if (attributes && attributes.length > 0) {
			textSpan.text(attributes.join(", "));

			spanParent.classed("hidden", false);
		} else {
			spanParent.classed("hidden", true);
		}
	}

	function removeElementFromArray(element, array) {
		var index = array.indexOf(element);
		if (index > -1) {
			array.splice(index, 1);
		}
	}

	function displayNodeInformation(node) {

        setSelectionInformationVisibility(true, false, false);

		setIriLabel(d3.select("#name"), node.labelForCurrentLanguage(), node.iri());

		/* Equivalent stuff. */
		var equivalentIriSpan = d3.select("#classEquivUri");
		listNodeArray(equivalentIriSpan, node.equivalents());

		d3.select("#typeNode").text(node.type());
		listNodeArray(d3.select("#individuals"), node.individuals());

		/* Disjoint stuff. */
		var disjointNodes = d3.select("#disjointNodes");
		var disjointNodesParent = d3.select(disjointNodes.node().parentNode);

		if (node.disjointWith() !== undefined) {
			disjointNodes.selectAll("*").remove();

			node.disjointWith().forEach(function (element, index) {
				if (index > 0) {
					disjointNodes.append("span").text(", ");
				}
				appendIriLabel(disjointNodes, element.labelForCurrentLanguage(), element.iri());
			});

			disjointNodesParent.classed("hidden", false);
		} else {
			disjointNodesParent.classed("hidden", true);
		}

		displayAttributes(node.attributes(), d3.select("#classAttributes"));

		setTextAndVisibility(d3.select("#nodeDescription"), node.descriptionForCurrentLanguage());
		setTextAndVisibility(d3.select("#nodeComment"), node.commentForCurrentLanguage());

		listAnnotations(d3.select("#classSelectionInformation"), node.annotations());
	}


	function listNodeArray(textSpan, nodes) {
		var spanParent = d3.select(textSpan.node().parentNode);

		if (nodes && nodes.length) {
			textSpan.selectAll("*").remove();
			nodes.forEach(function (element, index) {
				if (index > 0) {
					textSpan.append("span").text(", ");
				}
				appendIriLabel(textSpan, element.labelForCurrentLanguage(), element.iri());
			});

			spanParent.classed("hidden", false);
		} else {
			spanParent.classed("hidden", true);
		}
	}

	function setTextAndVisibility(label, value) {
		var parentNode = d3.select(label.node().parentNode);
		var hasValue = !!value;
		if (value) {
			label.text(value);
		}
		parentNode.classed("hidden", !hasValue);
	}


    function setLanguages(languages) {
        languages = languages || [];

        // Put the default and unset label on top of the selection labels
        languages.sort(function (a, b) {
            if (a === webvowl.util.constants().LANG_IRIBASED) {
                return -1;
            } else if (b === webvowl.util.constants().LANG_IRIBASED) {
                return 1;
            }
            if (a === webvowl.util.constants().LANG_UNDEFINED) {
                return -1;
            } else if (b === webvowl.util.constants().LANG_UNDEFINED) {
                return 1;
            }
            return a.localeCompare(b);
        });

        var languageSelection = d3.select("#language")
            .on("change", function () {
                graph.language(d3.event.target.value);
                updateGraphInformation();
                sidebar.updateSelectionInformation(lastSelectedElement);
            });

        languageSelection.selectAll("option").remove();
        languageSelection.selectAll("option")
            .data(languages)
            .enter().append("option")
            .attr("value", function (d) {
                return d;
            })
            .text(function (d) {
                return d;
            });

        if (!trySelectDefaultLanguage(languageSelection, languages, "en")) {
            if (!trySelectDefaultLanguage(languageSelection, languages, webvowl.util.constants().LANG_UNDEFINED)) {
                trySelectDefaultLanguage(languageSelection, languages, webvowl.util.constants().LANG_IRIBASED);
            }
        }
    }

    function trySelectDefaultLanguage(selection, languages, language) {
        var langIndex = languages.indexOf(language);
        if (langIndex >= 0) {
            selection.property("selectedIndex", langIndex);
            graph.language(language);
            return true;
        }

        return false;
    }

	function listAnnotations(container, annotationObject) {
		annotationObject = annotationObject || {};  //todo

		// Collect the annotations in an array for simpler processing
		var annotations = [];
		for (var annotation in annotationObject) {
			if (annotationObject.hasOwnProperty(annotation)) {
				annotations.push(annotationObject[annotation][0]);
			}
		}

		container.selectAll(".annotation").remove();
		container.selectAll(".annotation").data(annotations).enter().append("p")
				.classed("annotation", true)
				.classed("statisticDetails", true)
				.text(function (d) {
					return d.identifier + ":";
				})
				.append("span")
				.each(function (d) {
					appendIriLabel(d3.select(this), d.value, d.type === "iri" ? d.value : undefined);
				});
	}
    return sidebar;
};
