jQuery(document).ready(function(){
	jQuery("#label_add").click(function(){
		
			var wpmr_label_key = jQuery('#wpmr_label_key').val();
			var wpmr_input_type = jQuery('#wpmr_input_type').val();
			var wpmr_post_id = jQuery('#wpmr_post_id').val();

			if( wpmr_label_key != '' && wpmr_input_type != ''){
				var data = {
					action: 'wpmetarepeater_ajax',
					wpmr_label_key : wpmr_label_key,
					wpmr_input_type : wpmr_input_type,
					wpmr_post_id : wpmr_post_id,
					mode: 'wpmr_meta_add'
				};

				jQuery.post(ajax_var.ajax_url, data, function(response) {
					jQuery('#wpmr_newfields').html('<div class="wpmr_newfields_wrapper">'+response+'</div>');
				});
			}else{
				alert('All fields are mandatory');
			}
	});
});
function wpmr_remove_field( remove ){
	var row_remove = jQuery(remove).data('key');
	var wpmr_post_id = jQuery(remove).data('wpmr_post_id');
	var confrim = confirm("Are you sure?");
	if(confrim == true){
		jQuery(remove).parent().fadeOut('slow', function(){ jQuery(remove).remove(); });
		var data = {
			action: 'wpmetarepeater_ajax',
			delete_key : row_remove,
			wpmr_post_id : wpmr_post_id,
			mode: 'wpmr_meta_clear'
		};
		jQuery.post(ajax_var.ajax_url, data, function(response) {
			jQuery('#wpmr_newfields').html('<div class="wpmr_newfields_wrapper">'+response+'</div>');
		});
	}
}