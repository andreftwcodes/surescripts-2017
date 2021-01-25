<?php

set_time_limit(0);


$ListenerClockFile = 'C:/MSX_Listener_Clock.txt';
$SleepTime = 5;	
$WaitTime = 10;		
$MaxPossibleDifference = 11; 

sleep($WaitTime);



if(file_exists($ListenerClockFile))
{
	$Clock = file_get_contents($ListenerClockFile);
	if($Clock == '-1')
	{
		echo 'Speaker Stopped.';
		exit;
	}
	else if((mktime() - $Clock) < $MaxPossibleDifference)
	{
		echo 'Another instance of Speaker has already Started';
		exit;
	}
	else if((mktime() - $Clock) > $MaxPossibleDifference)
	{
		echo 'Speaker Started';
	}
	

	flush();
	while (true == true)
	{	
		file_put_contents($ListenerClockFile, mktime());
		
		
		sleep($SleepTime);
		exit;
	}
}
else
{
	file_put_contents($ListenerClockFile, mktime());
}




?>