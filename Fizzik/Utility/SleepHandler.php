<?php

namespace Fizzik\Utility;

class SleepHandler {
    private $duration; //sleep duration in seconds
    private $microDuration; //sleep duration in microseconds (1,000,000 microseconds = 1 second)

    public function __construct() {
        $this->duration = 0;
        $this->microDuration = 0;
    }

    public function add($amount, $microseconds = FALSE) {
        if ($microseconds === TRUE) {
            $this->microDuration += $amount;
        }
        else {
            $this->duration += $amount;
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
    }
}