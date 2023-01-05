<?php
/**
 * @package     Tag Groups
 *
 * @author      Christoph Amthor
 * @copyright   2019 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPL-3.0+
 */

if ( ! class_exists( 'TagGroups_Error' ) ) {

  /**
   * Error processing
   *
   */
  class TagGroups_Error {

    /**
     * verbosity
     */
    const NORMAL  = 'normal';
    const VERBOSE = 'verbose';

    /**
     * Creates a HTML presentation of the backtrace
     *
     * based on http://php.net/manual/en/function.debug-backtrace.php#112238
     *
     * @param  void
     * @return string
     */
    public static function get_backtrace() {

      $path = ABSPATH; //get_home_path();

      $e = new Exception();

      $trace = explode( "\n", $e->getTraceAsString() );

      // reverse array to make steps line up chronologically
      $trace = array_reverse( $trace );

      array_shift( $trace ); // remove {main}

      array_pop( $trace ); // remove call to this method

      $length = count( $trace );

      $result = array();

      for ( $i = 0; $i < $length; $i++ ) {

        if ( strpos( $trace[$i], 'TagGroups_Error::' ) !== false ) {

          continue;

        }

        // remove home path
        $output = str_replace( $path, '', substr( $trace[$i], strpos( $trace[$i], ' ' ) ) );

        $result[] = ( $i + 1 ) . ')' . $output;

      }

      return "\t" . implode( "\n\t", $result );

    }


    /**
     * Logs a message about a deprecated function and a backtrace
     *
     * @return void
     */
    public static function deprecated() {

      if ( ! self::is_debug() ) {

        return false;

      }


      if ( ! self::is_verbose() ) {

        return false;

      }
      
      self::log('[Tag Groups] Called a deprecated function.');

      error_log( TagGroups_Error::get_backtrace() );

    }


    /**
     * Alias for debug()
     *
     * @param mixed ...$params
     * @return boolean
     */
    public static function dump( ...$params ) {

      return self::debug( ...$params );

    }


    /**
     * Logs one or more variables, if debugging is on
     *
     * @return void
     */
    public static function debug() {

      if ( ! self::is_debug() ) {

        return false;

      }

      $number_of_arguments = func_num_args();

      if ( 0 == $number_of_arguments ) {

        error_log( TagGroups_Error::get_backtrace() );

        return true;

      }

      $args = func_get_args();

      foreach ( $args as $arg ) {

        error_log( var_export( $arg, true ) );

      }

      return true;

    }

    /**
     * wrapper for log(), additionally checks if verbose logging is on
     * 
     * Optionally just a message, or a formatted message followed by the parameters
     *
     * @param mixed ...$params
     * @return void
     */
    public static function verbose_log( ...$params ) {

      if ( ! self::is_verbose() ) {

        return false;

      }

      self::log( ...$params );

    }


    /**
     * Logs a formatted message, if debugging is on
     * 
     * Parameters can be:
     * - none: writes backtrace
     * - a string
     * - a formatted message for sprintf(), followed by the parameters
     *
     * @return void
     */
    public static function log() {

      if ( ! self::is_debug() ) {

        return false;

      }

      $number_of_arguments = func_num_args();

      if ( 0 == $number_of_arguments ) {

        error_log( TagGroups_Error::get_backtrace() );

        return true;

      }

      $args = func_get_args();

      if ( 1 == $number_of_arguments ) {

        if ( ! is_integer( $args[0] ) && ! is_string( $args[0] ) ) {

          $args[0] = print_r( $args[0], true );

        }

        error_log( $args[0] );

      } else {

        $format = $args[0];

        unset( $args[0] );

        foreach ( $args as &$arg ) {

          if ( ! is_integer( $arg ) && ! is_string( $arg ) ) {

            $arg = var_export( $arg, true );

          }

        }

        error_log( vsprintf( $format, $args ) );

      }

      if ( defined( 'CM_DEBUG' ) ) {

        $debug_level = (int) CM_DEBUG;

      } else {

        $debug_level = 1;

      }

      if ( $debug_level > 1 ) {

        error_log( TagGroups_Error::get_backtrace() );

      }

      return true;

    }


    /**
     * Whether WP debug mode is on
     *
     * @return boolean
     */
    static function is_debug() {

      if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {

        return true;

      }

      if ( defined( 'CM_DEBUG' ) ) {

        return true;

      }

      return false;

    }


    /**
     * Whether verbose debugging is on
     *
     * @return boolean
     */
    static function is_verbose() {

      if ( defined( 'CM_DEBUG' ) && strtolower( CM_DEBUG ) == self::VERBOSE ) {

        return true;

      }

      if ( TagGroups_Options::get_option( 'tag_group_verbose_debug', 0 ) ) {

        return true;

      }

      return false;

    }

  }

}
