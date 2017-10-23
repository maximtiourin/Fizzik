<?php
/**
 * Created by PhpStorm.
 * User: Maxim
 * Date: 10/22/2017
 * Time: 5:18 PM
 */

namespace Fizzik\Utility;


class AssocArray {
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
}