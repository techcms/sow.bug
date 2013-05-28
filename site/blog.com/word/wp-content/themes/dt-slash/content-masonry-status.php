<div class="article_box">
	<div class="article_t"></div>
	<div class="article g">
		<div class="artic-g">
			<div class="art-grey-t"></div>
			<div class="art-grey">       
				<?php the_content() ?>
			</div>
			<div class="art-grey-b"></div>
        </div>
        <span class="inf">
			<a class="ico_link date" href="<?php the_permalink() ?>">
				<?php printf( __('%s at %s', 'dt'), get_the_date(), get_the_time() ) ?>
			</a>
			<?php if( comments_open() ): // comments ?>
				<a href="<?php comments_link() ?>" class="ico_link comments">
					<?php echo get_comments_number() ?>
				</a>
			<?php endif ?>
		</span>
	</div><!-- .article g end -->
	<div class="article_footer_b"></div>
</div>