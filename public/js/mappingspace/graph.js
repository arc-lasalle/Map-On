
var ea;


$(document).ready(function () {
    ea = new editionArea();

    ea.setSize( 141, 230 );
    ea.showGraph(0);

    ea.setTitles([
        '<strong>Graph representation of the mappings</strong>',
        '<strong>Graph representation of the ontology</strong>',
        '<strong>Graph representation of the database</strong>'
    ]);

    $('#btn_show_mapping').click( function (){ ea.showGraph(0) } );
    $('#btn_show_ontology').click( function (){ ea.showGraph(1) } );
    $('#btn_show_database').click( function (){ ea.showGraph(2) } );

});


// ---------------------------------------------------------------------------
// Mapping Tab Options
// ---------------------------------------------------------------------------
/*
// Menu when we click a element on the Mapping graph.
var cursor_pos = [];
function showMenu( node, e, isNode ){
console.log("A");
    var show = false;

    //console.log("Type: ", node.data.type, " Node: ", node );

    //if ( e.clientX == cursor_pos[0] && e.clientY == cursor_pos[1] ) show = true; // Click and not dragged.

    cursor_pos[0] = e.clientX;
    cursor_pos[1] = e.clientY;

   // if ( !show ) return;

    $('#mapping_dropdown').css('left', (e.clientX-90)+'px');
    $('#mapping_dropdown').css('top', (e.clientY-85)+'px');

    $('#mapping_dropdown .url1').attr("href", php_vars.base_url + "index.php/mappedclass/createnew/" + php_vars.datasource_id + "/" + php_vars.mappedspace_id  + "/" + node.data.id );
    $('#mapping_dropdown .url2').attr("href", php_vars.base_url + "index.php/mappeddataproperty/addnew/" + php_vars.datasource_id + "/" + php_vars.mappedspace_id  + "/" + node.data.id );
    $('#mapping_dropdown .url3').attr("href", php_vars.base_url + "index.php/mappedobjectproperty/addnew/" + php_vars.datasource_id + "/" + php_vars.mappedspace_id  + "/" + node.data.id );
    $('#mapping_dropdown .url4').attr("href", php_vars.base_url + "index.php/mappedclass/expand/" + php_vars.datasource_id + "/" + php_vars.mappedspace_id  + "/" + node.data.id );
    $('#mapping_dropdown .url5').attr("href", php_vars.base_url + "index.php/mappeddataproperty/addnew/" + php_vars.datasource_id + "/" + php_vars.mappedspace_id  + "/" + node.data.mappedclass_id + "/" + node.data.dataproperty_id );
    $('#mapping_dropdown .url6').attr("href", php_vars.base_url + "index.php/mappedclass/delete/" + php_vars.datasource_id + "/" + php_vars.mappedspace_id  + "/" + node.data.id );
    $('#mapping_dropdown .url8').attr("data-content", node.data.uri );
    $('#mapping_dropdown .url8').popup({inline: true});
    

    var btnUrl, mappingspaces_buttons = "";

    for ( i = 0; i < php_vars.mappingspaces.length; i++ ) {

        if( php_vars.mappingspaces[i].id == php_vars.mappingspace.id ) {
            mappingspaces_buttons += '<div class="item disabled">' + php_vars.mappingspaces[i].name + '</div>';
        } else {

            btnUrl = php_vars.base_url + "index.php/mappedclass/move/" + node.data.id + "/" + php_vars.mappingspaces[i].id;

            mappingspaces_buttons += '<div class="item" onclick="window.location = \' ' + btnUrl + ' \'">';
            mappingspaces_buttons += php_vars.mappingspaces[i].name;
            mappingspaces_buttons += '</div>';
        }

    }

    $(".suburl7").html(mappingspaces_buttons);

    $('#mapping_dropdown').removeClass('hidden');

    if ( isNode ) {
        if ( node.data.type === 1 ) { // Object property
            $('#mapping_dropdown .item').removeClass('hidden');
            $('#mapping_dropdown .url5').addClass('hidden');
            if ( php_vars.user_technician != true ) $('#mapping_dropdown .url8').addClass('hidden');

        } else if ( node.data.type === 2 ) { // Table main
            $('#mapping_dropdown').addClass('hidden');

        } else if ( node.data.type === 3 ) { // Table
            $('#mapping_dropdown').addClass('hidden');

        } else if ( node.data.type === 4 ) { // Data property
            $('#mapping_dropdown .item').addClass('hidden');
            $('#mapping_dropdown .url5').removeClass('hidden');
            $('#mapping_dropdown .url6').removeClass('hidden');
            if ( php_vars.user_technician == true ) $('#mapping_dropdown .url8').removeClass('hidden');
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

*/

// ---------------------------------------------------------------------------
// Ontology Tab Options
// ---------------------------------------------------------------------------

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
// Database Tab Options
// ---------------------------------------------------------------------------

function dbgraph_add_search_box_Table(string_uri){

    if ( ea.actualtab !== 2 ) return false;

    var table_row = ea.hideSearchBox( "table", string_uri );

    table_row = table_row.split("->");

    ea.dbgraph_app.mapon_focus(table_row[0], table_row[1]); // Centrar

    return true;
}







