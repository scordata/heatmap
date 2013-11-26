var $loading = $('#loading'),
			$table,
			mapdiv = document.getElementById('map');
	FoursquareHeatmap = new Object();
	FoursquareHeatmap.map = {};
	FoursquareHeatmap.venueList = [];
	FoursquareHeatmap.infoWindow = new google.maps.InfoWindow();
	FoursquareHeatmap.radius = 4828; //3mi in m
	FoursquareHeatmap.currentPosition = {};
	FoursquareHeatmap.zoom = 15;
	FoursquareHeatmap.category = '';
	FoursquareHeatmap.locationPageSuccess = function (position) {
		"use strict";
		//ENTER CLIENT'S LAT AND LONG BELOW
		var lat = 40.752816;//position.coords.latitude;
		var	lng = -73.984059;//position.coords.longitude;
		FoursquareHeatmap.currentPosition.lat = lat;
		FoursquareHeatmap.currentPosition.lng = lng;
		FoursquareHeatmap.foursquareQuery(lat, lng, FoursquareHeatmap.radius);
	}
	FoursquareHeatmap.foursquareQuery = function (lat, lng) {
		"use strict";
		var $results = $('#results');
		$loading.fadeIn(3500);
		$.ajax({
			url: "https://api.foursquare.com/v2/venues/search",
			dataType: "json",
			data: {
				client_id: "N50XZT0AJ50Z5P4S2WOZDQ1C50F4UILQR5MB0ETZ4ZAX5PQ2",
				client_secret: "1LCYL00AHF45TS5LUPTBQCNR4UHNHS2GNOSUOSZ4EA0AGAT1",
				intent: "browse",
				radius: this.radius,
				ll: lat + "," + lng,
				categoryId: this.category,
				v: "20130105"
			},
			success: function (data) {
				var $tbody = $('tbody');
				$table = $('table');
				$tbody.children().remove();
				FoursquareHeatmap.venueList = data.response.venues.sort(function(a,b){
					if (a.hereNow.count < b.hereNow.count) {
						return 1;
					} else if (a.hereNow.count > b.hereNow.count) {
						return -1;
					} else {
						return 0;
					}
				});
				FoursquareHeatmap.initGMap(FoursquareHeatmap.venueList, lat, lng);
				$table.trigger('update');
			},
			error: function (xhr) {
				/// alert(xhr.responseText);
				console.log(xhr.responseText); // fail silently
			}
		});
	}
	FoursquareHeatmap.initGMap = function (venues, lat, lng) {
		delete map;
		var center = new google.maps.LatLng(lat, lng),
			myOptions = {
				zoom: this.zoom, /* zoom level of the map */
				center: center,
				draggable: true,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				mapTypeControl: false,
				panControl: false,
				streetViewControl: false,
				zoomControl: false,
				scaleControl: false,
				keyboardShortcuts: false,
				scrollwheel: false,
				mapTypeControlOptions: {
					mapTypeIDs: [google.maps.MapTypeId.ROADMAP, '_map_style']
				}
			};
		this.map = new google.maps.Map(mapdiv, myOptions); /* show the map in the element with the given div */
		$(mapdiv).append("<div id='loading'>Loading...</div>");
		//$.each(venues, function (index, venue) {
		for (var i = 0; i < venues.length; i++) {
			var latlng = new google.maps.LatLng(venues[i].location.lat, venues[i].location.lng),
				populationOptions = {
					strokeColor: "#009600",
					strokeOpacity: 0.75,
					strokeWeight: 1.5,
					fillColor: "#009600",
					fillOpacity: 0.50,
					map: this.map,
					center: latlng,
					radius: venues[i].hereNow.count * 5,
					clickable: true,
					html: FoursquareHeatmap.venueInfoOutput(venues[i])
				},
				venueCircle;
				if (venues[i].hereNow.count > 100) {
					populationOptions.strokeColor = "#f00";
					populationOptions.fillColor = "#f00";
					populationOptions.fillOpacity = 0.25;
				} else if (venues[i].hereNow.count > 50) {
					populationOptions.strokeColor = "#ffef00";
					populationOptions.fillColor = "#ffef00";
					populationOptions.fillOpacity = 0.40;
				} else if (venues[i].hereNow.count > 25) {
					populationOptions.strokeColor = "#009600";
					populationOptions.fillColor = "#009600";
					populationOptions.fillOpacity = 0.55;
				} else if (venues[i].hereNow.count > 10) {
					populationOptions.strokeColor = "#a3a3a3";
					populationOptions.fillColor = "#a3a3a3";
					populationOptions.fillOpacity = 0.65;
				} else { // less than 10, give them a lift
					populationOptions.radius = venues[i].hereNow.count * 10;
				}
				venueCircle = new google.maps.Circle(populationOptions)
				//console.log(venues[i].name, "HN:"+venues[i].hereNow.count, "R:"+venueCircle.radius);
			google.maps.event.addListener(venueCircle, 'click', this.showInfo);
		}//});
		this.map.setCenter(center);
		$loading = $('#loading');
		$loading.fadeOut(1);
	}
	FoursquareHeatmap.showInfo = function (event) {
		FoursquareHeatmap.infoWindow.setContent(this.html);
		FoursquareHeatmap.infoWindow.setPosition(this.center);
		FoursquareHeatmap.infoWindow.open(FoursquareHeatmap.map);
	}
	FoursquareHeatmap.venueInfoOutput = function (venue) {
		var cat = venue.categories[0].pluralName ? venue.categories[0].pluralName : venue.categories[0].name;
		return "Name: " + venue.name + "<br>Category: " + cat + "<BR>Address: " + venue.location.address +  "<BR>People Here: " + venue.hereNow.count + "<br><a target='_blank' href='" + venue.canonicalUrl + "'>Visit FourSquare Page</a>";
	}
	$(function () {
		"use strict";
		var $search = $('#search'),
			$distance = $('#distance'),
			$category = $('input[type="radio"]'),
			$table = $('table'),
			params = decodeURIComponent(window.location.search.replace(/\+/g,"%20")),
			pArray,
			loc,
			index,
			addr,
			value,
			key;
		params = params.substr(1); //remove ?
		$table.tablesorter();
		if (params.indexOf("cID=") !== -1 || params.indexOf("category=") !== -1) { //change default zoom level
			pArray = params.split("&");
			$.each(pArray, function () {
				value = this.split("=");
				key = value[0];
				value = value[1];
				if (key === "category" || key === "cID") {
					FoursquareHeatmap.category = value;
					$('input[value=' + value + ']').attr('checked', true);
				}
			});
		}
		if (params.indexOf("z=") !== -1 || params.indexOf("zoom=") !== -1) { //change default zoom level
			pArray = params.split("&");
			$.each(pArray, function () {
				value = this.split("=");
				key = value[0];
				value = value[1];
				if (key === "zoom" || key === "z") {
					FoursquareHeatmap.zoom = parseInt(value, 10);
				}
			});
		}
		if (params.indexOf("loc=") !== -1 || params.indexOf("q=") !== -1) { //oh boy a location!
			pArray = params.split("&");
			$.each(pArray, function () {
				value = this.split("=");
				key = value[0];
				value = value[1];
				if (key === "loc" || key === "q") {
					addr = value;
				} else if (key === "r" || key === "radius") {
					FoursquareHeatmap.radius = value;
				}
			});
			$search.val(addr);
			FoursquareHeatmap.manualLocation(addr);
		} else if (navigator.geolocation) { //fire up geolocation
			setTimeout(function () {
				navigator.geolocation.getCurrentPosition(FoursquareHeatmap.locationPageSuccess, FoursquareHeatmap.manualLocation, {enableHighAccuracy: true});
			}, 500);
		} else { //or just ask
			FoursquareHeatmap.manualLocation();
		}
		window.setInterval(function () {
			FoursquareHeatmap.foursquareQuery(FoursquareHeatmap.currentPosition.lat, FoursquareHeatmap.currentPosition.lng);
		}, 300000); //refresh map every 5 mins
		$category.click(function () {
			FoursquareHeatmap.category = $('input[type="radio"]:checked').val();
			FoursquareHeatmap.foursquareQuery(FoursquareHeatmap.currentPosition.lat, FoursquareHeatmap.currentPosition.lng);
		});
		$distance.change(function () {
			FoursquareHeatmap.radius = $distance.val();
			FoursquareHeatmap.foursquareQuery(FoursquareHeatmap.currentPosition.lat, FoursquareHeatmap.currentPosition.lng);
		});
		$search.change(function () {
			if ($search.val()) {
				FoursquareHeatmap.manualLocation($search.val());
			}
		});
		$('body').on('click', 'tr', function () {
			var latlng = $(this).find('.latlng').text().split(","),
				lat = parseFloat(latlng[0]).toFixed(3),
				lng = parseFloat(latlng[1]).toFixed(3),
				i = 0,
				venueLat,
				venueLng,
				infoWindow = FoursquareHeatmap.infoWindow,
				venueList = FoursquareHeatmap.venueList;
			for (i = 0; i < venueList.length; i++) {
				venueLat = venueList[i].location.lat.toFixed(3);
				venueLng = venueList[i].location.lng.toFixed(3);
				if ((lat === venueLat) && (lng === venueLng)) { //match! Show window!
					//console.log("match!");
					var contentString = FoursquareHeatmap.venueInfoOutput(venueList[i]);
					infoWindow.setContent(contentString);
					infoWindow.setPosition(new google.maps.LatLng(lat, lng));
					infoWindow.open(FoursquareHeatmap.map);
				}
			}
		});
	});
	