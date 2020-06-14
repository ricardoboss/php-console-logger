<?php
declare(strict_types=1);

require 'src/Console.php';

use ricardoboss\Console;

Console::open();

Console::debug("This is a debug message.");
Console::info("This is an info message.");
Console::notice("This is a notice message.");
Console::warn("This is a warning message.");
Console::error("This is an error message.");
Console::critical("This is a critical message.");
Console::alert("This is an alert message.");
Console::emergency("This is an emergency message.");

Console::info("This is a link: %s", Console::link("https://github.com/ricardoboss"));
Console::info("%s %s %s %s", Console::green("You can"), Console::cyanBack("mix"), Console::magenta("and match"), Console::apply("the colors", 'black', 'yellow'));
Console::info("%s %s", Console::underscore("and style"), Console::bold("the text"));

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

Console::warn("The streams get closed automatically when the runtime shuts down.");
