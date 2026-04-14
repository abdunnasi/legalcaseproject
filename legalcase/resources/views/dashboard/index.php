<?php ob_start(); ?>

<div class="space-y-6">

    <!-- Stats row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <?php
        $stats = [
            ['Total Cases',    $totalCases,   'bg-blue-600',   'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['Active Cases',   $activeCases,  'bg-amber-500',  'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Closed Cases',   $closedCases,  'bg-green-600',  'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Total Clients',  $totalClients, 'bg-purple-600', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ];
        foreach ($stats as [$label, $value, $color, $icon]): ?>
        <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="<?= $color ?> w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $icon ?>"/></svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-slate-800"><?= $value ?></div>
                <div class="text-slate-500 text-xs mt-0.5"><?= $label ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Case status breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800 text-sm">Recent Cases</h3>
                <a href="<?= APP_URL ?>/cases" class="text-xs text-blue-600 hover:underline">View all →</a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php if ($recentCases): foreach ($recentCases as $c): ?>
                <a href="<?= APP_URL ?>/cases/<?= $c['id'] ?>" class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-medium text-slate-800 truncate"><?= clean($c['title']) ?></div>
                        <div class="text-xs text-slate-400"><?= $c['case_number'] ?> · <?= clean($c['client_name'] ?? '—') ?></div>
                    </div>
                    <div><?= statusBadge($c['status']) ?></div>
                </a>
                <?php endforeach; else: ?>
                <div class="px-5 py-8 text-center text-slate-400 text-sm">No cases yet. <a href="<?= APP_URL ?>/cases/create" class="text-blue-600 hover:underline">Register one</a></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upcoming Hearings -->
        <div class="bg-white rounded-xl border border-slate-200">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-semibold text-slate-800 text-sm">Upcoming Hearings</h3>
                <a href="<?= APP_URL ?>/hearings" class="text-xs text-blue-600 hover:underline">All →</a>
            </div>
            <div class="divide-y divide-slate-100">
                <?php if ($upcomingHearings): foreach ($upcomingHearings as $h): ?>
                <div class="px-5 py-3.5">
                    <div class="flex items-start gap-3">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg px-2 py-1 text-center min-w-[44px]">
                            <div class="text-blue-700 font-bold text-sm leading-none"><?= date('d', strtotime($h['hearing_date'])) ?></div>
                            <div class="text-blue-500 text-xs"><?= date('M', strtotime($h['hearing_date'])) ?></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-800 truncate"><?= clean($h['title']) ?></div>
                            <div class="text-xs text-slate-400 truncate"><?= clean($h['case_title'] ?? '') ?></div>
                            <?php if ($h['hearing_time']): ?>
                            <div class="text-xs text-slate-400"><?= date('H:i', strtotime($h['hearing_time'])) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; else: ?>
                <div class="px-5 py-8 text-center text-slate-400 text-sm">No upcoming hearings.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Case status breakdown pills -->
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="font-semibold text-slate-800 text-sm mb-4">Case Status Overview</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <?php
            $statusLabels = ['filed'=>'Filed','under_investigation'=>'Investigating','hearing_scheduled'=>'Hearing Set','in_progress'=>'In Progress','closed'=>'Closed','dismissed'=>'Dismissed'];
            $statusColors = ['filed'=>'bg-blue-50 border-blue-200 text-blue-700','under_investigation'=>'bg-yellow-50 border-yellow-200 text-yellow-700','hearing_scheduled'=>'bg-purple-50 border-purple-200 text-purple-700','in_progress'=>'bg-orange-50 border-orange-200 text-orange-700','closed'=>'bg-green-50 border-green-200 text-green-700','dismissed'=>'bg-red-50 border-red-200 text-red-700'];
            foreach ($statusLabels as $key => $label): ?>
            <div class="border rounded-xl p-3 text-center <?= $statusColors[$key] ?>">
                <div class="text-xl font-bold"><?= $statusCounts[$key] ?? 0 ?></div>
                <div class="text-xs mt-0.5 font-medium"><?= $label ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
$title   = 'Dashboard';
require VIEW_PATH . '/layouts/app.php';
?>
