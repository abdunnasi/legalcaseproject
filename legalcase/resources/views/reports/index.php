<?php ob_start(); ?>

<div class="space-y-5">

    <!-- Summary cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <?php
        $activeCases  = ($statusCounts['in_progress'] ?? 0) + ($statusCounts['hearing_scheduled'] ?? 0) + ($statusCounts['filed'] ?? 0) + ($statusCounts['under_investigation'] ?? 0);
        $closedCases  = $statusCounts['closed'] ?? 0;
        $cards = [
            ['Total Cases',      $totalCases,   'bg-blue-600'],
            ['Active Cases',     $activeCases,  'bg-amber-500'],
            ['Closed Cases',     $closedCases,  'bg-green-600'],
            ['Total Clients',    $totalClients, 'bg-purple-600'],
        ];
        foreach ($cards as [$label, $val, $bg]): ?>
        <div class="bg-white rounded-xl border border-slate-200 p-5 text-center">
            <div class="text-3xl font-bold text-slate-800 <?= $bg ?> bg-clip-text" style="color:inherit"><?= $val ?></div>
            <div class="text-slate-500 text-sm mt-1"><?= $label ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Case breakdown by status -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800 text-sm">Cases by Status</h3>
                <a href="<?= APP_URL ?>/reports/cases" class="text-xs text-blue-600 hover:underline">Full report →</a>
            </div>
            <div class="p-5 space-y-3">
                <?php
                $statusList = ['filed'=>'Filed','under_investigation'=>'Under Investigation','hearing_scheduled'=>'Hearing Scheduled','in_progress'=>'In Progress','closed'=>'Closed','dismissed'=>'Dismissed'];
                $barColors  = ['filed'=>'bg-blue-500','under_investigation'=>'bg-yellow-500','hearing_scheduled'=>'bg-purple-500','in_progress'=>'bg-orange-500','closed'=>'bg-green-500','dismissed'=>'bg-red-400'];
                foreach ($statusList as $sk => $sl):
                    $count = $statusCounts[$sk] ?? 0;
                    $pct   = $totalCases > 0 ? round(($count / $totalCases) * 100) : 0;
                ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium text-slate-600"><?= $sl ?></span>
                        <span class="text-xs text-slate-400"><?= $count ?> (<?= $pct ?>%)</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full <?= $barColors[$sk] ?> rounded-full transition-all" style="width: <?= $pct ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Upcoming hearings summary -->
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800 text-sm">Hearings Overview</h3>
                <a href="<?= APP_URL ?>/reports/hearings" class="text-xs text-blue-600 hover:underline">Full report →</a>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-purple-700"><?= $upcoming7 ?></div>
                        <div class="text-xs text-purple-600 mt-1">Next 7 Days</div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-blue-700"><?= $upcoming30 ?></div>
                        <div class="text-xs text-blue-600 mt-1">Next 30 Days</div>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100">
                    <div class="flex justify-between text-sm py-1.5">
                        <span class="text-slate-500">View full schedule</span>
                        <a href="<?= APP_URL ?>/hearings" class="text-blue-600 hover:underline text-xs">Go to hearings →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print / Export actions -->
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="font-semibold text-slate-800 text-sm mb-3">Export Reports</h3>
        <div class="flex flex-wrap gap-3">
            <a href="<?= APP_URL ?>/reports/cases" class="flex items-center gap-2 border border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Cases Report
            </a>
            <a href="<?= APP_URL ?>/reports/hearings" class="flex items-center gap-2 border border-slate-200 rounded-lg px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Hearings Schedule
            </a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title   = 'Reports & Analytics';
require VIEW_PATH . '/layouts/app.php';
?>
