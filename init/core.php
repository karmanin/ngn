<?php

if (!defined('NGN_PATH')) die('NGN_PATH not defined'); // @LibStorageRemove
define('LIB_PATH', NGN_PATH . '/lib');                  // @LibStorageRemove
require_once LIB_PATH.'/core/common.func.php';

setConstant('VENDORS_PATH', NGN_PATH.'/vendors'); // @LibStorageRemove

define('CHARSET', 'UTF-8');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_general_ci');

require_once LIB_PATH.'/core/R.class.php'; // Registry Class
require_once LIB_PATH.'/core/LogWriter.class.php';
require_once LIB_PATH.'/core/Err.class.php';
require_once LIB_PATH.'/core/misc.func.php';
require_once LIB_PATH.'/core/Dir.class.php'; // Directory processing functions
require_once LIB_PATH.'/core/Arr.class.php'; // Array processing functions
require_once LIB_PATH.'/core/File.class.php'; // File processing functions
require_once LIB_PATH.'/core/Misc.class.php'; // Miscellaneous functions
require_once LIB_PATH.'/core/Lib.class.php'; // Librarys, classes
require_once LIB_PATH.'/core/O.class.php';
require_once LIB_PATH.'/core/Ngn.class.php';

date_default_timezone_set('Europe/Moscow');

// Важно! До установки Lib::$isCache = true никаких обращений к классам
// без предварительного подключения быть не должно
spl_autoload_register(['Lib', 'required']);         // @LibStorageRemove

Err::$show = true;

if (!defined('VENDORS_PATH')) die('VENDORS_PATH not defined (core/init)'); // @LibStorageRemove
if (!file_exists(VENDORS_PATH)) die('Folder "'.VENDORS_PATH.'" does not exists (core/init)'); // @LibStorageRemove

// Здесь ищем сторонние библиотеки
//define('INCL_PATH_DELIMITER', getOS() == 'win' ? ';' : ':');
set_include_path(VENDORS_PATH.PATH_SEPARATOR.get_include_path());  // @LibStorageRemove

set_exception_handler(['Err', 'exceptionHandler']);
set_error_handler(['Err', 'errorHandler']);
register_shutdown_function(['Err', 'shutdownHandler']);

// ------------------- config ------------------

/**
 * Каталог с логами
 */
define('LOGS_DIR', 'logs');

/**
 * Каталог с данными
 */
define('DATA_DIR', 'data');

/**
 * Каталог для загружаеммых на сервер файлов
 */
define('UPLOAD_DIR', 'u');

// ------------------ ngn-env -----------------

setConstant('NGN_ENV_PATH', dirname(NGN_PATH));

// ---------------- core constants -----------------

require NGN_PATH.'/config/constants/core.php';

