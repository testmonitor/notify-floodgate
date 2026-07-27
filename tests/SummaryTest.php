<?php

namespace TestMonitor\Floodgate\Tests;

use PHPUnit\Framework\Attributes\Test;
use TestMonitor\Floodgate\Notifications\Summary;

class SummaryTest extends TestCase
{
    #[Test]
    public function it_sets_a_single_key_value_pair_via_with(): void
    {
        // Given
        $summary = new Summary;

        // When
        $summary->with('count', 3);

        // Then
        $this->assertEquals(['count' => 3], $summary->data);
    }

    #[Test]
    public function it_merges_an_array_of_data_via_with(): void
    {
        // Given
        $summary = (new Summary)->with('count', 3);

        // When
        $summary->with(['name' => 'Acme']);

        // Then
        $this->assertEquals(['count' => 3, 'name' => 'Acme'], $summary->data);
    }

    #[Test]
    public function it_converts_to_an_array_omitting_unset_values(): void
    {
        // Given
        $summary = (new Summary)->message(':count issues assigned')->with(['count' => 3]);

        // When
        $result = $summary->toArray();

        // Then
        $this->assertEquals(['message' => ':count issues assigned', 'data' => ['count' => 3]], $result);
    }
}
