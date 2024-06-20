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

  /*
   * Session Key for UTM Parameters (default: 'utm')
   *
   * This key specifies the name used to access and store UTM parameters within the session data.
   *
   * If you're already using 'utm' for another purpose in your application,
   * you can customize this key to avoid conflicts.
   * Simply provide your preferred key name as a string value.
   */
  'session_key' => 'utm'
];
