<?php
declare(strict_types=1);

require 'src/Console.php';

use ricardoboss\Console;

Console::logLevel();
Console::debug("This is a debug message.");
Console::info("This is an info message.");
Console::notice("This is a notice message.");
Console::warn("This is a warning message.");
Console::error("This is an error message.");
Console::critical("This is a critical message.");
Console::alert("This is an alert message.");
Console::emergency("This is an emergency message.");

Console::info("This is a link: %s", Console::link("https://github.com/ricardoboss"));
Console::info("%s %s %s %s %s", Console::green("You can"), Console::cyanBack("mix"), Console::magenta("and"), Console::gray("match"), Console::apply("the colors", 'light_blue', 'yellow'));
Console::info("%s %s", Console::underscore("and style"), Console::bold("the text"));
Console::info(Console::apply("You can " . Console::reset("reset") . " all styles within", 'cyan', null, ['bold', 'underscore']));

Console::colors(false);
Console::warn("Now colors are turned off.");
Console::alert("This doesn't look alerting enough...");

Console::timestampFormat("d.m. H:i:s.v");
Console::critical("What year is it?!");
Console::timestampFormat("H:i:s");
Console::notice("You can change the timestamp format during runtime.");

Console::timestamps(false);
Console::warn("Timestamps are now disabled!");

Console::timestampFormat();
Console::colors();
Console::timestamps();
Console::notice("Call the methods with no format to restore the defaults.");

Console::info("This is a table:");
foreach (Console::table([
	[Console::yellow('Johnson'), '25', 'john@acme.com'],
	[Console::yellow('Jane'), '24', 'jane@example.com'],
], compact: true, borderColor: 'green', headers: ['name', 'age', 'email']) as $line) {
	Console::info($line);
}

Console::emergency("This has a really long tag");
Console::logLevelTag('info', 'INFORMATION');
Console::info("You can also adjust the tag for each log level.");
Console::info("The padding on the right gets adjusted automatically.");
Console::logLevelTag('emergency', 'EMERG');
Console::emergency("Now the tag is shorter.");
Console::logLevelTag('warning', 'WARN');
Console::logLevelTag('critical', 'CRIT');
Console::logLevelTag('notice', 'NOTI');
Console::logLevelTag('info', 'INFO');
Console::info("And even shorter");
Console::emergency("This is fine.");
