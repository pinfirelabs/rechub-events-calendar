<?php
    global $cmApiServer;
    global $search;

    if (!empty($cmServer)) {
        var_dump($cmServer); exit;
    }
    if (empty($cmServer)) {
        require_once(__DIR__ . '/../standalone-settings.php');
    }
    require_once(__DIR__ . '/../functions.php');

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
    var clubmanager_api_url = <?= json_encode($cmApiServer) ?>;
    var clubmanager_url = <?= json_encode($cmServer) ?>;
    var clubmanager_feed_url = "/feed.php?search=";
    var clubmanager_timezone = "<?= $generalInfo->timezone ?>";
    var clubmanager_clubs = <?= json_encode($clubs) ?>;
    var clubmanager_search = <?= json_encode($search) ?>;
</script>

<?php
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
    jQuery(document).ready(function() {
        window.cmCal();
    })
    
</script>