<?php

namespace Fizzik\Utility;

class SleepHandler {
    private $isset; //Whether or not some duration has been specified
    private $duration; //sleep duration in seconds
    private $microDuration; //sleep duration in microseconds (1,000,000 microseconds = 1 second)

    public function __construct() {
        $this->isset = FALSE;
        $this->duration = 0;
        $this->microDuration = 0;
    }

    /*
     * Adds the given duration to sleep with, optionally give duration in microseconds, if default = true,
     * then the sleep duration will only be added if no sleep duration has been set yet.
     */
    public function add($amount, $microseconds = FALSE, $default = FALSE) {
        if (!$default || ($default && !$this->isset)) {
            if ($microseconds === TRUE) {
                $this->microDuration += $amount;
            }
            else {
                $this->duration += $amount;
            }

            $this->isset = TRUE;
        }
    }

    public function execute() {
        if ($this->duration > 0) {
            sleep($this->duration);
        }
        if ($this->microDuration > 0) {
            usleep($this->microDuration);
        }
        $this->duration = 0;
        $this->microDuration = 0;
        $this->isset = FALSE;
    }
}