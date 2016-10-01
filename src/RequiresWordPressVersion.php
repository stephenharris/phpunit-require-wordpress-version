<?php

namespace StephenHarris\PHPUnit;

trait RequiresWordPressVersion {

  protected $operators = array(
    '<' => '<',
    'lt' => '<',
    '<=' => '<=',
    'le' => '<=',
    '>' => '>',
    'gt' => '>',
    '>=' => '>=',
    'ge' => '>=',
    '==' => '==',
    '=' => '==',
    'eq' => '==',
    '!=' => '!=',
    '<>' => '!=',
    'ne' => '!=',
  );

  protected function checkRequirements() {
    parent::checkRequirements();

	  $annotations = $this->getAnnotations();

    if ( empty($annotations) ) {
        return;
    }

    //$this->getAnnotations returns an array indexed by 'class' and 'method'
    //with annotations for the class and method respectively. We don't care about the location of the annotation
    //so we just merge them:
    $annotations = array_merge( $annotations['class'], $annotations['method'] );

      if ( empty($annotations['requires']) ) {
        return;
      }

      foreach ( $annotations['requires'] as $required ) {
        if ( $target = $this->parseRequiresWordPressVersion( $required ) ) {
          if ( ! version_compare( $this->getWordPressVersion(), $target['version'], $target['operator'] ) ) {

            $message = sprintf(
              'Requires WordPress %s %s; Running %s.',
              $this->translateVersionOperator( $target['operator'] ),
              $target['version'],
              $this->getWordPressVersion()
            );

            if ( $target['message'] ) {
                $message .= "\n" . $target['message'];
            }

            $this->markTestSkipped( $message );
          }
      	}
  		}
    }

    /**
     * get_bloginfo( 'version' ) might return a version like '4.5'. This function normalises it to '4.5.0'
     *
     * @return Normalised WordPress version
     */
    protected function getWordPressVersion() {
      $wp_version = $this->padVersion( get_bloginfo( 'version' ) );
      return $wp_version;
    }

		protected function padVersion( $version ) {
			$version = preg_replace( '/^(\d+\.\d+)(-.*)?$/', '$1.0$2', $version );
			return $version;
		}

    protected function parseRequiresWordPressVersion( $string ) {
      preg_match( '/WordPress (?P<operator><|lt|<=|le|>|gt|>=|ge|==|=|eq|!=|<>|ne)?\s*(?P<version>\d+\.\d+(\.\d+)?(-(stable|beta|b|RC|alpha|a|patch|pl|p)(-\d+)?)?)\s*(?P<message>.*)?/i', $string, $matches );

      if ( ! $matches ) {
        return;
      }

      $operator = ! empty( $matches['operator'] ) ? $matches['operator'] : '>=';
      return array(
				'version'  => $this->padVersion( $matches['version'] ),
				'operator' => $operator,
				'message'  => $matches['message']
			);
    }

    protected function translateVersionOperator( $operator ) {
      return $this->operators[ $operator ];
    }

}
