
// Own functions
// --------------
var dbgraph_internal_app;


function dbGraph_initialize( loaded_callback ) {

    dbGraph_define_elements();

    // Layout
    $('#area').css({'background':'none'});


    dbgraph_internal_app = new SQL.Designer();

    dbgraph_internal_app.mapon_setLoadedCallback(function() {
        if ( loaded_callback !== undefined ) loaded_callback(dbgraph_internal_app);
    });

    // Layout
    var elementToDrag = document.getElementById('area');
    draggable(elementToDrag);

    //$( "#area" ).draggable();
    $('#area svg').css({'border':'3px black solid'});
    if ( php_base_url === undefined ) var php_base_url = "http://localhost/mapon/";
    $('#area svg').css({'background':'transparent url(' + php_base_url + '/public/img/external/dbgraph/back.png)'});


    // Scale on mousewheel
    //if(window.addEventListener) document.addEventListener('DOMMouseScroll', scale_dbgraph, false); // Mozilla
    //document.onmousewheel = scale_dbgraph; //for IE/OPERA etc
    if(window.addEventListener) document.getElementById("graph_3_database").addEventListener('DOMMouseScroll', scale_dbgraph, false); // Mozilla
    document.getElementById("graph_3_database").onmousewheel = scale_dbgraph; //for IE/OPERA etc

    return dbgraph_internal_app;
}



SQL.Designer.prototype.mapon_focus = function( table_name, row_name ) {
    var element, elementX, elementY;

    element = this.mapon_getRow( table_name, row_name );

    if ( element === undefined ) return;

    this.mapon_deselectAll();

    var isRow = ( element.owner !== undefined && element.owner.select !== undefined );

    if ( isRow ) {
        elementX = parseInt(element.owner.x) + (element.owner.width/2);
        elementY = parseInt(element.owner.y) + (element.owner.height/2);

        element.owner.select(); // Select the table
        element.click(); // Click the row to show the info
    } else {
        elementX = parseInt(element.x) + (element.width/2);
        elementY = parseInt(element.y) + (element.height/2);

        element.select();
    }

    var pos_x = ($('#area').parent().width()/2) - elementX;
    var pos_y = ($('#area').parent().height()/2) - elementY;


    $('#container #area').css('transform', 'scale(1)');


    $('#area').css('left', pos_x);
    $('#area').css('top', pos_y);
}




// Override librery functions
// ---------------------------

SQL.Designer.prototype.mapon_addTable = function( table_name, pos_x , pos_y ) { /* finish adding new table */
    return this.addTable( table_name, pos_x, pos_y );
}

SQL.Designer.prototype.mapon_moveTable = function( table_name, pos_x , pos_y ) { /* finish adding new table */
    if ( table_name === undefined || table_name === "" ) return undefined;

    for ( var i = 0; i < this.tables.length; i++ ) {
        if (this.tables[i].getTitle().toLowerCase() === table_name.toLowerCase()) {
            this.tables[i].moveTo(pos_x , pos_y);
            return;
        }
    }

}


SQL.Designer.prototype.mapon_addRow = function( table_element, row_name, primary_key, type ) {
    var row = table_element.addRow(row_name,{ai:false, type:getTypeNum(type), stype: type});

    if ( primary_key === true ) {
        var key = table_element.addKey("PRIMARY","");
        key.addRow(row);
    }

    return row;
}


SQL.Designer.prototype.mapon_getRow = function( table_name, row_name ) {

    if ( table_name === undefined || table_name === "" ) return undefined;

    for ( var i = 0; i < this.tables.length; i++ ) {
        if ( this.tables[i].getTitle().toLowerCase() === table_name.toLowerCase() ) {

            if ( row_name === undefined || row_name === "" ) return this.tables[i];

            for ( var k = 0; k < this.tables[i].rows.length; k++ ) {
                if ( this.tables[i].rows[k].getTitle().toLowerCase() === row_name.toLowerCase() ) {
                    return this.tables[i].rows[k];
                }
            }
            break;
        }
    }
    return undefined;

}

SQL.Designer.prototype.mapon_deselectAll = function( tables, rows ) {
    rows = (rows === undefined) ? true : rows;
    tables = (tables === undefined) ? true : tables;

    for ( var i = 0; i < this.tables.length; i++ ) {
        if ( tables ) this.tables[i].deselect();
        for ( var k = 0; k < this.tables[i].rows.length; k++ ) {
            if ( rows ) this.tables[i].rows[k].deselect();
        }
    }
}

SQL.Designer.prototype.mapon_addForeginKey = function ( source_row_element, dest_row_element) {
    if ( dest_row_element === undefined || source_row_element === undefined ) return undefined;
    this.addRelation(source_row_element, dest_row_element);
}


