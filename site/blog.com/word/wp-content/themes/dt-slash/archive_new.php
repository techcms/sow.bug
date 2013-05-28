<?php get_header() ?>
<div id="bg">
	
	<?php get_template_part('mobile-menu') ?>
	<div id="top_bg"></div>
	<div id="holder">
		<?php get_template_part('aside'); ?>
		<?php $template_flag = of_get_option('blog_layout_type_select', 'standard'); ?>
		<div id="content">    
			<?php get_template_part('dt_archive', $template_flag); ?>
		</div>
	</div>
</div>
<?php get_footer() ?>