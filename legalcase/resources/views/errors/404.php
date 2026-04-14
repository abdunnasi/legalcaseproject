<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'IBM Plex Sans', sans-serif; }</style>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
    <div class="text-center max-w-md">
        <div class="w-20 h-20 rounded-2xl bg-blue-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h1 class="text-5xl font-bold text-slate-800 mb-2">404</h1>
        <h2 class="text-xl font-semibold text-slate-700 mb-3">Page Not Found</h2>
        <p class="text-slate-500 mb-8">The page you're looking for doesn't exist or has been moved.</p>
        <div class="flex gap-3 justify-center">
            <a href="javascript:history.back()" class="bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">Go Back</a>
            <a href="<?= defined('APP_URL') ? APP_URL : '/' ?>/dashboard" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-5 py-2.5 text-sm font-medium transition-colors">Dashboard</a>
        </div>
    </div>
</body>
</html>
