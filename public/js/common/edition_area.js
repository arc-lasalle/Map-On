var ea_externalPadding = 130;
var ea_internalPadding = 210;
var ea_timer;
var ea_self;

function editionArea () {

    $('.ui.accordion').accordion();
    $(window).on('resize', this.adjustHeight );
    ea_self = this;

    this.mapping_app_loaded = false;
    this.webvowl_app_loaded = false;
    this.dbgraph_app_loaded = false;

    this.mapping_app = undefined;
    this.webvowl_app = undefined;
    this.dbgraph_app = undefined;

    this.actualtab;
    this.tabTitles = [];
    this.options = [];

}



// ---------------------------------------------------------------------------
// General Style
// ---------------------------------------------------------------------------

editionArea.prototype.setSize = function ( externalPadding, internalPadding ) {
    if ( externalPadding !== undefined ) ea_externalPadding = externalPadding;
    if ( internalPadding !== undefined ) ea_internalPadding = internalPadding;
}

editionArea.prototype.setTitles = function ( titles ) {
    this.tabTitles = titles;
}


editionArea.prototype.showGraph = function ( graph_num ) {

    this.adjustHeight();

    $('.g_view_buttons .item').removeClass('active');
    $('.g_view_buttons .item').eq(graph_num).addClass('active');
    this.actualtab = graph_num;

    $('#graph_1_mapping, #graph_2_ontology, #graph_3_database').css({'height':'0px', 'visibility': 'hidden' });
    $('#options_1_mapping, #options_2_ontology, #options_3_database').css({'height':'0px', 'visibility': 'hidden' });

    $('.right_colum_title').html(this.tabTitles[graph_num]);

    if ( graph_num == 0 ) {
        $('#graph_1_mapping').css({'height':'', 'visibility': '' });
        $('#options_1_mapping').css({'height':'100%', 'visibility': '' });

    } else if ( graph_num == 1 ) {
        $('#graph_2_ontology').css({'height':'', 'visibility': '' });
        $('#options_2_ontology').css({'height':'100%', 'visibility': '' });

        if ( !this.webvowl_app_loaded ) {
            this.loadVowl();
            this.webvowl_app_loaded = true;

        } else if ( ea.options.ont_center_node !== undefined ) {
            console.log("Centeting: ", ea.options.ont_center_node);
            this.webvowl_app.centerNode( ea.options.ont_center_node );
        } else{
            console.log("Not centering");
        }

    } else if ( graph_num == 2 ) {
        $('#graph_3_database').css({'height':'', 'visibility': '' });
        $('#options_3_database').css({'height':'100%', 'visibility': '' });

        if ( !this.dbgraph_app_loaded ) {
            this.loadDbgraph();
            this.dbgraph_app_loaded = true;

        } else if ( ea.options.dbgraph_center_table !== undefined ) {
            this.dbgraph_app.mapon_focus( ea.options.dbgraph_center_table );
        }
    }

}

editionArea.prototype.adjustHeight = function() {
    var windowHeight = $(window).height();

    $('.g_left_col').css({'height':(windowHeight-ea_externalPadding), 'overflow': 'hidden' });
    $('.g_right_col').css({'height':(windowHeight-ea_externalPadding), 'overflow': 'hidden' });

    $('.g_left_col_scroll').css({'height':(windowHeight-ea_internalPadding) });
    $('.g_right_col_graph').css({'height':(windowHeight-ea_internalPadding) });

}




// ---------------------------------------------------------------------------
// WebVowl TAB
// ---------------------------------------------------------------------------


editionArea.prototype.loadVowl = function() {

    $('#ea_loader').removeClass('hidden');
    $('#ea_loader').html("Loading ontology...<br>&#8635;");

    // Initialize WebVOWL
    this.webvowl_app = webvowl.app();
    this.webvowl_app.initialize();
    if ( php_vars.ontology_layout !== undefined ) this.webvowl_app.setOntologyLayout( php_vars.ontology_layout );
    this.webvowl_app.setSaveOntologyLayoutCallback( saveLayoutOntology );
    this.webvowl_app.setOntologyInfoCallback( this.ont_showOntologyInfo );
    this.webvowl_app.setElementInfoCallback( this.ont_showElementInfo );

    var self = this;

    $.ajax({
        type: "POST",
        url: php_vars.base_url + "index.php/ontology/getvowl/" + php_vars.routes.datasource_id,
        success: function( data ) {
            try {
                self.webvowl_app.loadVowlFile(data);

                if ( self.options.ont_center_node !== undefined ) {
                    self.webvowl_app.centerNode( ea.options.ont_center_node );
                }
                $('#ea_loader').addClass('hidden');

            } catch( err ) {
                $('#ea_loader').html('Error loading<br><i class="warning icon"></i>');
                console.error(err);
            }


        }
    });

    function saveLayoutOntology ( node_uri, save, pos_x, pos_y ) {
        $.ajax({
            type: "POST",
            url: php_vars.base_url + "index.php/ontology/saveOntologyLayout/" + php_vars.routes.datasource_id,
            data: {"node_uri":node_uri, "save":save, "pos_x":pos_x, "pos_y":pos_y},
            success: function( data ) {
                //console.log("Saved");
            }
        });
    }
}

