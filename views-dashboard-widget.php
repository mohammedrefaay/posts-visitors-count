<?php
if ( current_user_can( 'reports-view' ) ) {
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');

function my_custom_dashboard_widgets()
{
	wp_add_dashboard_widget('postsVisitorsCount_dashboard_top_posts', 'عدد المشاهدات', 'postsVisitorsCount_dashboard_top_posts_callback', null, null, 'normal', 'high');
}

function postsVisitorsCount_dashboard_top_posts_callback(){

	require_once __DIR__ . '/views-style.php';
	$al_query_args = [
		'post_type' => 'post',
		'post_status' => 'publish',
		'posts_per_page' => 20,
		'paged' => 1,
		'meta_key' => 'postsVisitorsCount_meta',
		'orderby' => 'meta_value_num',
		'meta_type' => 'NUMBER',
		'order' => 'DESC',
		'date_query' =>  [
			[ 'after' => '1 week ago' ]
		]
	];
	$al_query = new WP_Query($al_query_args);

?>
	<div class="postsVisitorsCount-page-container widget-container">
		<table class="table">
			<thead>
				<tr>
					<th>العنوان</th>
					<th>المشاهدات</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($al_query->have_posts()) { while ($al_query->have_posts()) : $al_query->the_post(); ?>
						<tr>
							<td><a href="<?php echo get_permalink(); ?>" target="_blank"><?php echo get_the_title(); ?></a></td>
							<td><?php
									$post_views = get_post_meta(get_the_ID(), 'postsVisitorsCount_meta', true);
									echo $post_views !== '' ? $post_views : 'لا توجد مشاهدات';
									?></td>
						</tr>
					<?php endwhile;
					wp_reset_postdata();
				} else { ?>
					<tr>
						<td>لا توجد مقالات</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
<?php

}
}
