<?php

namespace Tests\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Sxml\Nodes\AbstractNode;
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

        /** @noinspection PhpParamsInspection */
        $this->assertEquals($parent->getChildren()[0]->dump(), $child->dump());
        /** @noinspection PhpParamsInspection */
        $this->assertEquals(AbstractNode::NODE_TYPE_FULL, $parent->getChildren()[0]->getType());
    }

    /**
     * @throws Exception
     */
    public function testCreateSingleChildNode(): void
    {
        $parent = new Node("div");
        $child = $parent->createChildNode('a', ['href' => 'https://example.com']);

        /** @noinspection PhpParamsInspection */
        $this->assertEquals($parent->getChildren()[0]->dump(), $child->dump());
        /** @noinspection PhpParamsInspection */
        $this->assertEquals(AbstractNode::NODE_TYPE_SINGLE, $parent->getChildren()[0]->getType());
    }
}