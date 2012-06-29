$(document).ready(function(){
	$('pre.grab-html').each(function(i, el) {
		var $html = $(this);
		var $php = $html.parent().prev().find('pre:first');
		var html = $($html.data('source')).html().replace(/	/g, "   ");

		console.log(html);
		$html.text(html);

		if ($php.height() >= $html.height()) { return; }

		$('<button class="show-hide btn btn-mini">Expand</button>').click(function(e) {
			var $btn = $(this);
			if ($html.data('expanded')) {
				$html.animate(
					{ height:$php.height() }, 
					300, 
					function() { $btn.text('Expand'); $html.data('expanded', false); }
				);
			} else {
				$html.animate(
					{ height:'100%' }, 
					300, 
					function() { $btn.text('Shrink'); $html.data('expanded', true); }
				);
			}
		}).appendTo($html.prev());

		$html.height($php.height());
	});

	prettyPrint();

	// fix sub nav on scroll
	var $win = $(window),
		$nav = $('.subnav'),
		navTop = $('.subnav').length && $('.subnav').offset().top - 40,
		isFixed = false;

	processScroll();

	$win.on('scroll', processScroll);

	function processScroll()
	{
		var i, scrollTop = $win.scrollTop()
		if (scrollTop >= navTop && !isFixed) {
			isFixed = true;
			$nav.addClass('subnav-fixed');
		} else if (scrollTop <= navTop && isFixed) {
			isFixed = false;
			$nav.removeClass('subnav-fixed');
	  	}
	}
}); 