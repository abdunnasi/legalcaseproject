<?php ob_start(); ?>

<div class="space-y-4">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <p class="text-slate-500 text-sm"><?= count($cases) ?> cases total · Generated <?= fDateTime(date('Y-m-d H:i:s')) ?></p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="flex items-center gap-2 border border-slate-200 rounded-lg px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <a href="<?= APP_URL ?>/reports" class="text-slate-400 hover:text-slate-600 text-sm flex items-center">← Back</a>
        </div>
    </div>

    <!-- Status summary row -->
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
        <?php
        $counts = [];
        foreach ($cases as $c) $counts[$c['status']] = ($counts[$c['status']] ?? 0) + 1;
        $statusMeta = ['filed'=>['Filed','bg-blue-50 border-blue-200 text-blue-700'],'under_investigation'=>['Investigating','bg-yellow-50 border-yellow-200 text-yellow-700'],'hearing_scheduled'=>['Hearing Set','bg-purple-50 border-purple-200 text-purple-700'],'in_progress'=>['In Progress','bg-orange-50 border-orange-200 text-orange-700'],'closed'=>['Closed','bg-green-50 border-green-200 text-green-700'],'dismissed'=>['Dismissed','bg-red-50 border-red-200 text-red-700']];
        foreach ($statusMeta as $sk => [$sl, $sc]): ?>
        <div class="border rounded-xl p-3 text-center <?= $sc ?>">
            <div class="text-xl font-bold"><?= $counts[$sk] ?? 0 ?></div>
            <div class="text-xs mt-0.5 font-medium"><?= $sl ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700 text-sm">All Cases</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Case No.</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Title</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Type</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Client</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Lawyer</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Status</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Filed</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Court</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($cases): foreach ($cases as $c): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs font-semibold text-blue-700"><?= clean($c['case_number']) ?></span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="<?= APP_URL ?>/cases/<?= $c['id'] ?>" class="font-medium text-slate-800 hover:text-blue-600 max-w-[200px] truncate block"><?= clean($c['title']) ?></a>
                        </td>
                        <td class="px-4 py-3 text-slate-500 capitalize text-xs"><?= $c['case_type'] ?></td>
                        <td class="px-4 py-3 text-slate-600 text-xs"><?= clean($c['client_name'] ?? '—') ?></td>
                        <td class="px-4 py-3 text-slate-600 text-xs"><?= clean($c['lawyer_name'] ?? '—') ?></td>
                        <td class="px-4 py-3"><?= statusBadge($c['status']) ?></td>
                        <td class="px-4 py-3 text-slate-400 text-xs"><?= fDate($c['filing_date']) ?></td>
                        <td class="px-4 py-3 text-slate-400 text-xs max-w-[120px] truncate"><?= clean($c['court_name'] ?? '—') ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="8" class="px-5 py-10 text-center text-slate-400">No cases found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    aside, header, button, a[href*="print"] { display: none !important; }
    body { background: white !important; }
    .bg-white { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
}
</style>

<?php
$content = ob_get_clean();
$title   = 'Cases Report';
require VIEW_PATH . '/layouts/app.php';
?>
