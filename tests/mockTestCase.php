<?php

use StephenHarris\PHPUnit\RequiresWordPressVersion;

class mockTestCase extends mockParentTestCase {

    use RequiresWordPressVersion;

    protected $annotations = array();

    public function triggerCheckRequirements() {
        $this->checkRequirements();
    }

    public function _setAnnotations( $annotations ) {
        $this->annotations = $annotations;
    }

    public function getAnnotations() {
        return $this->annotations;
    }

    public function markTestSkipped( $message ) {
        throw new \Exception( $message );
    }
}