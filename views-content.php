<?php

require_once __DIR__ . '/views-style.php';

$get_query_date = isset($_GET['query_date']) ? $_GET['query_date'] : 'today';
$error_status = false;

$al_query_args = [
	'post_type' => 'post',
	'post_status' => 'publish',
	'posts_per_page' => 30,
	'paged' => isset($_GET['paged']) ? $_GET['paged'] : 1,
	'meta_key' => 'postsVisitorsCount_meta',
	'orderby' => 'meta_value_num',
	'meta_type' => 'NUMBER',
	'order' => 'DESC',
];

$views_query_args = [
	'post_type' => 'post',
	'post_status' => 'publish',
	'posts_per_page' => -1,
	'meta_key' => 'postsVisitorsCount_meta',
	'orderby' => 'meta_value_num',
	'meta_type' => 'NUMBER',
	'order' => 'DESC',
];

// check the author
if(isset($_GET['author']) && $_GET['author'] !== 'all'){
	$al_query_args['author'] = $_GET['author']; 
	$views_query_args['author']= $_GET['author']; 
}

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
	$views_query_args['date_query'] = [
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
	$views_query_args['date_query'] = [
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
	$views_query_args['date_query'] = [
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
	$views_query_args['date_query'] = [
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
	$views_query_args['date_query'] = [
		[
			'after' => '1 year ago'
		]
	];
}

// Add date comparison period
if (isset($_GET['from']) && isset($_GET['to'])) {
	$from = strtotime($_GET['from']);
	$to = strtotime($_GET['to']);
	if ($from && $to) {
		if ($from < $to) {
			$al_query_args['date_query'] = [
				[
					'after'   => $_GET['from'],
					'before' => $_GET['to'],
					'inclusive' => true, 
				]
			];
			$views_query_args['date_query'] = [
				[
					'after'   => $_GET['from'],
					'before' => $_GET['to'],
					'inclusive' => true, 
				]
			];
			$get_query_date = 'period'; 
		} elseif ($from === $to) {
			$start_date = date('y-m-d', strtotime('-1 day', $from));
			$end_date = date('y-m-d', strtotime('+1 day', $from));
			$al_query_args['date_query'] = [
				[
					'after'   => $start_date,
					'before' => $end_date,
					'inclusive' => true, 
				]
			];
			$views_query_args['date_query'] = [
				[
					'after'   => $start_date,
					'before' => $end_date,
					'inclusive' => true, 
				]
			];
			$get_query_date = 'period'; 
		} else {
			$error_status = 'التاريخ غير صحيح .. يرجى التحقق من صحة التاريخ الذي تم إدخاله';
		}
	}
}
$al_query = new WP_Query($al_query_args);

$views_query = new WP_Query($views_query_args);

?>
<div class="postsVisitorsCount-page-container">
	<h2 class="title">اكثر المقالات مشاهدة</h2>
	<form action="">

		<?php if ($error_status) :  ?>
			<div class="date-error notice error">
				<p><?php echo $error_status; ?></p>
			</div>
		<?php endif; ?>

		<ul class="summary-tabs pagination">
			<label for='query_date_today' class="7-days page-item <?php echo $get_query_date === 'today' ? 'active' : ''; ?>">
				اخر 24 ساعه 
				<input id="query_date_today" name="query_date" type="radio" value="today" <?php echo $get_query_date === 'today' ? 'checked' : ''; ?>/>
			</label>
			<label for="query_date_this-week" class="7-days page-item <?php echo $get_query_date === 'this-week' ? 'active' : ''; ?>">
				اخر 7 ايام
				<input id="query_date_this-week" name="query_date" type="radio" value="this-week" <?php echo $get_query_date === 'this-week' ? 'checked' : ''; ?>/>
			</label>
			<label for="query_date_this-month" class="30-days page-item <?php echo $get_query_date === 'this-month' ? 'active' : ''; ?>">
				اخر 30 يوم
				<input id="query_date_this-month" name="query_date" type="radio" value="this-month" <?php echo $get_query_date === 'this-month' ? 'checked' : ''; ?>/>
			</label>
			<label for="query_date_last-three-months" class="quarter page-item <?php echo $get_query_date === 'last-three-months' ? 'active' : ''; ?>">
				اخر 3 شهور
				<input id="query_date_last-three-months" name="query_date" type="radio" value="last-three-months" <?php echo $get_query_date === 'last-three-months' ? 'checked' : ''; ?>/>
			</label>
			<label for="query_date_this-year" class="year page-item <?php echo $get_query_date === 'this-year' ? 'active' : ''; ?>">
				العام الاخير
				<input id="query_date_this-year" name="query_date" type="radio" value="this-year" <?php echo $get_query_date === 'this-year' ? 'checked' : ''; ?>/>
			</label>
			<label for="query_date_all-time" class="all-time page-item <?php echo $get_query_date === 'all-time' ? 'active' : ''; ?>">
				جميع المقالات
				<input id="query_date_all-time" name="query_date" type="radio" value="all-time" <?php echo $get_query_date === 'all-time' ? 'checked' : ''; ?>/>
			</label>
		</ul>

		<div class="date-selector">
			<div class="date-selector-dates">
				<div>
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
					</div>
				</div>

				<div>
					<span class="date-title">المؤلف</span>
					<div class="date-selector-dates">
						<select name="author" id="author" style="text-align: left;">
							<option value="all">all</option>
							<?php
							$post_authors = get_users(array('who' => 'authors'));
							foreach ($post_authors as $author) {
								?>
								<option <?php echo (intval($_GET['author']) == $author->ID) ? 'selected' : ''; ?> value="<?php echo esc_attr($author->ID); ?>">
									<?php echo esc_html($author->display_name); ?>
								</option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
			</div>

			<input type="hidden" name="page" value="posts-visitors-count">

			<div class="date-title">
				<button type="submit" class="button">تصفية البيانات</button>
			</div>
		</div>		
	</form>
	<table class="table">
		<thead>
			<tr>
				<th>العنوان</th>
				<th>المشاهدات</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			if ($al_query->have_posts()) { 
				while ($al_query->have_posts()) : $al_query->the_post(); ?>
					<tr>
						<td><a href="<?php echo get_permalink(); ?>" target="_blank"><?php echo get_the_title(); ?></a></td>
						<td><?php
								$post_views = get_post_meta(get_the_ID(), 'postsVisitorsCount_meta', true);
								if($post_views !== ''){
									echo $post_views; 
								}else{
									echo 'لا توجد مشاهدات';
								}
								?></td>
					</tr>
				<?php endwhile; wp_reset_postdata(); ?>


				<?php 
				$countTotalViews = 0; 
				while ($views_query->have_posts()) : $views_query->the_post();
					$post_views = get_post_meta(get_the_ID(), 'postsVisitorsCount_meta', true);
					if($post_views !== ''){
						$countTotalViews += $post_views; 
					}
				endwhile; wp_reset_postdata(); 
				?>
				<tr>
					<td>اجمالي المشاهدات</td>
					<td><?= $countTotalViews; ?></td>
				</tr>

			<?php } else { ?>
				<tr>
					<td>لا توجد مقالات</td>
				</tr>
			<?php }; ?>
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



<script>
	let dateFrom = document.getElementById('select-date-from'), 
		dateTo = document.getElementById('select-date-to'), 
		dateItem = document.querySelectorAll('label.page-item'); 

	function changeDateInput(){
		dateItem.forEach(function(i){
			i.classList.remove('active');
			i.querySelector('input').checked = false; 
		});
	}

	dateFrom.addEventListener('change', changeDateInput);
	dateTo.addEventListener('change', changeDateInput);

	dateItem.forEach(function(i){
		i.addEventListener('click', function(){
			dateFrom.value = ''; 
			dateTo.value = ''; 
		});
	});

</script>