<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
function find_time_difference ($time,$time1)
{
    $time = empty($time)?date('Y-m-d H:i:s'):$time;
    $time = strtotime($time);
    $time1 = $time1?$time1:date('Y-m-d H:i:s');
    $time = strtotime($time1) - $time; // to get the time since that moment
    $time = ($time<1)? 1 : $time;
    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }

}
function time_ago ($time)
{
    $time = empty($time)?date('Y-m-d H:i:s'):$time;
    $time = strtotime($time);
    $time = time() - $time; // to get the time since that moment
    $time = ($time<1)? 1 : $time;
    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }

}
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
function employeeUserName($string,$candidate_id)
{
    if(!empty($string))
    {
        $result = split_name($string);

        if(isset($result) && !empty($result))
        {
            $first_name = $result['first_name'];
            $middle_name = $result['middle_name'];
            $last_name = $result['last_name'];

            if(isset($middle_name) && !empty($middle_name))
            {
                $user_name = $first_name."." . $middle_name[0];
                if(isset($last_name) && !empty($last_name))
                {
                    $user_name = $user_name. $last_name[0];
                }
                return $user_name;
            }else{
                if(isset($last_name) && !empty($last_name))
                {
                    $user_name = $first_name."." . $last_name[0];

                }else{
                    $user_name = $first_name."." . $candidate_id;
                }
                return $user_name;
            }


        }
    }
    return false;
}

function split_name($string) {
    $arr = explode(' ', $string);
    $num = count($arr);
    $first_name = $middle_name = $last_name = null;
    if ($num == 1) {
        list($first_name) = $arr;
    }else if ($num == 2) {
        list($first_name, $last_name) = $arr;
    }else if ($num == 3) {
        list($first_name, $middle_name, $last_name) = $arr;
    } else {
        list($first_name) = $arr;
    }

    return (empty($first_name) || $num > 4) ? false : compact(
        'first_name', 'middle_name', 'last_name'
    );
}

function tester($testingVariable){
    echo "<pre>";
    print_r($testingVariable);
    echo "</pre>";
    exit("TESTER");
}


function testerForDisplay($testingVariable){
    echo "<pre>";
    print_r($testingVariable);
    echo "</pre>";
}
function getFirstLastName($name){
    $splitName = explode(' ', $name, 2);

    $first_name = $splitName[0];
    $last_name = !empty($splitName[1]) ? $splitName[1] : '';

    $result = ['first' => $first_name, 'last' => $last_name];
    return $result;
}

function getFirstName($name){
    $splitName = explode(' ', $name, 2);
    $first_name = $splitName[0];
    return $first_name;
}

function roundUpToAny($n,$x=5) {
    return round(($n+$x/2)/$x)*$x;
}
function roundUpToAnyHigh($n,$x=5) {
    return (ceil($n)%$x === 0) ? ceil($n) : round(($n+$x/2)/$x)*$x;
}

function generatePath($slug = 'pobpro')
{
    return $slug.DIRECTORY_SEPARATOR.date('FY').DIRECTORY_SEPARATOR;
}

/**
 * @return string
 */
function generateFileName($file, $path, $preserveFileUploadName = false)
{
    if ($preserveFileUploadName == true) {
        $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
        $filename_counter = 1;

        // Make sure the filename does not exist, if it does make sure to add a number to the end 1, 2, 3, etc...
        while (Storage::disk(config('voyager.storage.disk'))->exists($path.$filename.'.'.$file->getClientOriginalExtension())) {
            $filename = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension()).(string) ($filename_counter++);
        }
    } else {
        $filename = Str::random(20);

        // Make sure the filename does not exist, if it does, just regenerate
        while (Storage::disk(config('voyager.storage.disk'))->exists($path.$filename.'.'.$file->getClientOriginalExtension())) {
            $filename = Str::random(20);
        }
    }

    return $filename;
}


function db_date_format($date)
{
    if(empty($date))
    {
        return  date('Y-m-d');
    }

    $date = strtotime($date);
    return date('Y-m-d', $date);
}


function db_datetime_format($date)
{
    if(empty($date))
    {
        $date = date('Y-m-d H:i:s');
    }

    $date = strtotime($date);
    return date('Y-m-d H:i:s', $date);
}

function findEducation($value = 2)
{
    switch ($value)
    {
        case 1:
            $ret = "High-School / Secondary";
            break;
//        case 2:
//            $ret = "Bachelors Degree";
//            break;
        case 3:
            $ret = "Masters Degree";
            break;
        case 4:
            $ret = "PHD";
            break;
        default:
            $ret = "Bachelors Degree";
            break;

    }
    return $ret;
}

function salaryToId($value = 2)
{
    switch ($value)
    {
        case ($value < 2000):
            $ret = 1;
            break;
        case($value>2000 && $value < 4000):
            $ret = 2;
            break;
        case($value>4000 && $value < 6000):
            $ret = 3;
            break;
        case($value>6000 && $value < 8000):
            $ret = 4;
            break;
        case($value>8000 && $value < 12000):
            $ret = 5;
            break;
        case($value>12000 && $value < 20000):
            $ret = 6;
            break;
        default:
            $ret = 7;
            break;

    }
    return $ret;
}

function idToSalary($value)
{
    switch ($value)
    {
        case ($value == 1):
            $ret = '2,000';
            break;
        case($value == 2):
            $ret = '4,000';
            break;
        case($value == 3):
            $ret = '6,000';
            break;
        case($value == 4):
            $ret = '8,000';
            break;
        case($value == 5):
            $ret = '12,000';
            break;
        case($value == 6):
            $ret = '20,000';
            break;
        default:
            $ret = '';
            break;

    }
    return $ret;
}

function LetterStatusBtn($status)
{
    switch ($status)
    {
        case ($status == 1):
            $ret = '<small class="badge badge-primary ml-2"><small class="fas fa-check mr-1"></small> Assigned</small>';
            break;
        case ($status == 2):
            $ret = '<small class="badge badge-primary ml-2"><small class="fas fa-check mr-1"></small> Processing</small>';
            break;
        case ($status == 3):
            $ret = '<small class="badge badge-primary ml-2"><small class="fas fa-check mr-1"></small> Replied</small>';
            break;

        case ($status == 4):
            $ret = '<small class="badge badge-primary ml-2"><small class="fas fa-check mr-1"></small> Closed</small>';
            break;
        default:
            $ret = '<small class="badge badge-primary ml-2"><small class="fas fa-check mr-1"></small> New</small>';
            break;

    }
    return $ret;
}


/*
	* Get report type
	*/
 function getReportType($report_type = 0)
{
    $type = '';

    switch ($report_type) {
        case 0:
            $type = 'Daily';
            break;

        case 1:
            $type = 'Weekly';
            break;

        case 3:
            $type = 'Monthly';
            break;

        default:
            $type = 'Other';
            break;
    }

    return $type;
}
