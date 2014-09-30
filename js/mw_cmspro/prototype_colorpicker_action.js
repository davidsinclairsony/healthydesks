	
document.observe('dom:loaded', function () {
	
	if($('mw_cmspro_info_photo_background_color')) {
		var color_value = $('mw_cmspro_info_photo_background_color').value;
		var cp2 = new colorPicker('mw_cmspro_info_photo_background_color',{
			color:'#'+color_value,
			previewElement:'mw-color-preview'
		});
	}
	
})