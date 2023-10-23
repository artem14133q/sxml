<?php

namespace Tests\Unit\Html;

use Exception;
use Sxml\Nodes\AbstractNode;
use Sxml\Nodes\Html\DoctypeNode;
use Tests\Unit\SingleNodeWriterTest;

class DoctypeNodeWriterTest extends SingleNodeWriterTest
{
    const DATA_NAME = 'DOCTYPE';

    const DATA_A_1 = 'html';
    const DATA_A_1_VAL = true;

    /**
     * @param int $depth
     * @param bool $beautify
     * @param bool $prefix
     * @return string
     */
    protected function getResult(int $depth = 0, bool $beautify = false, bool $prefix = false): string
    {
        return implode(["<!", self::DATA_NAME, ' ', self::DATA_A_1, '>']);
    }

    /**
     * @param bool $prefix
     * @return AbstractNode
     * @throws Exception
     */
    protected function getNode(bool $prefix = false): AbstractNode
    {
        return new DoctypeNode([self::DATA_A_1 => self::DATA_A_1_VAL]);
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
            ],
            'options' => ['prefix' => null]
        ];
    }
}