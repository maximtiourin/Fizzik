<?php

namespace Fizzik\Utility;


class AssocArray {
    const AGGREGATE_SUM = 1;

    /*
     * Given a list of keys in a chain, will return true if those keys exist after chaining them start at an arr
     * object root. EX: if given array $arr, and a key chain of ['foo', 'bar', 'hello']
     * then will return whether or not the following key exists: $arr['foo']['bar']['hello']
     */
    public static function keyChainExists($arr, ...$chain) {
        $root = $arr;
        foreach ($chain as $key) {
            if (key_exists($key, $root)) {
                $root = $root[$key];
            }
            else {
                return false;
            }
        }

        return true;
    }

    /**
     * Given two associative arrays, lhs and rhs, will create a composite array containing a set of all key chains found
     * within both arrays, where any duplicate keys with numeric values had their values aggregated based on the aggregation type.
     * Default aggregation type is AGGREGATE_SUM
     * @param $lhs
     * @param $rhs
     * @param int $aggregationType
     */
    public static function aggregate(&$resultArray, &$lhs, &$rhs, $aggregationType = self::AGGREGATE_SUM) {
        switch ($aggregationType) {
            case self::AGGREGATE_SUM:
                self::aggregate_sum($resultArray, $lhs);
                self::aggregate_sum($resultArray, $rhs);
                break;
        }
    }

    private static function aggregate_sum(&$resultArray, &$src) {
        foreach ($src as $key => &$val) {
            if (key_exists($key, $resultArray)) {
                $rval = &$resultArray[$key];

                //Determine
                if (is_numeric($val) && is_numeric($rval)) {
                    //Aggregate
                    $resultArray[$key] = $resultArray[$key] + $val;
                }
                else if (is_array($val) && self::hasStringKeys($val) && is_array($rval) && self::hasStringKeys($rval)) {
                    //Chain
                    self::aggregate_sum($resultArray[$key], $src[$key]);
                }
                //Else Ignore
            }
            else {
                //set
                $resultArray[$key] = $val;
            }
        }
    }

    /*
     * Returns whether or not the given array has string keys
     */
    public static function hasStringKeys(&$array) {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}