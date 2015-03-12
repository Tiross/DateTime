<?php

use mageekguy\atoum;
use mageekguy\atoum\reports;


$report = $script->addDefaultReport();
$report->addField(new atoum\report\fields\runner\result\logo());

$dir = './build/logs';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$cloverWriter = new atoum\writers\file($dir . '/clover.xml');
$cloverReport = new atoum\reports\asynchronous\clover();
$cloverReport->addWriter($cloverWriter);

$runner->addReport($cloverReport);

$testGenerator = new atoum\test\generator();
$script->getRunner()->addTestsFromDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'tests');


$dir = './coverage';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$coverage = new atoum\report\fields\runner\coverage\html('json', $dir);
$coverage->setRootUrl('http://localhost');
$report->addField($coverage);
