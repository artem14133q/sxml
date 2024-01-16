<?php

namespace Tests\Unit;

use Exception;
use PHPUnit\Framework\TestCase;
use Sxml\Nodes\AbstractNode;
use Sxml\Nodes\Node;
use Sxml\Writers\AbstractWriter;
use Sxml\Writers\NodeWriter;

class NodeWriterTest extends TestCase
{
    const DATA_PREFIX = 'w';

    const DATA_NAME = 'div';
    const DATA_VALUE = 'Test';

    const DATA_A_1 = 'a';
    const DATA_A_2 = 'b';
    const DATA_A_3 = 'c';

    const DATA_A_1_VAL = 'aVal';
    const DATA_A_2_VAL = true;
    const DATA_A_3_VAL = false;

    /**
     * @param int $depth
     * @param bool $beautify
     * @param bool $prefix
     * @return string
     */
    protected function getResult(int $depth = 0, bool $beautify = false, bool $prefix = false): string
    {
        $newLine = $beautify ? AbstractWriter::NEW_LINE : '';
        $tabs = $beautify ? str_repeat(AbstractWriter::TAB, $depth) : '';
        $tab = $beautify ? AbstractWriter::TAB : '';
        $prefix = $prefix ? self::DATA_PREFIX . ':' : '';

        return implode([
            $tabs, "<",
                $prefix, self::DATA_NAME,
                " ", self::DATA_A_1, '="', self::DATA_A_1_VAL, '"',
                " ", self::DATA_A_2,
                " ", self::DATA_A_3, '="false"',
            '>', $newLine,
                $tabs, $tab, self::DATA_VALUE, $newLine,
            $tabs, '</', $prefix, self::DATA_NAME, '>'
        ]);
    }

    /**
     * @param bool $prefix
     * @return AbstractNode
     * @throws Exception
     */
    protected function getNode(bool $prefix = false): AbstractNode
    {
        $node = new Node(self::DATA_NAME, [
            self::DATA_A_1 => self::DATA_A_1_VAL,
            self::DATA_A_2 => self::DATA_A_2_VAL,
            self::DATA_A_3 => self::DATA_A_3_VAL,
        ], self::DATA_VALUE);

        return $prefix ? $node->setOption('prefix', self::DATA_PREFIX) : $node;
    }

    /**
     * @return array
     */
    protected function getDump(): array
    {
        return [
            'name' => self::DATA_NAME,
            'attributes' => [
                self::DATA_A_1 => self::DATA_A_1_VAL,
                self::DATA_A_2 => self::DATA_A_2_VAL,
                self::DATA_A_3 => self::DATA_A_3_VAL,
            ],
            'options' => [
                'prefix' => null,
            ],
            'value' => self::DATA_VALUE,
            'children' => []
        ];
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSerializeNodeWithPrefix(): void
    {
        $node = $this->getNode(true);

        $this->assertEquals($this->getResult(0, false, true), $node->writer()->asText());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSerializeNodeWithDepthBeautify(): void
    {
        $depths = [1, 2, 3];

        foreach ($depths as $depth) {
            $node = $this->getNode();

            $writerClass = $node->writerClass();

            /** @var NodeWriter $writer */
            $writer = new $writerClass($node, $depth, true);

            $this->assertEquals($this->getResult($depth, true), $writer->asText());
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testSerializeNodeBeautify(): void
    {
        $this->assertEquals($this->getResult(0, true), $this->getNode()->writer()->beautify()->asText());
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testDumpNode(): void
    {
        $this->assertEquals($this->getDump(), $this->getNode()->dump());
    }
}