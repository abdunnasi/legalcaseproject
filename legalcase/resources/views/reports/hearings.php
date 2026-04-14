<?php ob_start();

// Group hearings by month for a cleaner report view
$grouped = [];
foreach ($hearings as $h) {
    $month = date('F Y', strtotime($h['hearing_date']));
    $grouped[$month][] = $h;
}
?>

<div class="space-y-4">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <p class="text-slate-500 text-sm"><?= count($hearings) ?> hearing<?= count($hearings) != 1 ? 's' : '' ?> · Generated <?= fDateTime(date('Y-m-d H:i:s')) ?></p>
        <div class="flex gap-2">
            <button onclick="window.print()" class="flex items-center gap-2 border border-slate-200 rounded-lg px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <a href="<?= APP_URL ?>/reports" class="text-slate-400 hover:text-slate-600 text-sm flex items-center">← Back</a>
        </div>
    </div>

    <?php if ($grouped): foreach ($grouped as $month => $monthHearings): ?>
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-semibold text-slate-700 text-sm"><?= $month ?></h3>
            <span class="text-xs text-slate-400"><?= count($monthHearings) ?> hearing<?= count($monthHearings) != 1 ? 's' : '' ?></span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="text-left px-4 py-2.5 font-semibold text-slate-500 text-xs">Date</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-slate-500 text-xs">Time</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-slate-500 text-xs">Hearing</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-slate-500 text-xs">Case</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-slate-500 text-xs">Court / Room</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-slate-500 text-xs">Judge</th>
                        <th class="text-left px-4 py-2.5 font-semibold text-slate-500 text-xs">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($monthHearings as $h):
                        $isToday = $h['hearing_date'] === date('Y-m-d');
                        $isPast  = strtotime($h['hearing_date']) < strtotime('today') && $h['status'] === 'scheduled';
                    ?>
                    <tr class="hover:bg-slate-50 <?= $isToday ? 'bg-blue-50/60' : '' ?>">
                        <td class="px-4 py-3">
                            <span class="font-semibold text-slate-800 <?= $isPast ? 'text-red-600' : '' ?>">
                                <?= date('D, d', strtotime($h['hearing_date'])) ?>
                            </span>
                            <?php if ($isToday): ?>
                            <span class="ml-1 text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-medium">Today</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-slate-500 text-xs">
                            <?= $h['hearing_time'] ? date('H:i', strtotime($h['hearing_time'])) : '—' ?>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-slate-800 text-sm"><?= clean($h['title']) ?></div>
                        </td>
                        <td class="px-4 py-3">
                            <a href="<?= APP_URL ?>/cases/<?= $h['case_id'] ?>" class="text-blue-600 hover:underline font-mono text-xs font-semibold"><?= clean($h['case_number'] ?? '') ?></a>
                            <div class="text-slate-400 text-xs truncate max-w-[150px]"><?= clean($h['case_title'] ?? '') ?></div>
                        </td>
                        <td class="px-4 py-3 text-slate-500 text-xs">
                            <?= clean($h['court_name'] ?: '—') ?>
                            <?php if ($h['court_room']): ?>
                            <span class="text-slate-400"> · Rm <?= clean($h['court_room']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-slate-500 text-xs"><?= clean($h['judge_name'] ?: '—') ?></td>
                        <td class="px-4 py-3"><?= statusBadge($h['status']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; else: ?>
    <div class="bg-white rounded-xl border border-slate-200 px-5 py-12 text-center text-slate-400">
        No hearings found.
    </div>
    <?php endif; ?>
</div>

<style>
@media print {
    aside, header, button, a[href*="print"] { display: none !important; }
    body { background: white !important; }
}
</style>

<?php
$content = ob_get_clean();
$title   = 'Hearings Schedule Report';
require VIEW_PATH . '/layouts/app.php';
?>
