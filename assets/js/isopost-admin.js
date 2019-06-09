/*
  isopost : ADMIN SCRIPT
*/
function isopost_get_taxonomies(thisele){
	var selectedVal = thisele.find('option:selected').val();
	var isopoststring = jQuery('#isopoststring').val();

	if(selectedVal !=""){
		jQuery.ajax({
			url  : ajaxurl,
			type : "POST",
			data : {
				action   : 'isopost_call_taxonomies',
				security : isopoststring,
				posttype : selectedVal
			},
			beforeSend: function(){
				jQuery('#isopost_selterms').html('');
				jQuery('.isopost_loader').fadeIn();
				jQuery('#isopost_seltax').css("pointer-events", "none");
				thisele.css("pointer-events", "none");
			},
			success : function( response ) {
				if(response){
					thisele.css("pointer-events", "auto");
					jQuery('#isopost_seltax').html(response).css("pointer-events", "auto");
					jQuery('.isopost_loader').hide();
				}
			}
		});
	}else{
		var defaultHtml = '<option value="">Select</option>';
		jQuery('#isopost_seltax').html(defaultHtml);
		jQuery('#isopost_selterms').html('');
	}
}

jQuery('#isopost_selpost').change(function(){
	isopost_get_taxonomies(jQuery(this));
});

function isopost_get_taxo_terms(thisele){
	var selectedVal = thisele.find('option:selected').val();
	var isopoststring = jQuery('#isopoststring').val();

	if(selectedVal !=""){
		jQuery.ajax({
			url  : ajaxurl,
			type : "POST",
			data : {
				action   : 'isopost_call_taxonomy_terms',
				security : isopoststring,
				taxonomy : selectedVal
			},
			beforeSend: function(){
				jQuery('.isopost_loader').fadeIn();
				jQuery('#isopost_selterms').css("pointer-events", "none");
				thisele.css("pointer-events", "none");
			},
			success : function( response ) {
				if(response){
					thisele.css("pointer-events", "auto");
					jQuery('#isopost_selterms').html(response).css("pointer-events", "auto");
					jQuery('.isopost_loader').hide();
				}
			}
		});
	}else{
		jQuery('#isopost_selterms').html('');	
	}
}

jQuery('#isopost_seltax').change(function(){
	isopost_get_taxo_terms(jQuery(this));
});