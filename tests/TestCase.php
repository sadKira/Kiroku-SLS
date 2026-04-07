<?php

namespace Tests;
use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Throwable;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->registerSqliteCompatibilityFunctions();
    }

    protected function registerSqliteCompatibilityFunctions(): void
    {
        $connection = DB::connection();

        if ($connection->getDriverName() !== 'sqlite') {
            return;
        }

        $pdo = $connection->getPdo();

        if (! method_exists($pdo, 'sqliteCreateFunction')) {
            return;
        }

        $parseDateTime = static function ($value): ?DateTimeInterface {
            if ($value instanceof DateTimeInterface) {
                return $value;
            }

            if ($value === null) {
                return null;
            }

            $value = trim((string) $value);

            if ($value === '') {
                return null;
            }

            try {
                return new DateTimeImmutable($value);
            } catch (Throwable) {
                return null;
            }
        };

        $pdo->sqliteCreateFunction('MONTH', static function ($value) use ($parseDateTime) {
            $dateTime = $parseDateTime($value);

            return $dateTime ? (int) $dateTime->format('n') : null;
        }, 1);

        $pdo->sqliteCreateFunction('HOUR', static function ($value) use ($parseDateTime) {
            $dateTime = $parseDateTime($value);

            return $dateTime ? (int) $dateTime->format('G') : null;
        }, 1);
    }
}
