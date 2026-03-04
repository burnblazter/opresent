#!/usr/bin/env php
<?php

// tools/add_headers.php

$appDir = __DIR__ . '/../app';

$header = <<<'HDR'
/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */
HDR;

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($appDir, RecursiveDirectoryIterator::SKIP_DOTS)
);

$ok = $skip = 0;

foreach ($files as $file) {
    if ($file->getExtension() !== 'php') continue;

    $content = file_get_contents($file->getPathname());
    $relPath = '// ' . ltrim(str_replace(realpath(__DIR__ . '/..'), '', $file->getRealPath()), '/');

    if (str_contains($content, 'PresenSI by burnblazter')) {
        echo "[SKIP] $relPath\n";
        $skip++;
        continue;
    }

    // Sisipkan setelah <?php
    $new = preg_replace(
        '/^<\?php\s*/m',
        "<?php\n$relPath\n\n$header\n\n",
        $content,
        1
    );

    file_put_contents($file->getPathname(), $new);
    echo "[OK]   $relPath\n";
    $ok++;
}

echo "\nDone: $ok updated, $skip skipped.\n";