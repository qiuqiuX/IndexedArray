<?php

namespace QiuQiuX\IndexedArray;

include 'IndexedArray/src/IndexedArray.php';

class IndexedArrayTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers QiuQiuX\IndexedArray::createFormArray
     */
    public function testCreateFormArray()
    {
        $indexedArray = new IndexedArray(3);
        $indexedArray[] = 0;
        $indexedArray[] = 1;
        $indexedArray[] = 2;

        $array = [0,1,2];

        $fromArray = IndexedArray::createFormArray($array);

        $this->assertEquals($indexedArray, $fromArray);
        $this->assertEquals($array, $fromArray->toArray());
        $this->assertCount(3, $indexedArray);
    }

    /**
     * @covers QiuQiuX\IndexedArray::createFromFixedArray
     */
    public function testCreateFromFixedArray()
    {
        $fixedArray = new \SplFixedArray(3);
        $fixedArray[0] = 1;
        $fixedArray[1] = 'abc';
        $fixedArray[2] = ['1'];

        $fromIndexedArray = IndexedArray::createFromFixedArray($fixedArray);

        $indexedArray = new IndexedArray(3);
        $indexedArray[] = 1;
        $indexedArray[] = 'abc';
        $indexedArray[] = [1];

        $this->assertEquals($fromIndexedArray, $indexedArray);
        $this->assertCount(3, $fromIndexedArray);
    }

    /**
     * @covers QiuQiuX\IndexedArray::pop
     */
    public function testPop()
    {
        $first ='char1';
        $second ='char2';
        $third ='char3';

        $indexedArray = new IndexedArray(3);
        $indexedArray[] = $first;
        $indexedArray[] = $second;
        $indexedArray[] = $third;

        $this->assertEquals($third, $indexedArray->pop());
        $this->assertCount(2, $indexedArray);
    }

    /**
     * @covers QiuQiuX\IndexedArray::push
     */
    public function testPush()
    {
        $first = 'char1';
        $second = 'char2';
        $third = 'char3';
        $fourth = 'char4';

        $indexedArray = new IndexedArray(3);
        $indexedArray[] = $first;
        $indexedArray[] = $second;
        $indexedArray[] = $third;
        $indexedArray->push($fourth);

        $this->assertCount(4, $indexedArray);

        $this->assertEquals($fourth, $indexedArray->pop());
    }

    /**
     * @covers QiuQiuX\IndexedArray::shift
     */
    public function testShift()
    {
        $first ='char1';
        $second ='char2';
        $third ='char3';

        $indexedArray = new IndexedArray(3);
        $indexedArray[] = $first;
        $indexedArray[] = $second;
        $indexedArray[] = $third;

        $this->assertEquals($first, $indexedArray->shift());
        $this->assertCount(2, $indexedArray);
    }

    /**
     * @covers QiuQiuX\IndexedArray::unshift
     */
    public function testUnshift()
    {
        $first = 'char1';
        $second = 'char2';
        $third = 'char3';
        $fourth = 'char4';

        $indexedArray = new IndexedArray(3);
        $indexedArray[] = $first;
        $indexedArray[] = $second;
        $indexedArray[] = $third;
        $indexedArray->unshift($fourth);

        $this->assertCount(4, $indexedArray);

        $this->assertEquals($fourth, $indexedArray->shift());
    }

    /**
     * @covers QiuQiuX\IndexedArray::unique
     */
    public function testUnique()
    {
        $array = [
            1,
            1,
            '1',
            '12',
            'x'
        ];

        $fromArray = IndexedArray::createFormArray($array);
        $afterUnique = $fromArray->unique();

        $this->assertCount(3, $afterUnique);
        $this->assertEquals($afterUnique->toArray(), array_values(array_unique($array, SORT_REGULAR)));
    }

    /**
     * @covers QiuQiuX\IndexedArray::search
     */
    public function testSearch()
    {
        $array = [
            'char1',
            'char2',
            'char3',
            'char4',
            '5',
            6,
            5,
        ];

        $fromArray = IndexedArray::createFormArray($array);

        // search success returns the key
        $this->assertTrue($fromArray->search('6', false) == '5');
        $this->assertFalse($fromArray->search('6', true));
    }

    /**
     * @covers QiuQiuX\IndexedArray::transform
     */
    public function testTransform()
    {
        $array = [
            1,
            3,
            5
        ];

        $fromArray = IndexedArray::createFormArray($array);

        $callback = function($item) {
            return $item * $item;
        };

        $afterTransform = $fromArray->transform($callback);

        $newArr = array_map($callback, $array);

        $this->assertEquals(25, $afterTransform[2]);
        $this->assertEquals($newArr, $afterTransform->toArray());
    }

    /**
     * @covers QiuQiuX\IndexedArray::reverse
     */
    public function testReverse()
    {
        $array = [
            1,
            3,
            5
        ];

        $fromArray = IndexedArray::createFormArray($array);
        $afterReverse = $fromArray->reverse();

        $this->assertEquals(5, $afterReverse[0]);
        $this->assertEquals(array_reverse($array), $afterReverse->toArray());
    }

    /**
     * @covers QiuQiuX\IndexedArray::merge
     */
    public function testMerge()
    {
        $range1 = range(1, 100);
        $range2 = range(101, 200);

        $fromArray1 = IndexedArray::createFormArray($range1);
        $fromArray2 = IndexedArray::createFormArray($range2);

        $afterMerge = $fromArray1->merge($fromArray2);

        $this->assertCount(200, $afterMerge);
        $this->assertEquals(array_merge($range1, $range2), $afterMerge->toArray());
    }

    /**
     * @covers QiuQiuX\IndexedArray::getSize
     */
    public function testGetSize()
    {
        $range = range(1, 100);

        $fromArray = IndexedArray::createFormArray($range);

        $this->assertCount(100, $fromArray);

        $fromArray->pop();
        $this->assertCount(99, $fromArray);
    }

    public function testToJson()
    {
        $range = range(1, 100);

        $fromArray = IndexedArray::createFormArray($range);

        $this->assertJsonStringEqualsJsonString($fromArray->toJson(), json_encode($range));
    }

}

 