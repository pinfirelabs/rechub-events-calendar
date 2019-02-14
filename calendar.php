<?php
/**
 * @param string[] $clubs club definitions (club_ID => name)
 * @param EventCategory[] $eventCategories event categories
 * @param string $club_title title for club picker
 * @param bool $showLegend show calendar legend
 *
 * @type EventCategory {
 * 		@var int event_category_ID category ID
 * 		@var int club_ID club ID
 * 		@var string name name
 *		@var string color color
 * }
 */

// Format categories so they can easily be filtered by club
$categoriesByClub = [];
foreach ($eventCategories as $category)
{
	if (!array_key_exists($category->club_ID, $categoriesByClub))
	{
		$categoriesByClub[$category->club_ID] = [];
	}

	$categoriesByClub[$category->club_ID][] = [
		'id' => $category->event_category_ID,
		'text' => $category->name,
		'color' => $category->color,
	];
}

?>
<script type="text/javascript">
	var clubmanager_event_categories = <?= json_encode($categoriesByClub) ?>;
	console.log(clubmanager_event_categories);
</script>

<div id="cm-events-calendar-wrapper">
	<?php if (empty($_REQUEST['hideFilter']) || !$_REQUEST['hideFilter']): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">Filter</h4>
			</div>
			<div class="panel-body">
				<?php if (isset($clubs) && count($clubs) > 1): ?>
				<div class="col-md-6">
					<div class="form-group">
						<label class="control-label" for="cm-events-club-selector"><?= htmlspecialchars($club_title) ?></label>
						<br/>
						<select id="cm-events-club-selector" multiple class="form-control">
							<?php foreach ($clubs as $club_ID => $name): ?>
								<option value="<?= $club_ID ?>"><?= htmlspecialchars($name) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="col-md-6">
				<?php else: ?>
				<div class="col-md-12">
				<?php endif; ?>
					<label for="cm-events-category-selector" class="control-label">Categories</label>
					<br/>
					<select id="cm-events-category-selector" class="form-control" multiple>
					</select>
				</div>
			</div>
		</div>
	<?php endif // (empty($_REQUEST['hideFilter']) || !$_REQUEST['hideFilter']): ?>
	<?php if (!empty($showLegend) && empty($_REQUEST['hideLegend'])): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Legend</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-10 col-xs-12">
						<h4>Category</h4>
						<ul id="category-legend">
						</ul>
					</div>
					<div class="col-sm-2 col-xs-12">
						<h4>Status</h4>
						<ul id="status-legend" class="nav nav-pills nav-stacked">
							<li><span class="glyphicon status-open"></span> Open</li>
							<li><span class="glyphicon status-full"></span> Full</li>
							<li><span class="glyphicon status-started"></span> Started</li>
							<li><span class="glyphicon status-cancelled"></span> Cancelled</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	<?php endif // (empty($_REQUEST['hideLegend']) || !$_REQUEST['hideLegend']): ?>
	<p>&nbsp;</p>
	<div id="cm-events-calendar"></div>
</div>
