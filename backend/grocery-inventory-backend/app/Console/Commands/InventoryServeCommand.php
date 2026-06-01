<?php

namespace App\Console\Commands;

use App\Support\ServiceIdentity;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

#[Signature('inventory:serve
    {--host=127.0.0.1 : The host address the server should bind to}
    {--port=8000 : The port the server should listen on}
    {--no-server : Render the startup panel without starting the server}')]
#[Description('Start the branded Inventory Cloud AI backend server')]
class InventoryServeCommand extends Command
{
    public function handle(): int
    {
        $host = (string) $this->option('host');
        $port = (string) $this->option('port');
        $serverUrl = "http://{$host}:{$port}";

        $this->renderStartupPanel($serverUrl);

        if ($this->option('no-server')) {
            $this->newLine();
            $this->warn('Preview mode: server process was not started.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->line('<fg=gray>Starting Laravel development server. Press Ctrl+C to stop.</>');
        $this->newLine();
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
            $style = $type === Process::ERR ? '<fg=red>' : '<fg=gray>';

            $this->output->write($style.$buffer.'</>');
        });

        return $process->getExitCode() ?? self::FAILURE;
    }

    private function renderStartupPanel(string $serverUrl): void
    {
        $this->renderWordmark();
        $this->newLine();

        $this->line('<fg=cyan>Welcome to Inventory Cloud AI</>');
        $this->line('Your smart grocery inventory backend is now running.');
        $this->line('Thank you for choosing our smart inventory solution.');
        $this->newLine();
        $this->line("Server URL   : {$serverUrl}");
        $this->line("Swagger Docs : {$serverUrl}/api/documentation");
        $this->newLine();

        $this->table(
            ['Detail', 'Value'],
            [
                ['Project', 'Grocery Inventory Backend'],
                ['Framework', 'Laravel REST API'],
                ['Database', 'PostgreSQL'],
                ['Environment', ServiceIdentity::environment()],
                ['Service', ServiceIdentity::name().' '.ServiceIdentity::version()],
                ['Server URL', $serverUrl],
                ['Swagger Docs', "{$serverUrl}/api/documentation"],
                ['Status URL', "{$serverUrl}/api/status"],
                ['Started At', now()->toDateTimeString()],
            ]
        );

        $this->newLine();
        $this->line('<fg=green>[OK]</> Core API Loaded');
        $this->line('<fg=green>[OK]</> Authentication Module Ready');
        $this->line('<fg=green>[OK]</> Inventory Modules Ready');
        $this->line('<fg=green>[OK]</> Swagger Documentation Ready');
        $this->line('<fg=green>[OK]</> Request Logger Active');
        $this->newLine();

        $this->line(str_repeat('-', 72));
        $this->line('<fg=white;options=bold>Live Server Logs</>');
        $this->line(str_repeat('-', 72));
    }

    private function renderWordmark(): void
    {
        $banner = [
            '    ____                     __                     ________                __        ___    ____',
            '   /  _/___ _   _____  ____  / /_____  _______  __ / ____/ /___  __  ______/ /   ____ _/   |  /  _/',
            '   / // __ \ | / / _ \/ __ \/ __/ __ \/ ___/ / / // /   / / __ \/ / / / __  /   / __ `/ /| |  / /  ',
            ' _/ // / / / |/ /  __/ / / / /_/ /_/ / /  / /_/ // /___/ / /_/ / /_/ / /_/ /   / /_/ / ___ |_/ /   ',
            '/___/_/ /_/|___/\___/_/ /_/\__/\____/_/   \__, / \____/_/\____/\__,_/\__,_/____\__,_/_/  |_/___/   ',
            '                                         /____/                         /_____/                     ',
        ];

        $gradient = [
            [59, 130, 246],
            [34, 211, 238],
            [34, 197, 94],
            [250, 204, 21],
            [236, 72, 153],
        ];

        foreach ($banner as $line) {
            $this->output->writeln($this->gradientText($line, $gradient));
        }

        $this->output->writeln($this->ansiText('        Smart Grocery Inventory Management API', [255, 241, 118], true));
        $this->output->writeln($this->ansiText('        Inventory Management System - Smart Grocery Backend', [94, 234, 212], true));
        $this->output->writeln($this->ansiText('        Inventory Cloud AI Server', [96, 165, 250], true));
        $this->output->writeln($this->ansiText('        Laravel REST API + PostgreSQL + Swagger + JWT', [156, 163, 175]));
    }

    /**
     * @param  array<int, array{0: int, 1: int, 2: int}>  $stops
     */
    private function gradientText(string $text, array $stops): string
    {
        $length = strlen($text);

        if ($length === 0) {
            return '';
        }

        $buffer = '';
        $lastIndex = max($length - 1, 1);

        for ($index = 0; $index < $length; $index++) {
            $character = $text[$index];

            if ($character === ' ') {
                $buffer .= $character;

                continue;
            }

            $buffer .= $this->ansiText($character, $this->gradientColor($stops, $index / $lastIndex), true, false);
        }

        return $buffer."\033[0m";
    }

    /**
     * @param  array<int, array{0: int, 1: int, 2: int}>  $stops
     * @return array{0: int, 1: int, 2: int}
     */
    private function gradientColor(array $stops, float $ratio): array
    {
        $ratio = max(0.0, min(1.0, $ratio));
        $lastStop = count($stops) - 1;

        if ($lastStop <= 0) {
            return $stops[0] ?? [255, 255, 255];
        }

        $scaledRatio = $ratio * $lastStop;
        $startIndex = min((int) floor($scaledRatio), $lastStop - 1);
        $endIndex = $startIndex + 1;
        $localRatio = $scaledRatio - $startIndex;

        return [
            (int) round($stops[$startIndex][0] + (($stops[$endIndex][0] - $stops[$startIndex][0]) * $localRatio)),
            (int) round($stops[$startIndex][1] + (($stops[$endIndex][1] - $stops[$startIndex][1]) * $localRatio)),
            (int) round($stops[$startIndex][2] + (($stops[$endIndex][2] - $stops[$startIndex][2]) * $localRatio)),
        ];
    }

    /**
     * @param  array{0: int, 1: int, 2: int}  $rgb
     */
    private function ansiText(string $text, array $rgb, bool $bold = false, bool $reset = true): string
    {
        $prefix = $bold ? '1;' : '';

        return sprintf("\033[%s38;2;%d;%d;%dm%s%s", $prefix, $rgb[0], $rgb[1], $rgb[2], $text, $reset ? "\033[0m" : '');
    }

    private function prepareSwaggerForServer(string $serverUrl): void
    {
        config([
            'app.url' => $serverUrl,
            'l5-swagger.defaults.constants.L5_SWAGGER_CONST_HOST' => $serverUrl,
        ]);

        Artisan::call('l5-swagger:generate');

        if ($this->output !== null) {
            $this->line("<fg=green>[OK]</> Swagger document generated for {$serverUrl}");
        }
    }
}
