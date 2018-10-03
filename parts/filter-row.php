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
                        <label class="control-label" for="cm-events-club-selector">
                            <?= htmlspecialchars($club_title) ?></label>
                        <br />
                        <div class="cm-events-select2-wrapper">
                            <select id="cm-events-club-selector" multiple class="form-control cm-events-select2">
                                <?php foreach ($clubs as $club_ID => $name): ?>
                                <option value="<?= $club_ID ?>">
                                    <?= htmlspecialchars($name) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <?php else: ?>
                    <div class="col-md-12">
                        <?php endif; ?>
                        <label for="cm-events-category-selector" class="control-label">Categories</label>
                        <br />
                        <div class="cm-events-select2-wrapper">
                            <select id="cm-events-category-selector" class="form-control cm-events-select2" multiple>
                            </select>
                        </div>
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
</div>