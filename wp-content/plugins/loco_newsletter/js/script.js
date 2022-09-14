jQuery(document).ready(function(){
	jQuery('.wp-list-table tbody').sortable({
		stop:function(event,ui){
			var url = jQuery('#ajax-url').val() //jQuery('[name=_wp_http_referer]').val()+'&action=sorting'
			//var values = 
			var dt = new Object()
				dt.action = jQuery('#sorting_action').val();
				dt._ajax_nonce = jQuery('#_wpnonce').val()
				dt.current_page = jQuery('#current_page').val()
				dt.ids = jQuery("input[name='bulk-delete[]']").map(function(){return jQuery(this).val();}).get();

			jQuery.post(url,dt,function(response){
				if(response=='success') location.reload();
			})
			//console.log(values)
		},
	});


	if(jQuery('.text-date').length>0){
		jQuery('.text-date').datepicker({minDate:0,dateFormat: "dd MM yy"});	
	}
});