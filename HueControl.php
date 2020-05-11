<?php
$hueSaveddata = array();

function hueLightsSave() {
    global $hueIP; global $hueUser; global $hueSaveddata;
    $data = file_get_contents("http://" . $hueIP . "/api/" . $hueUser . "/lights/");
    $data = json_decode($data, true);
    foreach($data as $lightid => $light) {
        $hueSaveddata[$lightid] = $light["state"];
    }
}

function hueGroupGet($groupid) {
    global $hueIP; global $hueUser;
    $data = file_get_contents("http://" . $hueIP . "/api/" . $hueUser . "/groups/" . $groupid . "/");
    $data = json_decode($data, true);
    return $data;
}

function hueGroupTurn($groupid, $on) {
    global $hueIP; global $hueUser;
    if ($on == "on" || $on == "1") {
      $on = true;
    } else if ($on == "off" || $on == "0") {
      $on = false;
    }
    $stateArray = array (
      "on" => $on
    );
    $request_url = "http://" . $hueIP . "/api/" . $hueUser . "/groups/" . $groupid . "/action";
    $request_opts = [
        "http" => [
            "method" => "PUT",
            "header" => "Content-Type: application/x-www-form-urlencoded",
            "content"=> json_encode($stateArray)
        ]
    ];
    $request_context = stream_context_create($request_opts);
    $request_output = file_get_contents($request_url, false, $request_context);
    return $request_output;
}

function hueLightSetBrightness($lightid, $bri, $transitiontime) {
    global $hueIP; global $hueUser;
    $data = file_get_contents("http://" . $hueIP . "/api/" . $hueUser . "/lights/");
    $data = json_decode($data, true);
    $tmpdata = array();
    foreach($data as $datalightid => $light) {
        $tmpdata[$datalightid] = $light["state"];
    }

    if ($tmpdata[$lightid]["on"] == false) {
      echo "light is off";
      return;
    }


    if ((substr($bri, 0, 1) === '-') || (substr($bri, 0, 1) === '+')) {
      $bri = round(intval($bri) * 2.54);
      if (($tmpdata[$lightid]["bri"] + $bri) > 254) {
        $newbri = 254;
      } else if (($tmpdata[$lightid]["bri"] + $bri) < 0) {
        $newbri = 0;
      } else {
        $newbri = $tmpdata[$lightid]["bri"] + $bri;
      }
    } else {
      $newbri = round(intval($bri) * 2.54);
    }

    $stateArray = array (
      "bri" => $newbri,
      "transitiontime" => $transitiontime
    );

    $request_url = "http://" . $hueIP . "/api/" . $hueUser . "/lights/" . $lightid . "/state";
    $request_opts = [
        "http" => [
            "method" => "PUT",
            "header" => "Content-Type: application/x-www-form-urlencoded",
            "content"=> json_encode($stateArray)
        ]
    ];
    $request_context = stream_context_create($request_opts);
    $request_output = file_get_contents($request_url, false, $request_context);
    return $request_output;
}


function hueLightSetColor($lightid, $on, $bri, $hue, $sat, $transitiontime) {
    global $hueIP; global $hueUser;
    $stateArray = array (
      "on" => $on,
      "bri" => $bri,
      "hue" => $hue,
      "sat" => $sat,
      "transitiontime" => $transitiontime
    );

    $request_url = "http://" . $hueIP . "/api/" . $hueUser . "/lights/" . $lightid . "/state";
    $request_opts = [
        "http" => [
            "method" => "PUT",
            "header" => "Content-Type: application/x-www-form-urlencoded",
            "content"=> json_encode($stateArray)
        ]
    ];
    $request_context = stream_context_create($request_opts);
    $request_output = file_get_contents($request_url, false, $request_context);
    return $request_output;
}

function hueLightSetWhite($lightid, $on, $bri, $ct, $transitiontime) {
    global $hueIP; global $hueUser;
    $stateArray = array (
      "on" => $on,
      "bri" => $bri,
      "ct" => $ct,
      "transitiontime" => $transitiontime
    );

    $request_url = "http://" . $hueIP . "/api/" . $hueUser . "/lights/" . $lightid . "/state";
    $request_opts = [
        "http" => [
            "method" => "PUT",
            "header" => "Content-Type: application/x-www-form-urlencoded",
            "content"=> json_encode($stateArray)
        ]
    ];
    $request_context = stream_context_create($request_opts);
    $request_output = file_get_contents($request_url, false, $request_context);
    return $request_output;
}


function hueLightsRestore($transitiontime) {
    global $hueIP; global $hueUser; global $hueSaveddata;
    foreach ($hueSaveddata as $lightid => $light) {
        if ($light["colormode"] == "hs") {
          $stateArray = array (
            "on" => $light["on"],
            "bri" => $light["bri"],
            "hue" => $light["hue"],
            "sat" => $light["sat"],
            "colormode" => $light["colormode"],
            "transitiontime" => $transitiontime
          );
        } else if ($light["colormode"] == "xy") {
          $stateArray = array (
            "on" => $light["on"],
            "bri" => $light["bri"],
            "xy" => $light["xy"],
            "colormode" => $light["colormode"],
            "transitiontime" => $transitiontime
          );
        } else {
          $stateArray = array (
            "on" => $light["on"],
            "bri" => $light["bri"],
            "ct" => $light["ct"],
            "colormode" => $light["colormode"],
            "transitiontime" => $transitiontime
          );


        }
        $request_url = "http://" . $hueIP . "/api/" . $hueUser . "/lights/" . $lightid . "/state";
        $request_opts = [
            "http" => [
                "method" => "PUT",
                "header" => "Content-Type: application/x-www-form-urlencoded",
                "content"=> json_encode($stateArray)
            ]
        ];
        $request_context = stream_context_create($request_opts);
        $request_output = file_get_contents($request_url, false, $request_context);
    }
}


?>
