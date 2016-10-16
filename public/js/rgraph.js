
//Other javascript functions. 
function openPopup(capa){
        document.getElementById(capa).style.pixelLeft = (document.width/2) - 250;
        document.getElementById(capa).style.pixelTop = (document.height/2) - 300;
        document.getElementById(capa).style.visibility = "visible";
}

function closePopup(capa){
        //document.getElementById('fonsFosc').style.visibility = "hidden";
        document.getElementById(capa).style.visibility = "hidden";
}

//fer post atraves de javascript
function post_to_url(path, params, method) {
    method = method || "post"; // Set method to post by default, if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
        }
    }

    document.body.appendChild(form);
    form.submit();
}