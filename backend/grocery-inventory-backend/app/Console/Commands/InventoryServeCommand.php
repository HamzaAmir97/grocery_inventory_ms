<?php

namespace App\Console\Commands;

use App\Support\ServiceIdentity;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Boots the Laravel development server behind a branded, modern CLI banner.
 *
 * Keep this visually in sync with the frontend banner at:
 *   frontend/scripts/banner.mjs
 */
#[Signature('inventory:serve
    {--host=127.0.0.1 : The host address the server should bind to}
    {--port=8000 : The port the server should listen on}
    {--no-server : Render the startup banner without starting the server}')]
#[Description('Serve the Inventory backend with a branded startup banner')]
class InventoryServeCommand extends Command
{
    /** Inner width between the box borders. */
    private const WIDTH = 60;

    /** @var array{0: int, 1: int, 2: int} blue */
    private const C1 = [0x93, 0xC5, 0xFD];

    /** @var array{0: int, 1: int, 2: int} violet */
    private const C2 = [0xC4, 0xB5, 0xFD];

    /** @var array{0: int, 1: int, 2: int} orange */
    private const C3 = [0xFD, 0xBA, 0x74];

    /** @var array{0: int, 1: int, 2: int} green */
    private const ACCENT = [0x86, 0xEF, 0xAC];

    public function handle(): int
    {
        $host = (string) $this->option('host');
        $port = (string) $this->option('port');
        $serverUrl = "http://{$host}:{$port}";

        $this->write($this->renderBanner($host, $port)."\n");

        if ($this->option('no-server')) {
            $this->write('  '.$this->fg(self::C3).'Preview mode: server process was not started.'.$this->reset()."\n");

            return self::SUCCESS;
        }

        $this->prepareSwaggerForServer($serverUrl);

        $process = new Process(
            [PHP_BINARY, 'artisan', 'serve', "--host={$host}", "--port={$port}"],
            base_path(),
            [
                'APP_URL' => $serverUrl,
                'L5_SWAGGER_CONST_HOST' => $serverUrl,
                'INVENTORY_CLI_LOGS' => 'true',
            ] + $_ENV,
            null,
            null
        );

        $process->run(function (string $type, string $buffer): void {
            $color = $type === Process::ERR ? "\033[31m" : "\033[2m";

            $this->write($this->code($color).$buffer.$this->reset());
        });

        return $process->getExitCode() ?? self::FAILURE;
    }

    /**
     * Write raw output, bypassing Symfony's OutputFormatter (which interprets
     * `<...>` tags and would mangle box-drawing glyphs and ANSI escapes).
     *
     * Routing through the command's output (when available) keeps the banner
     * capturable by `Artisan::output()` in tests while still preserving the
     * exact characters via OUTPUT_RAW. When the command is invoked outside a
     * normal run (e.g. via reflection) we fall back to STDOUT.
     */
    private function write(string $text): void
    {
        if (isset($this->output)) {
            $this->output->getOutput()->write($text, false, OutputInterface::OUTPUT_RAW);

            return;
        }

        fwrite(STDOUT, $text);
    }

    private function prepareSwaggerForServer(string $serverUrl): void
    {
        config([
            'app.url' => $serverUrl,
            'l5-swagger.defaults.constants.L5_SWAGGER_CONST_HOST' => $serverUrl,
        ]);

        Artisan::call('l5-swagger:generate');

        $this->write(
            '  '.$this->fg(self::ACCENT).'✓'.$this->reset()
            .$this->code("\033[2m").' API documentation generated'.$this->reset()."\n\n"
        );
    }

    // ---------------------------------------------------------------------
    // Banner rendering (kept visually identical to frontend/scripts/banner.mjs)
    // ---------------------------------------------------------------------

    private function useColor(): bool
    {
        if (getenv('NO_COLOR') !== false) {
            return false;
        }

        if (isset($this->output)) {
            return $this->output->getOutput()->isDecorated();
        }

        return function_exists('stream_isatty') ? @stream_isatty(STDOUT) : false;
    }

