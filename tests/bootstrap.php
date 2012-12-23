<?php
/**
 * This file is part of the Asar Test Library
 *
 * (c) Wayne Duran <asartalo@projectweb.ph>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$srcPath = realpath(__DIR__ . '/../src');
$vendorPath = realpath(__DIR__ . '/../vendor');
$testDataPath = realpath(__DIR__) . DIRECTORY_SEPARATOR . 'data';
if (!file_exists($testDataPath)) {
    mkdir($testDataPath);
    mkdir($testDataPath. DIRECTORY_SEPARATOR . 'temp');
}

define('ASAR_TESTHELPER_TEMPDIRECTORY', $testDataPath);

require_once $vendorPath . '/autoload.php';