var map;
function initMap() {
  var myLatLng = {lat: parseFloat(Dgm.lat), lng: parseFloat(Dgm.lang)};

  var map = new google.maps.Map(document.getElementById('map'), {
    zoom:  parseFloat(Dgm.zoom),
    center: myLatLng,
	  zoomControlOptions: {
        position: google.maps.ControlPosition.RIGHT_CENTER
    },
	//scrollwheel: false,
  });

  var marker = new google.maps.Marker({
    position: myLatLng,
    map: map,
  });
}
/*
jQuery(document).ready(function(e) {
    jQuery(".container-27 ").click(function(){
		 jQuery('#map').css("pointer-events", "auto");
		 console.log('kilk bro');
		//enableScrollingWithMouseWheel();
	})
	
	jQuery( "#map" ).mouseleave(function() {
     	 jQuery('#map').css("pointer-events", "none"); 
    })
});*/