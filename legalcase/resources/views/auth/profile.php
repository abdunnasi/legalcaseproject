<?php ob_start(); ?>

<div class="max-w-2xl space-y-4">

    <!-- Profile card -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-2xl flex-shrink-0">
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
            <div>
                <h2 class="font-bold text-slate-800 text-lg"><?= clean($user['name']) ?></h2>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-slate-400 text-sm"><?= clean($user['email']) ?></span>
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 capitalize"><?= $user['role'] ?></span>
                </div>
            </div>
        </div>

        <form method="POST" action="<?= APP_URL ?>/profile/update" class="px-6 py-5 space-y-4">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="<?= clean($user['name']) ?>"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required value="<?= clean($user['email']) ?>"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                    <input type="tel" name="phone" value="<?= clean($user['phone'] ?? '') ?>" placeholder="+256 700 000 000"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
                    <input type="text" value="<?= ucfirst($user['role']) ?>" disabled
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 text-sm bg-slate-50 text-slate-400 cursor-not-allowed">
                    <p class="text-xs text-slate-400 mt-1">Role can only be changed by an administrator.</p>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4">
                <h3 class="font-semibold text-slate-700 text-sm mb-3">Change Password</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">New Password <span class="text-slate-400 font-normal">(leave blank to keep current)</span></label>
                        <input type="password" name="password" minlength="8" placeholder="Minimum 8 characters"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirm" minlength="8" placeholder="Repeat new password"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg px-5 py-2.5 text-sm transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Account info -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700 text-sm">Account Information</h3>
        </div>
        <dl class="divide-y divide-slate-100">
            <?php foreach (['Account created' => fDate($user['created_at']), 'Last updated' => fDate($user['updated_at']), 'Account status' => $user['is_active'] ? 'Active' : 'Inactive'] as $label => $value): ?>
            <div class="flex px-5 py-3 text-sm">
                <dt class="w-40 text-slate-500 flex-shrink-0"><?= $label ?></dt>
                <dd class="text-slate-800 font-medium"><?= $value ?></dd>
            </div>
            <?php endforeach; ?>
        </dl>
    </div>

</div>

<script>
// Client-side password confirmation check
document.querySelector('form').addEventListener('submit', function(e) {
    const pw  = this.querySelector('[name="password"]').value;
    const cpw = this.querySelector('[name="password_confirm"]').value;
    if (pw && pw !== cpw) {
        e.preventDefault();
        alert('Passwords do not match.');
    }
});
</script>

<?php
$content = ob_get_clean();
$title   = 'My Profile';
require VIEW_PATH . '/layouts/app.php';
?>
