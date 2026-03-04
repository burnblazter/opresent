<?php
// \app\Views\errors\cli\production.php

/**
 * PresenSI by burnblazter <hello@fael.my.id>
 * Fork of o-present by Josephine (github.com/josephines1/o-present)
 * @license GPL-3.0 | github.com/burnblazter
 */

// On the CLI, we still want errors in productions
// so just use the exception template.
include __DIR__ . '/error_exception.php';
