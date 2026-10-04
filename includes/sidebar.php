<aside class="sidebar">
    <p class="user-name"><?= e(current_user()['username']) ?></p>
    <a href="lost.php" class="<?= ($active ?? '') === 'lost' ? 'active' : '' ?>">Lost</a>
    <a href="found.php" class="<?= ($active ?? '') === 'found' ? 'active' : '' ?>">Found</a>
    <a href="updates.php" class="<?= ($active ?? '') === 'updates' ? 'active' : '' ?>">Updates</a>
    <a href="account.php" class="<?= ($active ?? '') === 'account' ? 'active' : '' ?>">Account</a>
    <hr>
    <a href="report_lost.php">Report Lost Item</a>
    <a href="report_found.php">Report Found Item</a>
</aside>
