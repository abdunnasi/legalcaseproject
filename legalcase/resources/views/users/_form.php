<?php
$isEdit = isset($user) && $user;
$v = fn(string $k) => clean($isEdit ? ($user[$k] ?? '') : '');
?>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
        <input type="text" name="name" required value="<?= $v('name') ?>" placeholder="Jane Nakato"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email Address <span class="text-red-500">*</span></label>
        <input type="email" name="email" required value="<?= $v('email') ?>" placeholder="jane@lawfirm.ug"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">
            <?= $isEdit ? 'New Password' : 'Password' ?>
            <?= !$isEdit ? '<span class="text-red-500">*</span>' : '' ?>
            <?= $isEdit ? '<span class="text-slate-400 font-normal">(leave blank to keep current)</span>' : '' ?>
        </label>
        <input type="password" name="password" <?= !$isEdit ? 'required' : '' ?> minlength="8" placeholder="Minimum 8 characters"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
        <input type="tel" name="phone" value="<?= $v('phone') ?>" placeholder="+256 700 000 000"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Role <span class="text-red-500">*</span></label>
        <select name="role" required class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <?php foreach (['admin'=>'Administrator','lawyer'=>'Lawyer','clerk'=>'Clerk','staff'=>'Staff'] as $rv => $rl): ?>
            <option value="<?= $rv ?>" <?= $v('role') === $rv ? 'selected' : '' ?>><?= $rl ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700">
    <strong>Role permissions:</strong> Admins have full access · Lawyers can manage their own cases · Clerks can view and update · Staff have read-only access
</div>
