// Settings
var ontologyFileName = "./ontology.json";

//var fileName = './demo_ontology_graph.json';
//$.getJSON( fileName, showGraph);

NODE_SIZE_CLASS = 6;
NODE_COLOR_CLASS = "rgb(255, 51, 51)";
NODE_COLOR_EQCLASS = "rgb(255, 120, 51)";
EDGE_COLOR_SUBCLASS = "black";

NODE_SIZE_PROPERTY = 3;
NODE_COLOR_PROPERTY = "rgb(51,51,255)";
EDGE_COLOR_SUBPROPERTY = "black";
EDGE_COLOR_PROPERTY_DOMAIN = "rgb(51,51,255)";
EDGE_COLOR_PROPERTY_RANGE = "rgb(51,51,255)";

DATA_PROPERTY_TYPES = [ "Thing", "topDataProperty", "string", "int", "dateTime" ];
NODE_COLOR_DATA = "black";
NODE_SIZE_DATA = 6;





$.getJSON( ontologyFileName, parseOntology);


var nnodes = {};

function parseOntology( data ) {
  var nodes = [];
  var edges = [];
  
  // add nodes for simple data types
  $.each( DATA_PROPERTY_TYPES, function( i, val ) {
    var n = { id: val, label: val };
    n.color = NODE_COLOR_DATA;
    n.size = NODE_SIZE_DATA;
    
    n.properties = [];
    
    nodes.push(n);
    nnodes[n.id] = n;
  });
  
  $.each( data.classes, function( i, val ) {
    //add sigmaJS graph fields to the given owl class object
    var x = enhanceClass(val);
    nodes.push(x.node);
    nnodes[x.node.id] = x.node;
    edges = edges.concat(x.edges);
  });
  
  $.each( data.properties, function( i, val ) {
    //add sigmaJS graph fields to the given owl property object
    var x = enhanceProperty(val);
    nodes.push(x.node);
    nnodes[x.node.id] = x.node;
    edges = edges.concat(x.edges);
  });

  
  // display the graph
  showGraph({ nodes:nodes, edges:edges });
}





function enhanceClass(c) {
/*
-- Input --
{
  "id": "Student",
  "subClassOf": [
    "Person"
  ],
  "equivalentClass": [
    "isTeachingAssistantOf some Subject"
  ]
} 
-- Enhanced output --
{
  "label": "Student",
  "id": "Student",
  "color": "rgb(255,204,102)",
  "size": 8,
  
  "name": "Student",
  "subClassOf": [
    "Person"
  ],
  "equivalentClass": [
    "isTeachingAssistantOf some Subject"
  ]
}
-- and edges --
{
  "id": "Student-subClassOf-Person",
  "source": "Person",
  "target": "Student",
  "label": "subClassOf"
}
*/

  if(!c.label)
    c.label = c.id;
  
  c.ontologyType = "class";
  
  c.color = NODE_COLOR_CLASS;
  if(c.equivalentClass)
    c.color = NODE_COLOR_EQCLASS;
  
  c.size = NODE_SIZE_CLASS;
  
  var subClassEdges = [];
  if(c.subClassOf)
  {
    $.each( c.subClassOf, function( i, s ) {
      var pid = c.id + "-subClassOf-" + s;
      var e = { id: pid, label: "subClassOf", source: s, target: c.id };
      e.color = EDGE_COLOR_SUBCLASS;
      e.type = "arrow";
      
      subClassEdges.push(e);
    });
  }
  
  c.properties = [];
  
  
  return { node:c, edges:subClassEdges };
}


function enhanceProperty(p) {
/*
-- Input --
{
  "id": "teaches",
  "domain": "Faculty",
  "range":  "Subject",
  "subPropertyOf": [
    "connectsToBox"
  ]
}
*/
  
  if(!p.label)
    p.label = p.id;
    
  p.descr = p.domain + " -> " + p.range;
  var ddescr = p.id + " ( " +  p.descr + " )";
  
  p.ontologyType = "property";
  
  p.color = NODE_COLOR_PROPERTY;
  
  p.size = NODE_SIZE_PROPERTY;
  
  var edges = [];
  if(p.subPropertyOf)
  {
    $.each( p.subPropertyOf, function( i, s ) {
      var pid = p.id + "-subPropertyOf-" + s;
      var e = { id: pid, label: "subPropertyOf", source: s, target: p.id };
      e.color = EDGE_COLOR_SUBPROPERTY;
      e.type = "arrow";
      
      edges.push(e);
    });
  }
  
  if(p.domain) {
    var domain = { id: p.id+"-domain", label: "domain", source: p.domain, target: p.id };
    domain.color = EDGE_COLOR_PROPERTY_DOMAIN;
    domain.type = "arrow";
    
    if(nnodes[p.domain]) {
      edges.push(domain);
      
      //add info to domain class
      nnodes[p.domain].properties.push(ddescr);
    }
    else {
      console.log("WARNING: Domain class not found for Property "+ddescr+".");
    }
  }
  
  if(p.range) {
    var range = { id: p.id+"-range", label: "range", source: p.id, target: p.range };
    range.color = EDGE_COLOR_PROPERTY_RANGE;
    range.type = "arrow";
    
    if(nnodes[p.range]) {
      edges.push(range);
      
      //add info to domain class
      nnodes[p.range].properties.push(ddescr);
    }
    else {
      console.log("WARNING: Range class not found for Property "+ddescr+".");
    }
  }
  
  return { node:p, edges:edges };
}
