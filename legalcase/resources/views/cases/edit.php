<?php ob_start(); ?>

<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-slate-800">Edit Case</h2>
                <p class="text-slate-500 text-sm mt-0.5 font-mono"><?= clean($case['case_number']) ?></p>
            </div>
            <a href="<?= APP_URL ?>/cases/<?= $case['id'] ?>" class="text-slate-400 hover:text-slate-600 text-sm">← Back to case</a>
        </div>

        <form method="POST" action="<?= APP_URL ?>/cases/<?= $case['id'] ?>/update" class="px-6 py-5 space-y-5">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Case Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required value="<?= clean($case['title']) ?>"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Case Type <span class="text-red-500">*</span></label>
                    <select name="case_type" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php foreach (['criminal'=>'Criminal','civil'=>'Civil','family'=>'Family','corporate'=>'Corporate','land'=>'Land/Property','other'=>'Other'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= $case['case_type'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php foreach (['filed'=>'Filed','under_investigation'=>'Under Investigation','hearing_scheduled'=>'Hearing Scheduled','in_progress'=>'In Progress','closed'=>'Closed','dismissed'=>'Dismissed'] as $v => $l): ?>
                        <option value="<?= $v ?>" <?= $case['status'] === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Assigned Lawyer</label>
                    <select name="lawyer_id" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— None —</option>
                        <?php foreach ($lawyers as $l): ?>
                        <option value="<?= $l['id'] ?>" <?= (int)$case['lawyer_id'] === (int)$l['id'] ? 'selected' : '' ?>><?= clean($l['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Client</label>
                    <select name="client_id" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— None —</option>
                        <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (int)$case['client_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= clean($c['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Court Name</label>
                    <input type="text" name="court_name" value="<?= clean($case['court_name'] ?? '') ?>"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Judge Name</label>
                    <input type="text" name="judge_name" value="<?= clean($case['judge_name'] ?? '') ?>"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Filing Date</label>
                    <input type="date" name="filing_date" value="<?= $case['filing_date'] ?? '' ?>"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Closing Date</label>
                    <input type="date" name="closing_date" value="<?= $case['closing_date'] ?? '' ?>"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"><?= clean($case['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg px-5 py-2.5 text-sm transition-colors">
                    Save Changes
                </button>
                <a href="<?= APP_URL ?>/cases/<?= $case['id'] ?>" class="text-slate-500 hover:text-slate-700 text-sm px-3 py-2.5">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$title   = 'Edit Case — ' . $case['case_number'];
require VIEW_PATH . '/layouts/app.php';
?>
