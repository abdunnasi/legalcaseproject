<?php ob_start(); ?>

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="<?= APP_URL ?>/clients" class="flex gap-2">
            <input type="text" name="search" value="<?= clean($search) ?>" placeholder="Search by name or phone..."
                class="bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-60">
            <button class="bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg px-3 py-2 text-sm transition-colors">Search</button>
            <?php if ($search): ?>
            <a href="<?= APP_URL ?>/clients" class="bg-slate-100 text-slate-500 rounded-lg px-3 py-2 text-sm hover:bg-slate-200">Clear</a>
            <?php endif; ?>
        </form>
        <a href="<?= APP_URL ?>/clients/create" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Client
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100">
            <span class="text-sm text-slate-500"><?= number_format($pager['total']) ?> client<?= $pager['total'] != 1 ? 's' : '' ?></span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Name</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Phone</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Email</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">ID Number</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Added</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($clients): foreach ($clients as $c): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold text-sm flex-shrink-0">
                                    <?= strtoupper(substr($c['full_name'], 0, 1)) ?>
                                </div>
                                <span class="font-medium text-slate-800"><?= clean($c['full_name']) ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600"><?= clean($c['phone'] ?: '—') ?></td>
                        <td class="px-4 py-3.5 text-slate-600"><?= clean($c['email'] ?: '—') ?></td>
                        <td class="px-4 py-3.5 text-slate-500 font-mono text-xs"><?= clean($c['id_number'] ?: '—') ?></td>
                        <td class="px-4 py-3.5 text-slate-400 text-xs"><?= fDate($c['created_at']) ?></td>
                        <td class="px-4 py-3.5">
                            <a href="<?= APP_URL ?>/clients/<?= $c['id'] ?>" class="text-blue-600 hover:text-blue-800 text-xs font-medium">View →</a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="px-5 py-12 text-center text-slate-400">No clients found. <a href="<?= APP_URL ?>/clients/create" class="text-blue-600 hover:underline">Add the first client</a></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($pager['pages'] > 1): ?>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between text-sm">
            <span class="text-slate-400">Page <?= $pager['current'] ?> of <?= $pager['pages'] ?></span>
            <div class="flex gap-1">
                <?php for ($i = max(1, $pager['current']-2); $i <= min($pager['pages'], $pager['current']+2); $i++): ?>
                <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
                   class="px-3 py-1.5 rounded-lg <?= $i === $pager['current'] ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?> text-xs transition-colors"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$title   = 'Clients';
require VIEW_PATH . '/layouts/app.php';
?>
