# IndexArray

modify from SplFixedArray that can use as array.

#### Examples:

	$a = new IndexedArray();

    $a[] = 'a';
    $a[] = 'b';
    $a[] = 'b';
    $a->pop();
    $a->shift();
    foreach ($a as $val) {
        echo $val;
    }

    echo count($a);      // 1

    $a[] = 'd';         // $a[1] = 'd';
    
***

    $indexedArray1 = new IndexedArray();
    $indexedArray2 = new IndexedArray();
    $max = 1000000;
    for ($i = 0; $i < $max; ++$i) {
        $indexedArray1[] = $i;
        $indexedArray2[] = $max + $i;
    }
    
    echo $indexedArray1->merge($indexedArray2)->getSize();
  


### API
* createFormArray
* createFromFixedArray
* pop
* push
* shift
* unshift
* unique
* last
* search
* transform
* reverse
* merge
* getSize
* toJson
* toArray
