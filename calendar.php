<?php
/**
 * @param string[] $clubs club definitions (club_ID => name)
 * @param EventCategory[] $eventCategories event categories
 * @param string $club_title title for club picker
 *
 * @type EventCategory {
 * 		@type int event_category_ID category ID
 * 		@type int club_ID club ID
 * 		@type string name name
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
		'text' => $category->name
	];
}

?>
<script type="text/javascript">
	var clubmanager_event_categories = <?= json_encode($categoriesByClub) ?>;
</script>

<div id="cm-events-calendar-wrapper">
	<div class="row">
		<div class="col-sm-10">
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
		</div>
		<div class="col-sm-2">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">Key</h4>
				</div>
				<div class="panel-body">
					<ul class="nav nav-pills nav-stacked">
						<li><span class="glyphicon status-open"></span> Open</li>
						<li><span class="glyphicon status-full"></span> Full</li>
						<li><span class="glyphicon status-started"></span> Started</li>
						<li><span class="glyphicon status-cancelled"></span> Cancelled</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<p>&nbsp;</p>
	<div id="cm-events-calendar"></div>
</div>
