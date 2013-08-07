<?php

class Model_Helper_Date {

  public static function getDifferenceTime($date) {
    list($year, $month, $day) = explode('-', $date);
    list($current_year, $current_month, $current_day) = explode('-', date('Y-m-d', time()));


    return (($current_year - $year) * 365 + (($month - $current_month) * 30) +  ($day - $current_day));
  }

}