coldharbour = {
	
	// Release under CC, Mark Mitchell @ withoutnations.com
	// SEE: www.withoutnations.com/wp-content/themes/highball/javascripts/application.js
	
	chTwitter: function() {
		// Wordpress public images directory
		var wpImages = "/wp-content/themes/coldharbour/images/";

		// Twitter: define widget
		var twTimeline = [
		'<h3 class="widget-title">Twitter</h3>',
		'<div class="widget-box">',
		'<div id="twitter-stream">',
		'<p><img src="' + wpImages + 'loading.gif" alt="Loading" /> Loading the Twitters!</p>',
		'</div></div>'
		].join("");

		// Twitter: load widget
		jQuery("#sidebar").find("#twitter").css("display","block").append(twTimeline);

		// Twitter: load script
		if (jQuery("#secondary").find(".widget-box")) {

		    getTwitters('twitter-stream', {
		        id: 'dogwonder',
		        count: 5,
		        enableLinks: true,
		        ignoreReplies: true,
		        clearContents: true,
		        template: '%text% <a href="http://twitter.com/%user_screen_name%/statuses/%id_str%/" class="timestamp">%time%</a>'
		    });
		}
	},
	chEmpty: function() {
		jQuery("#sidebar").find("#twitter").empty().css("display","none");
	}
	
}

if (matchMedia) {
    var mqDesktop = window.matchMedia("(min-width: 768px)");
    mqDesktop.addListener(chEvent);
    chEvent(mqDesktop);
}

function chEvent(mqDesktop) {
    if (mqDesktop.matches) {
        coldharbour.chTwitter();
    } else {
        coldharbour.chEmpty();
    }
}