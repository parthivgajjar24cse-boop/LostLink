<?php if (!$items): ?>
    <p class="empty">No reports are available in this section.</p>
<?php else: ?>
    <div class="table-wrap">
        <table id="item-table">
            <thead>
                <tr>
                    <th>IU No.</th>
                    <th>Item</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Phone No.</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <a href="item.php?id=<?= $item['id'] ?>">
                                <?= e($item['iu_number']) ?>
                            </a>
                        </td>
                        <td class="<?= item_class($item['type']) ?>">
                            <a class="item-link" href="item.php?id=<?= $item['id'] ?>">
                                <?= e($item['item_name']) ?>
                            </a>
                        </td>
                        <td><?= format_date($item['event_date']) ?></td>
                        <td><?= e(substr((string) $item['event_time'], 0, 5)) ?></td>
                        <td><?= e($item['location']) ?></td>
                        <td><?= e($item['phone']) ?></td>
                        <td class="<?= item_class($item['type']) ?>"><?= e($item['type']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
