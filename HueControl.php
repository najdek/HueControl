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
