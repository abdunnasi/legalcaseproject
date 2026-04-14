<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Access Denied</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'IBM Plex Sans', sans-serif; }</style>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 rounded-2xl bg-red-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m2-5V9m0 0V7m0 2h2M12 9H10m9.364-4.364A9 9 0 115.636 18.364 9 9 0 0121.364 4.636z"/>
            </svg>
        </div>
        <h1 class="text-5xl font-bold text-slate-800 mb-2">403</h1>
        <h2 class="text-xl font-semibold text-slate-700 mb-3">Access Denied</h2>
        <p class="text-slate-500 mb-8">You don't have permission to access this page. Contact your administrator if you believe this is an error.</p>
        <div class="flex gap-3 justify-center">
            <a href="javascript:history.back()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">Go Back</a>
            <a href="<?= APP_URL ?>/dashboard" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">Dashboard</a>
        </div>
    </div>
</body>
</html>
