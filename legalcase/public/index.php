<?php

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', BASE_PATH . '/resources/views');

require_once BASE_PATH . '/config/app.php';
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/app/Helpers/helpers.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

// Load base Model first — glob() is alphabetical so CaseModel loads before Model otherwise
require_once APP_PATH . '/Models/Model.php';
require_once APP_PATH . '/Models/Models.php';
require_once APP_PATH . '/Models/CaseModel.php';

// Load controllers
require_once APP_PATH . '/Controllers/AuthController.php';
require_once APP_PATH . '/Controllers/Controllers.php';

session_start();

require_once BASE_PATH . '/routes/web.php';
