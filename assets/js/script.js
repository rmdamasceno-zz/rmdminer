
function rmdAPIjquery(data){
	jQuery.ajax( {
		url: '/wp-json/rmdminer/v2/'+data['url']+'/',    
		method: 'POST',
		beforeSend: function ( xhr ) {
			xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
		},
		data:{
			data
		}
	} ).done( function ( response ) {
		console.log( response );
	} );
}