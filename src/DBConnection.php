<?php

final class DBConnection {
	private static $instances = [];
	private static $defaultConnection = null;

	private function __construct() {}

	public static function get( $config = null, $default = false ) {
		//use first connection in list if $config and defaultConnection not present
		$key = $config ? serialize( $config ) : self::$defaultConnection;
		$key = $key ?: key( self::$instances );

		if ( !$key ) {
                    throw new \Exception( 'No configuration present!' );
                }

		//connection already exists and it's ok?
		if ( isset( self::$instances[ $key ] )/* && self::$instances[ $key ]->ping()*/ ) {
                    return self::$instances[ $key ];
                }

		$connection = new SafeMySQL( $config );

		self::$instances[ $key ] = $connection;

		if ( $default ) {
                    self::$defaultConnection = $key;
                }

		return $connection;
	}
}