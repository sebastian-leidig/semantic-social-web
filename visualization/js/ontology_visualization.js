/**
 *  Displays the graph described by data using sigmaJS library.
 */
function showGraph(data) {
  s = new sigma({container: 'graph-container', graph: data });
  initForceAtlasLayout();
  initNodeSelectGreyout(s);
  resetDetails();
  
  s.bind('clickNode', displayDetails);
  s.bind('clickStage', resetDetails);
  
  // make data available for download
  var dataJSON = "text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(data));
  $('#graph-download').attr("href", "data:'" + dataJSON + "'");
}


// Add a method to the graph model that returns an
// object with every neighbors of a node inside:
sigma.classes.graph.addMethod('neighbors', function(nodeId) {
    var k,
        neighbors = {},
        index = this.allNeighborsIndex[nodeId] || {};

    for (k in index)
      neighbors[k] = this.nodesIndex[k];

    return neighbors;
});


/**
 *  Display details about the selected node on the page.
 */
function displayDetails(e) {
  var nodeId = e.data.node.id;
  
  var title = e.data.node.label;
  
  // show description
  $('#details-description').empty();
  if(e.data.node.descr)
    $('#details-description').html(e.data.node.descr);
  
  //display class/property details
  $('#details-class').hide();
  $('#details-property').hide();
  if(e.data.node.ontologyType)
  {
    if(e.data.node.ontologyType == "class") {
      $('#details-class').show();
      title = "Class: "+title;
    }
    else if(e.data.node.ontologyType == "property") {
      $('#details-property').show();
      title = "Property: "+title;
    }
  }
  
  listIfPresent(e.data.node.subClassOf, '#details-superclasses', "");
  listIfPresent(e.data.node.subPropertyOf, '#details-superproperties', "");
  
  listIfPresent(e.data.node.properties, '#details-class-properties', "");
  
  if(e.data.node.equivalentClass)
    listIfPresent(e.data.node.equivalentClass, '#details-additional', "Equivalent to ");
  else
    listIfPresent(e.data.node.inverseOf, '#details-additional', "Inverse of ");
  
  $('#details-title').html(title);
}
/**
 *  Resets the displayDetails().
 */
function resetDetails(e) {
  var x; // fake event object with default details
  x = { data: { node: { label:"Social Web Ontology", descr:"<i>Click a node in the graph to display its details.</i>" } } };
  displayDetails(x);
}

function listIfPresent(arr, domSelector, prefixText)
{
  $(domSelector).empty();
  if(arr) {
    var items = [];
    $.each( arr, function( i, val ) {
      items.push( "<li>" + prefixText + val + "</li>" );
    });
    $( "<ul/>", {
      //"class": "my-new-list",
      html: items.join( "" )
    }).appendTo( domSelector );
  }
  else {
    $(domSelector).text("None");
  }
}

/**
 *  Initialize the automatic layout (positioning of nodes) for the graph.
 */
function initForceAtlasLayout()
{
    // this below adds x, y attributes as well as size = degree of the node 
    var i,
            nodes = s.graph.nodes(),
            len = nodes.length;

    for (i = 0; i < len; i++) {
        nodes[i].x = Math.random();
        nodes[i].y = Math.random();
        //nodes[i].size = s.graph.degree(nodes[i].id);
        //nodes[i].color = nodes[i].center ? '#333' : '#666';
    }

    // Refresh the display:
    s.refresh();

    // ForceAtlas Layout
    s.startForceAtlas2();
    //s.stopForceAtlas2();
}

/**
 * Initialize the functionality to highlight a node's connections by clicking on it.
 */
function initNodeSelectGreyout(s)
{
    // We first need to save the original colors of our
    // nodes and edges, like this:
    s.graph.nodes().forEach(function(n) {
        n.originalColor = n.color;
    });
    s.graph.edges().forEach(function(e) {
        e.originalColor = e.color;
    });

    // When a node is clicked, we check for each node
    // if it is a neighbor of the clicked one. If not,
    // we set its color as grey, and else, it takes its
    // original color.
    // We do the same for the edges, and we only keep
    // edges that have both extremities colored.
    s.bind('clickNode', function(e) {
        var nodeId = e.data.node.id,
            toKeep = s.graph.neighbors(nodeId);
        toKeep[nodeId] = e.data.node;

        s.graph.nodes().forEach(function(n) {
            if (toKeep[n.id])
                n.color = n.originalColor;
            else
                n.color = '#eee';
        });

        s.graph.edges().forEach(function(e) {
            if (toKeep[e.source] && toKeep[e.target])
                e.color = e.originalColor;
            else
                e.color = '#eee';
        });

        // Since the data has been modified, we need to
        // call the refresh method to make the colors
        // update effective.
        s.refresh();
    });

    // When the stage is clicked, we just color each
    // node and edge with its original color.
    s.bind('clickStage', function(e) {
        s.graph.nodes().forEach(function(n) {
            n.color = n.originalColor;
        });

        s.graph.edges().forEach(function(e) {
            e.color = e.originalColor;
        });

        // Same as in the previous event:
        s.refresh();
    });
    
    // When the stage is clicked, we just color each
    // node and edge with its original color.
    s.bind('clickStage', function(e) {
      s.graph.nodes().forEach(function(n) {
        n.color = n.originalColor;
      });

      s.graph.edges().forEach(function(e) {
        e.color = e.originalColor;
      });

      // Same as in the previous event:
      s.refresh();
    });
}
