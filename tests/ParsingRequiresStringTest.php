<?php

use MonkeryTestCase\BrainMonkeyWpTestCase;
use Brain\Monkey\Functions;

class ParsingRequiresStringTest extends BrainMonkeyWpTestCase {

    function setUp() {
        parent::setUp();
        $this->test_subject = new mockTestCase();

    }

    public function testNoAnnotations() {
        $this->test_subject->triggerCheckRequirements();
    }


    /**
     * @dataProvider requiresStringFailsProvider
     */
    public function testWordPressVersionRequiredButFails( $requires, $wp_version, $exception ) {

        Functions::expect('get_bloginfo')
            ->zeroOrMoreTimes()
            ->with('version')
            ->andReturn( $wp_version );

        $this->test_subject->_setAnnotations(
            array(
                'class' => array(),
                'method' => array(
                    'requires' => array(
                        $requires
                    ),
                )
            )
        );

        $this->setExpectedException( \Exception::class, $exception );

        $this->test_subject->triggerCheckRequirements();

    }

    public function requiresStringFailsProvider() {
        return array(

            //Greater than or equal to
            'no operator' => array( "WordPress 4.5.1", "4.5.0", "Requires WordPress >= 4.5.1; Running 4.5.0." ),
            '>= operator' => array( "WordPress >= 4.5.1", "4.5.0", "Requires WordPress >= 4.5.1; Running 4.5.0." ),
            'ge operator' => array( "WordPress ge 4.5.1", "4.5.0", "Requires WordPress >= 4.5.1; Running 4.5.0." ),
            'greater or equal than alpha' => array( "WordPress >= 4.5.1-alpha", "4.5.0", "Requires WordPress >= 4.5.1-alpha; Running 4.5.0." ),
            'alpha greater or equal than' => array( "WordPress >= 4.5.0", "4.5.0-alpha-123", "Requires WordPress >= 4.5.0; Running 4.5.0-alpha-123." ),

            //Greater than
            '> operator' => array( "WordPress > 4.5.1", "4.5.1", "Requires WordPress > 4.5.1; Running 4.5.1." ),
            'gt operator' => array( "WordPress gt 4.5.1", "4.5.1", "Requires WordPress > 4.5.1; Running 4.5.1." ),
            'greater than alpha' => array( "WordPress > 4.5.1-alpha", "4.5.1-alpha", "Requires WordPress > 4.5.1-alpha; Running 4.5.1-alpha." ),

            //Equal to
            '== operator' => array( "WordPress == 4.5.1", "4.5.1-alpha", "Requires WordPress == 4.5.1; Running 4.5.1-alpha." ),
            '= operator' => array( "WordPress == 4.5.1-alpha", "4.5.1", "Requires WordPress == 4.5.1-alpha; Running 4.5.1." ),
            'eq operator' => array( "WordPress == 4.5.1", "4.5.2", "Requires WordPress == 4.5.1; Running 4.5.2." ),
            //'unequal alphas' => array( "WordPress == 4.5.1-alpha-1", "4.5.1-alpha-2", "Requires WordPress == 4.5.1-alpha-1; Running 4.5.1-alpha-2." ),

            //Not equal to
            '!= operator' => array( "WordPress != 4.5.1", "4.5.1", "Requires WordPress != 4.5.1; Running 4.5.1." ),
            'ne operator' => array( "WordPress ne 4.5.1", "4.5.1", "Requires WordPress != 4.5.1; Running 4.5.1." ),
            '<> operator' => array( "WordPress <> 4.5.1", "4.5.1", "Requires WordPress != 4.5.1; Running 4.5.1." ),

            //Less than or equal to
            '<= operator' => array( "WordPress <= 4.5.1", "4.5.2", "Requires WordPress <= 4.5.1; Running 4.5.2." ),
            'le operator' => array( "WordPress le 4.5.1", "4.5.2", "Requires WordPress <= 4.5.1; Running 4.5.2." ),
            'less than alpha' => array( "WordPress <= 4.5.1-alpha", "4.5.1", "Requires WordPress <= 4.5.1-alpha; Running 4.5.1." ),

            //Less than or equal to
            '<= operator' => array( "WordPress <= 4.5.1", "4.5.2", "Requires WordPress <= 4.5.1; Running 4.5.2." ),
            'le operator' => array( "WordPress le 4.5.1", "4.5.2", "Requires WordPress <= 4.5.1; Running 4.5.2." ),
            'less than or equal alpha' => array( "WordPress <= 4.5.1-alpha", "4.5.1", "Requires WordPress <= 4.5.1-alpha; Running 4.5.1." ),


        );
    }

    /**
     * @dataProvider requiresStringPassesProvider
     */
    public function testWordPressVersionRequiredButPasses( $requires, $wp_version ) {

        Functions::expect('get_bloginfo')
            ->zeroOrMoreTimes()
            ->with('version')
            ->andReturn( $wp_version );

        $this->test_subject->_setAnnotations(
            array(
                'class' => array(),
                'method' => array(
                    'requires' => array(
                        $requires
                    ),
                )
            )
        );

        $this->test_subject->triggerCheckRequirements();

    }

    public function requiresStringPassesProvider() {
        return array(
            'identical version' => array( "WordPress 4.5.1", "4.5.1" ),
            'simple case' => array( "WordPress 4.5.1", "4.5.2" ),
            'missing patch version' => array( "WordPress 4.5.0", "4.5" ),
        );
    }

}