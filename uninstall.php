<?php
// Block direct access + running outside the WP uninstall process.
defined( 'ABSPATH' ) || exit;
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// DayPick writes no data to the database (no options, transients or tables);
// there is nothing to clean up during uninstall.
