	
	var $loading = $('#loading'),
			$table,
			mapdiv = document.getElementById('map');
	fshm = new Object();
	fshm.map = {};
	fshm.venueList = [];
	fshm.infoWindow = new google.maps.InfoWindow();
	fshm.radius = 4828; //3mi in m
	fshm.currentPosition = {};
	fshm.zoom = 15;
	fshm.category = '';
	fshm.locationPageSuccess = function (position) {
		"use strict";
		//ENTER CLIENT'S LAT AND LONG BELOW
		var lat = 40.752816;//position.coords.latitude;
		var	lng = -73.984059;//position.coords.longitude;
		fshm.currentPosition.lat = lat;
		fshm.currentPosition.lng = lng;
		fshm.foursquareQuery(lat, lng, fshm.radius);
	}
	fshm.foursquareQuery = function (lat, lng) {
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
				fshm.venueList = data.response.venues.sort(function(a,b){
					if (a.hereNow.count < b.hereNow.count) {
						return 1;
					} else if (a.hereNow.count > b.hereNow.count) {
						return -1;
					} else {
						return 0;
					}
				});
				fshm.initGMap(fshm.venueList, lat, lng);
				$table.trigger('update');
			},
			error: function (xhr) {
				console.log(xhr.responseText);
			}
		});
		//console.log(fshm);
	}
	fshm.initGMap = function (venues, lat, lng) {
		delete map;
		var center = new google.maps.LatLng(lat, lng),
			myOptions = {
				zoom: this.zoom,
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
		this.map = new google.maps.Map(mapdiv, myOptions);
		$(mapdiv).append("<div id='loading'>Live Section Loading...</div>");
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
					html: fshm.venueInfoOutput(venues[i])
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
				} else { 
					populationOptions.radius = venues[i].hereNow.count * 10;
				}
				venueCircle = new google.maps.Circle(populationOptions)
			google.maps.event.addListener(venueCircle, 'click', this.showInfo);
		}
		this.map.setCenter(center);
		$loading = $('#loading');
		$loading.fadeOut(1);
	}
	fshm.showInfo = function (event) {
		fshm.infoWindow.setContent(this.html);
		fshm.infoWindow.setPosition(this.center);
		fshm.infoWindow.open(fshm.map);
	}
	fshm.venueInfoOutput = function (venue) {
		var cat = venue.categories[0].pluralName ? venue.categories[0].pluralName : venue.categories[0].name;
		//console.log(venue);
		$.ajax({
		 	type: 'POST',
		  	url: "locsaver.php",//url of receiver file on server
		 	data: {
		 		name: venue.name,
		 		location: venue.location.address,
		 		herenow: venue.hereNow.count
		 		//lnk: data.response.venues.canonicalUrl
		 	} //your data
		 	//success: console.log("test written..."), //callback when ajax request finishes
		 	//error: console.log("could not write to test")
		});
		//console.log(/*"Name: " venue.name + " Address: " + venue.location.address + " herenow: " +*/ venue.hereNow.count);
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
		params = params.substr(1); 
		if (params.indexOf("loc=") !== -1 || params.indexOf("q=") !== -1) { 
			pArray = params.split("&");
			$.each(pArray, function () {
				value = this.split("=");
				key = value[0];
				value = value[1];
				if (key === "loc" || key === "q") {
					addr = value;
				} else if (key === "r" || key === "radius") {
					fshm.radius = value;
				}
			});
			$search.val(addr);
			fshm.manualLocation(addr);
		} else if (navigator.geolocation) { 
			setTimeout(function () {
				navigator.geolocation.getCurrentPosition(fshm.locationPageSuccess, fshm.manualLocation, {enableHighAccuracy: true});
			}, 500);
		} else { 
			fshm.manualLocation();
		}
		window.setInterval(function () {
			fshm.foursquareQuery(fshm.currentPosition.lat, fshm.currentPosition.lng);
		}, 300000); 
		$category.click(function () {
			fshm.category = $('input[type="radio"]:checked').val();
			fshm.foursquareQuery(fshm.currentPosition.lat, fshm.currentPosition.lng);
		});
		$distance.change(function () {
			fshm.radius = $distance.val();
			fshm.foursquareQuery(fshm.currentPosition.lat, fshm.currentPosition.lng);
		});
		$search.change(function () {
			if ($search.val()) {
				fshm.manualLocation($search.val());
			}
		});
		$('body').on('click', 'tr', function () {
			var latlng = $(this).find('.latlng').text().split(","),
				lat = parseFloat(latlng[0]).toFixed(3),
				lng = parseFloat(latlng[1]).toFixed(3),
				i = 0,
				venueLat,
				venueLng,
				infoWindow = fshm.infoWindow,
				venueList = fshm.venueList;
			for (i = 0; i < venueList.length; i++) {
				venueLat = venueList[i].location.lat.toFixed(3);
				venueLng = venueList[i].location.lng.toFixed(3);
				if ((lat === venueLat) && (lng === venueLng)) {
					var contentString = fshm.venueInfoOutput(venueList[i]);
					infoWindow.setContent(contentString);
					infoWindow.setPosition(new google.maps.LatLng(lat, lng));
					infoWindow.open(fshm.map);
				}
			}
		});
	});
	

	