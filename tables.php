<?php
declare(strict_types=1);

use ricardoboss\Console;

require_once 'src/Console.php';

$simple = [
	['col1' => 'row1-1', 'col2' => 'row1-2', 'col3' => '', 'col4' => ''],
	['col1' => 'row2-1', 'col2' => 'row2-2', 'col3' => '', 'col4' => 'row2-4'],
];

$configs = [
	[$simple, 'ascii' => false, 'compact' => false, 'noOuterBorder' => false, 'noInnerBorder' => false],
	[$simple, 'ascii' => true, 'compact' => false, 'noOuterBorder' => false, 'noInnerBorder' => false],
	[$simple, 'ascii' => false, 'compact' => true, 'noOuterBorder' => false, 'noInnerBorder' => false],
	[$simple, 'ascii' => true, 'compact' => true, 'noOuterBorder' => false, 'noInnerBorder' => false],
	[$simple, 'ascii' => false, 'compact' => false, 'noOuterBorder' => true, 'noInnerBorder' => false],
	[$simple, 'ascii' => true, 'compact' => false, 'noOuterBorder' => true, 'noInnerBorder' => false],
	[$simple, 'ascii' => false, 'compact' => true, 'noOuterBorder' => true, 'noInnerBorder' => false],
	[$simple, 'ascii' => true, 'compact' => true, 'noOuterBorder' => true, 'noInnerBorder' => false],
	[$simple, 'ascii' => false, 'compact' => false, 'noOuterBorder' => false, 'noInnerBorder' => true],
	[$simple, 'ascii' => true, 'compact' => false, 'noOuterBorder' => false, 'noInnerBorder' => true],
	[$simple, 'ascii' => false, 'compact' => true, 'noOuterBorder' => false, 'noInnerBorder' => true],
	[$simple, 'ascii' => true, 'compact' => true, 'noOuterBorder' => false, 'noInnerBorder' => true],
	[$simple, 'ascii' => false, 'compact' => false, 'noOuterBorder' => true, 'noInnerBorder' => true],
	[$simple, 'headers' => ['First Column', 'Second Column', 'Third Column', 'Fourth Column']],
	[$simple, 'noHeaders' => true],
];

foreach ($configs as $i => $params) {
	$namedParams = implode(', ', array_keys(array_filter($params, static fn ($v, $k) => $v && is_string($k), ARRAY_FILTER_USE_BOTH)));

	Console::info("");
	Console::info("Table config #$i: $namedParams");
	Console::info("");

	foreach (call_user_func_array([Console::class, 'table'], $params) as $line) {
		Console::info($line);
	}
}