    /**
     * @param  array{0: int, 1: int, 2: int}  $rgb
     */
    private function fg(array $rgb): string
    {
        return $this->useColor() ? "\033[38;2;{$rgb[0]};{$rgb[1]};{$rgb[2]}m" : '';
    }

    private function code(string $seq): string
    {
        return $this->useColor() ? $seq : '';
    }

    private function reset(): string
    {
        return $this->code("\033[0m");
    }

    private static function lerp(int $a, int $b, float $t): int
    {
        return (int) round($a + ($b - $a) * $t);
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private static function grad3(float $t): array
    {
        if ($t < 0.5) {
            $u = $t / 0.5;

            return [
                self::lerp(self::C1[0], self::C2[0], $u),
                self::lerp(self::C1[1], self::C2[1], $u),
                self::lerp(self::C1[2], self::C2[2], $u),
            ];
        }

        $u = ($t - 0.5) / 0.5;

        return [
            self::lerp(self::C2[0], self::C3[0], $u),
            self::lerp(self::C2[1], self::C3[1], $u),
            self::lerp(self::C2[2], self::C3[2], $u),
        ];
    }

    private function gradient(string $str): string
    {
        if (! $this->useColor()) {
            return $str;
        }

        $chars = mb_str_split($str);
        $n = max(count($chars) - 1, 1);
        $out = '';
        foreach ($chars as $i => $char) {
            $out .= $this->fg(self::grad3($i / $n)).$char;
        }

        return $out.$this->reset();
    }

    private function plainLength(string $str): int
    {
        return mb_strlen((string) preg_replace('/\033\[[0-9;]*m/', '', $str));
    }

    private function border(string $left, string $right): string
    {
        return $this->gradient($left.str_repeat('─', self::WIDTH).$right);
    }

    private function row(string $colored): string
    {
        $pad = max(0, self::WIDTH - $this->plainLength($colored));
        $edge = $this->fg(self::C2).'│'.$this->reset();

        return $edge.$colored.str_repeat(' ', $pad).$edge;
    }

    private function detail(string $label, string $value, ?string $color = null): string
    {
        $color ??= $this->fg(self::C1);
        $dim = $this->code("\033[2m");

        return $this->row(
            '   '.$dim.'▸ '.$this->reset()
            .$dim.str_pad($label, 12).$this->reset()
            .$color.$value.$this->reset()
        );
    }

    private function renderBanner(string $host, string $port): string
    {
        $bold = $this->code("\033[1m");
        $dim = $this->code("\033[2m");
        $base = "http://{$host}:{$port}";
        $version = $this->getLaravel()->version();

        $lines = [
            '',
            $this->border('╭', '╮'),
            $this->row(''),
            $this->row('   '.$this->gradient('╔═╗')),
            $this->row('   '.$this->gradient('╠═╣').'  '.$bold.$this->gradient('I N V E N T O R Y').$this->reset()),
            $this->row('   '.$this->gradient('╚═╝').'  '.$dim.'Grocery Inventory Management System'.$this->reset()),
            $this->row(''),
            $this->detail('Service', 'Backend · Laravel '.explode('.', $version)[0], $this->fg(self::C2)),
            $this->detail('Environment', ServiceIdentity::environment(), $this->fg(self::C3)),
            $this->detail('Local', "http://{$host}:{$port}"),
            $this->detail('API', "{$base}/api"),
            $this->detail('Docs', "{$base}/api/documentation"),
            $this->row(''),
            $this->row('   '.$this->fg(self::ACCENT).'●'.$this->reset().' API server starting'.$dim.' — press Ctrl+C to stop'.$this->reset()),
            $this->row(''),
            $this->border('╰', '╯'),
            '',
        ];

        return implode("\n", $lines);
    }
}
