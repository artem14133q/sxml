<?php

namespace Tests\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Sxml\Nodes\AbstractNode;
use Sxml\Nodes\Node;

class NodeChildrenFindTest extends TestCase
{
    protected const PREFIX_W = 'w';
    protected const PREFIX_A = 'a';

    protected const TREE = [
        self::PREFIX_W . ':span' => [
            'type' => AbstractNode::NODE_TYPE_FULL,
            'attr' => ['col' => '1', 'row' => '2'],
            'value' => 'Text'
        ],
        'a' => [
            'type' => AbstractNode::NODE_TYPE_SINGLE,
            'attr' => ['href' => 'https://example.com'],
        ],
        self::PREFIX_W . ':h1' => [
            'type' => AbstractNode::NODE_TYPE_FULL,
            'attr' => ['style' => 'text-weight: bold;', 'col' => '12', 'row' => '1'],
            'value' => 'Title'
        ],
        'h2' => [
            'type' => AbstractNode::NODE_TYPE_FULL,
            'attr' => ['style' => 'text-weight: bold;', 'col' => '12'],
            'value' => 'Small Title',
        ],
        self::PREFIX_W . ':h2' => [
            'type' => AbstractNode::NODE_TYPE_FULL,
            'attr' => ['style' => 'text-weight: bold;', 'col' => '12'],
            'value' => 'Small Title 2',
        ],
        self::PREFIX_A . ':span' => [
            'type' => AbstractNode::NODE_TYPE_FULL,
            'attr' => ['class' => 'title'],
            'value' => 'text',
        ],
        self::PREFIX_A . ':div' => [
            'type' => AbstractNode::NODE_TYPE_FULL,
            'attr' => ['style' => 'text-weight: normal;', 'col' => '6'],
            'value' => 'Block',
        ],
        self::PREFIX_A . ':form' => [
            'type' => AbstractNode::NODE_TYPE_FULL,
            'attr' => ['action' => 'https://example.com', 'method' => 'POST'],
        ],
    ];

    /**
     * @throws Exception
     */
    protected function createNodeTree(): Node
    {
        $parent = (new Node('body'))->setOption('xmlns', [self::PREFIX_W => true, self::PREFIX_A => true]);

        foreach (self::TREE as $name => $parameters) {
            $parent->createChildNode($name, $parameters['attr'], $parameters['value'] ?? null, $parameters['type']);
        }

        return $parent;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testFindByPureName(): void
    {
        $tree = $this->createNodeTree();

        [$span, $a, $h1, $h2, $w_h2, $w_span] = $tree->getChildren();

        /** @noinspection PhpParamsInspection */
        self::assertEquals([$span, $w_span], $tree->findByName('span'));
        /** @noinspection PhpParamsInspection */
        self::assertEquals($a, $tree->findByName('a')[0]);
        /** @noinspection PhpParamsInspection */
        self::assertEquals($h1, $tree->findByName('h1')[0]);
        /** @noinspection PhpParamsInspection */
        self::assertEquals([$h2, $w_h2], $tree->findByName('h2'));
    }

    /**
     * @throws Exception
     */
    public function testFindByPurePrefix(): void
    {
        $tree = $this->createNodeTree();

        [$span, /* $a */, $h1, /* $h2 */, $h2] = $tree->getChildren();

        $children = [$span, $h1, $h2];
        $find = $tree->findByName('w:');

        /** @noinspection PhpParamsInspection */
        for ($i = 0; $i < count($children); ++$i) {
            self::assertEquals($children[$i], $find[$i]);
        }
    }

    /**
     * @throws Exception
     */
    public function testFindByPrefixAndName(): void
    {
        $tree = $this->createNodeTree();

        /** @noinspection PhpParamsInspection */
        self::assertEquals($tree->getChildren()[4], $tree->findByName('w:h2')[0]);
    }
}