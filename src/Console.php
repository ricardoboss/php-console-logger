<?php
declare(strict_types=1);

namespace ricardoboss;

use BadMethodCallException;
use DateTime;
use RuntimeException;
use Throwable;
use function count;

/**
 * Class Console
 *
 * @method static string black(string $text)
 * @method static string red(string $text)
 * @method static string green(string $text)
 * @method static string yellow(string $text)
 * @method static string blue(string $text)
 * @method static string magenta(string $text)
 * @method static string cyan(string $text)
 * @method static string white(string $text)
 * @method static string default(string $text)
 * @method static string blackBack(string $text)
 * @method static string redBack(string $text)
 * @method static string greenBack(string $text)
 * @method static string yellowBack(string $text)
 * @method static string blueBack(string $text)
 * @method static string magentaBack(string $text)
 * @method static string cyanBack(string $text)
 * @method static string whiteBack(string $text)
 * @method static string defaultBack(string $text)
 * @method static string bold(string $text)
 * @method static string underscore(string $text)
 * @method static string blink(string $text)
 * @method static string reverse(string $text)
 * @method static string conceal(string $text)
 */
class Console
{
	/** @var resource|null */
	private static $output_stream;

	/** @var resource|null */
	private static $error_stream;

	private static bool $timestamps = true;
	private static bool $colors = true;
	private static string $timestamp_format = "d.m.y H:i:s.v";

	private static array $availableForegroundColors = [
		'black' => ['set' => 30, 'unset' => 39],
		'red' => ['set' => 31, 'unset' => 39],
		'green' => ['set' => 32, 'unset' => 39],
		'yellow' => ['set' => 33, 'unset' => 39],
		'blue' => ['set' => 34, 'unset' => 39],
		'magenta' => ['set' => 35, 'unset' => 39],
		'cyan' => ['set' => 36, 'unset' => 39],
		'white' => ['set' => 37, 'unset' => 39],
		'default' => ['set' => 39, 'unset' => 39],
	];

	private static array $availableBackgroundColors = [
		'black' => ['set' => 40, 'unset' => 49],
		'red' => ['set' => 41, 'unset' => 49],
		'green' => ['set' => 42, 'unset' => 49],
		'yellow' => ['set' => 43, 'unset' => 49],
		'blue' => ['set' => 44, 'unset' => 49],
		'magenta' => ['set' => 45, 'unset' => 49],
		'cyan' => ['set' => 46, 'unset' => 49],
		'white' => ['set' => 47, 'unset' => 49],
		'default' => ['set' => 49, 'unset' => 49],
	];

	private static array $availableOptions = [
		'bold' => ['set' => 1, 'unset' => 22],
		'underscore' => ['set' => 4, 'unset' => 24],
		'blink' => ['set' => 5, 'unset' => 25],
		'reverse' => ['set' => 7, 'unset' => 27],
		'conceal' => ['set' => 8, 'unset' => 28],
	];

	public static function isOpen(): bool
	{
		return self::$output_stream !== null && self::$error_stream !== null;
	}

	public static function open(): void
	{
		try {
			self::$output_stream = fopen('php://stdout', 'w');
			self::$error_stream = fopen('php://stderr', 'w');

			register_shutdown_function(function () {
				if (self::$output_stream !== false)
					try {
						fclose(self::$output_stream);
					} catch (Throwable $ignored) {
					}

				if (self::$error_stream !== false)
					try {
						fclose(self::$error_stream);
					} catch (Throwable $ignored) {
					}
			});
		} catch (Throwable $throwable) {
			throw new RuntimeException("Unable to open output streams!", 0, $throwable);
		}
	}

	public static function timestamps(bool $enable = true): void
	{
		self::$timestamps = $enable;
	}

	public static function colors(bool $enable = true): void
	{
		self::$colors = $enable;
	}

	public static function timestampFormat(string $format = "d.m.y H:i:s.v"): void
	{
		self::$timestamp_format = $format;
	}

	public static function link(string $link): string
	{
		return Console::cyan(Console::underscore($link));
	}

	public static function debug(string $message, ...$args): void
	{
		self::writeln("[DEBUG ] " . vsprintf($message, $args));
	}

	public static function info(string $message, ...$args): void
	{
		self::writeln("[INFO  ] " . vsprintf($message, $args));
	}

	public static function notice(string $message, ...$args): void
	{
		self::writeln("[NOTICE] " . vsprintf($message, $args), 'blue');
	}

	public static function warn(string $message, ...$args): void
	{
		self::writeln("[WARN  ] " . vsprintf($message, $args), 'yellow');
	}

	public static function error(string $message, ...$args): void
	{
		self::writeln("[ERROR ] " . vsprintf($message, $args), 'red', null, [], true);
	}

	public static function critical(string $message, ...$args): void
	{
		self::writeln("[CRITIC] " . vsprintf($message, $args), 'magenta', null, ['bold'], true);
	}

	public static function alert(string $message, ...$args): void
	{
		self::writeln("[ALERT ] " . vsprintf($message, $args), null, 'yellow', ['bold'], true);
	}

	public static function emergency(string $message, ...$args): void
	{
		self::writeln("[EMERG ] " . vsprintf($message, $args), null, 'red', ['bold', 'underscore'], true);
	}

	public static function writeln(
		string $message,
		?string $foreground = null,
		?string $background = null,
		array $options = [],
		bool $error = false): void
	{
		self::write($message, $foreground, $background, $options, "\r\n", $error);
	}

	public static function write(
		string $message,
		?string $foreground = null,
		?string $background = null,
		array $options = [],
		?string $suffix = null,
		bool $error = false): void
	{
		$stream = $error ? self::$error_stream : self::$output_stream;

		if (self::$colors)
			$message = self::apply($message, $foreground, $background, $options);

		if (self::$timestamps) {
			$time = '[' . (new DateTime())->format(self::$timestamp_format) . ']';
			$message = $time . ' ' . $message;
		}

		fwrite($stream, $message . $suffix);
		fflush($stream);
	}

	public static function apply(
		string $text,
		?string $foreground = null,
		?string $background = null,
		array $options = []): string
	{
		$setCodes = [];
		$unsetCodes = [];

		if (null !== $foreground) {
			$foreground = self::$availableForegroundColors[$foreground];

			$setCodes[] = $foreground['set'];
			$unsetCodes[] = $foreground['unset'];
		}

		if (null !== $background) {
			$background = self::$availableBackgroundColors[$background];

			$setCodes[] = $background['set'];
			$unsetCodes[] = $background['unset'];
		}

		foreach ($options as $option) {
			$option = self::$availableOptions[$option];

			$setCodes[] = $option['set'];
			$unsetCodes[] = $option['unset'];
		}

		if (0 === count($setCodes)) {
			return $text;
		}

		return sprintf(
			"\033[%sm%s\033[%sm",
			implode(';', $setCodes),
			$text,
			implode(';', $unsetCodes)
		);
	}

	public static function __callStatic(string $name, array $arguments): string
	{
		$name = strtolower($name);

		if (in_array($name, array_keys(self::$availableOptions))) {
			return self::apply($arguments[0], null, null, [$name]);
		}

		$backPos = strpos(strtolower($name), 'back');
		$background = $backPos !== false;
		$color = $background ? substr($name, 0, $backPos) : $name;

		if ($background && in_array($color, array_keys(self::$availableBackgroundColors))) {
			return self::apply($arguments[0], null, $color, []);
		} elseif (in_array($color, array_keys(self::$availableForegroundColors))) {
			return self::apply($arguments[0], $color, null, []);
		}

		throw new BadMethodCallException("Method not found: $name");
	}
}
