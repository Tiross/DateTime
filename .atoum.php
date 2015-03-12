<?php

use mageekguy\atoum;

$report = $script->addDefaultReport();

// This will add the atoum logo before each run.
$report->addField(new atoum\report\fields\runner\atoum\logo());

// This will add a green or red logo after each run depending on its status.
$report->addField(new atoum\report\fields\runner\result\logo());

$testGenerator = new atoum\test\generator();
$testGenerator->setTestClassesDirectory(__DIR__ . '/tests');
