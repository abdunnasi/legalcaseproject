<?php ob_start(); ?>

<div class="space-y-4">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <p class="text-slate-500 text-sm"><?= count($users) ?> system user<?= count($users) != 1 ? 's' : '' ?></p>
        <a href="<?= APP_URL ?>/users/create" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add User
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">User</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Role</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Phone</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Status</th>
                    <th class="text-left px-4 py-3 font-semibold text-slate-600 text-xs uppercase tracking-wider">Joined</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($users as $u): ?>
                <tr class="hover:bg-slate-50 transition-colors <?= !$u['is_active'] ? 'opacity-50' : '' ?>">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0
                                <?= ['admin'=>'bg-red-100 text-red-700','lawyer'=>'bg-blue-100 text-blue-700','clerk'=>'bg-green-100 text-green-700','staff'=>'bg-slate-100 text-slate-700'][$u['role']] ?? 'bg-slate-100 text-slate-700' ?>">
                                <?= strtoupper(substr($u['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="font-medium text-slate-800"><?= clean($u['name']) ?></div>
                                <div class="text-slate-400 text-xs"><?= clean($u['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3.5">
                        <?php
                        $roleColors = ['admin'=>'bg-red-100 text-red-700','lawyer'=>'bg-blue-100 text-blue-700','clerk'=>'bg-green-100 text-green-700','staff'=>'bg-slate-100 text-slate-600'];
                        $rc = $roleColors[$u['role']] ?? 'bg-slate-100 text-slate-600';
                        ?>
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium <?= $rc ?> capitalize"><?= $u['role'] ?></span>
                    </td>
                    <td class="px-4 py-3.5 text-slate-500 text-sm"><?= clean($u['phone'] ?: '—') ?></td>
                    <td class="px-4 py-3.5">
                        <?php if ($u['is_active']): ?>
                        <span class="inline-flex items-center gap-1 text-xs text-green-700 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span> Active
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1 text-xs text-slate-400 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 inline-block"></span> Inactive
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3.5 text-slate-400 text-xs"><?= fDate($u['created_at']) ?></td>
                    <td class="px-4 py-3.5">
                        <div class="flex items-center gap-3">
                            <a href="<?= APP_URL ?>/users/<?= $u['id'] ?>/edit" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>
                            <?php if ($u['id'] !== auth()['id']): ?>
                            <form method="POST" action="<?= APP_URL ?>/users/<?= $u['id'] ?>/toggle" class="inline">
                                <?= csrf_field() ?>
                                <button class="text-xs font-medium <?= $u['is_active'] ? 'text-red-500 hover:text-red-700' : 'text-green-600 hover:text-green-800' ?>">
                                    <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
$title   = 'User Management';
require VIEW_PATH . '/layouts/app.php';
?>
