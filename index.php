<html>
	<head>
		<?php $basePath = 'vendor/bower-asset'; ?>

		<link rel='stylesheet' href='<?= $basePath ?>/fullcalendar/dist/fullcalendar.css'>
		<script src='<?= $basePath ?>/jquery/dist/jquery.min.js'></script>
		<script src='<?= $basePath ?>/moment/min/moment.min.js'></script>
		<script src='<?= $basePath ?>/fullcalendar/dist/fullcalendar.js'></script>

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

		<link rel="stylesheet" href="<?= $basePath ?>/select2/dist/css/select2.min.css">
		<script src="<?= $basePath ?>/select2/dist/js/select2.min.js"></script>

		<style type="text/css">
			html {
				position: relative;
				min-height: 100%;
			}	
		</style>

		<link rel="stylesheet" href="assets/css/cm-events-calendar.css" />

		<title>Calendar test</title>
	</head>
	<body>
		<div class="container">
			<div class="page-header">
				<h1>Calendar test</h1>
			</div>
			<?php
				require_once('standalone-settings.php');
				require_once('functions.php');

				$generalInfo = makeServiceCall('GET', '/api/general');
				$eventCategories = makeServiceCall('GET', '/api/eventCategory');
				
				$clubs = [];
				foreach ($generalInfo->clubs as $club)
				{
					$clubs[$club->club_ID] = $club->name;
				}

				$club_title = ucwords($generalInfo->strings->clubs);
			?>
			<script type="text/javascript">
				var clubmanager_feed_url = '/standalone-feed.php';
				var clubmanager_timezone = "<?= $generalInfo->timezone ?>";
				var clubmanager_clubs = <?= json_encode($clubs) ?>;
			</script>
			
			<?php
				require_once('calendar.php');
			?>
		</div>
		
		<script type="text/javascript" src="assets/js/cm-events-calendar.js"></script>
	</body>
</html>
