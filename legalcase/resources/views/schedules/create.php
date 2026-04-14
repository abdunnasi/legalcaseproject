<?php ob_start(); ?>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-800">Schedule a Hearing</h2>
            <p class="text-slate-500 text-sm mt-0.5">Set a court date for a registered case.</p>
        </div>
        <form method="POST" action="<?= APP_URL ?>/hearings/store" class="px-6 py-5 space-y-5">
            <?= csrf_field() ?>
            <?php require VIEW_PATH . '/schedules/_form.php'; ?>
            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg px-5 py-2.5 text-sm transition-colors">Schedule Hearing</button>
                <a href="<?= APP_URL ?>/hearings" class="text-slate-500 hover:text-slate-700 text-sm px-3 py-2.5">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$title   = 'Schedule Hearing';
require VIEW_PATH . '/layouts/app.php';
?>
