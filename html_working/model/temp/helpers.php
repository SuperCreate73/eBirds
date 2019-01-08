<?php

function convertirTimezone($time,$deTz,$versTz)
    {
        // timezone by php friendly values
        $date = new DateTime($time, new DateTimeZone($deTz));
        $date->setTimezone(new DateTimeZone($versTz));
        $time= $date->format('Y-m-d H:i:s');
        return $time;
	}

