<?php 
/*
Plugin Name: Comment Approval Notification
Plugin URI: http://mattsblog.ca/plugins/comment-approval-notification/
Description: Sends an email to the comment author when their comment gets approved (only if it's held for moderation).
Version: 1.1.1
Author: Matt Freedman
Author URI: http://mattsblog.ca/
*/

class comment_approval_notification {

	function comment_approval_notification() {

		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		if ( $GLOBALS[$wp_version] >= '2.7' )
			register_uninstall_hook( __FILE__, array( &$this, 'uninstall' ) );

		add_action( 'wp_set_comment_status', array( &$this, 'comment_status' ), 10, 2 );
		add_action( 'admin_menu', array( &$this, 'add_options' ) );

		remove_shortcode( 'blog_domain' );
		remove_shortcode( 'comment_author' );
		remove_shortcode( 'comment_author_email' );
		remove_shortcode( 'comment_author_url' );
		remove_shortcode( 'comment_author_IP' );
		remove_shortcode( 'comment_date' );
		remove_shortcode( 'comment_date_gmt' );
		remove_shortcode( 'comment_time' );
		remove_shortcode( 'comment_content' );
		remove_shortcode( 'comment_permalink' );
		remove_shortcode( 'post_author' );
		remove_shortcode( 'post_date' );
		remove_shortcode( 'post_date_gmt' );
		remove_shortcode( 'post_time' );
		remove_shortcode( 'post_title' );
		remove_shortcode( 'post_category' );
		remove_shortcode( 'post_excerpt' );

	}

	function activate() {

		$options['name'] = get_option( 'blogname' );
		$options['email'] = 'wordpress@' . preg_replace( '#^www\.#', '', strtolower( $_SERVER['SERVER_NAME'] ) );
		$options['subject'] = 'Your comment on [blog_domain] has been approved!';
		$options['body'] = 'Hello [comment_author],' . "\n\r" . 'Your comment on the post [post_title] has been approved and can now be viewed at:' . "\n\r" . '<[comment_permalink]>';

		add_option( 'comment_notification', $options );

	}

	function uninstall() {

		remove_option( 'comment_notification' );

	}

	function add_options() {

		add_options_page( 'Comment Approval Notification', 'Comment Approval Notification', 'manage_options', 'comment-approval-notification', array( &$this, 'options_page' ) );

	}

	function comment_status( $comment_id, $comment_status ) {

		if ( $comment_status == 'approve' ) {

			$comment = get_comment( $comment_id, 'OBJECT' );
			$options = get_option( 'comment_notification' );

			$to = $comment->comment_author_email;
			$from = $options['name'] . ' <' . $options['email'] . '>';
			$subject = $this->process_shortcodes( $options['subject'] );
			$message = $this->process_shortcodes( $options['body'] );
			$headers = 'From: ' . $from . "\r\n" .
			'Reply-To: ' . $from . "\r\n" .
			'X-Mailer: PHP/' . phpversion();

			wp_mail( $to, $subject, $message, $headers );

		}

	}

	function options_page() {

		$options = get_option( 'comment_notification' );
	?>
	<div class="wrap">

		<h2>Comment Approval Notification Settings</h2>
		<p>Here you can change the default email sent to comment authors when their comment is approved.</p>
		<p>Shortcodes such as <em>[comment_author]</em> may be used in the email subject and body fields. For a full list of available shortcodes, please see <a href="http://mattsblog.ca/plugins/comment-approval-notification/#shortcodes" title="List of available shortcodes." target="_blank">here</a>.</p>

		<form method="post" action="options.php">

			<?php wp_nonce_field('update-options'); ?>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="comment_notification[name]">From Name</label></th>
					<td><input type="text" name="comment_notification[name]" value="<?php echo $options['name']; ?>" size="45" />
					<br />
					<span class="setting-description">Enter the <strong>name</strong> you want to be displayed in the "From" and "Reply-to" fields of the email.</span></td>
				</tr>
                        	<tr valign="top">
	                                <th scope="row"><label for="comment_notification[email]">From Email</label></th>
					<td><input type="text" name="comment_notification[email]" value="<?php echo $options['email']; ?>" size="45" />
					<br />
					<span class="setting-description">Enter the <strong>email</strong> you want to be displayed in the "From" and "Reply-to" fields of the email.</span></td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="comment_notification[subject]">Email Subject</label></th>
					<td><input type="text" name="comment_notification[subject]" value="<?php echo $options['subject']; ?>" size="45" />
					<br />
					<span class="setting-description">Enter the subject line of the email. You may use shortcodes here.</span></td>
				</tr>
                        	<tr valign="top">
                                	<th scope="row"><label for="comment_notification[body]">Email Body</label></th>
					<td><textarea name="comment_notification[body]" cols="60" rows="10" style="width: 98%;" class="code"><?php echo $options['body']; ?></textarea>
					<br />
					<span class="setting-description">Enter the text you want the body of the email to read. You may use shortcodes here.</span></td>
				</tr>
			</table>

			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="comment_notification" />
			<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php _e( 'Save Changes' ) ?>" /></p>
		</form>
	</div>
	<?php 
	}

	function process_shortcodes( $content ) {

		$shortcodes = array(
			'blog_domain',
			'comment_author',
			'comment_author_email',
			'comment_author_url',
			'comment_author_IP',
			'comment_date',
			'comment_time',
			'comment_content',
			'comment_permalink',
			'post_author',
			'post_date',
			'post_time',
			'post_title',
			'post_category',
			'post_excerpt'
		);

		foreach( $shortcodes as $shortcode )
			$content = str_ireplace( '[' . $shortcode . ']', $this->shortcode( $shortcode ), $content );

		return $content;

	}


	function shortcode( $shortcode ) {

			global $comment;
			$post_id = $comment->comment_post_ID;
			$post = get_post( $post_id, 'OBJECT' );

			switch ( $shortcode ) {

				case 'blog_domain' :
					return preg_replace( '#^www\.#', '', strtolower( $_SERVER['SERVER_NAME'] ) );
					break;
				case 'comment_author' :
					return $comment->comment_author;
					break;
				case 'comment_author_email' :
					return $comment->comment_author_email;
					break;
				case 'comment_author_url' :
					return $comment->comment_author_url;
					break;
				case 'comment_author_IP' :
					return $comment->comment_author_IP;
					break;
				case 'comment_date' :
					return date_i18n( get_option( 'date_format' ), strtotime( $comment->comment_date ) );
					break;
				case 'comment_time' :
					return date_i18n( get_option( 'time_format' ), strtotime( $comment->comment_date ) );
					break;
				case 'comment_content' :
					return $comment->comment_content;
					break;
				case 'comment_permalink' :
					return get_comment_link( $comment->comment_ID );
					break;
				case 'post_author' :
					return $post->post_author;
					break;
				case 'post_date' :
					return date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) );
					break;
				case 'post_time' :
					return date_i18n( get_option( 'time_format' ), strtotime( $post->post_date ) );
					break;
				case 'post_title' :
					return $post->post_title;
					break;
				case 'post_category' :
					return $post->post_category;
					break;
				case 'post_excerpt' :
					return $post->post_excerpt;
					break;

			}

	}

}

$comment_approval_notification = new comment_approval_notification;

?>
