<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'IBM Plex Sans', sans-serif; }</style>
</head>
<body class="h-full bg-[#0a1f3e]">

<div class="min-h-screen flex">
    <!-- Left branding panel -->
    <div class="hidden lg:flex w-1/2 flex-col justify-between p-12 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0f2744] to-[#0a1520]"></div>
        <!-- Decorative grid -->
        <div class="absolute inset-0 opacity-5" style="background-image: linear-gradient(#ffffff 1px, transparent 1px), linear-gradient(90deg, #ffffff 1px, transparent 1px); background-size: 40px 40px;"></div>

        <div class="relative z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                </div>
                <div>
                    <div class="text-white font-bold text-xl">LegalCase Pro</div>
                    <div class="text-slate-400 text-xs tracking-widest uppercase">Case Management System</div>
                </div>
            </div>
        </div>

        <div class="relative z-10">
            <blockquote class="text-2xl font-light text-white/90 leading-relaxed mb-6">
                "Justice is the constant and perpetual wish to render every one his due."
            </blockquote>
            <p class="text-slate-400 text-sm">— Justinian I</p>
        </div>

        <div class="relative z-10 grid grid-cols-3 gap-4">
            <?php foreach ([['Cases', 'Managed'], ['Clients', 'Served'], ['Hearings', 'Tracked']] as $stat): ?>
            <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                <div class="text-2xl font-bold text-white">—</div>
                <div class="text-slate-400 text-xs mt-1"><?= $stat[0] ?> <?= $stat[1] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right login form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-sm">
            <!-- Mobile logo -->
            <div class="lg:hidden mb-8 text-center">
                <div class="inline-flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-blue-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    </div>
                    <span class="text-white font-bold text-lg">LegalCase Pro</span>
                </div>
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-white">Welcome back</h2>
                <p class="text-slate-400 mt-1 text-sm">Sign in to your account to continue</p>
            </div>

            <?php $errFlash = flash('error'); if ($errFlash): ?>
            <div class="mb-4 flex items-center gap-2 bg-red-500/10 border border-red-500/30 text-red-400 rounded-lg px-4 py-3 text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <?= clean($errFlash['message']) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= APP_URL ?>/auth/login" class="space-y-4">
                <?= csrf_field() ?>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Email address</label>
                    <input type="email" name="email" required autocomplete="email"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all"
                        placeholder="you@lawfirm.ug">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1.5">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="pw" required autocomplete="current-password"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all pr-10"
                            placeholder="••••••••">
                        <button type="button" onclick="const f=document.getElementById('pw');f.type=f.type==='password'?'text':'password';"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg py-2.5 text-sm transition-colors mt-2">
                    Sign in to system
                </button>
            </form>

            <div class="mt-8 p-4 bg-white/5 rounded-xl border border-white/10">
                <p class="text-slate-500 text-xs font-medium mb-2 uppercase tracking-wider">Demo Credentials</p>
                <div class="space-y-1 text-xs text-slate-400">
                    <div class="flex justify-between"><span>Admin:</span><span class="font-mono text-slate-300">admin@legalcase.ug</span></div>
                    <div class="flex justify-between"><span>Lawyer:</span><span class="font-mono text-slate-300">lawyer@legalcase.ug</span></div>
                    <div class="flex justify-between"><span>Password:</span><span class="font-mono text-slate-300">password</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
