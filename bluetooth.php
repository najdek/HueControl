<?php
// This script will turn on/off all lights in group based on bluetooth device ping response

$main_path = "/home/pi/hue";

$statusFile = $main_path . "/last-status.txt";
$logFile = $main_path . "/log.txt";

$bt_address = "01:23:45:67:89:00"; // address of bluetooth device (e.g., smartphone, smartwatch)
$off_retries = 3; // lights will turn off if ping fails this amount of times
$hueIP = "192.168.1.123"; // ip address of bridge
$hueUser = "1234"; // api key for bridge
$hueGroupId = "1"; // id of group to turn on/off

include("HueControl.php");

$bt_status = shell_exec('if l2ping '.$bt_address.' -s 0 -c 1 -t 10 > /dev/null; then printf "connected"; else printf "not_connected"; fi');

if (file_exists($statusFile)) {
  $lastStatus = json_decode(file_get_contents($statusFile), true);
} else {
  $lastStatus["state"] = "not_connected";
}

if ($bt_status == "connected") {
  echo "Is connected";
  if ($lastStatus["state"] == "connected") {
  } else {
    $newStatus["state"] = "connected";
    $newStatus["huedata"] = hueGroupGet($hueGroupId);
    file_put_contents($statusFile, json_encode($newStatus));
    hueGroupTurn($hueGroupId, true);
    file_put_contents($logFile, date('Y-m-d_H:i:s') . "|on" . PHP_EOL, FILE_APPEND);
  }
} else {
  echo "Not connected";
  if ($lastStatus["state"] == "not_connected") {
  } else {
    if (($lastStatus["retries"] < $off_retries) && ($off_retries !== 0)) {
      $newStatus["retries"] = $lastStatus["retries"] + 1;
      $newStatus["state"] = "connected";
      $newStatus["huedata"] = $lastStatus["huedata"];
      file_put_contents($statusFile, json_encode($newStatus));
    } else {
      $newStatus["state"] = "not_connected";
      $newStatus["huedata"] = hueGroupGet($hueGroupId);
      file_put_contents($statusFile, json_encode($newStatus));
      hueGroupTurn($hueGroupId, false);
      file_put_contents($logFile, date('Y-m-d_H:i:s') . "|off" . PHP_EOL, FILE_APPEND);
    }
  }
}

if (file_exists($logFile)) {
  $log = array_reverse(file($logFile));
  $log = array_slice($log, 0, 100);
  file_put_contents($logFile, array_reverse($log));
}
