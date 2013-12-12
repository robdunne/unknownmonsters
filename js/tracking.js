$(document).ready(function() {
	getTracking();
	setmap();
	setPadding();
	getImages();
	liveMentions();
});

$(window).resize(function() {
	setPadding();
});

// Set nav padding
function setPadding() {
	var p = parseInt($(document).width()/100);
	var n = $('#nav').height();
	$('#nav a').css('padding',p);
}

// Graphs
function getTracking() {
	// x axis is time
	// y axis is number of mentions
	$.getJSON('app/data/tracking.json',function(data) {		
		var graph = new Rickshaw.Graph({
				element: document.querySelector("#graph"),
				series: [
				{
					name: "Chengdu Sasquatch",
					data: data.bigfoot,
					color: '#970027'
				},
				{
					name: "Funan River Mermaid",
					data: data.mermaid,
					color: '#F5003D'
				},
				{
					name: "Leshan Tablets",
					data: data.mermaid,
					color: '#B30015'
				}
				]
		});

		var x_axis = new Rickshaw.Graph.Axis.Time({ graph: graph });

		var y_axis = new Rickshaw.Graph.Axis.Y({
				graph: graph,
				orientation: 'left',
				tickFormat: Rickshaw.Fixtures.Number.formatKMBT,
				element: document.getElementById('y_axis'),
		});
	
		var legend = new Rickshaw.Graph.Legend({
			element: document.querySelector('#legend'),
			graph: graph
		});

		graph.render();
	});
}

// Maps
function setmap() {
	google.maps.event.addDomListener(window, 'load', initialize);
}

function initialize() {
	var mapDiv = document.getElementById('map');
	var map = new google.maps.Map(mapDiv, {
	  center: new google.maps.LatLng(30.6586, 104.0647),
	  zoom: 10,
	  mapTypeId: google.maps.MapTypeId.ROADMAP
	});

	var infoWindow = new google.maps.InfoWindow({
	  position: map.getCenter(),
	  content: 'R&D Location'
	});
	infoWindow.open(map);
}

// Images
function getImages() {
	var url = 'https://api.datamarket.azure.com/Bing/Search/v1/Composite?Sources=%27web%2Bimage%2Bnews%27&Query=%27sasquatch%27';
	var encodedAppKey = 'OndNSTU2T1RjYVhTWkJCQnhQNGttQXIvQ29ydlZpRWlsSVI4UVFoWUM2SVk=';

	$.ajax({
		type: 'GET',
		url: url,
		dataType: "json",
		context: this,
		beforeSend: function (xhr) {
			//base64 encoded: ignore:key
			xhr.setRequestHeader('Authorization', 'Basic '+encodedAppKey);
		},
		success: function(data,status){
			$('#container').empty();
			//parse data...     
			for(var i=0;i<data.d.results[0].Image.length;i++) {
				$('#container').append('<div class="item"><img src="'+data.d.results[0].Image[i].MediaUrl+'"></div>');
			}
		
			setTimeout(function() {
				var container = document.querySelector('#container');
				var msnry = new Masonry( container, {
				  // options
				  itemSelector: '.item'
				});
			},500);   
		}
	});
}

function liveMentions() {
	$.ajax({
		type: 'GET',
		url: 'app/comments.php',
		dataType: "json",
		success: function(data,status){
			$('#mentions').empty();
			//parse data...     
       		for(var i=0;i<data.length;i++) {
       			$('#mentions').append(data[i].title);
       		}
		}
	});	
}