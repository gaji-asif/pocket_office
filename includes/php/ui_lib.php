<?php

/**
 * Description
 * 
 * @param
 * @return
 */
function calcResultsPerPage($windowHeight, $newTable = FALSE) {
    $denominator = $newTable ? NEW_ROWS_PER_PAGE_DENOMINATOR : ROWS_PER_PAGE_DENOMINATOR;
    return floor($windowHeight / $denominator);
}
