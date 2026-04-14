<?php ob_start(); ?>

<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-800">Case Information</h2>
            <p class="text-slate-500 text-sm mt-0.5">A unique case number will be assigned automatically.</p>
        </div>

        <form method="POST" action="<?= APP_URL ?>/cases/store" class="px-6 py-5 space-y-5">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Case Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="e.g., Uganda v. John Doe — Robbery"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Case Type <span class="text-red-500">*</span></label>
                    <select name="case_type" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select type...</option>
                        <?php foreach (['criminal'=>'Criminal','civil'=>'Civil','family'=>'Family','corporate'=>'Corporate','land'=>'Land/Property','other'=>'Other'] as $v => $l): ?>
                        <option value="<?= $v ?>"><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Filing Date</label>
                    <input type="date" name="filing_date" value="<?= date('Y-m-d') ?>"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Assigned Lawyer</label>
                    <select name="lawyer_id" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select lawyer...</option>
                        <?php foreach ($lawyers as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= clean($l['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Client</label>
                    <select name="client_id" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select client...</option>
                        <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= clean($c['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-xs text-slate-400 mt-1"><a href="<?= APP_URL ?>/clients/create" target="_blank" class="text-blue-600 hover:underline">+ Add new client</a></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Court Name</label>
                    <input type="text" name="court_name" placeholder="e.g., High Court of Uganda"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Judge Name</label>
                    <input type="text" name="judge_name" placeholder="Hon. Justice ..."
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Description / Background</label>
                    <textarea name="description" rows="4" placeholder="Brief summary of the case..."
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg px-5 py-2.5 text-sm transition-colors">
                    Register Case
                </button>
                <a href="<?= APP_URL ?>/cases" class="text-slate-500 hover:text-slate-700 text-sm px-3 py-2.5">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$title   = 'Register New Case';
require VIEW_PATH . '/layouts/app.php';
?>