editionArea.prototype.ont_showOntologyInfo = function ( ontologyInfo ) {

    var metadata = "";
    for (var key in ontologyInfo.metadata) {
        metadata += '<b>' + ontologyInfo.metadata[key][0].identifier + ': </b><br>';
        metadata += ontologyInfo.metadata[key][0].value + '<br>';
    }

    var sidebar_info_content = ' \
        <a href="' + (ontologyInfo['about']['url'] || 'javascript:void(0)') + '" target="_blank">\
        '+ (ontologyInfo['about'] || 'Unknown.') + '<br/> \
        </a>\
        '+ (ontologyInfo['description'] || 'No description available.') +'<br/> \
        <p><b>Version: </b>'+ ( ontologyInfo['version'] || '--' ) +'</p> \
        <p><b>Author(s): </b>'+ (ontologyInfo['authors'] || '--') +'</p> \
        \
        <h3>Metadata</h3> \
        '+ metadata +' \
        \
        <h3>Statistics</h3> \
        <p>Classes: '+ ontologyInfo['classCount'] +'</p> \
        <p>Object prop.: '+ ontologyInfo['objectPropertyCount'] +'</p> \
        <p>Datatype prop.: '+ ontologyInfo['datatypePropertyCount'] +'</p> \
        <p>Individuals: '+ ontologyInfo['individualCount'] +'</p> \
        <p>Nodes: '+ ontologyInfo['nodeCount'] +'</p> \
        <p>Edges: '+ ontologyInfo['edgeCount'] +'</p> \
    ';

    $('.webvowl_title').text( ontologyInfo['title'] || "No title" );
    $( "#sidebar_ontology_info" ).append( sidebar_info_content );

}

editionArea.prototype.ont_showElementInfoButtons = function ( elementInfo ) { return ""; }
editionArea.prototype.ont_showElementInfo = function ( elementInfo ) {

    // Filled by WebVowl library
    //$( "#sidebar_ontology_element_info" ).html( element info... );

    var buttons_and_warnings = ea_self.ont_showElementInfoButtons(elementInfo);
    $( "#sidebar_ontology_element_buttons" ).html( buttons_and_warnings );

}

// ---------------------------------------------------------------------------
// DBGraph TAB
// ---------------------------------------------------------------------------


editionArea.prototype.loadDbgraph = function() {

    var self = this;

    $('#ea_loader').removeClass('hidden');
    $('#ea_loader').html("Loading database graph...<br>&#8635;");

    dbGraph_initialize( function( graph ){
        var table_cols, table, col, fk_origin, fk_dest;
        var tables = php_vars.dbgraph_layout;
        //var cols = JSON.parse(php_vars.db_columns);
        var layout = php_vars.dbgraph_layout;

        self.dbgraph_app = graph;

        // Add tables and rows
        for ( var i = 0; i < tables.length; i++ ) {
            var table = graph.mapon_addTable( tables[i].name, 300, 100 + (100*i) );

            //table_cols = cols[tables[i].id];
            table_cols = tables[i].columns;

            if ( typeof table_cols !== 'undefined' ) {
                for ( var k = 0; k < table_cols.length; k++ ) {
                    col = graph.mapon_addRow( table, table_cols[k].name, (table_cols[k].primarykey == 1), table_cols[k].type );
                }
            }


        }

        // Add FK's
        for ( var i = 0; i < tables.length; i++ ) {
            table_cols = tables[i].columns;

            for ( var k = 0; k < table_cols.length; k++ ) {

                if ( table_cols[k].foreigntable != "" ) {
                    fk_origin = graph.mapon_getRow( tables[i].name, table_cols[k].name );
                    fk_dest = graph.mapon_getRow( table_cols[k].foreigntable, table_cols[k].foreignkey );

                    graph.mapon_addForeginKey( fk_origin, fk_dest );
                }
            }
        }

        graph.alignTables();
        graph.sync();

        for ( var i = 0; i < tables.length; i++ ) {
            if ( tables[i].layoutX == 0 && tables[i].layoutY == 0 ) continue;
            console.log("Entra");
            graph.mapon_moveTable( tables[i].name, tables[i].layoutX, tables[i].layoutY );
        }

        graph.sync();
        graph.mapon_setClickElementCallback( self.dbgraph_showElementInfo );
        graph.mapon_setMoveTableCallback( saveDbgraphTableLayout );

        if ( self.options.dbgraph_center_table !== undefined ) {
            self.dbgraph_app.mapon_focus( ea.options.dbgraph_center_table );
        }

        $('#ea_loader').addClass('hidden');

        function saveDbgraphTableLayout ( data ) {

            $.ajax({
                type: "POST",
                url: php_vars.base_url + "index.php/datasource/saveDbgraphLayout/" + php_vars.routes.datasource_id,
                data: {"table_id":data.table.title, "save":true, "pos_x":data.table.x, "pos_y":data.table.y},
                success: function( data ) {
                    //console.log("Saved");
                }
            });

        }

    });

}

