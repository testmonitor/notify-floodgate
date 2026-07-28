<?php

namespace TestMonitor\Floodgate\Tests;

use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use TestMonitor\Floodgate\Notifications\Summary;

class SummaryTest extends TestCase
{
    #[Test]
    public function it_has_no_channel_by_default(): void
    {
        // Given
        $summary = new Summary;

        // Then
        $this->assertFalse($summary->hasChannel('mail'));
    }

    #[Test]
    public function it_registers_a_channel_builder(): void
    {
        // Given
        $summary = new Summary;

        // When
        $summary->channel('mail', fn () => 'a mailable');

        // Then
        $this->assertTrue($summary->hasChannel('mail'));
    }

    #[Test]
    public function it_resolves_a_channel_builder_with_the_given_parameters(): void
    {
        // Given
        $summary = (new Summary)->channel('mail', fn ($user, $items) => [$user, $items]);

        // When
        $result = $summary->resolveChannel('mail', 'user', ['item']);

        // Then
        $this->assertEquals(['user', ['item']], $result);
    }

    #[Test]
    public function it_throws_when_resolving_an_unregistered_channel(): void
    {
        // Given
        $summary = new Summary;

        // Then
        $this->expectException(RuntimeException::class);

        // When
        $summary->resolveChannel('mail');
    }
}