function getTypeNum( type_name ) {

    type_name = type_name.toLowerCase();

    switch ( type_name ) {
        // Mysql
        case "mediumtext": type_name = "varchar"; break;
        case "string": type_name = "varchar"; break;
        case "mediumint unsigned": type_name = "mediumint"; break;
        case "int unsigned": type_name = "integer"; break;
        case "mediumint unsigned": type_name = "mediumint"; break;
        // Postgres
        case "bool": type_name = "bit"; break;
        case "serial": type_name = "integer"; break;
        case "int2": type_name = "smallint"; break;
        case "int4": type_name = "integer"; break;
        case "numeric": type_name = "integer"; break;
        case "bytea": type_name = "bit"; break;
        case "bpchar": type_name = "varchar"; break;
        case "tsvector": type_name = "varchar"; break;
        case "_text": type_name = "varchar"; break;
    }


    var dbgraph_internal_types =  {
        "integer": 0, "tinyint": 1, "smallint": 2, "mediumint": 3, "int": 4, "bigint": 5,
        "decimal": 6, "single precision": 7, "double": 8, "char": 9, "varchar": 10,
        "text": 11, "binary": 12, "varbinary": 13, "bolb": 14, "date": 15, "time": 16,
        "datetime": 17, "year": 18, "timestamp": 19, "enum": 20, "set": 21, "bit": 22
    };



    if ( dbgraph_internal_types[type_name] === undefined ) {
        console.log("Type not found: ", type_name);
        return 0;
    }

    return dbgraph_internal_types[type_name];
}


SQL.Designer.prototype.mapon_setLoadedCallback = function(cbk) {
    var original_function = this.init2;
    var self = this;

    this.init2 = function() {
        original_function.call( self );
        cbk();
    }
}

SQL.Designer.prototype.mapon_setClickElementCallback = function(cbk) {
    var original_rm_function = this.rowManager.select;
    var self_rm = this.rowManager;
    var self = this;

    SQL.RowManager.prototype.select = function(row) {
        self.mapon_deselectAll( false, true );

        original_rm_function.call( self_rm, row );

        if ( row.owner !== undefined && row.owner.data !== undefined ) {
            row.owner.data.x = row.owner.x;
            row.owner.data.y = row.owner.y;
        }

        if ( row !== false ) cbk( {row: row.data || undefined, table: row.owner.data || undefined} );
    }

}

var dbgraph_internal_last_clicked_table;
var dbgraph_internal_last_clicked_table_x;
var dbgraph_internal_last_clicked_table_y;

SQL.Designer.prototype.mapon_setMoveTableCallback = function(cbk) {
    var original_tm_function = this.tableManager.select;
    var self_tm = this.tableManager;
    var self = this;

    SQL.TableManager.prototype.select = function(table, multi) {

        original_tm_function.call( self_tm, table, multi );

        if ( table.data === undefined ) return;

        if ( table.data.title === dbgraph_internal_last_clicked_table &&
             table.x === dbgraph_internal_last_clicked_table_x &&
            table.y ===dbgraph_internal_last_clicked_table_y
        ) {
            return;
        }

        var fisrt_time = (dbgraph_internal_last_clicked_table === undefined);

        dbgraph_internal_last_clicked_table = table.data.title;
        dbgraph_internal_last_clicked_table_x = table.x;
        dbgraph_internal_last_clicked_table_y = table.y;

        table.data.x = table.x;
        table.data.y = table.y;

        if (!fisrt_time) cbk( {table: table.data } );

    }

}


/*
var dbgraph_internal_move_table_callback;

var dbgraph_internal_old_moveto = SQL.Table.prototype.moveTo;
SQL.Table.prototype.moveTo = function(x, y) {
    dbgraph_internal_old_moveto.call( this, x, y);
    if ( dbgraph_internal_move_table_callback !== undefined ) dbgraph_internal_move_table_callback(x,y);
    console.log("Entra: x:", x, ", y:", y, ", This: ", this);
}*/

/*
SQL.RowManager.prototype.mapon_setClickElementCallback = function(cbk) {
    var original_function = this.select;
    var self = this;

    SQL.RowManager.prototype.select = function(row) {
        original_function.call( self, row );
        cbk( row );
    }
}
*/


// Canvas size
// --------------

SQL.Designer.prototype.sync = function() {
    var t = -1, min_x = 0, min_y = 0, max_x = 0, max_y = 0;
    var border = 20;

    for ( var i = 0; i < this.tables.length; i++ ) {
        t = this.tables[i];
        t.x = parseInt(t.x);
        t.y = parseInt(t.y);

        if ( t.x < min_x ) min_x = t.x;
        if ( t.x + t.width > max_x ) max_x = t.x + t.width;

        if ( t.y < min_y ) min_y = t.y;
        if ( t.y + t.height > max_y ) max_y = t.y + t.height;

    }

    if ( min_x < 0 || min_y < 0 ) {
        for ( var i = 0; i < this.tables.length; i++ ) {
            t = this.tables[i];

            t.moveTo(t.x - min_x + border, t.y - min_y + border);
        }
    }

    this.width = Math.abs(min_x) + Math.abs(max_x) + border*2;
    this.height = Math.abs(min_y) + Math.abs(max_y) + border*2;
    this.map.sync();

    if (this.vector) {
        this.dom.svg.setAttribute("width", this.width );
        this.dom.svg.setAttribute("height", this.height );
    }

}

