<?php
    global $cmApiServer;
	global $search;
	global $cmServer;

    if (empty($cmServer)) {
        require_once(__DIR__ . '/../standalone-settings.php');
    }
    require_once(__DIR__ . '/../functions.php');
    
?>
<script type="text/javascript">
    var clubmanager_search = <?= json_encode($search) ?>;
</script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        fetchGeneral(localStorage.getItem('cmApiserver') || '<?= $cmApiServer ?>').then(function(general) {
            fetchCategoriesByClub(localStorage.getItem('cmApiServer') || '<?=$cmApiServer ?>', Object.keys(general.clubs)).then(function(catByClub) {
                window.cmCal(
                    localStorage.getItem('cmApiServer') || <?=json_encode($cmApiServer) ?>, 
                    <?= json_encode($cmServer) ?>,
                    catByClub,
                    general
                )
            })
        })
    })
    
</script>
