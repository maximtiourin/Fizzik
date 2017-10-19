<?php

namespace Fizzik\Utility;


class Console {
    private static $animate_dotdotdot = ["", ".", "..", "..."];
    private $animate_dotdotdot_count;

    public function __construct() {
        $this->resetAnimations();
    }

    /*
     * Returns a frame of console text animation of ..., depending on the last known state of it
     */
    public function animateDotDotDot() {
        $frame = self::$animate_dotdotdot[$this->animate_dotdotdot_count];
        $this->animate_dotdotdot_count = ($this->animate_dotdotdot_count + 1) % count(self::$animate_dotdotdot);
        return $frame;
    }

    public function resetAnimations() {
        $this->animate_dotdotdot_count = 0;
    }
}