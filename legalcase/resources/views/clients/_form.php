<?php
// $client is set when editing; unset/null when creating
$isEdit = isset($client) && $client;
$v = fn(string $k) => clean($isEdit ? ($client[$k] ?? '') : '');
?>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
        <input type="text" name="full_name" required value="<?= $v('full_name') ?>" placeholder="e.g., Nakato Sarah"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
        <input type="tel" name="phone" value="<?= $v('phone') ?>" placeholder="+256 700 000 000"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
        <input type="email" name="email" value="<?= $v('email') ?>" placeholder="client@example.com"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">National ID / Passport No.</label>
        <input type="text" name="id_number" value="<?= $v('id_number') ?>" placeholder="CM00000000000UG"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Date of Birth</label>
        <input type="date" name="date_of_birth" value="<?= $isEdit ? ($client['date_of_birth'] ?? '') : '' ?>"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
        <input type="text" name="address" value="<?= $v('address') ?>" placeholder="Plot 12, Kampala Road, Kampala"
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
        <textarea name="notes" rows="3" placeholder="Additional notes about this client..."
            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"><?= $v('notes') ?></textarea>
    </div>
</div>
