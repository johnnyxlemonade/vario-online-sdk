<?php

declare(strict_types=1);

/**
 * @param string[] $paths
 *
 * @return Generator<string>
 */
function findPhpFiles(array $paths): Generator
{
    foreach ($paths as $path) {
        if (!is_dir($path)) {
            continue;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if (!$file instanceof SplFileInfo) {
                continue;
            }

            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            yield $file->getPathname();
        }
    }
}

$basePath = dirname(__DIR__);

$paths = [
    $basePath . DIRECTORY_SEPARATOR . 'src',
    $basePath . DIRECTORY_SEPARATOR . 'tests',
];

$checked = 0;
$failed = false;

foreach (findPhpFiles($paths) as $file) {
    $checked++;

    $command = sprintf(
        'php -l %s',
        escapeshellarg($file),
    );

    passthru($command, $exitCode);

    if ($exitCode !== 0) {
        $failed = true;
    }
}

echo sprintf(
    'Lint %s (%d PHP files checked)%s',
    $failed ? 'failed' : 'OK',
    $checked,
    PHP_EOL,
);

exit($failed ? 1 : 0);
