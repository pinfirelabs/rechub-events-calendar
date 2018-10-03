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

<div>
	<?= include('./parts/filter-row.php') ?>
	<p>&nbsp;</p>
</div>