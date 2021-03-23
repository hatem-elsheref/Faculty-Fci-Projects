<?php
//https://paginas.fe.up.pt/~ec/files_0506/slides/04_AssociationRules.pdf
//https://www.javatpoint.com/apriori-algorithm-in-machine-learning
//https://www.it.uu.se/edu/course/homepage/infoutv/ht06/dut-ch6-association-2.pdf
//https://www.codeproject.com/Articles/70371/Apriori-Algorithm
namespace Elsheref\KBS\LastChapter;

$transactions_1 = [
    100 => ['A','C','D'],
    200 => ['B','C','E'],
    300 => ['A','B','C','E'],
    400 => ['B','E']
];
$transactions_2 = [
    100 => ['A','B'],
    200 => ['B','D'],
    300 => ['B','C'],
    400 => ['A','B','D'],
    500 => ['A','C'],
    600 => ['B','C'],
    700 => ['A','C'],
    800 => ['A','B','C','E'],
    900 => ['A','B','C']
];

$transactions_3 = [
    100 => ['A','C','B'],
    200 => ['A','C'],
    300 => ['A','D'],
    400 => ['B','E','F']
];

$transactions_4 = [
    100 => ['K','A','D','B'],
    200 => ['D','A','C','E','B'],
    300 => ['A','C','B','E'],
    400 => ['B','A','D']
];


function apriorAlgorithm(array $transactions , float $minSupport , float $minConfidence , $percentageMode = true){

    if (empty($transactions))
        return 'please enter a valid transactions dataset';

    if ($percentageMode)
    $minSupport = ($minSupport / 100) * count($transactions);

    // generate the item list of my (store as for example)

    $lastSituation = [];

    $itemList = [];
    foreach ($transactions as $transactionId => $transactionItemList){
        $itemList = array_merge($itemList , $transactionItemList);
    }

    $frequencyItemList = array_count_values($itemList);

    array_map(function ($key , $value) use ($minSupport , &$frequencyItemList){

        if ($value < $minSupport){
        unset($frequencyItemList[$key]);
        }

    } , array_keys($frequencyItemList),array_values($frequencyItemList));


    $itemList = array_keys($frequencyItemList);
    sort($itemList);



    $list = level($transactions , $itemList , $minSupport);
    $lastSituation = $list;

    while (true){

        $list = level($transactions , $list , $minSupport);

        if (empty($list)){
            $list = $lastSituation;
            break;
        }
        else
           $lastSituation = $list;
    }



    $rules = getRules($list);


    $result = [];

    foreach ($rules as $rule){

        $mainItems = array_values(array_unique(str_split(implode('',$rule))));
        $support = getSupport($transactions , $mainItems);
//        $support = getSupport($transactions , $rule);

        $res = confidence($transactions , $rule , $mainItems , $support , $minConfidence);
        $result = array_merge($result , $res);
    }



    var_dump($result);
//    return $result;

}

function confidence(array $transactions , array $lisOfRules , array $mainItems , float $support , float $minConfidence){


    $result = [];


    foreach ($lisOfRules as $rule){
        $rule = [...str_split($rule)];
//        echo $support . '/' . getSupport($transactions , $rule).PHP_EOL;
        $confidence = $support / getSupport($transactions , $rule);
        $confidence = $confidence * 100;
        echo 'confidence = '.$confidence . ' > '.$minConfidence .PHP_EOL ;
        if ($confidence >= $minConfidence){
            $string = '{ ' . implode('',$rule) . ' -> ' . implode(',' , array_diff($mainItems , $rule)) . ' }';
            $result[] =  ['rule' => $string , 'support' => $support , 'confidence' => $confidence . '%'];
//            array_push($result , ['message' => $string , 'support' => $support , 'confidence' => $confidence]);

        }
    }


    return $result;

}

function getRules(array $list){

    $result = [];
    foreach ($list as $itemList){
        $all_possible_strings = [];
        foreach ($itemList as $item){
            array_push($all_possible_strings , $item.implode('',array_diff($itemList , [$item])));
        }

        $res = [];
        foreach ($all_possible_strings as $item){
            $res = array_merge($res , generateAllPossibleValues($item));
        }

        $data = array_unique($res);
        $new = [];
        foreach ($data as $item){
            $x = str_split($item);
            sort($x);
            array_push($new , implode('',$x));
        }

        array_push($result , array_unique($new));
    }

    return $result;
}

function level(array $transactions , array $itemList , float $minSupport){
    $newList = [];
    $unique = [];
    $tmp = [];
    for ($i = 0 ; $i < count($itemList) - 1 ; $i++){

        for ($j = $i + 1 ; $j < count($itemList)  ; $j++){
            array_push($tmp , $itemList[$i]);
            array_push($tmp , $itemList[$j]);

            if (is_array($tmp[0]) && is_array($tmp[1]))
                $tmp = [...$tmp[0] , ...$tmp[1]];
            elseif (is_array($tmp[0]) && !is_array($tmp[1]))
                $tmp = [$tmp[0] , ...$tmp[0]];
            elseif (!is_array($tmp[0]) && is_array($tmp[1]))
                $tmp = [$tmp[0] , ...$tmp[1]];


            $counter = 0;
            foreach ($transactions as $id => $items){
                $notFound = false;
                for ($k = 0 ; $k < count($tmp) ; $k++){
                    if (!in_array($tmp[$k] , $items)){
                        $notFound = true;
                        break;
                    }
                }
                if (!$notFound)
                    $counter++;

            }

            if ($counter >= $minSupport){
                sort($tmp);
                $tmp = array_unique($tmp);
                $implodedItems = implode('' , $tmp);
                if (!in_array($implodedItems , array_keys($unique))){
                    array_push($newList , $tmp);
                    $unique[$implodedItems] = $counter;
                }
            }

            $tmp = [];
        }
    }


    return $newList;
}

function generateAllPossibleValues(string $string){

    $len = strlen($string);
    $arr = array();

    //This loop maintains the starting character
    for($i = 0; $i < $len; $i++){
        //This loop adds the next character every iteration for the subset to form and add it to the array
        for($j = 0; $j < $len - $i; $j++){
            $s = substr($string,$i,($j+1));
            if (strlen(substr($string,$i,($j+1))) !== strlen($string))
                array_push($arr,$s);
        }
    }

    return $arr;

}

function getSupport(array $transactions , array $setOfItems)
{
    echo 'current row => ' . implode(',' , $setOfItems).PHP_EOL;
    $counter = 0;
    foreach ($transactions as $id => $items) {
        $Founded = true;
        for ($k = 0; $k < count($setOfItems); $k++) {
           $v = $setOfItems[$k] . ' => in =>  ' . implode(',' , $items);
           $v.=in_array($setOfItems[$k], $items) ? ' yes' : ' no';
           echo $v.PHP_EOL;

            if (!in_array($setOfItems[$k], $items)) {
                $Founded = false;
                break;
            }
        }

        if ($Founded)
            $counter++;

    }
    return $counter;
}



// test the algorithm
$strong_association_rules = \Elsheref\KBS\LastChapter\apriorAlgorithm($transactions_4 , 60 , 80 , true);



