<?php
$isEdit = isset($hearing) && $hearing;
$v = fn(string $k) => clean($isEdit ? ($hearing[$k] ?? '') : '');
$preCase = (int)($_GET['case_id'] ?? 0);
?>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Hearing Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" required value="<?= $v('title') ?>" placeholder="e.g., Bail Application Hearing"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Case <span class="text-red-500">*</span></label>
        <select name="case_id" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Select a case...</option>
            <?php foreach ($cases as $c):
                $selected = ($isEdit ? (int)$hearing['case_id'] : $preCase) === (int)$c['id']; ?>
            <option value="<?= $c['id'] ?>" <?= $selected ? 'selected' : '' ?>><?= clean($c['case_number'] ?? '') ?> — <?= clean($c['title']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Hearing Date <span class="text-red-500">*</span></label>
        <input type="date" name="hearing_date" required value="<?= $isEdit ? ($hearing['hearing_date'] ?? '') : '' ?>"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Time</label>
        <input type="time" name="hearing_time" value="<?= $isEdit ? ($hearing['hearing_time'] ?? '') : '' ?>"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Court Name</label>
        <input type="text" name="court_name" value="<?= $v('court_name') ?>" placeholder="e.g., High Court of Uganda"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Court Room</label>
        <input type="text" name="court_room" value="<?= $v('court_room') ?>" placeholder="e.g., Room 3A"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Judge Name</label>
        <input type="text" name="judge_name" value="<?= $v('judge_name') ?>" placeholder="Hon. Justice ..."
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <?php if ($isEdit): ?>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
        <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <?php foreach (['scheduled'=>'Scheduled','completed'=>'Completed','postponed'=>'Postponed','cancelled'=>'Cancelled'] as $sv => $sl): ?>
            <option value="<?= $sv ?>" <?= ($hearing['status'] ?? '') === $sv ? 'selected' : '' ?>><?= $sl ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
        <textarea name="notes" rows="3" placeholder="Any additional notes..."
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"><?= $v('notes') ?></textarea>
    </div>
</div>