// Ejecuted when we click a tabl row of the graph.
editionArea.prototype.dbgraph_showElementInfoButtons = function ( elementInfo ) { return ""; }
editionArea.prototype.dbgraph_showElementInfo = function ( elementInfo ) {

    //console.log("Clicked element: ", elementInfo);

    var sidebar_info_content = ' \
        <h3 style="margin-top: 0px;"><u>Row info</u></h3> \
        <p><b>Table:</b> '+ elementInfo.table.title +'</p> \
        <p><b>Row name:</b> '+ elementInfo.row.title +'</p> \
        <p><b>Row type:</b> '+ elementInfo.row.stype +'</p> \
        <p><b>Comment:</b> <br>'+ (elementInfo.row.comment || 'No comment.') +'</p> \
    ';

    sidebar_info_content += ea_self.dbgraph_showElementInfoButtons( elementInfo );

    $( "#sidebar_dbgraph_info" ).html( sidebar_info_content );
    if ( !$('#tab_row_info').hasClass('active') ) $('#tab_row_info').click();

}


// ---------------------------------------------------------------------------
// ALL TABS
// ---------------------------------------------------------------------------

$('.ea_class_search_box, .ea_class_search_box + i').click( function() { ea.showSearchBox("class", 0); });
$('.ea_class_search_box').keyup(function(){ ea.showSearchBox("class", 500); });

$('.ea_dataproperty_search_box, .ea_dataproperty_search_box + i').click( function() { ea.showSearchBox("data_property", 0); });
$('.ea_dataproperty_search_box').keyup(function(){ ea.showSearchBox("data_property", 500); });

$('.ea_table_search_box, .ea_table_search_box + i').click( function() { ea.showSearchBox("table", 0); });
$('.ea_table_search_box').keyup(function(){ ea.showSearchBox("table", 500); });

$('#horizontal_collapse').click( function() {
    var visible = $('#left_grid').is(":visible");
    if ( visible ) {
        $('#left_grid').hide();
        $('#horizontal_collapse').removeClass('left').addClass('right');
        $('#right_grid').removeClass('eleven wide').addClass('sixteen wide');
    } else {
        $('#left_grid').show();
        $('#horizontal_collapse').removeClass('right').addClass('left');
        $('#right_grid').removeClass('sixteen wide').addClass('eleven wide');
    }

});


editionArea.prototype.showSearchBox = function( type, timeout ) {
    // Step 1: Show the search box.
    // Step 2: Hide the search box when the element is clicked.

    if ( timeout !== undefined && timeout != 0 ) {
        clearTimeout( ea_timer );
        var self = this;
        ea_timer = setTimeout( function() { self.showSearchBox(type); }, timeout);
        return;
    }

    var url, data, div_selector, container_selector;

    if ( this.actualtab == 0 ) container_selector = "#options_1_mapping ";
    if ( this.actualtab == 1 ) container_selector = "#options_2_ontology ";
    if ( this.actualtab == 2 ) container_selector = "#options_3_database ";

    if ( type == "data_property" ) {
        div_selector = ".ea_dataproperty_search_results";
        url = php_vars.base_url + 'index.php/mapping/suggestdataproperty';
        data = {
            string: $(container_selector+'.ea_dataproperty_search_box').val(),
            class: php_vars.routes.mappedclass_id,
            datasource_id: php_vars.routes.datasource_id
        };
    } else if ( type == "class" ) {
        div_selector = ".ea_class_search_results";
        url = php_vars.base_url + 'index.php/mapping/suggestclass';
        data = {
            string: $(container_selector+'.ea_class_search_box').val(),
            datasource_id: php_vars.routes.datasource_id
        };

    } else if ( type == "table" ) {
        div_selector = ".ea_table_search_results";
        url = php_vars.base_url + 'index.php/mapping/suggesttable';
        data = {
            string: $(container_selector+'.ea_table_search_box').val(),
            datasource_id: php_vars.routes.datasource_id
        };
    }

    // Show loader until receiving load() data.
    $(container_selector+div_selector).html('<div class="ui inverted active dimmer"><div class="ui mini text loader">Searching....</div></div><br/><br/>');

    $(container_selector+div_selector).load( url, data );

    $(container_selector+div_selector).fadeIn();


}

editionArea.prototype.hideSearchBox = function( type, set_value ) {
    var container_selector, div_selector;

    if ( this.actualtab == 0 ) container_selector = "#options_1_mapping ";
    if ( this.actualtab == 1 ) container_selector = "#options_2_ontology ";
    if ( this.actualtab == 2 ) container_selector = "#options_3_database ";
    if ( type == "data_property" ) div_selector = ".ea_dataproperty_search_box";
    if ( type == "class" ) div_selector = ".ea_class_search_box";
    if ( type == "table" ) div_selector = ".ea_table_search_box";


    if ( set_value !== undefined ) {
        $(container_selector + div_selector).val(set_value);
    }

    $(".ea_search_box").fadeOut();

    return $(container_selector + div_selector).val();

}