SQL.Designer.prototype.alignTables = function() {
    //var win = OZ.DOM.win();
    //var avail = win[0] - OZ.$("bar").offsetWidth;
    var tables_per_line =  Math.sqrt(this.tables.length);
    var avail = tables_per_line * 250;
    var x = 10;
    var y = 10;
    var max = 0;
console.log("Tables per line: ",tables_per_line );
    this.tables.sort(function(a,b){
        return b.getRelations().length - a.getRelations().length;
    });

    for (var i=0;i<this.tables.length;i++) {
        var t = this.tables[i];
        var w = t.dom.container.offsetWidth;
        var h = t.dom.container.offsetHeight;
        if (x + w > avail) {
            x = 10;
            y += 10 + max;
            max = 0;
        }
        t.moveTo(x,y);
        x += 10 + w;
        if (h > max) { max = h; }
    }

    this.sync();
}


// Scale on mouse wheel
// ---------------------



var dbscale = 1;
function scale_dbgraph(event) {
    var delta = 0;

    if (!event) event = window.event;

    var old_pos = $( "#container #area" ).offset();
    var old_center_x = old_pos.left + (($('#container #area').width() * dbscale) / 2);
    var old_center_y = old_pos.top + (($('#container #area').height() * dbscale) / 2);

    // normalize the delta
    if (event.wheelDelta) {
        delta = event.wheelDelta / 60; // IE and Opera
    } else if (event.detail) {
        delta = -event.detail / 2;// W3C
    }

    if (delta > 0) {
        dbscale += 0.1;
    } else {
        dbscale -= 0.1;
    }

    if ( dbscale < 0.1 ) dbscale = 0.1;

    var old_position = $( "#container #area" ).offset();

    $('#container #area').css('transform', 'scale(' + dbscale + ')');


    var new_center_x = $( "#container #area" ).offset().left + (($('#container #area').width() * dbscale) / 2);
    var new_center_y = $( "#container #area" ).offset().top + (($('#container #area').height() * dbscale) / 2);

    var pos_x = $( "#container #area" ).offset().left + ( old_center_x - new_center_x);
    //var pos_y = $( "#container #area" ).offset().top + ( old_center_y - new_center_y);
    var pos_y = old_pos.top;

    // El centro está arriba en el medio.
    $( "#container #area" ).offset( { top: pos_y, left: pos_x} ); // Position before scaling

}


// Fixing library bugs
// -------------------

// Initialize all buttons and elements of the view
// Are not needed, but if not defined shows an error.
function dbGraph_define_elements() {
    var ids = [
        "addtable", "removetable", "aligntables", "cleartables", "addrow", "edittable", "tablekeys",
        "saveload", "editrow", "uprow", "downrow", "foreigncreate", "foreignconnect", "foreigndisconnect",
        "removerow", "options", "toggle", "bar", "minimap", "background", "throbber", "windowok",
        "windowcancel", "opts", "controls", "language", "optionlocale", "hide", "optionhide", "vector",
        "optionvector", "showsize", "optionshowsize", "showtype", "optionshowtype", "db", "optiondb",
        "snap", "optionsnap", "optionsnapnotice", "pattern", "optionpattern", "optionpatternnotice",
        "optionsnotice", "clientlocalsave", "clientsave", "clientlocalload", "clientlocallist",
        "clientload", "clientsql", "dropboxsave", "dropboxload", "dropboxlist", "quicksave", "serversave",
        "serverload", "serverlist", "serverimport", "client", "server", "output", "backendlabel", "backend",
        "io", "keys", "keyadd", "keyremove", "keyedit", "keytypelabel", "keynamelabel", "keyfieldslabel",
        "keyavaillabel", "keyslistlabel", "keyslist", "keytype", "keyname", "keyleft", "keyright", "keyfields",
        "keyavail", "tablenamelabel", "tablecommentlabel", "table"
    ];

    $( "#area" ).append("<div id='the_options' style='display: none; visibility: hidden;'></div>");
    for ( var i in ids ) {
        $( "#the_options" ).append( "<a id='" + ids[i] + "'></a>" );
    }
}

// Disabling Rubberband (for dragging compatibility)
SQL.Rubberband = function(owner) {}

// Avoid message "Are you sure to leave the page?"
window.onbeforeunload = function(e) {}

// Avoid requesting names for the buttons.
SQL.Designer.prototype.requestLanguage = function() { this.flag--; };

// Avoid opening windows when doubleclicj
SQL.Window = function(owner) {};
SQL.Window.prototype.open = function(title, content, callback) {}

// Disable editing options when double click
SQL.TableManager.prototype.edit = function(e) {};
SQL.Row.prototype.expand = function() {};

// Change url
SQL.Designer.prototype.requestDB = function() { /* get datatypes file */
    var db = this.getOption("db");
    var bp = this.getOption("staticpath");
    if ( php_vars.base_url === undefined ) var php_base_url = "http://localhost/mapon/";
    var url = php_vars.base_url + "public/js/external/dbgraph/db/"+db+"/datatypes.xml";
    OZ.Request(url, this.dbResponse.bind(this), {method:"get", xml:true});
}