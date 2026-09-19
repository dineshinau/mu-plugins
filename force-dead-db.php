<?php
// add_action( 'init', function () {
//     global $wpdb;
//     // Connection is already open at this point — sabotage it, then force a query.
//     mysqli_close( $wpdb->dbh );
//     $wpdb->query( 'SELECT 1' ); // this failed query triggers bail() -> dead_db()
//     die( 'should not reach here' );
// } );
