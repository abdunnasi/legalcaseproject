<?php ob_start(); ?>

<div class="space-y-4">
    <!-- Actions bar -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" action="<?= APP_URL ?>/cases" class="flex gap-2 flex-wrap">
            <input type="text" name="search" value="<?= clean($filters['search'] ?? '') ?>" placeholder="Search cases..."
                class="bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-52">
            <select name="status" class="bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <?php foreach (['filed','under_investigation','hearing_scheduled','in_progress','closed','dismissed'] as $s): ?>
                <option value="<?= $s ?>" <?= ($filters['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucwords(str_replace('_', ' ', $s)) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="type" class="bg-white border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Types</option>
                <?php foreach (['criminal','civil','family','corporate','land','other'] as $t): ?>
                <option value="<?= $t ?>" <?= ($filters['type'] ?? '') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg px-3 py-2 text-sm transition-colors">Filter</button>
            <?php if ($filters['search'] || $filters['status'] || $filters['type']): ?>
            <a href="<?= APP_URL ?>/cases" class="bg-slate-100 text-slate-500 rounded-lg px-3 py-2 text-sm hover:bg-slate-200">Clear</a>
            <?php endif; ?>
        </form>
        <?php if (hasRole(['admin','lawyer','clerk'])): ?>
        <a href="<?= APP_URL ?>/cases/create" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Register Case
        </a>
        <?php endif; ?>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <span class="text-sm text-slate-500"><?= number_format($pager['total']) ?> case<?= $pager['total'] != 1 ? 's' : '' ?> found</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Case No.</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Title</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Type</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Client</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Lawyer</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Filed</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($cases): foreach ($cases as $c): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-xs text-blue-700 font-semibold"><?= clean($c['case_number']) ?></span>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-medium text-slate-800 max-w-xs truncate"><?= clean($c['title']) ?></div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="text-slate-500 capitalize"><?= $c['case_type'] ?></span>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600"><?= clean($c['client_name'] ?? '—') ?></td>
                        <td class="px-4 py-3.5 text-slate-600"><?= clean($c['lawyer_name'] ?? '—') ?></td>
                        <td class="px-4 py-3.5"><?= statusBadge($c['status']) ?></td>
                        <td class="px-4 py-3.5 text-slate-400 text-xs"><?= fDate($c['filing_date']) ?></td>
                        <td class="px-4 py-3.5">
                            <a href="<?= APP_URL ?>/cases/<?= $c['id'] ?>" class="text-blue-600 hover:text-blue-800 text-xs font-medium">View →</a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="8" class="px-5 py-12 text-center text-slate-400">No cases found. <a href="<?= APP_URL ?>/cases/create" class="text-blue-600 hover:underline">Register the first case</a></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pager['pages'] > 1): ?>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between text-sm">
            <span class="text-slate-400">Page <?= $pager['current'] ?> of <?= $pager['pages'] ?></span>
            <div class="flex gap-1">
                <?php for ($i = max(1, $pager['current']-2); $i <= min($pager['pages'], $pager['current']+2); $i++): ?>
                <a href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"
                   class="px-3 py-1.5 rounded-lg <?= $i === $pager['current'] ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?> text-xs transition-colors"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$title   = 'Cases';
require VIEW_PATH . '/layouts/app.php';
?>
