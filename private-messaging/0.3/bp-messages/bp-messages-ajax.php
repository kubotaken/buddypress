<?php

function messages_ajax_send_reply() {
	global $bp_messages_image_base;
	
	check_ajax_referer('messages_sendreply');

	$result = messages_send_message($_REQUEST['send_to'], $_REQUEST['subject'], $_REQUEST['content'], $_REQUEST['thread_id'], true); 

	if ( $result['status'] ) { ?>
			<div class="avatar-box">
				<?php if ( function_exists('xprofile_get_avatar') ) 
					echo xprofile_get_avatar($result['reply']->sender_id, 1);
				?>
	
				<h3><?php echo bp_core_get_userlink($result['reply']->sender_id) ?></h3>
				<small><?php echo bp_format_time($result['reply']->date_sent) ?></small>
			</div>
			<?php echo $result['reply']->message; ?>
			<div class="clear"></div>
		<?php
	} else {
		$result['message'] = '<img src="' . $bp_messages_image_base . '/warning.gif" alt="Warning" /> &nbsp;' . $result['message'];
		echo "-1|" . $result['message'];
	}
}
add_action( 'wp_ajax_messages_send_reply', 'messages_ajax_send_reply' );

function messages_ajax_markunread() {
	if ( !isset($_POST['thread_ids']) ) {
		echo "-1|" . __('There was a problem marking messages as unread.');
	} else {
		$thread_ids = explode( ',', $_POST['thread_ids'] );
		
		for ( $i = 0; $i < count($thread_ids); $i++ ) {
			BP_Messages_Thread::mark_as_unread($thread_ids[$i]);
		}
	}
}
add_action( 'wp_ajax_messages_markunread', 'messages_ajax_markunread' );

function messages_ajax_markread() {
	if ( !isset($_POST['thread_ids']) ) {
		echo "-1|" . __('There was a problem marking messages as read.');
	} else {
		$thread_ids = explode( ',', $_POST['thread_ids'] );

		for ( $i = 0; $i < count($thread_ids); $i++ ) {
			BP_Messages_Thread::mark_as_read($thread_ids[$i]);
		}
	}
}
add_action( 'wp_ajax_messages_markread', 'messages_ajax_markread' );

function messages_ajax_delete() {
	if ( !isset($_POST['thread_ids']) ) {
		echo "-1|" . __('There was a problem deleting messages.');
	} else {
		$thread_ids = explode( ',', $_POST['thread_ids'] );

		for ( $i = 0; $i < count($thread_ids); $i++ ) {
			BP_Messages_Thread::delete($thread_ids[$i]);
		}
		
		echo __('Messages deleted.');
	}
}
add_action( 'wp_ajax_messages_delete', 'messages_ajax_delete' );

function messages_ajax_close_notice() {
	global $userdata;

	if ( !isset($_POST['notice_id']) ) {
		echo "-1|" . __('There was a problem closing the notice.');
	} else {
		$notice_ids = get_usermeta( $userdata->ID, 'closed_notices' );
	
		$notice_ids[] = (int) $_POST['notice_id'];
		
		update_usermeta( $userdata->ID, 'closed_notices', $notice_ids );
	}
}
add_action( 'wp_ajax_messages_close_notice', 'messages_ajax_close_notice' );

?>