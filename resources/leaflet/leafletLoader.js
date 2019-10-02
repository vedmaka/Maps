window.mapsLeafletList = [];

(function( $, mv, L ) {
	$( document ).ready( function() {
		$( '.maps-leaflet' ).each( function() {
			let map = L.map(
				$(this).attr('id'),
				{}
			);

			new L.tileLayer.provider('OpenStreetMap',{}).addTo(map);

			map.fitWorld();

			L.marker([1,1]).addTo(map);
		} );
	} );
})(jQuery, window.mediaWiki, L);
