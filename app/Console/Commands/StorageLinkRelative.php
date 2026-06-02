<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class StorageLinkRelative extends Command
{
    protected $signature = 'storage:link-relative {--force : Replace an existing storage directory or link}';

    protected $description = 'Create storage symlink using relative path (for cPanel/shared hosting)';

    public function handle(): int
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (! is_dir($target)) {
            $this->error('Target directory does not exist: '.$target);

            return 1;
        }

        if ($this->isStorageLinked($link, $target)) {
            $this->info('Storage link already exists.');

            return 0;
        }

        if (file_exists($link)) {
            if (! $this->option('force')) {
                $this->error('A file or directory already exists at: '.$link);
                $this->line('Run with --force to replace it.');

                return 1;
            }

            if (! $this->removeExisting($link)) {
                $this->error('Could not remove existing path: '.$link);

                return 1;
            }
        }

        if (PHP_OS_FAMILY === 'Windows') {
            return $this->createWindowsLink($link, $target);
        }

        return $this->createUnixLink($link, $target);
    }

    private function isStorageLinked(string $link, string $target): bool
    {
        if (! file_exists($link)) {
            return false;
        }

        if (is_link($link)) {
            return true;
        }

        $linkReal = realpath($link);
        $targetReal = realpath($target);

        return $linkReal && $targetReal && $linkReal === $targetReal;
    }

    private function removeExisting(string $link): bool
    {
        if (is_link($link)) {
            return unlink($link);
        }

        if (is_dir($link)) {
            if (count(scandir($link)) > 2) {
                return File::deleteDirectory($link);
            }

            return rmdir($link);
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $path = str_replace('/', '\\', $link);
            exec('cmd /c rmdir '.escapeshellarg($path).' 2>&1', $output, $code);

            return $code === 0;
        }

        return unlink($link);
    }

    private function createWindowsLink(string $link, string $target): int
    {
        if ($this->createWindowsJunction($link, $target)) {
            $this->info('Storage link created successfully (directory junction).');

            return 0;
        }

        if (@symlink($target, $link)) {
            $this->info('Storage link created successfully (absolute path).');

            return 0;
        }

        $this->error('Failed to create storage link. Enable Developer Mode or run the terminal as Administrator.');

        return 1;
    }

    private function createWindowsJunction(string $link, string $target): bool
    {
        $linkPath = str_replace('/', '\\', $link);
        $targetPath = str_replace('/', '\\', realpath($target) ?: $target);

        exec(
            'cmd /c mklink /J '.escapeshellarg($linkPath).' '.escapeshellarg($targetPath).' 2>&1',
            $output,
            $code
        );

        return $code === 0;
    }

    private function createUnixLink(string $link, string $target): int
    {
        $publicPath = realpath(public_path());
        $targetReal = realpath($target);

        if ($publicPath && $targetReal) {
            $relativeTarget = $this->getRelativePath($publicPath, $targetReal);

            if ($relativeTarget !== null && @symlink($relativeTarget, $link)) {
                $this->info('Storage link created successfully (relative path).');

                return 0;
            }
        }

        if (@symlink($target, $link)) {
            $this->info('Storage link created successfully (absolute path).');

            return 0;
        }

        $this->error('Failed to create symlink. Check permissions and that symlinks are allowed.');

        return 1;
    }

    private function getRelativePath(string $from, string $to): ?string
    {
        $fromParts = explode(DIRECTORY_SEPARATOR, rtrim($from, DIRECTORY_SEPARATOR));
        $toParts = explode(DIRECTORY_SEPARATOR, rtrim($to, DIRECTORY_SEPARATOR));

        $commonLength = 0;
        $maxLength = min(count($fromParts), count($toParts));

        while ($commonLength < $maxLength && $fromParts[$commonLength] === $toParts[$commonLength]) {
            $commonLength++;
        }

        $upCount = count($fromParts) - $commonLength;
        $downParts = array_slice($toParts, $commonLength);
        $relative = str_repeat('..'.DIRECTORY_SEPARATOR, $upCount).implode(DIRECTORY_SEPARATOR, $downParts);

        return $relative ?: null;
    }
}
