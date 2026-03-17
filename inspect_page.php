<?php
require __DIR__ . '/vendor/autoload.php';

$rc = new ReflectionClass(\Filament\Pages\Page::class);
$prop = $rc->getProperty('view');
echo "Property: view\n";
echo "Static: " . ($prop->isStatic() ? 'Yes' : 'No') . "\n";
echo "Type: " . ($prop->getType() ? $prop->getType()->getName() : 'None') . "\n";

$propIcon = $rc->getProperty('navigationIcon');
echo "Property: navigationIcon\n";
echo "Static: " . ($propIcon->isStatic() ? 'Yes' : 'No') . "\n";