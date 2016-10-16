
var ea;

$(document).ready(function () {
    ea = new editionArea();

    ea.setSize( 141, 230 );
    //$('.g_left_col_scroll').css({'height':(windowHeight-150) });
    //$('.g_right_col_graph').css({'height':(windowHeight-210) });
    ea.showGraph(0);

    ea.setTitles([
        '<strong>Data source graph representation:</strong> <i>click on a column item to map it to a class</i>',
        '<strong>Graph representation of the ontology</strong>',
        '<strong>Graph representation of the database</strong>'
    ]);


    $('#btn_show_mapping').click( function (){ ea.showGraph(0) } );
    $('#btn_show_ontology').click( function (){ ea.showGraph(1) } );
    $('#btn_show_database').click( function (){ ea.showGraph(2) } );

});

// ---------------------------------------------------------------------------
// Ontology Tab Options
// ---------------------------------------------------------------------------


editionArea.prototype.ont_showElementInfoButtons = function ( elementInfo ) {
    //console.log("EE:" , elementInfo);

    //$('#sidebar_ontology_objprop_selector').addClass('hidden');
    $('#sidebar_ontology_warn').addClass('hidden');
    $('#sidebar_ontology_btn').addClass('hidden');

    if ( elementInfo['element'] === 'none' ) return;

    if ( elementInfo['element'] === 'node' ) {

        $('#sidebar_ontology_btn').removeClass('hidden');

        $( "#sidebar_ontology_btn" ).unbind('click').click(function() {

            //add_search_box_Class( gl_getQName(php_vars.prefixes, elementInfo['iri']) ); // In ontology tab
            ea.showGraph(0);
            add_search_box_Class( gl_getQName(php_vars.prefixes, elementInfo['iri']) ); // In mapping tab

        });

    } else {

        $('#sidebar_ontology_warn').removeClass('hidden');
        $('#sidebar_ontology_warn_msg').html('Not a class.');

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
    db_selected_row = elementInfo;

    $( "#sidebar_dbgraph_btn_select").removeClass('hidden');

    $( "#sidebar_dbgraph_btn_select" ).unbind('click').click(function() {
        //add_search_box_Table( db_selected_row.table.title+'->'+db_selected_row.row.title ); // In ontology tab
        ea.showGraph(0);

        // Provisional for enabling the button in the table list of tab 0
        if ( !$("#table_id_" + elementInfo.table.title).hasClass('checked') ) $("#table_id_" + elementInfo.table.title).click();

        add_search_box_Table( db_selected_row.table.title+'->'+db_selected_row.row.title ); // In mapping tab
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


