# IndexArray

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
    
    $start = microtime(1);
    echo $indexedArray1->merge($indexedArray2)->getSize();    // 2000000
    $end = microtime(1);
    echo "time :" . ($end - $start);                          // 0.975s
    echo memory_get_usage(1);                                 // 186122240 => 177.5m
    echo memory_get_peak_usage(1);                            // 346030080 => 330m
  


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
