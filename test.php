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

Console::timestamps(false);

Console::warn("Timestamps are now disabled!");
Console::info("Wanna know which time it is?");

Console::timestamps(true);

Console::notice("They are back now.");
Console::info("This is a link: %s", Console::link("https://github.com/ricardoboss"));
Console::info("%s %s %s %s", Console::green("You can"), Console::cyanBack("mix"), Console::magenta("and match"), Console::apply("the colors", 'black', 'yellow'));
Console::info("%s %s", Console::underscore("and style"), Console::bold("the text"));

Console::warn("The streams get closed automatically when the runtime shuts down.");
