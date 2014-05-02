$(document).ready(function()
{
	var scroller = $('.isu-slider').scroller();
	$('.customize select').change(function()
	{
		var n = $(this).attr('name');
		var v = $(this).val();
		if (n == 'caption' || n == 'directional' || n == 'controls')
		{
			scroller.find('.'+ n)['fade'+ (v == 'Yes' ? 'In' : 'Out')]();
		}
		else if (n == 'transition')
		{
			scroller.scroller({
				transition: v.toLowerCase()
			});
		}
	});
});
