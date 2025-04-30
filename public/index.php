<?php

use App\Kernel;
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations
=======
=======
<<<<<<< HEAD
=======
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
<<<<<<< HEAD
<<<<<<< HEAD
=======
<<<<<<< HEAD
>>>>>>> origin/gestionOffre
=======
>>>>>>> origin/gestionOffre
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

// Inclure l’autoloader de smalot/pdf-parser avec une vérification
$autoloaderPath = __DIR__ . '/../vendor/smalot/pdf-parser/alt_autoload.php';
if (file_exists($autoloaderPath)) {
    require_once $autoloaderPath;
} else {
    throw new \RuntimeException('Autoloader for smalot/pdf-parser not found at ' . $autoloaderPath . '. Please install smalot/pdf-parser manually.');
}

require __DIR__.'/../vendor/autoload.php';

// The check is to ensure we don't use .env in production
if (!isset($_SERVER['APP_ENV']) && !isset($_ENV['APP_ENV'])) {
    if (!class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }
    // Charger .env et .env.local (si ce dernier existe)
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__.'/../.env');
    $envLocalPath = __DIR__.'/../.env.local';
    if (file_exists($envLocalPath)) {
        $dotenv->load($envLocalPath);
    }
}

$env = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'dev';
$debug = (bool) ($_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? ('prod' !== $env));

if ($debug) {
    umask(0000);

    Debug::enable();
}

$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
<<<<<<< HEAD
<<<<<<< HEAD
$kernel->terminate($request, $response);
=======
$kernel->terminate($request, $response);
>>>>>>> origin/gestionOffre
<<<<<<< HEAD
=======
>>>>>>> origin/gestionUserReclamation
=======
>>>>>>> origin/gestions-formations
=======
>>>>>>> origin/gestionOffre
=======
$kernel->terminate($request, $response);
>>>>>>> origin/gestionOffre
>>>>>>> origin/gestionOffre
>>>>>>> 76179cffc7b72c06d034d39a661eec43d0ee6421
