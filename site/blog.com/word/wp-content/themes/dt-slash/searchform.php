<form method="get" class="c_search" action="<?php echo home_url('/'); ?>">
	<div class="p"><?php _e( 'Search:', LANGUAGE_ZONE ) ?></div>
    <div class="i-h">
        <input name="s" type="text" placeholder="" class="int" />
        <a href="#" onClick="$('.c_search').submit(); return false;"></a>
    </div>
</form>