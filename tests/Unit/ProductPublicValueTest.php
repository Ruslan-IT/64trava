<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductPublicValueTest extends TestCase
{
    public function test_public_value_is_present_only_when_it_has_content(): void
    {
        $this->assertTrue(Product::hasPublicValue('Gelato'));
        $this->assertTrue(Product::hasPublicValue('26-30'));
        $this->assertTrue(Product::hasPublicValue(22));
        $this->assertTrue(Product::hasPublicValue(22.5));
        $this->assertTrue(Product::hasPublicValue('1500.00'));
        $this->assertTrue(Product::hasPublicValue('  citrus  '));

        $this->assertFalse(Product::hasPublicValue(null));
        $this->assertFalse(Product::hasPublicValue(''));
        $this->assertFalse(Product::hasPublicValue('   '));
        $this->assertFalse(Product::hasPublicValue(0));
        $this->assertFalse(Product::hasPublicValue(0.0));
        $this->assertFalse(Product::hasPublicValue('0'));
        $this->assertFalse(Product::hasPublicValue('0.00'));
        $this->assertFalse(Product::hasPublicValue('0.0'));
    }
}
