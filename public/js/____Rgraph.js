//levels to show
//http://groups.google.com/group/javascript-information-visualization-toolkit/browse_thread/thread/7a2e6db601039dc9/3b6bb34aad1eac04?lnk=gst&q=rgraph+node+distance#3b6bb34aad1eac04

var labelType, useGradients, nativeTextSupport, animate;

//javascript functions for controling Rgraph

(function() {
  var ua = navigator.userAgent,
      iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
      typeOfCanvas = typeof HTMLCanvasElement,
      nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'),
      textSupport = nativeCanvasSupport 
        && (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
  //I'm setting this based on the fact that ExCanvas provides text support for IE
  //and that as of today iPhone/iPad current text support is lame
  labelType = (!nativeCanvasSupport || (textSupport && !iStuff))? 'Native' : 'HTML';
  nativeTextSupport = labelType == 'Native';
  useGradients = nativeCanvasSupport;
  animate = !(iStuff || !nativeCanvasSupport);
})();

var Log = {
  elem: false,
  write: function(text){
    if (!this.elem) 
      this.elem = document.getElementById('log');
    this.elem.innerHTML = text;
    this.elem.style.left = (500 - this.elem.offsetWidth / 2) + 'px';
  }
};


function init_rgraph(id){

    //init data
    //var jsonX = [ { id: "id-1", name: "Oikopedia", data: { "$dim": 10.0} },{id: "id-2",name: "test221",data: { "$dim": 10.0, "$angularWidth":5 },adjacencies: [{ "nodeTo": "id-1", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-3",name: "test221",data: { "$dim": 10.0, "$angularWidth":5 },adjacencies: [{ "nodeTo": "id-1", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-5",name: "test221",data: { "$dim": 10.0, "$angularWidth":5 },adjacencies: [{ "nodeTo": "id-1", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-6",name: "test221",data: { "$dim": 10.0, "$angularWidth":5 },adjacencies: [{ "nodeTo": "id-1", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-7",name: "test221",data: { "$dim": 10.0, "$angularWidth":5 },adjacencies: [{ "nodeTo": "id-1", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4",name: "test1",data: { "$dim": 10.0, "$angularWidth":5 },adjacencies: [{ "nodeTo": "id-1", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_1",name: "test1.1",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_2",name: "test1.2",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_3",name: "test1.3",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_4",name: "test1.4",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_5",name: "test1.5",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_6",name: "test1.6",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_7",name: "test1.7",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_8",name: "test1.8",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_9",name: "test1.9",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_10",name: "test1.1",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_20",name: "test1.2",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_30",name: "test1.3",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_40",name: "test1.4",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_50",name: "test1.5",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_60",name: "test1.6",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_70",name: "test1.7",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_80",name: "test1.8",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]},{id: "id-4_90",name: "test1.9",data: { "$dim": 10.0 },adjacencies: [{ "nodeTo": "id-4", "data": { "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } }]}];
    //var id_1 = [ { id: "id_1", name: "Oikopedia", data: { "$dim": 10.0} }, { id: "id_2", name: "Workspaces", data: { "$dim": 10.0, "$angularWidth":5 }, adjacencies: [ { "nodeTo": "id_1", "data": {"weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "id_3", name: "CaseRepository", data: { "$dim": 10.0, "$angularWidth":5 }, adjacencies: [ { "nodeTo": "id_1", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "id_4", name: "test1", data: { "$dim": 10.0, "$angularWidth":5 }, adjacencies: [ { "nodeTo": "id_1", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "id_4_1", name: "test1.1", data: { "$dim": 10.0 }, adjacencies: [ { "nodeTo": "id_4", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "id_4_2", name: "test1.2", data: { "$dim": 10.0 }, adjacencies: [ { "nodeTo": "id_4", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] } ];
    //var id_2 = [ { id: "id_2", name: "Workspaces", data: { "$dim": 10.0 } }, { id: "id_1", name: "Oikopedia", data: { "$dim": 10.0, "$angularWidth":50 }, adjacencies: [ { "nodeTo": "id_2", "data": {"weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "blank1", name: "", data: { "$dim": 0.0, "$angularWidth":50 }, adjacencies: [ { "nodeTo": "id_2", "data": {"weight": 3, "relations": "", "$color": "#FFFFFF" } } ] }, { id: "id_3", name: "CaseRepository", data: { "$dim": 10.0 }, adjacencies: [ { "nodeTo": "id_1", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "id_4", name: "test1", data: { "$dim": 10.0 }, adjacencies: [ { "nodeTo": "id_1", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] } ];
    //var id_3 = [ { id: "id_3", name: "CaseRepository", data: { "$dim": 10.0 } }, { id: "id_1", name: "Oikopedia", data: { "$dim": 10.0, "$angularWidth":50}, adjacencies: [ { "nodeTo": "id_3", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "blank1", name: "", data: { "$dim": 0, "$angularWidth":50 }, adjacencies: [ { "nodeTo": "id_3", "data": {"weight": 3, "relations": "", "$color": "#FFFFFF" } } ] }, { id: "id_2", name: "Workspaces", data: { "$dim": 10.0 }, adjacencies: [ { "nodeTo": "id_1", "data": {"weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "id_4", name: "test1", data: { "$dim": 10.0 }, adjacencies: [ { "nodeTo": "id_1", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] } ];
    //var id_4 = [ { id: "id_4", name: "test1", data: { "$dim": 10.0} }, { id: "id_1", name: "Oikopedia", data: { "$dim": 10.0, "$angularWidth":5 }, adjacencies: [ { "nodeTo": "id_4", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "id_2", name: "Workspaces", data: { "$dim": 10.0 }, adjacencies: [ { "nodeTo": "id_1", "data": {"weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "id_3", name: "CaseRepository", data: { "$dim": 10.0}, adjacencies: [ { "nodeTo": "id_1", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "id_4_1", name: "test1.1", data: { "$dim": 10.0, "$angularWidth":5  }, adjacencies: [ { "nodeTo": "id_4", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "id_4_2", name: "test1.2", data: { "$dim": 10.0, "$angularWidth":5 }, adjacencies: [ { "nodeTo": "id_4", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] } ];
    //var id_4_2 = [ { id: "id_4_2", name: "test1.2", data: { "$dim": 10.0 } },{ id: "id_1", name: "Oikopedia", data: { "$dim": 10.0}, adjacencies: [ { "nodeTo": "id_4", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "blank1", name: "", data: { "$dim": 0.0, "$angularWidth":50 }, adjacencies: [ { "nodeTo": "id_4_2", "data": {"weight": 3, "relations": "", "$color": "#FFFFFF" } } ] }, { id: "id_4", name: "test1", data: { "$dim": 10.0, "$angularWidth":50 }, adjacencies: [ { "nodeTo": "id_4_2", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "id_4_1", name: "test1.1", data: { "$dim": 10.0 }, adjacencies: [ { "nodeTo": "id_4", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] } ];
    //var id_4_1 = [ { id: "id_4_1", name: "test1.1", data: { "$dim": 10.0 } }, { id: "id_1", name: "Oikopedia", data: { "$dim": 10.0}, adjacencies: [ { "nodeTo": "id_4", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "blank1", name: "", data: { "$dim": 0.0, "$angularWidth":50 }, adjacencies: [ { "nodeTo": "id_4_1", "data": {"weight": 3, "relations": "", "$color": "#FFFFFF" } } ] }, { id: "id_4", name: "test1", data: { "$dim": 10.0, "$angularWidth":50 }, adjacencies: [ { "nodeTo": "id_4_1", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] }, { id: "id_4_2", name: "test1.2", data: { "$dim": 10.0 }, adjacencies: [ { "nodeTo": "id_4", "data": { "weight": 3, "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" } } ] } ];
    //end
    
    $jit.RGraph.Plot.EdgeTypes.implement({  
        'mySpecialType': {  
            'render': function(adj, canvas) {  

                //print your custom edge to canvas  
                var pos1 = adj.nodeFrom.pos.getc(true);
                var pos2 = adj.nodeTo.pos.getc(true);
                canvas.getCtx().globalCompositeOperation='destination-over'; 	

                this.edgeHelper.line.render({ x: pos1.x, y: pos1.y }, { x: pos2.x, y: pos2.y }, canvas);  

                var domlabel = document.getElementById(adj.nodeFrom.id+"-"+adj.nodeTo.id);
                if(!domlabel) {
                    domlabel= document.createElement('div');
                    domlabel.id = adj.nodeFrom.id+"-"+adj.nodeTo.id;
                    domlabel.innerHTML = adj.data.relations;
                    var style = domlabel.style;
                    style.position = 'absolute';
                    style.color = '#000000';
                    style.fontSize = '10px';
                    style.margin = '-10px 0px';
                    this.labels.getLabelContainer().appendChild(domlabel);
                }

                //now adjust the label placement
                var radius = this.viz.canvas.getSize();	
                domlabel.style.left = parseInt((pos1.x + pos2.x + radius.width - domlabel.offsetWidth) /2) + 'px';
                domlabel.style.top = parseInt((pos1.y + pos2.y + radius.height) /2) + 'px';

            }  
        }  
    });
 
	
    //init RGraph
    var rgraph = new $jit.RGraph({
        //Where to append the visualization
        injectInto: 'infovis',

        //Optional: create a background canvas that plots
        //concentric circles.
        background: {
            CanvasStyles: {
                    //strokeStyle: '#555'
            },
            numberOfCircles: 2,
            levelDistance: 125
        },

        //Add navigation capabilities:
        //zooming by scrolling and panning.
        Navigation: {
            //overridable: true,  
            enable: true,
            panning: false,
            zooming: 0
        },

        //Set Node and Edge styles.
        Node: {
            overridable: true, //sense el overridable no funcionava el $dim
            color: '#7878ff'
        },

        Edge: {
            overridable: true,
            color: '#FF3838',
            'type': 'mySpecialType' 
        },

        Events: {
            enable: true,  
            type: 'Native',  
            onMouseEnter: function(node, eventInfo, e){ 
                rgraph.canvas.getElement().style.cursor = 'pointer';
                if(node.id == "id_3" || node.id == "id_2"){
                    rgraph.graph.addAdjacence({"id": "id_2"},{"id": "id_3"},{ "id":"id_2-id_3", "relations": "<input type='button' value='1' onclick='openPopup(\"show-relations\");'/>" });
                }
                rgraph.fx.plot();
            },

            onMouseLeave: function(node, eventInfo, e){
                rgraph.canvas.getElement().style.cursor = '';
                //delete edge label
                if(node.id == "id_3" || node.id == "id_2"){
                    //Delete edge label 
                    //labels.disposeLabel(id) function doesn't work well. 
                    adjacenceLabel = document.getElementById("id_2-id_3");
                    if(adjacenceLabel==null){
                        adjacenceLabel = document.getElementById("id_3-id_2");

                        father = adjacenceLabel.parentNode;
                        father.removeChild(adjacenceLabel);

                        rgraph.graph.removeAdjacence("id_2","id_3");
                    }else{
                        father = adjacenceLabel.parentNode;
                        father.removeChild(adjacenceLabel);

                        //delete adjacence
                        rgraph.graph.removeAdjacence("id_3","id_2");
                    }
                }
                rgraph.refresh();
            },

            onClick: function(node, eventInfo, e) {
                //Avoid show popup when you click outside the concepts.
                if(node.id != undefined){
                    //focused node
                    if(node._depth==0){
                        openPopup("show-instances");
                    //relations	
                    }else{
                        //rgraph.labels.hideLabels(true);
                        //rgraph.labels.hideLabels(false);
                        document.getElementById("infovis").innerHTML="";

                        init_rgraph(eval(node.id));
                        //rgraph.loadJSON(eval(node.id));
                        //rgraph.refresh();

                        //alert("reload page for node: "+ node.id);
                    }
                }
            }

        },

        //Change father-child distance.
        levelDistance: 125,

        onBeforePlotLine: function(adj){
        },

        onBeforeCompute: function(node){
        },

        onCreateLabel: function(domElement, node){
            domElement.innerHTML = node.name;
        },

        //Change some label dom properties.
        //This method is called each time a label is plotted.
        onPlaceLabel: function(domElement, node){
            var style = domElement.style;
            style.display = '';
            style.cursor = 'pointer';

                        //label dels nodes de 0 i 1 nivell
            if (node._depth < 2) {
                style.fontSize = "0.8em";
                style.color = "#000000";

            } else {
                style.display = 'none';
            }

            var left = parseInt(style.left);
            var w = domElement.offsetWidth;
            style.left = (left - w / 2) + 'px';
        }
    });

    //this line help to show concentric circles in internet explorer 8.  
    rgraph.canvas.scale(1, 1);
    //load JSON data
    rgraph.loadJSON(eval(id));
    rgraph.refresh();
}

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