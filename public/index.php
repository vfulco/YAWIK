<?php
/**
 * Cross Applicant Management
 *
 * @category Cam
 * @package Public
 * @copyright 2013 Cross Solution <http://cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @license GPLv3
 */
 
ini_set('display_errors', true);
ini_set('error_reporting', E_ALL | E_STRICT);
/*
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
// Composer autoloading
if (file_exists('vendor/autoload.php')) {
    $loader = include 'vendor/autoload.php';
} else {
    throw new \RuntimeException('Could not initialize autoloading.');
}
    
// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
