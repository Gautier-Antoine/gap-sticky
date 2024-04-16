<?php
	// ! GET ATTRIBUTES
	// var_dump($attributes);
	// $query = gap_sticky_query($attributes);
	if (isset($attributes)) {
		$postType = (isset($attributes['postType'])) ? $attributes['postType'] : 'post';
		$numberPosts = (isset($attributes['numberPosts'])) ? $attributes['numberPosts'] : '3';
		$isSticky = (isset($attributes['isSticky'])) ? $attributes['isSticky'] : true;
	}

	$args = array(
		'post_type' => $postType,
		'posts_per_page' => $numberPosts,
		'post_status'           => array('publish'),
		'order'                 => 'DESC',
		'orderby'               => 'date',
		'ignore_sticky_posts' => true,
	);
	if ($isSticky) {
		$sticky = get_option('sticky_posts');
		if (!empty($sticky)) {
			rsort($sticky);
			$args['post__in'] = $sticky;
		}
	}
	$query = new WP_Query($args);

	// ob_start();
	// gap_sticky_html($query, $attributes);
	// return ob_get_clean();
	// var_dump(get_block_wrapper_attributes());
?>
<div <?php echo get_block_wrapper_attributes(); ?>>
<?php
	while ($query->have_posts()) : $query->the_post();

		$title = get_the_title();
		$link = get_the_permalink();
		$list = explode(', ', get_the_excerpt());
		$html = '';
		foreach ($list as $elem) {
			$html .= '<!-- wp:list-item --><li>'. $elem .'</li><!-- /wp:list-item -->';
		}

		echo do_blocks(
			'<!-- wp:cover {
				"useFeaturedImage":true,
				"dimRatio":0,
				"focalPoint":{"x":0,"y":0},
				"isDark":false,
				"className":"post-call",
				"layout":{"type":"constrained"}
			} -->
			<div class="wp-block-cover has-text-color post-call">
				<span
					aria-hidden="true"
					class="wp-block-cover__background has-background-dim-0 has-background-dim"
				></span>
				<div class="wp-block-cover__inner-container">
					<!-- wp:group {
						"backgroundColor":"white",
						"layout":{"type":"flex", "orientation":"vertical", "verticalAlignment":"center"}
					} -->
					<div class="wp-block-group has-white-background-color has-background">
						<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
						<div class="wp-block-group">
							<!-- wp:post-title /-->
							<!-- wp:post-date {"format":"M Y"} /-->
						</div>
						<!-- /wp:group -->

						<!-- wp:gap/stacks /-->

						<!-- wp:list -->
						<ul>' . $html . '</ul>
						<!-- /wp:list -->

						<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
						<div class="wp-block-buttons">
							<!-- wp:button {"textAlign":"center","textColor":"gat-white","style":{"elements":{"link":{"color":{"text":"var:preset|color|gat-white"}}}}}
							} -->
							<div class="wp-block-button">
								<a
									class="wp-block-button__link has-text-align-center has-gat-white-color has-text-color has-link-color wp-element-button"
									href="' . $link . '"
								>Go to ' . $title . '</a>
							</div>
							<!-- /wp:button -->
						</div>
						<!-- /wp:buttons -->
					</div>
					<!-- /wp:group -->
				</div>
			</div>
			<!-- /wp:cover -->'
		);
	endwhile;
?>
</div>