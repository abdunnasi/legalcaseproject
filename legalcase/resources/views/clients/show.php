<?php ob_start(); ?>

<div class="space-y-4 max-w-4xl">

    <!-- Client header card -->
    <div class="bg-white rounded-xl border border-slate-200 px-6 py-5">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-purple-100 flex items-center justify-center text-purple-700 font-bold text-xl flex-shrink-0">
                    <?= strtoupper(substr($client['full_name'], 0, 1)) ?>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800"><?= clean($client['full_name']) ?></h2>
                    <div class="flex flex-wrap gap-3 mt-1 text-sm text-slate-500">
                        <?php if ($client['phone']): ?><span>📱 <?= clean($client['phone']) ?></span><?php endif; ?>
                        <?php if ($client['email']): ?><span>✉️ <?= clean($client['email']) ?></span><?php endif; ?>
                        <?php if ($client['id_number']): ?><span class="font-mono text-xs">🪪 <?= clean($client['id_number']) ?></span><?php endif; ?>
                    </div>
                    <?php if ($client['address']): ?>
                    <p class="text-slate-400 text-sm mt-1">📍 <?= clean($client['address']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= APP_URL ?>/clients/<?= $client['id'] ?>/edit"
               class="bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg px-4 py-2 text-sm font-medium transition-colors">
                Edit Client
            </a>
        </div>

        <?php if ($client['notes']): ?>
        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
            <span class="font-medium">Notes:</span> <?= nl2br(clean($client['notes'])) ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Details grid -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700 text-sm">Personal Details</h3>
        </div>
        <dl class="divide-y divide-slate-100">
            <?php
            $details = [
                'Date of Birth' => fDate($client['date_of_birth']),
                'National ID'   => $client['id_number'] ?: '—',
                'Phone'         => $client['phone'] ?: '—',
                'Email'         => $client['email'] ?: '—',
                'Address'       => $client['address'] ?: '—',
                'Added'         => fDateTime($client['created_at']),
            ];
            foreach ($details as $label => $value): ?>
            <div class="flex px-5 py-3 text-sm">
                <dt class="w-40 text-slate-500 flex-shrink-0"><?= $label ?></dt>
                <dd class="text-slate-800 font-medium"><?= clean((string)$value) ?></dd>
            </div>
            <?php endforeach; ?>
        </dl>
    </div>

    <!-- Associated Cases -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-700 text-sm">Case History (<?= count($cases) ?>)</h3>
            <a href="<?= APP_URL ?>/cases/create" class="text-xs text-blue-600 hover:underline">+ New case for client</a>
        </div>
        <div class="divide-y divide-slate-100">
            <?php if ($cases): foreach ($cases as $c): ?>
            <a href="<?= APP_URL ?>/cases/<?= $c['id'] ?>" class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 transition-colors">
                <span class="font-mono text-xs text-blue-700 font-semibold bg-blue-50 px-2 py-0.5 rounded"><?= clean($c['case_number']) ?></span>
                <span class="flex-1 text-sm font-medium text-slate-800 truncate"><?= clean($c['title']) ?></span>
                <span class="text-xs text-slate-400 capitalize"><?= $c['case_type'] ?></span>
                <?= statusBadge($c['status']) ?>
            </a>
            <?php endforeach; else: ?>
            <div class="px-5 py-8 text-center text-slate-400 text-sm">No cases linked to this client yet.</div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
$title   = $client['full_name'];
require VIEW_PATH . '/layouts/app.php';
?>
