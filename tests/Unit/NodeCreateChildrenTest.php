<?php

namespace Tests\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Sxml\Nodes\Enums\NodeType;
use Sxml\Nodes\Node;

class NodeCreateChildrenTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testCreateFullChildNode(): void
    {
        $parent = new Node("div");
        $child = $parent->createChildNode('span', [], 'Value');

        $this->assertEquals($parent->getChildren()[0]->dump(), $child->dump());
        $this->assertEquals(NodeType::Full, $parent->getChildren()[0]->getType());
    }

    /**
     * @throws Exception
     */
    public function testCreateSingleChildNode(): void
    {
        $parent = new Node("div");
        $child = $parent->createChildNode('a', ['href' => 'https://example.com']);

        $this->assertEquals($parent->getChildren()[0]->dump(), $child->dump());
        $this->assertEquals(NodeType::Single, $parent->getChildren()[0]->getType());
    }
}