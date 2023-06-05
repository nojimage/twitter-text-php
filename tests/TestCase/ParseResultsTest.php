<?php

/**
 * @author    Takashi Nojima
 * @copyright Copyright 2018, Takashi Nojima
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package   Twitter.Text
 */

namespace Twitter\Text\TestCase;

use PHPUnit\Framework\TestCase;
use Twitter\Text\ParseResults;

/**
 * Twitter Text ParseResults Unit Tests
 *
 * @author    Takashi Nojima
 * @copyright Copyright 2018, Takashi Nojima
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License v2.0
 * @package   Twitter.Text
 */
class ParseResultsTest extends TestCase
{
    private ParseResults $results;

    /**
     * Set up fixtures
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->results = new ParseResults();
    }

    /**
     * Tears down fixtures
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->results);
    }

    /**
     * test for new result
     *
     * @return void
     */
    public function testConstruct()
    {
        $result = new ParseResults(192, 685, true, [0, 210], [0, 210]);

        $this->assertSame(192, $result->weightedLength);
        $this->assertSame(685, $result->permillage);
        $this->assertSame(true, $result->valid);
        $this->assertSame(0, $result->displayRangeStart);
        $this->assertSame(210, $result->displayRangeEnd);
        $this->assertSame(0, $result->validRangeStart);
        $this->assertSame(210, $result->validRangeEnd);
    }

    /**
     * test get empty result
     *
     * @return void
     */
    public function testConstructEmpty()
    {
        $result = new ParseResults();

        $this->assertSame(0, $result->weightedLength);
        $this->assertSame(0, $result->permillage);
        $this->assertSame(false, $result->valid);
        $this->assertSame(0, $result->displayRangeStart);
        $this->assertSame(0, $result->displayRangeEnd);
        $this->assertSame(0, $result->validRangeStart);
        $this->assertSame(0, $result->validRangeEnd);
    }

    /**
     * test for array
     */
    public function testToArray()
    {
        $result = new ParseResults(192, 685, true, [0, 210], [0, 210]);

        $this->assertSame([
            'weightedLength' => 192,
            'valid' => true,
            'permillage' => 685,
            'displayRangeStart' => 0,
            'displayRangeEnd' => 210,
            'validRangeStart' => 0,
            'validRangeEnd' => 210,
        ], $result->toArray());
    }

    /**
     * test set variable
     *
     * @dataProvider dataSetVariable
     */
    public function testSetVariable($message, $key, $value, $expected)
    {
        $this->results->$key = $value;

        $this->assertSame($expected, $this->results->$key, $message);
    }

    /**
     * data for testSetVariable
     *
     * @return array
     */
    public function dataSetVariable()
    {
        return [
            ['weightedLength to be integer', 'weightedLength', '1', 1],
            ['permillage to be integer', 'permillage', '1', 1],
            ['isValid to be boolean', 'valid', '1', true],
            ['displayRangeStart to be integer', 'displayRangeStart', '0', 0],
            ['displayRangeEnd to be integer', 'displayRangeEnd', '0', 0],
            ['validRangeStart to be integer', 'validRangeStart', '0', 0],
            ['validRangeEnd to be integer', 'validRangeEnd', '0', 0],
        ];
    }

    /**
     * test set valiable
     *
     * @dataProvider dataSetInvalidRange
     */
    public function testSetInvalidRange($message, $key, $value)
    {
        $this->expectException(\RangeException::class);

        $this->results->$key = $value;
    }

    /**
     * data for testSetInvalidRange
     *
     * @return array
     */
    public function dataSetInvalidRange()
    {
        return [
            ['displayRangeStart less than displayRangeEnd', 'displayRangeStart', 1],
            ['validRangeStart less than validRangeEnd', 'validRangeStart', 1],
        ];
    }
}
