jQuery(document).ready(function(){
	jQuery(".pfs-post-link").click(function(){
		var $box = jQuery(this).siblings('div');
		if ('relative' == jQuery(this).parents('article').css('position')) {
			var top = 0;
			var diff = jQuery(this).parents('article').offset();
			var left = ( jQuery(window).width() - $box.width() )/2 - diff.left;
		} else {
			var top = 90;
			var left = ( jQuery(window).width() - $box.width() )/2;
		}
		if (top<0) top = 50;
		$box.css({top:top+"px",left:left+"px"}).show();
	});
	jQuery(".closex").click(function(){
		jQuery(this).parent().hide();
	});
    jQuery("form.pfs").submit(function() {

       jQuery("form.pfs").ajaxSubmit({
            type: "POST",
            url: jQuery(this).attr('action'),
            dataType:'json',
            beforeSend: function(){
                jQuery('.pfs-post-form #post').val('posting...');
            },
            complete: function(request,textStatus,error) {

					data = {};
					try {
						 data = jQuery.parseJSON(request.responseText); //bug here!!!
					} catch(err){
						// alert( "#error:"+err);
						resp = request.responseText;
						res = resp.match(/\[.*\].*/g);
						for(var i in res){
							arr = res[i].split("=>");
							key = arr[0].substr(arr[0].indexOf('[')+1, arr[0].lastIndexOf(']')-1);
							data[key] = arr[1];
						}
					};

					if (data.error.length>1) {
						jQuery('#pfs-alert').addClass('error').html('<p>'+data.error+'</p>').show();
						jQuery('.pfs-post-form #post').val('Post');
					} else {
						jQuery('#pfs-alert').addClass('success').html('<p>'+data.success+'</p>').show();
						jQuery('form.pfs').reset();
						setTimeout( "location.reload()", 3*1000 );
					}
            }
        });
        return false;
    });
});