<?php
include_once("HueControl.php");

// SET HUE BRIDGE IP ADDRESS
$hueIP = "";
// SET HUE BRIDGE USERNAME
$hueUser = "";

// SAVE LIGHTS
hueLightsSave();

// SET COLOR
//   VARIABLES:
//   * light id
//   * turned on [true/false]
//   * brightness [0-254]
//   * hue [0-65535]
//   * saturation [0-254]
//   * transition time [centiseconds]
hueLightSetColor(3, true, 254, 182*235, 254, 2);

sleep(2);
// SET WHITE
//   VARIABLES:
//   * light id
//   * turned on [true/false]
//   * brightness [0-254]
//   * white color temperature [154-500]
//   * transition time [centiseconds]
hueLightSetWhite(3, true, 100, 400, 2);

sleep(2);
// SET BRIGHTNESS
//   VARIABLES:
//   * light id
//   * string with brightness or brightness offset.
//     Example values:
//     "-50" to decrease current brightness by 50%
//     "50" to set brightness to 50%
//     "+50" to increase current brightness by 50%
//   * transition time [centiseconds]
hueLightSetBrightness(3, "+50", 5);

sleep(2);
// RESTORE LIGHTS
//   VARIABLES:
//   * transition time [centiseconds]
hueLightsRestore(2);

?>
