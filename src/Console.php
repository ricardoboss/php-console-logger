<?php
declare(strict_types=1);

namespace ricardoboss;

use BadMethodCallException;
use DateTime;
use RangeException;
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
 * @method static string light_gray(string $text)
 * @method static string gray(string $text)
 * @method static string light_red(string $text)
 * @method static string light_green(string $text)
 * @method static string light_yellow(string $text)
 * @method static string light_blue(string $text)
 * @method static string light_magenta(string $text)
 * @method static string light_cyan(string $text)
 * @method static string white(string $text)
 *
 * @method static string default(string $text)
 *
 * @method static string blackBack(string $text)
 * @method static string redBack(string $text)
 * @method static string greenBack(string $text)
 * @method static string yellowBack(string $text)
 * @method static string blueBack(string $text)
 * @method static string magentaBack(string $text)
 * @method static string cyanBack(string $text)
 * @method static string light_grayBack(string $text)
 * @method static string grayBack(string $text)
 * @method static string light_redBack(string $text)
 * @method static string light_greenBack(string $text)
 * @method static string light_yellowBack(string $text)
 * @method static string light_blueBack(string $text)
 * @method static string light_magentaBack(string $text)
 * @method static string light_cyanBack(string $text)
 * @method static string whiteBack(string $text)
 *
 * @method static string defaultBack(string $text)
 *
 * @method static string bold(string $text)
 * @method static string underscore(string $text)
 * @method static string blink(string $text)
 * @method static string reverse(string $text)
 * @method static string conceal(string $text)
 */
class Console
{
	private static bool $timestamps = true;
	private static bool $colors = true;
	private static string $timestamp_format = "d.m.y H:i:s.v";
	private static int $log_level = 0;

	private static string $colorFormat = "\033[%s;1m";     // 16-bit colors
//	private static $extended = "\033[%s;5;%sm";     // 256-bit colors; 38 = foreground, 48 = background
	private static string $reset = "\033[0m";

	private static array $foregroundColors = [
		'black' => 30,
		'red' => 31,
		'green' => 32,
		'yellow' => 33,
		'blue' => 34,
		'magenta' => 35,
		'cyan' => 36,
		'light_gray' => 37,
		'gray' => 90,
		'light_red' => 91,
		'light_green' => 92,
		'light_yellow' => 93,
		'light_blue' => 94,
		'light_magenta' => 95,
		'light_cyan' => 96,
		'white' => 97,

		'default' => 39,
	];

	private static array $backgroundColors = [
		'black' => 40,
		'red' => 41,
		'green' => 42,
		'yellow' => 43,
		'blue' => 44,
		'magenta' => 45,
		'cyan' => 46,
		'light_gray' => 47,
		'gray' => 100,
		'light_red' => 101,
		'light_green' => 102,
		'light_yellow' => 103,
		'light_blue' => 104,
		'light_magenta' => 105,
		'light_cyan' => 106,
		'white' => 107,

		'default' => 49,
	];

	private static array $availableOptions = [
		'bold' => ['set' => 1, 'unset' => 22],
		'underscore' => ['set' => 4, 'unset' => 24],
		'blink' => ['set' => 5, 'unset' => 25],
		'reverse' => ['set' => 7, 'unset' => 27],
		'conceal' => ['set' => 8, 'unset' => 28],
	];

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

	public static function logLevel(int $level = 0): void
	{
		if ($level < 0 || $level > 7) {
			throw new RangeException("Log level can only be set between 0 and 7 (inclusive).");
		}

		self::$log_level = $level;
	}

	public static function link(string $link): string
	{
		return Console::cyan(Console::underscore($link));
	}

	public static function debug(string $message, ...$args): void
	{
		if (self::$log_level > 0) {
			return;
		}

		self::writeln("[DEB] " . vsprintf($message, $args), 'gray');
	}

	public static function info(string $message, ...$args): void
	{
		if (self::$log_level > 1) {
			return;
		}

		self::writeln("[INF] " . vsprintf($message, $args));
	}

	public static function notice(string $message, ...$args): void
	{
		if (self::$log_level > 2) {
			return;
		}

		self::writeln("[NTC] " . vsprintf($message, $args), 'blue');
	}

	public static function warn(string $message, ...$args): void
	{
		if (self::$log_level > 3) {
			return;
		}

		self::writeln("[WRN] " . vsprintf($message, $args), 'yellow');
	}

	public static function error(string $message, ...$args): void
	{
		if (self::$log_level > 4) {
			return;
		}

		self::writeln("[ERR] " . vsprintf($message, $args), 'red', null, [], true);
	}

	public static function critical(string $message, ...$args): void
	{
		if (self::$log_level > 5) {
			return;
		}

		self::writeln("[CRT] " . vsprintf($message, $args), 'magenta', null, ['bold'], true);
	}

	public static function alert(string $message, ...$args): void
	{
		if (self::$log_level > 6) {
			return;
		}

		self::writeln("[ALT] " . vsprintf($message, $args), null, 'yellow', ['bold'], true);
	}

	public static function emergency(string $message, ...$args): void
	{
		self::writeln("[EGY] " . vsprintf($message, $args), null, 'red', ['bold', 'underscore'], true);
	}

	public static function writeln(
		string $message,
		?string $foreground = null,
		?string $background = null,
		array $options = [],
		bool $error = false
	): void
	{
		self::write($message, $foreground, $background, $options, "\r\n", $error);
	}

	public static function write(
		string $message,
		?string $foreground = null,
		?string $background = null,
		array $options = [],
		?string $eol = null,
		bool $error = false
	): void
	{
		$stream = $error ? STDERR : STDOUT;
		$message = self::apply($message, $foreground, $background, $options);

		if (self::$timestamps) {
			$time = '[' . (new DateTime())->format(self::$timestamp_format) . ']';
			$message = $time . ' ' . $message;
		}

		fwrite($stream, $message . $eol);
		fflush($stream);
	}

	public static function apply(
		string $text,
		?string $foreground = null,
		?string $background = null,
		array $options = []
	): string
	{
		if (!self::$colors) {
			return $text;
		}

		$setCodes = [];
		$unsetCodes = [];

		if (null !== $foreground) {
			$setCodes[] = self::$foregroundColors[$foreground];
			$unsetCodes[] = self::$foregroundColors['default'];
		}

		if (null !== $background) {
			$setCodes[] = self::$backgroundColors[$background];
			$unsetCodes[] = self::$backgroundColors['default'];
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
			"%s%s%s",
			sprintf(self::$colorFormat, implode(';', $setCodes)),
			$text,
			sprintf(self::$colorFormat, implode(';', $unsetCodes)),
		);
	}

	public static function reset(string $text): string
	{
		return self::$reset . $text;
	}

	public static function __callStatic(string $name, array $arguments): string
	{
		$name = strtolower($name);

		if (array_key_exists($name, self::$availableOptions)) {
			return self::apply($arguments[0], null, null, [$name]);
		}

		$backPos = stripos($name, 'back');
		$background = $backPos !== false;
		$color = $background ? substr($name, 0, $backPos) : $name;

		if ($background && array_key_exists($color, self::$backgroundColors)) {
			return self::apply($arguments[0], null, $color, []);
		}

		if (array_key_exists($color, self::$foregroundColors)) {
			return self::apply($arguments[0], $color, null, []);
		}

		throw new BadMethodCallException("Method not found: $name");
	}
}
