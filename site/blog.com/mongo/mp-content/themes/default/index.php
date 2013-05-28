<?php
get_template_part('header'); global $link_format, $featured_format;
/* FEATURED FORMAT WILL SHOWCASE CORE AJAX LOADING FUNCTIONALITY */
$features = mp_get_content($featured_format);
$got_content = false;
if (is_array($features[0])) {
	$featured_mongo_id = $mp->get_mongoid_as_string($features[0]['_id']);
	$published_ago_distance = mingo_meantime($features[0]['updated']);
	$published_date = mingo_meantime(false,false,'date');
	$published_ago = sprintf(__('( Last updated %s ago - %s )'),$published_ago_distance ,  $published_date);
	$got_content = true;
}
?>

<div id="site-wrapper" class="radius5">
    <nav id="primary-navigation"><?php mp_content($link_format); ?></nav>
    <div id="primary-content">
		<article>
			<?php if($got_content){ ?>
			<header class="title">
				<h3 class="article-title header"><?php echo $features[0]['title']; ?></h3>
				<time datetime="<?php echo $features[0]['updated']; ?>"><?php echo $published_ago; ?></time>
			</header>
			<div class="content fetch-content" data-mongo-id="<?php echo $featured_mongo_id; ?>" data-nonce="<?php echo mp_create_nonce('objects-form'); ?>" data-shortcodes="true">
				<?php // THIS WILL GET REPLACED VIA AJAX CALL TO COLLECT CONTENT ?>
			</div>
			<?php } ?>
		</article>
	</div>
    <div id="secondary-content"><?php get_template_part('sidebar'); ?></div>
</div>

<?php get_template_part('footer'); ?>