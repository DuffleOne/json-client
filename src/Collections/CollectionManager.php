<?php

namespace Duffleman\JSONClient\Collections;

use Illuminate\Support\Collection;

/**
 * Class CollectionManager.
 */
class CollectionManager
{
    /**
     * Build a Collection or Generic depending
     * on what you pass into the function.
     *
     * @param array $array
     *
     * @return Generic|Collection
     */
    public static function build(array $array)
    {
        // If we pass a HashMap, return a Generic.
        if (has_string_keys($array)) {
            return new Generic($array);
        }

        // Otherwise, turn each member into a Generic where appropriate.
        $array = array_map(function ($item) {
            return new Generic($item);
        }, $array);

        // Return a Collection of Generics.
        return new Collection($array);
    }
}
