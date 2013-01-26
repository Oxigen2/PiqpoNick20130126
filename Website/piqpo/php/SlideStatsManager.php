<?php
require_once(getenv("DOCUMENT_ROOT")."/inc_piqpo.php");

class SlideStatsManager
{
	function __construct()
	{
	}	

    function recordUserSlideView( $slideId, $userId )
    {
        // SlideView::create( $userId, $slideId, DBManager::now(), 'browser');
    }
    
    function recordUserSlideFollow( $slideId, $userId )
    {
        SlideFollow::create( $userId, $slideId, DBManager::now(), 'browser');
    }
    
    function recordDeviceSlideView( $slideId, $guid )
    {
        // $this->writeRecord( $slideId, $guid, false );
    }
    
    function recordDeviceSlideFollow( $slideId, $guid )
    {
        $this->writeRecord( $slideId, $guid, true );
    }
    
    // Returns array of ("total" => {views in last day}, "title" => {slide title})
    function lastDaySlideViews()
    {
        $query = "  SELECT count( sv.slide_id ) as total, s.title
                    FROM slide_view sv, slide s
                    WHERE sv.slide_id = s.slide_id
                    AND sv.view_time > adddate( now( ) , -1 )
                    GROUP BY sv.slide_id
                    ORDER BY count( sv.slide_id ) DESC";
        
        $output = array();
		$db = new DBManager("piqpo");
		$result = $db->query($query);
		while ($myrow = mysql_fetch_array($result))
        {
            $row = array( "total" => $myrow["total"], "title" => $myrow["title"] );
            $output[] = $row;
        }
        
        return $output;
    }
    
    function recentSlideViews( $max )
    {
        $query = "  SELECT sv.view_time, u.name, s.title
                    FROM slide_view sv, slide s, user u
                    WHERE sv.slide_id = s.slide_id
                    AND sv.user_id = u.user_id
                    ORDER BY sv.view_time DESC ";
        
        if ( $max > 0 )
        {
            $query .= "LIMIT 0, {$max}";
        }
        
        $output = array();
		$db = new DBManager("piqpo");
		$result = $db->query($query);
		while ($myrow = mysql_fetch_array($result))
        {
            $row = array( "time" => $myrow["view_time"], "user" => $myrow["name"], "slide" => $myrow["title"] );
            $output[] = $row;
        }
        
        return $output;
    }
    
    function recentSlideFollows( $max )
    {
        $query = "  SELECT sf.follow_time, u.name, s.title
                    FROM slide_follow sf, slide s, user u
                    WHERE sf.slide_id = s.slide_id
                    AND sf.user_id = u.user_id
                    ORDER BY sf.follow_time desc ";
        
        if ( $max > 0 )
        {
            $query .= "LIMIT 0, {$max}";
        }
        
        $output = array();
		$db = new DBManager("piqpo");
		$result = $db->query($query);
		while ($myrow = mysql_fetch_array($result))
        {
            $row = array( "time" => $myrow["follow_time"], "user" => $myrow["name"], "slide" => $myrow["title"] );
            $output[] = $row;
        }
        
        return $output;
    }
    
    private function writeRecord( $slideId, $guid, $follow )
    {
        $queryArray = array( "guid" => "'".$guid."'" );
        $device = Device::loadFromDB($queryArray);
        
        if ( count( $device ) )
        {
            if ( $follow )
            {
                SlideFollow::create($device[0]->userId(), $slideId, DBManager::now(), 'screensaver');
            }
            else
            {
                SlideView::create($device[0]->userId(), $slideId, DBManager::now(), 'screensaver');
            }
        }        
    }
    
}
?>
