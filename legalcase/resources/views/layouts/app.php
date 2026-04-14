<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> — <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['IBM Plex Sans', 'sans-serif'],
                        mono: ['IBM Plex Mono', 'monospace']
                    },
                    colors: {
                        brand: {
                            DEFAULT: '#1a3c5e',
                            light: '#2563a8',
                            muted: '#e8eff8'
                        },
                        surface: {
                            DEFAULT: '#f8fafc',
                            card: '#ffffff',
                            border: '#e2e8f0'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'IBM Plex Sans', sans-serif;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            color: #cbd5e1;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.10);
            color: #fff;
        }

        .sidebar-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .sidebar-link svg {
            width: 1rem;
            height: 1rem;
            flex-shrink: 0;
        }

        .nav-section {
            font-size: 0.7rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0 1rem;
            margin-top: 1.25rem;
            margin-bottom: 0.25rem;
            display: block;
        }
    </style>
</head>

<body class="h-full bg-slate-50">
    <div class="flex h-screen overflow-hidden">

        <!-- ── SIDEBAR ─────────────────────────────────── -->
        <aside class="w-60 flex-shrink-0 bg-[#0f2744] flex flex-col overflow-y-auto">
            <!-- Logo -->
            <div class="px-5 py-5 border-b border-white/10">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-white font-semibold text-sm leading-tight">LegalCase</div>
                        <div class="text-slate-400 text-xs">Pro</div>
                    </div>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 px-3 py-4">
                <?php $currentUri = '/' . trim(substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen(parse_url(APP_URL, PHP_URL_PATH) ?? '')), '/'); ?>

                <a href="<?= APP_URL ?>/dashboard" class="sidebar-link <?= str_starts_with($currentUri, '/dashboard') ? 'active' : '' ?>">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                <p class="nav-section">Case Work</p>

                <a href="<?= APP_URL ?>/cases" class="sidebar-link <?= str_starts_with($currentUri, '/cases') ? 'active' : '' ?>">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Cases
                </a>

                <a href="<?= APP_URL ?>/clients" class="sidebar-link <?= str_starts_with($currentUri, '/clients') ? 'active' : '' ?>">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Clients
                </a>

                <a href="<?= APP_URL ?>/hearings" class="sidebar-link <?= str_starts_with($currentUri, '/hearings') ? 'active' : '' ?>">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Hearings
                </a>

                <p class="nav-section">Analytics</p>

                <a href="<?= APP_URL ?>/reports" class="sidebar-link <?= str_starts_with($currentUri, '/reports') ? 'active' : '' ?>">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Reports
                </a>

                <?php if (hasRole('admin')): ?>
                    <p class="nav-section">Administration</p>
                    <a href="<?= APP_URL ?>/users" class="sidebar-link <?= str_starts_with($currentUri, '/users') ? 'active' : '' ?>">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        User Management
                    </a>
                <?php endif; ?>
            </nav>

            <!-- User info -->
            <div class="px-3 pb-4 border-t border-white/10 pt-4">
                <a href="<?= APP_URL ?>/profile" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-all">
                    <div class="w-8 h-8 rounded-full bg-blue-500/30 flex items-center justify-center text-blue-300 text-sm font-bold">
                        <?= strtoupper(substr(auth()['name'], 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-white text-xs font-medium truncate"><?= clean(auth()['name']) ?></div>
                        <div class="text-slate-400 text-xs capitalize"><?= auth()['role'] ?></div>
                    </div>
                </a>
                <form action="<?= APP_URL ?>/auth/logout" method="GET" class="mt-1">
                    <button class="w-full sidebar-link text-left">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- ── MAIN CONTENT ────────────────────────────── -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top bar -->
            <header class="bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between flex-shrink-0">
                <h1 class="text-slate-800 font-semibold text-base"><?= $title ?? 'Dashboard' ?></h1>
                <div class="flex items-center gap-4">
                    <!-- Notifications bell -->
                    <?php
                    $notifCount = NotificationModel::unreadCount(auth()['id']);
                    ?>
                    <div class="relative">
                        <button onclick="document.getElementById('notif-panel').classList.toggle('hidden')" class="relative p-1.5 text-slate-500 hover:text-slate-800 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <?php if ($notifCount > 0): ?>
                                <span class="absolute top-0.5 right-0.5 w-2 h-2 bg-red-500 rounded-full"></span>
                            <?php endif; ?>
                        </button>
                        <div id="notif-panel" class="hidden absolute right-0 top-9 w-80 bg-white rounded-xl shadow-xl border border-slate-200 z-50">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                                <span class="font-semibold text-sm text-slate-800">Notifications</span>
                                <?php if ($notifCount > 0): ?>
                                    <form action="<?= APP_URL ?>/notifications/read" method="POST" class="inline">
                                        <?= csrf_field() ?>
                                        <button class="text-xs text-blue-600 hover:underline">Mark all read</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <div class="max-h-72 overflow-y-auto divide-y divide-slate-100">
                                <?php
                                $notifs = NotificationModel::getForUser(auth()['id'], 8);
                                if ($notifs): foreach ($notifs as $n): ?>
                                        <div class="px-4 py-3 <?= !$n['is_read'] ? 'bg-blue-50' : '' ?>">
                                            <div class="text-xs font-medium text-slate-800"><?= clean($n['title']) ?></div>
                                            <div class="text-xs text-slate-500 mt-0.5"><?= clean($n['message']) ?></div>
                                            <div class="text-xs text-slate-400 mt-1"><?= fDateTime($n['created_at']) ?></div>
                                        </div>
                                    <?php endforeach;
                                else: ?>
                                    <div class="px-4 py-6 text-center text-slate-400 text-sm">No notifications</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <span class="text-slate-300">|</span>
                    <span class="text-sm text-slate-500"><?= fDate(date('Y-m-d')) ?></span>
                </div>
            </header>

            <!-- Flash messages -->
            <div class="px-6 pt-4">
                <?php
                $flash = flash('success');
                if ($flash): ?>
                    <div class="flex items-center gap-3 bg-green-50 text-green-800 border border-green-200 rounded-lg px-4 py-3 text-sm mb-0" id="flash-msg">
                        <svg class="w-4 h-4 flex-shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <?= clean($flash['message']) ?>
                        <button onclick="document.getElementById('flash-msg').remove()" class="ml-auto text-green-600 hover:text-green-800">✕</button>
                    </div>
                <?php endif;
                $errFlash = flash('error');
                if ($errFlash): ?>
                    <div class="flex items-center gap-3 bg-red-50 text-red-800 border border-red-200 rounded-lg px-4 py-3 text-sm mb-0" id="flash-err">
                        <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <?= clean($errFlash['message']) ?>
                        <button onclick="document.getElementById('flash-err').remove()" class="ml-auto text-red-600 hover:text-red-800">✕</button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto px-6 py-4">
                <?= $content ?? '' ?>
            </main>
        </div>
    </div>
    <script>
        // Auto-dismiss flash messages after 5s
        setTimeout(() => {
            document.getElementById('flash-msg')?.remove();
            document.getElementById('flash-err')?.remove();
        }, 5000);
        // Close notification panel on outside click
        document.addEventListener('click', (e) => {
            const panel = document.getElementById('notif-panel');
            if (panel && !e.target.closest('[onclick]') && !panel.classList.contains('hidden')) {
                panel.classList.add('hidden');
            }
        });
    </script>
</body>

</html>