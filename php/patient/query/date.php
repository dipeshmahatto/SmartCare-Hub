<?php
function getCurrentDay()
{
    return strtoupper(date('l'));
}

$currentDay = getCurrentDay();
$days = ['SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY'];
?>