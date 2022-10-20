<?php
declare(strict_types=1);

namespace ricardoboss;

use BadMethodCallException;
use DateTime;
use InvalidArgumentException;
use RangeException;
use Stringable;
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
	public const FOREGROUNDS = [
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

	public const BACKGROUNDS = [
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

	public const OPTIONS = [
		'bold' => ['set' => 1, 'unset' => 22],
		'underscore' => ['set' => 4, 'unset' => 24],
		'blink' => ['set' => 5, 'unset' => 25],
		'reverse' => ['set' => 7, 'unset' => 27],
		'conceal' => ['set' => 8, 'unset' => 28],
	];

	private static bool $timestamps = true;
	private static bool $colors = true;
	private static string $timestamp_format = "d.m.y H:i:s.v";
	private static int $log_level = 0;

	private static string $colorFormat = "\033[%s;1m";     // 16-bit colors
//	private static $extended = "\033[%s;5;%sm";     // 256-bit colors; 38 = foreground, 48 = background
	private static string $reset = "\033[0m";

	private static array $logLevelTags = [
		'debug'     => 'DEBUG    ',
		'info'      => 'INFO     ',
		'notice'    => 'NOTICE   ',
		'warning'   => 'WARNING  ',
		'error'     => 'ERROR    ',
		'critical'  => 'CRITICAL ',
		'alert'     => 'ALERT    ',
		'emergency' => 'EMERGENCY',
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

	public static function logLevelTag(string $level, string $tag, bool $autoAdjustLengths = true): void
	{
		if (!array_key_exists($level, self::$logLevelTags)) {
			throw new InvalidArgumentException("Log level '$level' is not valid.");
		}

		self::$logLevelTags[$level] = $tag;

		if (!$autoAdjustLengths) {
			return;
		}

		$maxLength = max(array_map('mb_strlen', array_map('rtrim', self::$logLevelTags)));
		foreach (self::$logLevelTags as $key => $value) {
			self::$logLevelTags[$key] = str_pad(rtrim($value), $maxLength, ' ', STR_PAD_RIGHT);
		}
	}

	public static function link(string $link): string
	{
		return self::blue(self::underscore($link));
	}

	public static function debug(string $message, ...$args): void
	{
		if (self::$log_level > 0) {
			return;
		}

		self::writeln("[" . self::$logLevelTags['debug'] . "] " . vsprintf($message, $args), 'gray');
	}

	public static function info(string $message, ...$args): void
	{
		if (self::$log_level > 1) {
			return;
		}

		self::writeln("[" . self::$logLevelTags['info'] . "] " . vsprintf($message, $args));
	}

	public static function notice(string $message, ...$args): void
	{
		if (self::$log_level > 2) {
			return;
		}

		self::writeln("[" . self::$logLevelTags['notice'] . "] " . vsprintf($message, $args), 'blue');
	}

	public static function warn(string $message, ...$args): void
	{
		if (self::$log_level > 3) {
			return;
		}

		self::writeln("[" . self::$logLevelTags['warning'] . "] " . vsprintf($message, $args), 'yellow');
	}

	public static function error(string $message, ...$args): void
	{
		if (self::$log_level > 4) {
			return;
		}

		self::writeln("[" . self::$logLevelTags['error'] . "] " . vsprintf($message, $args), 'red', error: true);
	}

	public static function critical(string $message, ...$args): void
	{
		if (self::$log_level > 5) {
			return;
		}

		self::writeln("[" . self::$logLevelTags['critical'] . "] " . vsprintf($message, $args), 'magenta', options: ['bold'], error: true);
	}

	public static function alert(string $message, ...$args): void
	{
		if (self::$log_level > 6) {
			return;
		}

		self::writeln("[" . self::$logLevelTags['alert'] . "] " . vsprintf($message, $args), background: 'yellow', options: ['bold'], error: true);
	}

	public static function emergency(string $message, ...$args): void
	{
		self::writeln("[" . self::$logLevelTags['emergency'] . "] " . vsprintf($message, $args), background: 'red', options: ['bold'], error: true);
	}

	public static function strip(string $formattedMessage): string
	{
		$pattern = sprintf(preg_quote(self::$colorFormat, '/'), '.*?');

		return preg_replace('/' . $pattern . '/', '', $formattedMessage);
	}

	/**
	 * @param iterable<int, array<array-key, scalar|\Stringable|null>|array> $data
	 * @return iterable<int, string>
	 */
	public static function table(iterable $data, bool $ascii = false, bool $compact = false, bool $noOuterBorder = false, bool $noInnerBorder = false, string $borderColor = 'gray'): iterable
	{
		if (!is_array($data)) {
			$rows = [];
			foreach ($data as $row) {
				$rows[] = $row;
			}
		} else {
			$rows = $data;
		}

		if (empty($rows)) {
			return "";
		}

		foreach ($rows as &$row) {
			foreach ($row as &$cell) {
				if (is_array($cell)) {
					$cell = implode(', ', $cell);
				} else if (is_a($cell, Stringable::class) || is_numeric($cell)) {
					$cell = (string)$cell;
				}
			}
		}
		unset($row, $cell);

		$columns = array_reduce($rows, static function (array $carry, array $row): array {
			foreach ($row as $key => $value) {
				if (!isset($carry[$key])) {
					$carry[$key] = [];
				}

				$carry[$key][] = $value;
			}

			return $carry;
		}, []);

		$columnWidths = array_map(static function (array $column): int {
			return max(array_map('mb_strlen', array_map([self::class, 'strip'], $column)));
		}, $columns);

		$hsep = $ascii ? '-' : '─';
		$vsep = self::$borderColor($ascii ? '|' : '│');
		$cross = $ascii ? '+' : '┼';

		$crossStart = $ascii ? '+' : '├';
		$crossEnd = $ascii ? '+' : '┤';
		$crossStartTop = $ascii ? '+' : '┌';
		$crossMidTop = $ascii ? '+' : '┬';
		$crossEndTop = $ascii ? '+' : '┐';
		$crossStartBottom = $ascii ? '+' : '└';
		$crossMidBottom = $ascii ? '+' : '┴';
		$crossEndBottom = $ascii ? '+' : '┘';

		$headerHsep = $compact ? $hsep : ($ascii ? '=' : '═');
		$headerCrossStart = $compact ? $crossStart : ($ascii ? '+' : '╞');
		$headerCrossMid = $compact ? $cross : ($ascii ? '+' : '╪');
		$headerCrossEnd = $compact ? $crossEnd : ($ascii ? '+' : '╡');

		$printSeparator = static function (bool $top = false, bool $bottom = false, bool $header = false) use (
			$columnWidths, $noOuterBorder, $noInnerBorder, $borderColor,
			$hsep, $cross,
			$headerHsep, $headerCrossStart, $headerCrossMid, $headerCrossEnd,
			$crossStart, $crossEnd, $crossStartTop, $crossMidTop, $crossEndTop, $crossStartBottom, $crossMidBottom, $crossEndBottom,
		): string {
			$crossStart = $top ? $crossStartTop : ($header ? $headerCrossStart : ($bottom ? $crossStartBottom : $crossStart));
			$crossMid = $top ? $crossMidTop : ($header ? $headerCrossMid : ($bottom ? $crossMidBottom : $cross));
			$crossEnd = $top ? $crossEndTop : ($header ? $headerCrossEnd : ($bottom ? $crossEndBottom : $crossEnd));

			$sep = $header ? $headerHsep : $hsep;

			if (empty($columnWidths)) {
				return $crossStart . $sep . $crossEnd;
			}

			$output = '';
			$col = 0;
			foreach ($columnWidths as $width) {
				if ($noOuterBorder) {
					if ($col === 0 || $col === count($columnWidths) - 1) {
						$sepWidth = $width + 1; // no padding at/start/end of row
					} else {
						$sepWidth = $width + 2; // padding between cell walls
					}
				} else if ($noInnerBorder) {
					if ($col > 0 && $col < count($columnWidths) - 1) {
						$sepWidth = $width; // no padding from cell to cell
					} else {
						$sepWidth = $width + 1; // padding at start/end of row
					}
				} else {
					$sepWidth = $width + 2; // padding in each cell is the same
				}

				$sepStr = str_repeat($sep, $sepWidth);

				if ($col === 0) {
					if (!$noOuterBorder) {
						$output .= $crossStart;
					}
				} else if (!$noInnerBorder) {
					$output .= $crossMid;
				} else {
					$output .= $sep;
				}

				$output .= $sepStr;

				$col++;
			}

			if (!$noOuterBorder) {
				$output .= $crossEnd;
			}

			return Console::$borderColor($output);
		};

		$strPadVisual = static function (string $value, int $width): string
		{
			$actualLength = mb_strlen($value);
			$strippedLength = mb_strlen(self::strip($value));
			return str_pad($value, $width + ($actualLength - $strippedLength));
		};

		$header = array_shift($rows);
		if (!$noOuterBorder) {
			yield $printSeparator(top: true);
		}

		$output = '';
		foreach ($header as $key => $value) {
			if ((empty($output) && !$noOuterBorder) || (!empty($output) && !$noInnerBorder)) {
				$output .= $vsep . ' ';
			}
			$output .= $strPadVisual($value, $columnWidths[$key]) . ' ';
		}

		if ($noInnerBorder && $noOuterBorder) {
			yield $output;
		} else {
			if ($noOuterBorder) {
				yield $output;
			} else {
				yield $output . $vsep;
			}

			if (!$noInnerBorder) {
				yield $printSeparator(header: true);
			}
		}

		$rowCount = count($rows);
		foreach ($rows as $i => $row) {
			$output = "";
			foreach ($row as $key => $value) {
				if ((empty($output) && !$noOuterBorder) || (!empty($output) && !$noInnerBorder)) {
					$output .= $vsep . ' ';
				}
				$output .= $strPadVisual($value, $columnWidths[$key]) . ' ';
			}

			if ($noOuterBorder) {
				yield $output;
			} else {
				yield $output . $vsep;
			}

			$lastLine = $i === $rowCount - 1;

			if (((!$lastLine && !$noInnerBorder) || ($lastLine && !$noOuterBorder)) && ($lastLine || !$compact)) {
				yield $printSeparator(bottom: $lastLine);
			}
		}
	}

	public static function writeln(
		string $message,
		?string $foreground = null,
		?string $background = null,
		array $options = [],
		string $eol = "\r\n",
		bool $error = false
	): void
	{
		self::write($message, $foreground, $background, $options, $eol, $error);
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
			$setCodes[] = self::FOREGROUNDS[$foreground];
			$unsetCodes[] = self::FOREGROUNDS['default'];
		}

		if (null !== $background) {
			$setCodes[] = self::BACKGROUNDS[$background];
			$unsetCodes[] = self::BACKGROUNDS['default'];
		}

		foreach ($options as $option) {
			$option = self::OPTIONS[$option];

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

		if (array_key_exists($name, self::OPTIONS)) {
			return self::apply($arguments[0], null, null, [$name]);
		}

		$backPos = stripos($name, 'back');
		$background = $backPos !== false;
		$color = $background ? substr($name, 0, $backPos) : $name;

		if ($background && array_key_exists($color, self::BACKGROUNDS)) {
			return self::apply($arguments[0], null, $color, []);
		}

		if (array_key_exists($color, self::FOREGROUNDS)) {
			return self::apply($arguments[0], $color, null, []);
		}

		throw new BadMethodCallException("Method not found: $name");
	}
}
