$(document).ready(function() {
	$('.link-buy').click(function(e) {
		var url = $(this).attr('href');

		gtag('event', 'conversion', {
			'send_to': 'AW-840767866/O6F4CP7wn6YBEPqy9JAD',
			'value': 10,
			'currency': 'USD'
		});

		fbq('track', 'Lead', {
			content_name: $(this).data('sku'),
			content_category: $(this).data('shop'),
			value: 1,
			currency: 'USD'
		});
		/* gtagConversion($(this).attr('href')); */
	});
});