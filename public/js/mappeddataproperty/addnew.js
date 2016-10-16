
var ea;


$(document).ready(function () {
    ea = new editionArea();

    ea.setSize( 141, 230 );
    ea.showGraph(0);

    ea.setTitles([
        '<strong>Data source graph representation:</strong> <i>click on a column item to map it to a class</i>',
        '<strong>Graph representation of the ontology</strong>',
        '<strong>Graph representation of the database</strong>'
    ]);

    ea.options.ont_center_node = php_vars.uriMappedClass;
    ea.options.dbgraph_center_table = php_vars.sourcetable_name;

    console.log("Center1: ",ea.options.ont_center_node );

    $('#btn_show_mapping').click( function (){ ea.showGraph(0) } );
    $('#btn_show_ontology').click( function (){ ea.showGraph(1) } );
    $('#btn_show_database').click( function (){ ea.showGraph(2) } );

});

// ---------------------------------------------------------------------------
// Ontology Tab Options
// ---------------------------------------------------------------------------


editionArea.prototype.ont_showElementInfoButtons = function ( elementInfo ) {
    $('#sidebar_ontology_btn').addClass('hidden');
    $('#sidebar_ontology_warning').addClass('hidden');


    if ( elementInfo['element'] === "datatype_property" ) {

        if ( elementInfo['domain']['iri'] === php_vars.uriMappedClass || elementInfo['domain']['label'] === "Thing" ) {

            $('#sidebar_ontology_btn').removeClass('hidden');

            $( "#sidebar_ontology_btn" ).unbind('click').click(function() {
                var selected_property_QName = gl_getQName( php_vars.prefixes, elementInfo['iri'] );
                //add_search_box_Dataproperty( selected_property_QName ); // Tab dbgraph
                ea.showGraph(0);
                add_search_box_Dataproperty( selected_property_QName ); // Tab mapping
            });

        } else {
            $('#sidebar_ontology_warning').removeClass('hidden');
            $('#sidebar_ontology_warning_msg').text( 'This datatype is from another domain.' );
        }

    } else if ( elementInfo['element'] !== "none" ) {
        $('#sidebar_ontology_warning').removeClass('hidden');
        $('#sidebar_ontology_warning_msg').text( "Is not a Datatype." );
    }
}


function ont_add_search_box_Class( string_uri ){

    if ( ea.actualtab !== 1 ) return false;

    var search_box_value = ea.hideSearchBox( "class", string_uri );

    var uri = gl_getUri( php_vars.prefixes, search_box_value );

    ea.webvowl_app.centerNode( uri );

    return true;
}

function ont_add_search_box_Dataproperty( string_uri ){

    if ( ea.actualtab !== 1 ) return false;

    var search_box_value = ea.hideSearchBox( "data_property", string_uri );

    var uri = gl_getUri( php_vars.prefixes, search_box_value );

    ea.webvowl_app.centerProperty( uri );

    return true;
}

// ---------------------------------------------------------------------------
// DatabaseGraph Tab Options
// ---------------------------------------------------------------------------


editionArea.prototype.dbgraph_showElementInfoButtons = function ( elementInfo ) {

    $("#sidebar_dbgraph_btn_select, #sidebar_dbgraph_warning").addClass('hidden');

    if ( elementInfo.table.title == php_vars.sourcetable_name ) {
        $("#sidebar_dbgraph_btn_select").removeClass('hidden');
    } else {
        $("#sidebar_dbgraph_warning").removeClass('hidden');
        $("#sidebar_dbgraph_warning_msg").text("Not from " + php_vars.sourcetable_name + " table.");
    }

    db_selected_row = elementInfo;

    $( "#sidebar_dbgraph_btn_select" ).unbind('click').click(function() {
        //add_search_box_Table( db_selected_row.table.title+'->'+db_selected_row.row.title ); // Tab dbgraph
        ea.showGraph(0);

        // Provisional for enabling the button in the table list of tab 0
        if ( !$("#table_id_" + elementInfo.table.title).hasClass('checked') ) $("#table_id_" + elementInfo.table.title).click();

        add_search_box_Table( db_selected_row.table.title+'->'+db_selected_row.row.title ); // Tab mapping
    });

}

// ---------------------------------------------------------------------------
// Database Tab Options
// ---------------------------------------------------------------------------

function dbgraph_add_search_box_Table(string_uri){

    if ( ea.actualtab !== 2 ) return false;

    var table_row = ea.hideSearchBox( "table", string_uri );

    table_row = table_row.split("->");

    ea.dbgraph_app.mapon_focus(table_row[0], table_row[1]); // Centrar

    return true;
}

