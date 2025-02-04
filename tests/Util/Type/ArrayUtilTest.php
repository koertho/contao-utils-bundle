<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\UtilsBundle\Tests\Util\Type;

use HeimrichHannot\UtilsBundle\Tests\AbstractUtilsTestCase;
use HeimrichHannot\UtilsBundle\Util\Type\ArrayUtil;
use PHPUnit\Framework\MockObject\MockBuilder;

class ArrayUtilTest extends AbstractUtilsTestCase
{
    public function getTestInstance(array $parameters = [], ?MockBuilder $mockBuilder = null)
    {
        return new ArrayUtil();
    }

    public function testInsertBeforeKey(): void
    {
        $instance = $this->getTestInstance();
        $array = ['a' => 'A', 'b' => 'B', 'c' => 'C'];

        $instance::insertBeforeKey($array, 'e', 'f', 'F');
        $this->assertSame(
            ['a' => 'A', 'b' => 'B', 'c' => 'C', 'f' => 'F'], $array);

        $instance::insertBeforeKey($array, ['z'], 'h', 'H');
        $this->assertSame(
            ['a' => 'A', 'b' => 'B', 'c' => 'C', 'f' => 'F', 'h' => 'H'], $array);

        $instance::insertBeforeKey($array, ['f', 'h'], 'd', 'D');
        $this->assertSame(
            ['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'f' => 'F', 'h' => 'H'], $array);

        $this->expectException(\InvalidArgumentException::class);
        $instance::insertBeforeKey($array, 3, 'x', 'Y');
    }

    public function testRemoveValue()
    {
        $arrayUtil = $this->getTestInstance();

        $array = [0 => 0, 1 => 1, 2 => 2];
        $result = $arrayUtil->removeValue(1, $array);
        $this->assertTrue($result);
        $this->assertCount(2, $array);
        $this->assertArrayHasKey(0, $array);
        $this->assertArrayHasKey(2, $array);

        $result = $arrayUtil->removeValue(1, $array);
        $this->assertFalse($result);
    }
}
