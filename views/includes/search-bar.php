<form class="form-wrapper cf"  name = "item-search" action="<?php echo $app->form_path('browse/search'); ?>" method = "GET">

	<input type="text" name="item-search" placeholder="Search the garage..." required>
	<button type="submit">Search</button>
	
</form>