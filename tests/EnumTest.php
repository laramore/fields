<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Laramore\Elements\{
    EnumElement, EnumManager, Element, ElementManager
};

final class EnumTest extends TestCase
{
    public function testEnumClass()
    {
        $operator = new EnumElement('name', 'native');
        $operator2 = new EnumElement('name', 'native', 'description');

        $this->assertTrue($operator instanceof Element);
    }

    public function testEnumManagerClass()
    {
        $manager = new EnumManager();

        $this->assertTrue($manager instanceof ElementManager);

        $manager->set(new EnumElement('name', 'native'));
    }

    public function testWrongEnum()
    {
        $manager = new EnumManager();

        $this->expectException(ErrorException::class);

        $manager->set(new class() {});
    }
}
