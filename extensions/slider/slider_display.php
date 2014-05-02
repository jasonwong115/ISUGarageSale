<?php global $app; ?>
<div class = "spacing-bar">
    <!-- Display Garage Sale Logo -->
	<div class = "logo">
		<a href="/">
		    <img src ="<?php echo $app->inner_path('views/images/Logov2.png'); ?>" 
		        height="200" width ="200" alt="Garage Sale Logo"/>
		</a>
	</div><!--Insert your logo in this section-->
	
	<!-- Social media linkage -->
	<div class = "facebook-overlay">
	
		<img  src="<?php echo $app->inner_path('views/images/faceIcon.png'); ?>" 
		    height="50" width ="200"  border="0" usemap="#Map2" alt="Garage Sale Social">
		    
		<map name="Map2" id="Map2">
		<area shape="rect" coords="129,16,146,33" href="https://www.facebook.com/ISUGarageSale" alt="facebook" />
		<area shape="rect" coords="156,18,171,30" href="https://twitter.com/ISUGarageSale" alt="twitter" />
		</map>
	</div><!--Link your website to Facebook and Twitter to this section-->
	
	
</div><!--spacing-bar container-->

<div class="slider-background">
<!--Slider inspired from ISU Bookstore-->
<div class="isu-slider isu-scroller">
    <div class="slide-container">
        <ul>
<!--Slide one-->
<li><img class="slide" src="<?php echo $app->inner_path('extensions/slider/spacer.gif'); ?>" style="height:0px; display: block;" id="spacer">
	<a href="<?php echo $app->form_path('browse/search?item-search=ticket'); ?>">
	<img class="slide" src="<?php echo $app->inner_path('extensions/slider/slide1.png'); ?>">
	<img class="slide" src="<?php echo $app->inner_path('extensions/slider/spacer.gif'); ?>" style="height:0px; display:block;" id="spacer">
	<div class="caption">Get ready for football season! Buy your tickets now!</div></li>

<!--Slide two-->
<li><img class="slide" src="<?php echo $app->inner_path('extensions/slider/spacer.gif'); ?>" style="height:0px; display: block;" id="spacer">
	<a href="<?php echo $app->form_path('browse/category/books'); ?>">
	<img class="slide" src="<?php echo $app->inner_path('extensions/slider/slide2.png'); ?>"></a>
	<img class="slide" src="http://www.ubs.iastate.edu/img/img_rotation/spacer.gif" style="height:0px; display:block;" id="spacer">
	<div class="caption">Textbooks, novels, and more.</div></li>

<!--Slide three-->	
<li><img class="slide" src="<?php echo $app->inner_path('extensions/slider/spacer.gif'); ?>" style="height:0px; display: block;" id="spacer">
	<a href="<?php echo $app->form_path('browse/category/electronics'); ?>">
	<img class="slide" src="<?php echo $app->inner_path('extensions/slider/slide3.png'); ?>"></a>
	<img class="slide" src="<?php echo $app->inner_path('extensions/slider/spacer.gif'); ?>" style="height:0px; display:block;" id="spacer">
	<div class="caption">Get the technology you need to succeed!</div></li>

<!--Slide four-->	
<li><img class="slide" src="<?php echo $app->inner_path('extensions/slider/spacer.gif'); ?>" style="height:0px; display: block;" id="spacer">
	<a href="<?php echo $app->form_path('browse/search?item-search=sweatshirt'); ?>">
	<img class="slide" src="<?php echo $app->inner_path('extensions/slider/slide4.png'); ?>"></a>
	<img class="slide" src="<?php echo $app->inner_path('extensions/slider/spacer.gif'); ?>" style="height:0px; display:block;" id="spacer">
	<div class="caption">Support CyclONE Nation! Get some ISU clothing!</div></li>

<!--Slide five-->
<li><img class="slide" src="<?php echo $app->inner_path('extensions/slider/spacer.gif'); ?>" style="height:0px; display: block;" id="spacer">
	<a href="<?php echo $app->form_path('browse/category/general'); ?>">
	<img class="slide" src="<?php echo $app->inner_path('extensions/slider/slide5.png'); ?>"></a>
	<img class="slide" src="<?php echo $app->inner_path('extensions/slider/spacer.gif'); ?>" style="height:0px; display:block;" id="spacer">
	<div class="caption">Get the supplies you need to pass your classes.</div></li>

<!--Slide six-->	
<li><img class="slide" src="<?php echo $app->inner_path('extensions/slider/spacer.gif'); ?>" style="height:0px; display: block;" id="spacer">
	<a href="<?php echo $app->form_path('browse/category/general'); ?>">
	<img class="slide" src="<?php echo $app->inner_path('extensions/slider/slide6.png'); ?>"></a>
	<img class="slide" src="<?php echo $app->inner_path('extensions/slider/spacer.gif'); ?>" style="height:0px; display:block;" id="spacer">
	<div class="caption">Gotta look good! Search for some new clothes.</div></li>

        </ul>
    </div>
    <div class="nav-container">
	<ul class="directional">
			<!-- These spans show the directional arrow buttons on side of the slideshow -->
				<li class="prev"><span><span></span></span></li>
				<li class="next"><span><span></span></span></li>
			</ul>
        <ul class="controls">
             <li class="slideshow"><span></span></li><!-- This span shows the play/pause button -->
            <!-- These spans show the empty circles that represent each slide -->
            <li><span></span></li>
            <li><span></span></li>
            <li><span></span></li>
            <li><span></span></li>
            <li><span></span></li>
            <li><span></span></li>
        </ul>
    </div>
</div>
</div>
