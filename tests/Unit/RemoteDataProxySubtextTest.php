<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class RemoteDataProxySubtextTest extends TestCase
{
    public function test_subtext_splits_and_resolves_multiple_columns(): void
    {
        $firstRow = ['1', '2161101360', 'RABIUL ASDIANA', 'MAGISTER KEUANGAN SYARIAH (S2)'];
        $subtextCol = 'B, D';

        $subtextCols = preg_split('/[,;\-]+/', (string)$subtextCol, -1, PREG_SPLIT_NO_EMPTY);
        $this->assertEquals(['B', ' D'], $subtextCols);

        $indexes = [];
        foreach ($subtextCols as $colItem) {
            $colItem = strtoupper(trim($colItem));
            $indexes[] = ord($colItem) - ord('A');
        }

        $this->assertEquals([1, 3], $indexes);

        $parts = [];
        foreach ($indexes as $idx) {
            $parts[] = $firstRow[$idx];
        }

        $result = implode(' • ', $parts);
        $this->assertEquals('2161101360 • MAGISTER KEUANGAN SYARIAH (S2)', $result);
    }
}

