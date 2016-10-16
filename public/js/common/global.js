
$(document).ready(function() {

    // Clears the default value of a text field when we start writing.
    $('.gl_clear_default').focusin(function () {
        if ($(this).val() == $(this).attr('value')) {
            $(this).val("");
        }
    });
    $('.gl_clear_default').focusout(function () {
        if ($(this).val() == "") {
            $(this).val($(this).attr('value'));
        }
    });

    // Hides all the elements with class "gl_clickOut_hide" if we click outside the element.
    $(document).click(function (e) {
        if ( !$(e.target).hasClass('gl_clickOut_hide') &&
             !$(e.target).hasClass('gl_clickOut_disable') &&
             !$(e.target).parent().hasClass('gl_clickOut_hide') &&
             !$(e.target).parent().parent().hasClass('gl_clickOut_hide') ) {
            $(".gl_clickOut_hide").fadeOut();
        }
    });

});

// Get uri from QName
function gl_getUri( prefixes, QName ) {
    var prefixs = JSON.parse( prefixes );
    var uri_parts = QName.split(":");
    var res = "";

    prefixs.forEach( function( prefix ) {
        if ( prefix.prefix === uri_parts[0] ) {
            res = prefix.iri + uri_parts[1];
            return;
        }
    });

    return res;
}

// Get QName from uri
function gl_getQName( prefixes, URI ) {
    var prefixs = JSON.parse( prefixes );
    var res = "";

    prefixs.forEach( function( prefix ) {
        if ( URI.indexOf( prefix.iri ) > -1 ) {
            res = URI.replace( prefix.iri, prefix.prefix + ":" );
            return;
        }
    });

    return res;
}