<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Laramore\Exceptions\LockException;
use Laramore\Elements\{
    Enum, EnumManager, Element, ElementManager
};

final class EnumTest extends TestCase
{
    public function testEnumClass()
    {
        $operator = new Enum('name', 'native');
        $operator2 = new Enum('name', 'native', 'description');

        $this->assertTrue($operator instanceof Element);
    }

    public function testEnumManagerClass()
    {
        $manager = new EnumManager();

        $this->assertTrue($manager instanceof ElementManager);

        $manager->set(new Enum('name', 'native'));
    }

    public function testWrongEnum()
    {
        $manager = new EnumManager();

        $this->expectException(ErrorException::class);

        $manager->set(new class() {});
    }
}
