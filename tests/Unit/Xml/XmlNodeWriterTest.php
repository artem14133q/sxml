<?php

namespace Tests\Unit\Xml;

use Exception;
use Sxml\Nodes\AbstractNode;
use Sxml\Nodes\Xml\XmlNode;
use Tests\Unit\SingleNodeWriterTest;

class XmlNodeWriterTest extends SingleNodeWriterTest
{
    const DATA_NAME = 'xml';

    const DATA_A_1 = 'version';
    const DATA_A_2 = 'encoding';
    const DATA_A_3 = 'standalone';

    const DATA_A_1_VAL = '1.0';
    const DATA_A_2_VAL = 'UTF-8';
    const DATA_A_3_VAL = 'yes';

    /**
     * @param int $depth
     * @param bool $beautify
     * @param bool $prefix
     * @return string
     */
    protected function getResult(int $depth = 0, bool $beautify = false, bool $prefix = false): string
    {
        return implode([
            "<?",
                self::DATA_NAME,
                " ", self::DATA_A_1, '="', self::DATA_A_1_VAL, '"',
                " ", self::DATA_A_2, '="', self::DATA_A_2_VAL, '"',
                " ", self::DATA_A_3, '="', self::DATA_A_3_VAL, '"',
            '?>'
        ]);
    }

    /**
     * @param bool $prefix
     * @return AbstractNode
     * @throws Exception
     */
    protected function getNode(bool $prefix = false): AbstractNode
    {
        return new XmlNode([
            self::DATA_A_1 => self::DATA_A_1_VAL,
            self::DATA_A_2 => self::DATA_A_2_VAL,
            self::DATA_A_3 => self::DATA_A_3_VAL
        ]);
    }
}