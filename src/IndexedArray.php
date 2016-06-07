<?php

namespace QiuQiuX\IndexedArray;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use Iterator;
use SplFixedArray;

class IndexedArray implements ArrayAccess, Countable, Iterator
{

    public $bucketSize;
    protected $currentSize = 0;
    public $fixedArray;

    public function __construct($bucketSize = 8)
    {
        $this->bucketSize = $bucketSize;
        $this->fixedArray = new SplFixedArray($this->bucketSize);
    }

    /**
     * Create from array.
     * @param $array
     * @param bool $saveIndex
     * @return static
     */
    public static function createFormArray($array, $saveIndex = true)
    {
        if ($saveIndex) {
            $max = max(array_keys($array)) + 1;
        } else {
            $max = count($array);
        }

        $indexArray = new static(0);
        $indexArray->fixedArray = SplFixedArray::fromArray($array, $saveIndex);
        $indexArray->currentSize = $max;
        $indexArray->bucketSize = $max;
        return $indexArray;
    }

    /**
     * Create from SplFixedArray.
     * @param SplFixedArray $fixedArray
     * @return static
     */
    public static function createFromFixedArray(SplFixedArray $fixedArray)
    {
        if (!$fixedArray instanceof SplFixedArray) {
            $type = is_object($fixedArray) ? get_class($fixedArray) : gettype($fixedArray);
            throw new InvalidArgumentException('SplFixedArray require,' . $type . ' given.');
        }

        $size = count($fixedArray);
        $indexArray = new static($size);
        $indexArray->fixedArray = $fixedArray;
        $indexArray->currentSize = $size;
        $indexArray->bucketSize = $size;
        return $indexArray;
    }

    /**
     * @return mixed|null
     */
    public function pop()
    {
        if ($this->currentSize == 0) {
            return null;
        }

        $val = $this->fixedArray[$this->currentSize - 1];
        $this->fixedArray->offsetUnset(--$this->currentSize);
        return $val;
    }

    /**
     * @param $val
     */
    public function push($val)
    {
        $this->checkAndResizeIfNecessary();
        $this->fixedArray[$this->currentSize++] = $val;
    }

    /**
     * @return mixed|null
     */
    public function shift()
    {
        if ($this->currentSize == 0) {
            return null;
        }

        $array = $this->fixedArray->toArray();
        $val = array_shift($array);
        $this->fixedArray = SplFixedArray::fromArray($array, false);
        --$this->currentSize;
        $this->adjustSize();

        return $val;
    }

    /**
     * @param $val
     */
    public function unshift($val)
    {
        $this->checkAndResizeIfNecessary();

        $array = $this->fixedArray->toArray();
        array_unshift($array, $val);
        $this->fixedArray = SplFixedArray::fromArray($array);
        ++$this->currentSize;
        $this->adjustSize();
    }

    /**
     * Remove the duplicate values from the instance,
     * return a new IndexedArray instance.
     * @return IndexedArray
     */
    public function unique()
    {
        $array = $this->toArray();
        $array = array_unique($array, SORT_REGULAR);
        return static::createFormArray($array, false);
    }

    /**
     * @param $value
     * @param bool $strict
     * @return bool|int
     */
    public function search($value, $strict = false)
    {
        $array = $this->toArray();
        if (!$this->isCallable($value)) {
            return array_search($value, $array, $strict);
        }

        foreach ($array as $key => $item) {
            if (call_user_func($value, $item, $key)) {
                return $key;
            }
        }

        return false;
    }

    /**
     * Transform the value using the callback function,
     * return a new IndexedArray instance.
     * @param callable $callback
     * @param bool $reference
     * @return $this
     */
    public function transform(callable $callback, $reference = true)
    {
        $array = $this->toArray();
        $reference ? array_walk($array, $callback) : array_map($callback, $array);
        $indexedArray = static::createFormArray($array);
        $indexedArray->bucketSize = $this->getSize();

        return $indexedArray;
    }

    /**
     * Reverse the values,
     * return a new IndexedArray instance.
     * @return IndexedArray
     */
    public function reverse()
    {
        $array = $this->toArray();
        return static::createFormArray(array_reverse($array));
    }

    /**
     * Merge the given IndexedArrays,
     * return a new IndexedArray instance.
     * @param IndexedArray $indexedArray
     * @return IndexedArray
     */
    public function merge(IndexedArray $indexedArray)
    {
        $newIndexArray = clone $this;

        $currentSize = $newIndexArray->currentSize;

        // Manually set the size to avoid multiple adjustments.
        $total = $currentSize + $indexedArray->currentSize;
        $newIndexArray->fixedArray->setSize($total);
        $newIndexArray->bucketSize = $total;
        $newIndexArray->currentSize = $total;

        foreach ($indexedArray as $val) {
            $newIndexArray->fixedArray->offsetSet($currentSize++, $val);
        }

        return $newIndexArray;
    }

    protected function adjustSize()
    {
        $this->fixedArray->setSize($this->bucketSize);
    }

    protected function checkAndResizeIfNecessary()
    {
        if ($this->currentSize + 1 > $this->bucketSize) {
            $this->bucketSize = $this->bucketSize << 1;
            $this->fixedArray->setSize($this->bucketSize);
        }
    }

    protected function isCallable($value)
    {
        return is_callable($value);
    }

    public function offsetExists($offset)
    {
        return $this->fixedArray->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->fixedArray->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->push($value);
        } else {
            $offset = intval($offset);
            if ($offset >= $this->currentSize) {
                $this->currentSize = $offset + 1;
                $this->bucketSize = $this->currentSize;
                $this->adjustSize();
            }
            $this->fixedArray->offsetSet($offset, $value);
        }
    }

    public function offsetUnset($offset)
    {
        $this->fixedArray->offsetUnset($offset);
    }

    public function count()
    {
        return $this->getSize();
    }

    public function getSize()
    {
        return $this->currentSize;
    }

    public function toJson($option = 0)
    {
        return json_encode($this->toArray(), $option);
    }

    public function __toString()
    {
        return $this->toJson();
    }

    public function toArray()
    {
        $array = $this->fixedArray->toArray();
        if ($this->currentSize != $this->bucketSize) {
            $array = array_splice($array, 0, $this->currentSize);
        }
        return $array;
    }

    public function current()
    {
        return $this->fixedArray->current();
    }

    public function next()
    {
        $this->fixedArray->next();
    }

    public function valid()
    {
        if ($this->key() < $this->currentSize) {
            return true;
        }

        return false;
    }

    public function key()
    {
        return $this->fixedArray->key();
    }

    public function rewind()
    {
        $this->fixedArray->rewind();
    }
}
 