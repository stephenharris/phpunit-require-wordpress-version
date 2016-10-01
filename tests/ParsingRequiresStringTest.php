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

        Functions::expect('get_bloginfo')->zeroOrMoreTimes()
            ->with('version')
            ->andReturn( $wp_version );

        $this->test_subject->_setAnnotations( array(
          'class' => array(),
          'method' => array(
              'requires' => array( $requires ),
          )
        ));

        $this->setExpectedException( \Exception::class, $exception );

        $this->test_subject->triggerCheckRequirements();

    }

    public function requiresStringFailsProvider() {
      return array(
        //Greater than or equal to
        'no operator' => array( "WordPress 4.5.1", "4.5", "Requires WordPress >= 4.5.1; Running 4.5.0." ),
        '>= operator' => array( "WordPress >= 4.5.1", "4.5", "Requires WordPress >= 4.5.1; Running 4.5.0." ),
        'ge operator' => array( "WordPress ge 4.5.1", "4.5", "Requires WordPress >= 4.5.1; Running 4.5.0." ),
        'greater or equal than alpha' => array( "WordPress >= 4.5.1-alpha", "4.5.0", "Requires WordPress >= 4.5.1-alpha; Running 4.5.0." ),
      	'alpha greater or equal than' => array( "WordPress >= 4.5.0", "4.5-alpha-123", "Requires WordPress >= 4.5.0; Running 4.5.0-alpha-123." ),
        'require alpha 2' => array( "WordPress 4.5.1-alpha-2", "4.5.1-alpha-1", "Requires WordPress >= 4.5.1-alpha-2; Running 4.5.1-alpha-1." ),
        'require rc' => array( "WordPress 4.5.1-rc-2", "4.5.1-alpha-5", "Requires WordPress >= 4.5.1-rc-2; Running 4.5.1-alpha-5." ),

        //Greater than
        '> operator' => array( "WordPress > 4.5.1", "4.5.1", "Requires WordPress > 4.5.1; Running 4.5.1." ),
        'gt operator' => array( "WordPress gt 4.5.1", "4.5.1", "Requires WordPress > 4.5.1; Running 4.5.1." ),
        'greater than alpha' => array( "WordPress > 4.5.1-alpha", "4.5.1-alpha", "Requires WordPress > 4.5.1-alpha; Running 4.5.1-alpha." ),

        //Equal to
        '== operator' => array( "WordPress == 4.5.1", "4.5.1-alpha", "Requires WordPress == 4.5.1; Running 4.5.1-alpha." ),
        '= operator' => array( "WordPress == 4.5.1-alpha", "4.5.1", "Requires WordPress == 4.5.1-alpha; Running 4.5.1." ),
        'eq operator' => array( "WordPress == 4.5.1", "4.5.2", "Requires WordPress == 4.5.1; Running 4.5.2." ),
        'unequal alphas' => array( "WordPress == 4.5.1-alpha-1", "4.5.1-alpha-2", "Requires WordPress == 4.5.1-alpha-1; Running 4.5.1-alpha-2." ),

        //Not equal to
      	'!= operator' => array( "WordPress != 4.5.1", "4.5.1", "Requires WordPress != 4.5.1; Running 4.5.1." ),
        'ne operator' => array( "WordPress ne 4.5.1", "4.5.1", "Requires WordPress != 4.5.1; Running 4.5.1." ),
        '<> operator' => array( "WordPress <> 4.5.1", "4.5.1", "Requires WordPress != 4.5.1; Running 4.5.1." ),

        //Less than
        '< operator' => array( "WordPress < 4.5.1", "4.5.1", "Requires WordPress < 4.5.1; Running 4.5.1." ),
        'l operator' => array( "WordPress lt 4.5.1", "4.5.1", "Requires WordPress < 4.5.1; Running 4.5.1." ),
        'less than alpha' => array( "WordPress < 4.5.1-alpha", "4.5.1-alpha", "Requires WordPress < 4.5.1-alpha; Running 4.5.1-alpha." ),

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

			Functions::expect('get_bloginfo')->zeroOrMoreTimes()
					->with('version')
					->andReturn( $wp_version );

			$this->test_subject->_setAnnotations( array(
				'class' => array(),
				'method' => array(
						'requires' => array( $requires ),
				)
			));

      $this->test_subject->triggerCheckRequirements();
    }

    public function requiresStringPassesProvider() {
        return array(
            'identical version' => array( "WordPress 4.5.1", "4.5.1" ),
            'simple case' => array( "WordPress 4.5.1", "4.5.2" ),
            'alpha < beta' => array( "WordPress > 4.5.1-alpha-5", "4.5.1-beta-1" ),
						'alpha < RC' => array( "WordPress > 4.5.1-alpha-5", "4.5.1-rc-1" ),
						'alpha 1 < alpha 2' => array( "WordPress > 4.5.1-alpha-1", "4.5.1-alpha-2" ),
						'0 patch optional' => array( "WordPress == 4.5", "4.5" ),
        );
    }


		/**
		 * @expectedException Exception
		 * @expectedExceptionMessage Requires WordPress >= 4.4.0; Running 4.3.0.
		 *                           Tested behaviour requires term meta data
		 */
		public function testCustomMessage() {

			Functions::expect('get_bloginfo')->zeroOrMoreTimes()
					->with('version')
					->andReturn( '4.3' );

			$this->test_subject->_setAnnotations( array(
				'class' => array(),
				'method' => array(
						'requires' => array( 'WordPress 4.4.0 Tested behaviour requires term meta data' ),
				)
			));

      $this->test_subject->triggerCheckRequirements();
    }

		public function testVersionCaseInsensitive() {
			Functions::expect('get_bloginfo')->zeroOrMoreTimes()
					->with('version')
					->andReturn( '4.3-alpha-1234' );

			$this->test_subject->_setAnnotations( array(
				'class' => array(),
				'method' => array(
						'requires' => array( 'WordPress == 4.3-aLPhA-1234' ),
				)
			));

			$this->test_subject->triggerCheckRequirements();
		}

		/**
		 * @expectedException Exception
		 * @expectedExceptionMessage Requires WordPress >= 4.4.0; Running 4.3.0.
		 */
		public function testWordPressCaseInsensitive() {
			Functions::expect('get_bloginfo')->zeroOrMoreTimes()
					->with('version')
					->andReturn( '4.3' );

			$this->test_subject->_setAnnotations( array(
				'class' => array(),
				'method' => array(
						'requires' => array( 'Wordpress 4.4.0' ),
				)
			));

			$this->test_subject->triggerCheckRequirements();
		}

		/**
		 * @expectedException Exception
		 * @expectedExceptionMessage Requires WordPress != 4.3.0; Running 4.3.0.
		 */
		public function testWithout0PatchVersion() {
			Functions::expect('get_bloginfo')->zeroOrMoreTimes()
				->with('version')
				->andReturn( '4.3' );

			$this->test_subject->_setAnnotations( array(
				'class' => array(),
				'method' => array(
						'requires' => array( 'Wordpress != 4.3' ),
				)
			));
			$this->test_subject->triggerCheckRequirements();
		}

		/**
		 * @expectedException Exception
		 * @expectedExceptionMessage Requires WordPress != 4.3.0; Running 4.3.0.
		 */
		public function testWith0PatchVersion() {
			Functions::expect('get_bloginfo')->zeroOrMoreTimes()
				->with('version')
				->andReturn( '4.3' );

			$this->test_subject->_setAnnotations( array(
				'class' => array(),
				'method' => array(
						'requires' => array( 'Wordpress != 4.3.0' ),
				)
			));
			$this->test_subject->triggerCheckRequirements();
		}

		/**
		 * @expectedException Exception
		 * @expectedExceptionMessage Requires WordPress != 4.3.0-alpha-123; Running 4.3.0-alpha-123.
		 */
		public function testPatchVersionWithout0PatchAndAlpha() {
			Functions::expect('get_bloginfo')->zeroOrMoreTimes()
				->with('version')
				->andReturn( '4.3-alpha-123' );

			$this->test_subject->_setAnnotations( array(
				'class' => array(),
				'method' => array(
					'requires' => array( 'Wordpress != 4.3-alpha-123' ),
				)
			));
			$this->test_subject->triggerCheckRequirements();
		}

		/**
		 * @expectedException Exception
		 * @expectedExceptionMessage Requires WordPress != 4.3.0-alpha-123; Running 4.3.0-alpha-123.
		 */
		public function testPatchVersionWith0PatchAndAlpha() {
			Functions::expect('get_bloginfo')->zeroOrMoreTimes()
				->with('version')
				->andReturn( '4.3-alpha-123' );

			$this->test_subject->_setAnnotations( array(
				'class' => array(),
				'method' => array(
					'requires' => array( 'Wordpress != 4.3.0-alpha-123' ),
				)
			));
			$this->test_subject->triggerCheckRequirements();
		}

}
