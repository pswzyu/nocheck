<?php

function get_yearmonth_page($start, $end, $page_now, $page_size)
{
    // first split the string into year and month
    $start = explode("-", $start);
    $start_y = $start[0];
    $start_m = $start[1];
    
    $end = explode("-", $end);
    $end_y = $end[0];
    $end_m = $end[1];
    
    if ( intval($start_y.$start_m) > intval( $end_y.$end_m ) ) {
        die("yearmonth.php:start is larger than end!");
    }
    
    $start_m = intval($start_m);
    $start_y = intval($start_y);
    $end_m = intval($end_m);
    $end_y = intval($end_y);
    
    $result = array( "total_page"=>0, "pagename_list"=>[] );
    
    $result["total_page"] = ceil((($end_y - $start_y)*12 + $end_m - $start_m + 1)
            / $page_size );
    
    $ignore = $page_now * $page_size;
    $have_ignored = 0;
    
    while( true )
    {
        if ($start_m < 10)
        {
            $strstart_m = "0".$start_m;
        }else{
            $strstart_m = "".$start_m;
        }
        
        if ( $have_ignored >= $ignore )
        {
            $result["pagename_list"][] = "".$start_y."-".$strstart_m;
        }
        $have_ignored ++;
        
        // increase the year month by one month
        $start_m ++;
        if ( $start_m >= 13 )
        {
            $start_m = 1;
            $start_y += 1;
        }
        
        // end when reach to the end or hit the page size requirement
        // because we should include the last month, so month should add 1
        if ( ($start_m == $end_m+1 && $start_y == $end_y)
                || count($result["pagename_list"]) >= $page_size )
        {
            break;
        }
        
    }
    
    return $result;
}


print_r( get_yearmonth_page("2012-5", "2013-11", 5, 3) );


?>
