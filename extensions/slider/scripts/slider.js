/*Created by Jason Wong by following a tutorial on:
http://www.webchiefdesign.co.uk/blog/simple-jquery-slideshow/index.php*/
$(document).ready(function() {
	var speed = 5000; //Set the speed of the transition here!
	var slidePosition = 0;
	var slideWidth = 500;
	var slides = $('.slide');
	var numberOfSlides = slides.length; //Automatically adjusts for number of slides
	var slideShowInterval;
	var paused = false;
	
	slideShowInterval = setInterval(changePosition, speed);
	slides.wrapAll('<div id="slidesHolder"></div>')
	slides.css({ 'float' : 'left' });
	
	$('#slidesHolder').css('width', slideWidth * numberOfSlides);
	$('#slideshow')
		.append('<span class="nav" id="leftNav">Move Left</span>')
		.append('<span class="nav" id="rightNav">Move Right</span>');
	
	manageNav(slidePosition);
	
	//Click actions
	$('.nav').bind('click', function() {
		//Allows wrapping around to other side
		if($(this).attr('id')=='rightNav'){
			if(slidePosition==numberOfSlides-1){
				slidePosition = 0;
			}
			else{
				slidePosition=slidePosition+1;
			}
		}
		//Allows wrapping around to toher side
		if($(this).attr('id')=='leftNav'){
			if(slidePosition==0){
				slidePosition = numberOfSlides-1;
			}
			else{
				slidePosition=slidePosition-1;
			}
		}
		if(paused==false){
			manageNav(slidePosition);
			clearInterval(slideShowInterval);
			slideShowInterval = setInterval(changePosition, speed);
			moveSlide();
		}
	});
	
	//Set to automatically show buttons, but can be hidden if needed
	function manageNav(position) {
		$('#leftNav').show();
		$('#rightNav').show();
	}
	
	function changePosition() {
		if(slidePosition == numberOfSlides - 1) {
			slidePosition = 0;
			manageNav(slidePosition);
		} else {
			slidePosition++;
			manageNav(slidePosition);
		}
		moveSlide();
	}
	
	function moveSlide() {
			$('#slidesHolder').animate({'marginLeft' : slideWidth*(-slidePosition)});
	}
});