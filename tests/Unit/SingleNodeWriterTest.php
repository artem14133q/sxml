<?php

namespace Tests\Unit;

use Exception;
use Sxml\Nodes\AbstractNode;
use Sxml\Nodes\SingleNode;
use Sxml\Writers\AbstractWriter;

class SingleNodeWriterTest extends NodeWriterTest
{
    /**
     * @param int $depth
     * @param bool $beautify
     * @param bool $prefix
     * @return string
     */
    protected function getResult(int $depth = 0, bool $beautify = false, bool $prefix = false): string
    {
        $tabs = $beautify ? str_repeat(AbstractWriter::TAB, $depth) : '';
        $prefix = $prefix ? self::DATA_PREFIX . ':' : '';

        return implode([
            $tabs, "<",
                $prefix, self::DATA_NAME,
                " ", self::DATA_A_1, '="', self::DATA_A_1_VAL, '"',
                " ", self::DATA_A_2,
                " ", self::DATA_A_3, '="false"',
            '/>'
        ]);
    }

    /**
     * @param bool $prefix
     * @return AbstractNode
     * @throws Exception
     */
    protected function getNode(bool $prefix = false): AbstractNode
    {
        $node = new SingleNode(self::DATA_NAME, [
            self::DATA_A_1 => self::DATA_A_1_VAL,
            self::DATA_A_2 => self::DATA_A_2_VAL,
            self::DATA_A_3 => self::DATA_A_3_VAL,
        ]);

        return $prefix ? $node->setOption('prefix', self::DATA_PREFIX) : $node;
    }

    /**
     * @return array
     */
    protected function getDump(): array
    {
        return [
            'name' => static::DATA_NAME,
            'attributes' => [
                static::DATA_A_1 => static::DATA_A_1_VAL,
                static::DATA_A_2 => static::DATA_A_2_VAL,
                static::DATA_A_3 => static::DATA_A_3_VAL,
            ],
            'options' => ['prefix' => null]
        ];
    }
}