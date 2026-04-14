<?php ob_start(); ?>

<div class="space-y-4">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <p class="text-slate-500 text-sm"><?= number_format($pager['total']) ?> hearing<?= $pager['total'] != 1 ? 's' : '' ?> total</p>
        <?php if (hasRole(['admin','lawyer','clerk'])): ?>
        <a href="<?= APP_URL ?>/hearings/create" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Schedule Hearing
        </a>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Date & Time</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Hearing</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Case</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Court / Room</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Judge</th>
                        <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if ($hearings): foreach ($hearings as $h):
                        $isPast     = strtotime($h['hearing_date']) < strtotime('today');
                        $isToday    = $h['hearing_date'] === date('Y-m-d');
                    ?>
                    <tr class="hover:bg-slate-50 transition-colors <?= $isToday ? 'bg-blue-50/50' : '' ?>">
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-slate-800 <?= $isPast && $h['status'] === 'scheduled' ? 'text-red-600' : '' ?>">
                                <?= fDate($h['hearing_date']) ?>
                                <?= $isToday ? '<span class="ml-1 text-xs bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-medium">Today</span>' : '' ?>
                            </div>
                            <?php if ($h['hearing_time']): ?>
                            <div class="text-slate-400 text-xs"><?= date('H:i', strtotime($h['hearing_time'])) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-medium text-slate-800"><?= clean($h['title']) ?></div>
                        </td>
                        <td class="px-4 py-3.5">
                            <a href="<?= APP_URL ?>/cases/<?= $h['case_id'] ?>" class="text-blue-600 hover:underline text-xs font-mono font-semibold"><?= clean($h['case_number'] ?? '') ?></a>
                            <div class="text-slate-500 text-xs truncate max-w-[160px]"><?= clean($h['case_title'] ?? '') ?></div>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 text-xs">
                            <?= clean($h['court_name'] ?: '—') ?>
                            <?php if ($h['court_room']): ?><span class="text-slate-400"> · Rm <?= clean($h['court_room']) ?></span><?php endif; ?>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 text-xs"><?= clean($h['judge_name'] ?: '—') ?></td>
                        <td class="px-4 py-3.5"><?= statusBadge($h['status']) ?></td>
                        <td class="px-4 py-3.5">
                            <a href="<?= APP_URL ?>/hearings/<?= $h['id'] ?>/edit" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="7" class="px-5 py-12 text-center text-slate-400">No hearings scheduled yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($pager['pages'] > 1): ?>
        <div class="px-5 py-3 border-t border-slate-100 flex items-center justify-between text-sm">
            <span class="text-slate-400">Page <?= $pager['current'] ?> of <?= $pager['pages'] ?></span>
            <div class="flex gap-1">
                <?php for ($i = max(1, $pager['current']-2); $i <= min($pager['pages'], $pager['current']+2); $i++): ?>
                <a href="?page=<?= $i ?>" class="px-3 py-1.5 rounded-lg <?= $i === $pager['current'] ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?> text-xs transition-colors"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
$title   = 'Court Schedule';
require VIEW_PATH . '/layouts/app.php';
?>
