function kkl_backend_addFilter(url, filterParameter, newValue) {

	var newurl = url;
	var regex = new RegExp('[\\?&]' + filterParameter + '=([^&#]*)');
	var results = regex.exec(url);

	if(results == null) {
		// append
		newurl += "&" + filterParameter + "=" + newValue;
	}else{
		// replace
		var pattern = '(' + filterParameter + '=)[^\&]+';
		var re = new RegExp(pattern, "g");
		newurl = url.replace(re, '$1' + newValue);
	}

	return newurl;
}

jQuery(document).ready(function() {
	jQuery('#toplevel_page_kkl_ligatool').addClass("current");
	jQuery('#toplevel_page_kkl_ligatool a').addClass("current");
	jQuery('.pickfixture').datetimepicker({
 		dateFormat: "yy-mm-dd",
    	timeFormat:  "HH:mm"
 	});
});


jQuery(document).ready(function() {
 
});

