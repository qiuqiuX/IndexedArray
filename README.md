# IndexArray

#### Example:

	$a = new IndexArray();

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
  


### API
* createFormArray
* createFromFixedArray
* pop
* push
* shift
* unshift
* unique
* search
* transform
* reverse
* getSize
* toJson
* toArray
