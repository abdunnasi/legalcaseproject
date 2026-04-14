<?php

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base   = parse_url(APP_URL, PHP_URL_PATH) ?? ''; // null-safe: APP_URL may have no path
$uri    = '/' . trim(substr($uri, strlen($base)), '/');
$method = $_SERVER['REQUEST_METHOD'];

// ─── Auth Routes ─────────────────────────────────────────────────
if ($uri === '/auth/login' || $uri === '/') {
    if ($method === 'POST') AuthController::login();
    else                    AuthController::showLogin();
    exit;
}
if ($uri === '/auth/logout') {
    AuthController::logout();
    exit;
}

// ─── Protected Routes (require login) ───────────────────────────
AuthMiddleware::handle();

$m = []; // capture group container for preg_match calls

// ── Dashboard ────────────────────────────────────────────────────
if ($uri === '/dashboard') {
    DashboardController::index();

    // ── Cases ────────────────────────────────────────────────────────
} elseif ($uri === '/cases' && $method === 'GET') {
    CaseController::index();
} elseif ($uri === '/cases/create' && $method === 'GET') {
    CaseController::create();
} elseif ($uri === '/cases/store' && $method === 'POST') {
    CaseController::store();
} elseif (preg_match('#^/cases/(\d+)$#', $uri, $m) && $method === 'GET') {
    CaseController::show((int)$m[1]);
} elseif (preg_match('#^/cases/(\d+)/edit$#', $uri, $m) && $method === 'GET') {
    CaseController::edit((int)$m[1]);
} elseif (preg_match('#^/cases/(\d+)/update$#', $uri, $m) && $method === 'POST') {
    CaseController::update((int)$m[1]);
} elseif (preg_match('#^/cases/(\d+)/delete$#', $uri, $m) && $method === 'POST') {
    CaseController::delete((int)$m[1]);
} elseif (preg_match('#^/cases/(\d+)/note$#', $uri, $m) && $method === 'POST') {
    CaseController::addNote((int)$m[1]);

    // ── Clients ──────────────────────────────────────────────────────
} elseif ($uri === '/clients' && $method === 'GET') {
    ClientController::index();
} elseif ($uri === '/clients/create' && $method === 'GET') {
    ClientController::create();
} elseif ($uri === '/clients/store' && $method === 'POST') {
    ClientController::store();
} elseif (preg_match('#^/clients/(\d+)$#', $uri, $m) && $method === 'GET') {
    ClientController::show((int)$m[1]);
} elseif (preg_match('#^/clients/(\d+)/edit$#', $uri, $m) && $method === 'GET') {
    ClientController::edit((int)$m[1]);
} elseif (preg_match('#^/clients/(\d+)/update$#', $uri, $m) && $method === 'POST') {
    ClientController::update((int)$m[1]);

    // ── Documents ────────────────────────────────────────────────────
} elseif ($uri === '/documents/upload' && $method === 'POST') {
    DocumentController::upload();
} elseif (preg_match('#^/documents/(\d+)/download$#', $uri, $m)) {
    DocumentController::download((int)$m[1]);
} elseif (preg_match('#^/documents/(\d+)/delete$#', $uri, $m) && $method === 'POST') {
    DocumentController::delete((int)$m[1]);

    // ── Hearings / Schedule ──────────────────────────────────────────
} elseif ($uri === '/hearings' && $method === 'GET') {
    HearingController::index();
} elseif ($uri === '/hearings/create' && $method === 'GET') {
    HearingController::create();
} elseif ($uri === '/hearings/store' && $method === 'POST') {
    HearingController::store();
} elseif (preg_match('#^/hearings/(\d+)/edit$#', $uri, $m) && $method === 'GET') {
    HearingController::edit((int)$m[1]);
} elseif (preg_match('#^/hearings/(\d+)/update$#', $uri, $m) && $method === 'POST') {
    HearingController::update((int)$m[1]);

    // ── Users (admin only) ───────────────────────────────────────────
} elseif ($uri === '/users' && $method === 'GET') {
    UserController::index();
} elseif ($uri === '/users/create' && $method === 'GET') {
    UserController::create();
} elseif ($uri === '/users/store' && $method === 'POST') {
    UserController::store();
} elseif (preg_match('#^/users/(\d+)/edit$#', $uri, $m) && $method === 'GET') {
    UserController::edit((int)$m[1]);
} elseif (preg_match('#^/users/(\d+)/update$#', $uri, $m) && $method === 'POST') {
    UserController::update((int)$m[1]);
} elseif (preg_match('#^/users/(\d+)/toggle$#', $uri, $m) && $method === 'POST') {
    UserController::toggle((int)$m[1]);

    // ── Reports ──────────────────────────────────────────────────────
} elseif ($uri === '/reports') {
    ReportController::index();
} elseif ($uri === '/reports/cases') {
    ReportController::cases();
} elseif ($uri === '/reports/hearings') {
    ReportController::hearings();

    // ── Notifications ────────────────────────────────────────────────
} elseif ($uri === '/notifications/read' && $method === 'POST') {
    NotificationController::markRead();

    // ── Profile ──────────────────────────────────────────────────────
} elseif ($uri === '/profile') {
    ProfileController::index();
} elseif ($uri === '/profile/update' && $method === 'POST') {
    ProfileController::update();

    // ── 404 ──────────────────────────────────────────────────────────
} else {
    http_response_code(404);
    view('errors.404');
}
