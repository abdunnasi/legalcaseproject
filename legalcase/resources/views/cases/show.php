<?php ob_start(); ?>

<div class="space-y-4">
    <!-- Header -->
    <div class="bg-white rounded-xl border border-slate-200 px-6 py-5">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="font-mono text-sm font-bold text-blue-700 bg-blue-50 border border-blue-200 rounded-lg px-3 py-1"><?= clean($case['case_number']) ?></span>
                    <?= statusBadge($case['status']) ?>
                    <span class="text-slate-400 text-sm capitalize"><?= $case['case_type'] ?></span>
                </div>
                <h2 class="text-xl font-bold text-slate-800 mt-2"><?= clean($case['title']) ?></h2>
                <div class="flex flex-wrap gap-4 mt-2 text-sm text-slate-500">
                    <span>👤 <?= clean($case['client_name'] ?? 'No client') ?></span>
                    <span>⚖️ <?= clean($case['lawyer_name'] ?? 'Unassigned') ?></span>
                    <span>🏛️ <?= clean($case['court_name'] ?? '—') ?></span>
                    <span>📅 Filed: <?= fDate($case['filing_date']) ?></span>
                </div>
                <?php if ($case['description']): ?>
                <p class="text-slate-600 text-sm mt-3 max-w-2xl"><?= nl2br(clean($case['description'])) ?></p>
                <?php endif; ?>
            </div>
            <?php if (hasRole(['admin','lawyer'])): ?>
            <div class="flex gap-2">
                <a href="<?= APP_URL ?>/cases/<?= $case['id'] ?>/edit" class="bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg px-4 py-2 text-sm font-medium transition-colors">Edit Case</a>
                <?php if (hasRole('admin')): ?>
                <form method="POST" action="<?= APP_URL ?>/cases/<?= $case['id'] ?>/delete" onsubmit="return confirm('Delete this case? This cannot be undone.')">
                    <?= csrf_field() ?>
                    <button class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 rounded-lg px-4 py-2 text-sm font-medium transition-colors">Delete</button>
                </form>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabs -->
    <div x-data="{ tab: 'documents' }" class="space-y-4">
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <!-- Tab bar -->
            <div class="flex border-b border-slate-200 overflow-x-auto" id="tabs">
                <?php
                $tabs = [
                    ['documents', 'Documents (' . count($documents) . ')'],
                    ['hearings',  'Hearings ('  . count($hearings)  . ')'],
                    ['notes',     'Case Notes (' . count($notes)    . ')'],
                ];
                foreach ($tabs as [$id, $label]):
                ?>
                <button onclick="showTab('<?= $id ?>')" id="tab-<?= $id ?>"
                    class="tab-btn px-5 py-3.5 text-sm font-medium border-b-2 transition-colors whitespace-nowrap">
                    <?= $label ?>
                </button>
                <?php endforeach; ?>
            </div>

            <!-- Documents tab -->
            <div id="panel-documents" class="tab-panel">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
                    <h3 class="font-semibold text-slate-700 text-sm">Case Documents</h3>
                    <form method="POST" action="<?= APP_URL ?>/documents/upload" enctype="multipart/form-data" class="flex items-end gap-2 flex-wrap">
                        <?= csrf_field() ?>
                        <input type="hidden" name="case_id" value="<?= $case['id'] ?>">
                        <select name="doc_type" class="border border-slate-300 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <?php foreach (['evidence'=>'Evidence','affidavit'=>'Affidavit','court_ruling'=>'Court Ruling','contract'=>'Contract','petition'=>'Petition','other'=>'Other'] as $v => $l): ?>
                            <option value="<?= $v ?>"><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="title" placeholder="Document title" class="border border-slate-300 rounded-lg px-2 py-1.5 text-xs w-36 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="file" name="document" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="text-xs text-slate-500">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-3 py-1.5 text-xs font-medium transition-colors">Upload</button>
                    </form>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php if ($documents): foreach ($documents as $d): ?>
                    <div class="flex items-center gap-4 px-5 py-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-800 truncate"><?= clean($d['title']) ?></div>
                            <div class="text-xs text-slate-400"><?= ucwords(str_replace('_',' ',$d['doc_type'])) ?> · <?= clean($d['uploader'] ?? '—') ?> · <?= fDate($d['created_at']) ?></div>
                        </div>
                        <div class="flex gap-2">
                            <a href="<?= APP_URL ?>/documents/<?= $d['id'] ?>/download" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Download</a>
                            <?php if (hasRole(['admin','lawyer'])): ?>
                            <form method="POST" action="<?= APP_URL ?>/documents/<?= $d['id'] ?>/delete" onsubmit="return confirm('Delete this document?')" class="inline">
                                <?= csrf_field() ?>
                                <button class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <div class="px-5 py-8 text-center text-slate-400 text-sm">No documents uploaded yet.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Hearings tab -->
            <div id="panel-hearings" class="tab-panel hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between flex-wrap gap-3">
                    <h3 class="font-semibold text-slate-700 text-sm">Scheduled Hearings</h3>
                    <a href="<?= APP_URL ?>/hearings/create?case_id=<?= $case['id'] ?>" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-3 py-1.5 text-xs font-medium transition-colors">+ Schedule Hearing</a>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php if ($hearings): foreach ($hearings as $h): ?>
                    <div class="flex items-start gap-4 px-5 py-3.5">
                        <div class="bg-blue-50 border border-blue-200 rounded-xl px-3 py-2 text-center flex-shrink-0">
                            <div class="text-blue-700 font-bold text-base leading-tight"><?= date('d', strtotime($h['hearing_date'])) ?></div>
                            <div class="text-blue-500 text-xs"><?= date('M Y', strtotime($h['hearing_date'])) ?></div>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-slate-800"><?= clean($h['title']) ?></div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                <?php if ($h['hearing_time']): ?><?= date('H:i', strtotime($h['hearing_time'])) ?> · <?php endif; ?>
                                <?= clean($h['court_name'] ?: $case['court_name'] ?: '—') ?>
                                <?php if ($h['court_room']): ?> · Room <?= clean($h['court_room']) ?><?php endif; ?>
                            </div>
                        </div>
                        <?= statusBadge($h['status']) ?>
                    </div>
                    <?php endforeach; else: ?>
                    <div class="px-5 py-8 text-center text-slate-400 text-sm">No hearings scheduled.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Notes tab -->
            <div id="panel-notes" class="tab-panel hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-700 text-sm mb-3">Add Note / Progress Update</h3>
                    <form method="POST" action="<?= APP_URL ?>/cases/<?= $case['id'] ?>/note" class="flex gap-2">
                        <?= csrf_field() ?>
                        <textarea name="note" rows="2" required placeholder="Add a case note or progress update..."
                            class="flex-1 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                        <button class="self-end bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-4 py-2 text-sm font-medium transition-colors">Post</button>
                    </form>
                </div>
                <div class="divide-y divide-slate-100">
                    <?php if ($notes): foreach ($notes as $n): ?>
                    <div class="flex gap-3 px-5 py-3.5">
                        <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 text-xs font-bold flex-shrink-0 mt-0.5">
                            <?= strtoupper(substr($n['author'] ?? '?', 0, 1)) ?>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500"><span class="font-medium text-slate-700"><?= clean($n['author'] ?? 'Unknown') ?></span> · <?= fDateTime($n['created_at']) ?></div>
                            <p class="text-sm text-slate-700 mt-1"><?= nl2br(clean($n['note'])) ?></p>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <div class="px-5 py-8 text-center text-slate-400 text-sm">No notes yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(id) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('border-blue-600','text-blue-700');
        b.classList.add('border-transparent','text-slate-500','hover:text-slate-700');
    });
    document.getElementById('panel-' + id).classList.remove('hidden');
    const btn = document.getElementById('tab-' + id);
    btn.classList.add('border-blue-600','text-blue-700');
    btn.classList.remove('border-transparent','text-slate-500','hover:text-slate-700');
}
showTab('documents');
</script>

<?php
$content = ob_get_clean();
$title   = 'Case: ' . $case['case_number'];
require VIEW_PATH . '/layouts/app.php';
?>
