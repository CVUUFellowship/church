<?php
/* $Id: approve_entry.php,v 1.50.2.4 2008/03/04 13:36:08 cknudsen Exp $ */
include_once 'includes/init.php';
require ( 'includes/classes/WebCalMailer.class' );

$error = '';

if ( $readonly == 'Y' )
  $error = print_not_auth (4);

$user = getValue ( 'user' );
$type = getValue ( 'type' );
$id = getValue ( 'id' );

// Allow administrators to approve public events.
$app_user = ( $PUBLIC_ACCESS == 'Y' && ! empty ( $public ) && $is_admin
  ? '__public__' : ( $is_assistant || $is_nonuser_admin ? $user : $login ) );
// If User Access Control is enabled, we check to see if they are
// allowed to approve for the specified user.
if ( access_is_enabled () && ! empty ( $user ) && $user != $login &&
    access_user_calendar ( 'approve', $user ) )
  $app_user = $user;

if ( empty ( $error ) && $id > 0 )
  update_status ( 'A', $app_user, $id, $type );

// Return to login TIMEZONE.
set_env ( 'TZ', $TIMEZONE );
if ( empty ( $error ) && empty ( $mailerError ) ) {
  do_redirect ( ! empty ( $ret ) && $ret == 'listall'
    ? 'list_unapproved.php'
    : ( ( ! empty ( $ret ) && $ret == 'list'
        ? 'list_unapproved.php?'
        : 'view_entry.php?id=' . $id . '&amp;' ) . 'user=' . $app_user ) );
  exit;
}


?>
