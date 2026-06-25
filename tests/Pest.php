<?php

declare(strict_types=1);

require_once __DIR__.'/TestCase.php';

use Tests\TestCase;

uses(TestCase::class)->in(__DIR__.'/Feature', __DIR__.'/Unit');
