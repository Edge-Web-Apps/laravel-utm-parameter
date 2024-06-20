<?php

return [
  /*
   * Control Overwriting UTM Parameters (default: false)
   *
   * This setting determines how UTM parameters are handled within a user's session.
   *
   * - Enabled (true): New UTM parameters will overwrite existing ones during the session.
   * - Disabled (false): The initial UTM parameters will persist throughout the session.
   */
  'override_utm_parameters' => false,
];
