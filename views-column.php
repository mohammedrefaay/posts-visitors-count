<?php
if ( current_user_can( 'reports-view' ) ) {

add_filter('manage_post_posts_columns', function ($columns) {
	$order = isset($_GET['order']) ? $_GET['order'] : false;
	$orderby = $order === 'desc' ? 'asc' : 'desc';
	$link = 'edit.php?order_by_views=true&order=' . $orderby;
	$url = '<a href="' . esc_url($link) . '">' .  esc_html__('عدد المشاهدات') . '</a>';
	return array_merge($columns, ['posts_visits' => $url]);
});
add_action('manage_post_posts_custom_column', function ($column_key, $post_id) {
	if ($column_key == 'posts_visits') {
		$views = get_post_meta($post_id, 'postsVisitorsCount_meta', true);
		if (intval($views) < 1) {
			echo '<span style="color:red;">';
			echo esc_html__('لا توجد مشاهدات');
			echo '</span>';
		} else {
			echo $views;
		}
	}
}, 10, 2);

// Adding Sort feature
function wpa84258_admin_posts_sort_last_name($query)
{
	global $pagenow;
	if (
		is_admin()
		&& 'edit.php' == $pagenow
		&& isset($_GET['order'])
		&& isset($_GET['order_by_views'])
	) {
		$order = $_GET['order'];
		$query->set('meta_key', 'postsVisitorsCount_meta');
		$query->set('orderby', 'meta_value_num');
		$query->set('meta_type' , 'NUMBER');
		$query->set('order', $order === 'asc' ? 'ASC' : 'DESC');
	}
}
add_action('pre_get_posts', 'wpa84258_admin_posts_sort_last_name');

}