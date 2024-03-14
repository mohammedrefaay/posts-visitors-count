<?php

require_once __DIR__ . '/views-style.php';

$get_query_date = isset($_GET['query_date']) ? $_GET['query_date'] : 'today';
$error_status = false;

$sort_links = array(
	'today' => 'admin.php?page=posts-visitors-count&query_date=today',
	'this_week' => 'admin.php?page=posts-visitors-count&query_date=this-week',
	'this_month' => 'admin.php?page=posts-visitors-count&query_date=this-month',
	'this_year' => 'admin.php?page=posts-visitors-count&query_date=this-year',
	'last_three_months' => 'admin.php?page=posts-visitors-count&query_date=last-three-months',
	'all_time' => 'admin.php?page=posts-visitors-count&query_date=all-time'
);
$al_query_args = array(
	'post_type' => 'post',
	'post_status' => 'publish',
	'posts_per_page' => 30,
	'paged' => isset($_GET['paged']) ? $_GET['paged'] : 1,
	'meta_key' => 'postsVisitorsCount_meta',
	'orderby' => 'meta_value_num',
	'meta_type' => 'NUMBER',
	'order' => 'DESC',
);

// Add todays filter
if ($get_query_date === 'today') {
	$today = getdate();
	$al_query_args['date_query'] = [
		[
			'year'  => $today['year'],
			'month' => $today['mon'],
			'day'   => $today['mday'],
		]
	];
}

// Add this week's filter
if ($get_query_date === 'this-week') {
	$al_query_args['date_query'] = [
		[
			'after' => '1 week ago'
		]
	];
}

// Add this month's filter
if ($get_query_date === 'this-month') {
	$al_query_args['date_query'] = [
		[
			'after' => '1 month ago'
		]
	];
}

// Add last 3 weeks filter
if ($get_query_date === 'last-three-months') {
	$al_query_args['date_query'] = [
		[
			'after'   => '-3 month',
		]
	];
}

// Add last year's filter
if ($get_query_date === 'this-year') {
	$al_query_args['date_query'] = [
		[
			'after' => '1 year ago'
		]
	];
}

// Add date comparison period
if ($get_query_date === 'period' && isset($_GET['from']) && isset($_GET['to'])) {
	$from = strtotime($_GET['from']);
	$to = strtotime($_GET['to']);
	if ($from && $to) {
		if ($from < $to) {
			$al_query_args['date_query'] = [
				[
					'after'   => $_GET['from'],
					'before' => $_GET['to']
				]
			];
		} elseif ($from === $to) {
			$start_date = date('y-m-d', strtotime('-1 day', $from));
			$end_date = date('y-m-d', strtotime('+1 day', $from));
			$al_query_args['date_query'] = [
				[
					'after'   => $start_date,
					'before' => $end_date
				]
			];
		} else {
			$error_status = 'التاريخ غير صحيح .. يرجى التحقق من صحة التاريخ الذي تم إدخاله';
		}
	} else {
		$error_status = 'التاريخ غير صحيح .. يرجى التحقق من صحة التاريخ الذي تم إدخاله';
	}
}
$al_query = new WP_Query($al_query_args);

?>
<div class="postsVisitorsCount-page-container">
	<h2 class="title">اكثر المقالات مشاهدة</h2>
	<ul class="summary-tabs pagination">
		<li class="7-days page-item <?php echo $get_query_date === 'today' ? 'active' : ''; ?>"><a href="<?php echo $sort_links['today']; ?>">اخر 24 ساعه</a></li>
		<li class="7-days page-item <?php echo $get_query_date === 'this-week' ? 'active' : ''; ?>"><a href="<?php echo $sort_links['this_week']; ?>">اخر 7 ايام</a></li>
		<li class="30-days page-item <?php echo $get_query_date === 'this-month' ? 'active' : ''; ?>"><a href="<?php echo $sort_links['this_month']; ?>">اخر 30 يوم</a></li>
		<li class="quarter page-item <?php echo $get_query_date === 'last-three-months' ? 'active' : ''; ?>"><a href="<?php echo $sort_links['last_three_months']; ?>">اخر 3 شهور</a></li>
		<li class="year page-item <?php echo $get_query_date === 'this-year' ? 'active' : ''; ?>"><a href="<?php echo $sort_links['this_year']; ?>">العام الاخير</a></li>
		<li class="all-time page-item <?php echo $get_query_date === 'all-time' ? 'active' : ''; ?>"><a href="<?php echo $sort_links['all_time']; ?>">جميع المقالات</a></li>
	</ul>
	<div class="date-selector">
		<form action="admin.php?page=posts-visitors-count&query_date=period">
			<input type="hidden" name="page" value="posts-visitors-count">
			<input type="hidden" name="query_date" value="period">
			<span class="date-title">او خلال الفتره الزمنيه</span>
			<div class="date-selector-dates">
				<div class="from">
					من
					<?php
					$current_date_from = '';
					if (isset($_GET['from']) && strtotime($_GET['from'])) {
						$current_date_from = $_GET['from'];
					}
					?>
					<input type="date" name="from" value="<?php echo $current_date_from; ?>" lang="ar-SA" id="select-date-from">
				</div>
				<div class="to">
					الي
					<?php
					$current_date_to = '';
					if (isset($_GET['to']) && strtotime($_GET['to'])) {
						$current_date_to = $_GET['to'];
					}
					?>
					<input type="date" name="to" value="<?php echo $current_date_to; ?>" lang="ar-SA" id="select-date-to">
				</div>
				<div class="submit">
					<button type="submit" class="button">عرض</button>
				</div>
			</div>
		</form>
		<?php if ($error_status) :  ?>
			<div class="date-error notice error">
				<p><?php echo $error_status; ?></p>
			</div>
		<?php endif; ?>
	</div>
	<table class="table">
		<thead>
			<tr>
				<th>العنوان</th>
				<th>المشاهدات</th>
			</tr>
		</thead>
		<tbody>
			<?php if ($al_query->have_posts()) : while ($al_query->have_posts()) : $al_query->the_post(); ?>
					<tr>
						<td><a href="<?php echo get_permalink(); ?>" target="_blank"><?php echo get_the_title(); ?></a></td>
						<td><?php
								$post_views = get_post_meta(get_the_ID(), 'postsVisitorsCount_meta', true);
								echo $post_views !== '' ? $post_views : 'لا توجد مشاهدات';
								?></td>
					</tr>
				<?php endwhile;
				wp_reset_postdata();
			else : ?>
				<tr>
					<td>لا توجد مقالات</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<div class="al-pagination pagination">
		<?php
		echo paginate_links(
			[
				'current' =>  isset($_GET['paged']) ? $_GET['paged'] : 1,
				'total' => $al_query->max_num_pages,
				'prev_next' => false,
				'format' => '?paged=%#%',
			]
		); ?>
	</div>
</div>