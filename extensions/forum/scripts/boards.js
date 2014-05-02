/* For use with the forum boards admin */
// init jquery
$(function(){

	$('div.hidden_form').hide();

	// add click even to reveal form links
	$('a.reveal_form').click(function(e)
	{
		form_to_reveal = $(this).attr('href');
		$(form_to_reveal).toggle();
		e.preventDefault();
	});
});
