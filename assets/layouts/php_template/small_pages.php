
<?php if ($pageObj->paginator->getNumPages() > 1): ?>
    <div class="input-group" style="width: 1px;">
        <?php if ($pageObj->paginator->getPrevUrl()): ?>
            <span class="input-group-btn">
                <a href="<?php echo $pageObj->paginator->getPrevUrl(); ?>" class="btn btn-default" type="button">&laquo; Prev</a>
            </span>
        <?php endif; ?>

        <select class="form-control paginator-select-page" style="width: auto; cursor: pointer; -webkit-appearance: none; -moz-appearance: none; appearance: none;">
            <?php foreach ($pageObj->paginator->getPages() as $page): ?>
                <?php if ($page['url']): ?>
                    <option value="<?php echo $page['url']; ?>"<?php if ($page['isCurrent'])
					        echo ' selected'; ?>>
                        Page <?php echo $page['num']; ?>
                    </option>
                <?php else: ?>
                    <option disabled><?php echo $page['num']; ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>

        <?php if ($pageObj->paginator->getNextUrl()): ?>
            <span class="input-group-btn">
                <a href="<?php echo $pageObj->paginator->getNextUrl(); ?>" class="btn btn-default" type="button">Next &raquo;</a>
            </span>
        <?php endif; ?>
    </div>
<?php endif; ?>