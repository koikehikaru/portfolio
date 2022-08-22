<?php

class Mailform {
	
	// private property
	private $send_address          = array();
	private $thanks_page_url       = '';
	
	private $send_subject          = '';
	private $send_body             = '';
	
	private $reply_mail            = 0;
	private $send_name             = '';
	private $thanks_subject        = '';
	private $thanks_body           = '';
	private $thanks_body_signature = '';
	
	private $domain_name           = '';
	private $token                 = '';
	
	
	private $referer               = '';
	private $addr                  = '';
	private $host                  = '';
	private $agent                 = '';
	
	
	private $javascript_comment    = '送信前の入力チェックは動作しませんでした。';
	private $now_url               = '';
	private $before_url            = '';
	
	
	private $order_count           = '0';
	private $order_isset           = array();
	private $post_isset            = array();
	private $mail_address          = '';
	private $reply_mail_address    = false;
	
	
	private $my_result             = false;
	private $you_result            = false;
	
	
	// writing time addon property
	private $writing_time          = '0';
	
	
	// confirm addon property
	private $confirm_window        = '1';
	
	
	// attachment addon property
	private $jpg                   = 1;
	private $png                   = 1;
	private $gif                   = 1;
	private $zip                   = 0;
	private $pdf                   = 0;
	private $doc                   = 0;
	private $xls                   = 0;
	private $upload_max_size       = 2000000;
	
	private $attachment_tmp_name   = array();
	private $attachment_name       = array();
	private $attachment_type       = array();
	private $attachment_flag       = false;
	
	
	// carbon-copy addon property
	private $cc_address            = array();
	private $bcc_address           = array();
	
	
	// csv-record addon property
	private $csv_save_path         = '';
	private $duplicate_ban         = 0;
	private $mail_on               = 1;
	
	
	// block addon property
	private $block_ip              = array();
	private $block_word            = array();
	private $block_address         = array();
	
	
	// dear-name addon property
	private $company_before        = '';
	private $company_after         = '';
	private $name_before           = '';
	private $name_after            = '';
	
	
	
	
	// public construct
	public function __construct() {
		
		include( dirname( __FILE__ ) .'/config.php' );
		
		
		$this->send_address          = $rm_send_address;
		$this->thanks_page_url       = $rm_thanks_page_url;
		
		$this->send_subject          = $rm_send_subject;
		$this->send_body             = $rm_send_body;
		
		$this->reply_mail            = $rm_reply_mail;
		$this->send_name             = $rm_send_name;
		$this->thanks_subject        = $rm_thanks_subject;
		$this->thanks_body           = $rm_thanks_body;
		$this->thanks_body_signature = $rm_thanks_body_signature;
		
		
		$this->domain_name           = $_SERVER['HTTP_HOST'];
		$session_id                  = htmlspecialchars( session_id(), ENT_QUOTES, 'UTF-8' );
		$this->token                 = sha1( $session_id );
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/confirm/confirm-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/confirm/confirm-config.php' );
			include( dirname( __FILE__ ) .'/../addon/confirm/config-include.php' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/attachment-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/attachment-config.php' );
			include( dirname( __FILE__ ) .'/../addon/attachment/config-include.php' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/carbon-copy/carbon-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/carbon-copy/carbon-config.php' );
			include( dirname( __FILE__ ) .'/../addon/carbon-copy/config-include.php' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/csv-record/admin/php/csv-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/csv-record/admin/php/csv-config.php' );
			include( dirname( __FILE__ ) .'/../addon/csv-record/admin/php/config-include.php' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/block/block-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/block/block-config.php' );
			include( dirname( __FILE__ ) .'/../addon/block/config-include.php' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/dear-name/dear-config.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/dear-name/dear-config.php' );
			include( dirname( __FILE__ ) .'/../addon/dear-name/config-include.php' );
		}
		
	}
	
	
	
	
	// public javascript_action_check
	public function javascript_action_check() {
		
		if ( ! ( isset( $_POST['javascript_action'] ) && $_POST['javascript_action'] === 'true' ) ) {
			echo 'spam_failed-0001,不正な操作が行われたようです。';
			exit;
		}
		
	}
	
	
	
	
	// public referer_check
	public function referer_check() {
		
		if ( $this->domain_name !== '' ) {
			if ( strpos( $_SERVER['HTTP_REFERER'], $this->domain_name ) === false ) {
				echo 'spam_failed-0002,不正な操作が行われたようです。';
				exit;
			}
		}
		
	}
	
	
	
	
	// public token_check
	public function token_check() {
		
		if ( ! ( isset( $_POST['token'] ) && $_POST['token'] === $this->token ) ) {
			echo 'spam_failed-0004,不正な操作が行われたようです。';
			exit;
		}
		
	}
	
	
	
	
	// public token_get
	public function token_get() {
		
		if ( isset( $_POST['token_get'] ) && $_POST['token_get'] === 'true' ) {
			echo 'token_success,'. $this->token;
		}
		
	}
	
	
	
	
	// public mail_address_check
	public function mail_address_check( $name, $post, $bool ) {
		
		if ( $name === 'mail_address' || $bool === true ) {
			if ( ! preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $post ) ) {
				echo 'spam_failed-0005,正しくないメールアドレスです。';
				exit;
			}
		}
		
	}
	
	
	
	
	// public reply_address_set
	public function reply_address_set( $name, $post ) {
		
		if ( $name === 'mail_address' ) {
			$this->mail_address       = str_replace( array( "\r\n", "\r", "\n" ), '', $post );
			$this->reply_mail_address = true;
		}
		
	}
	
	
	
	
	// public sanitize_post
	public function sanitize_post( $p, $mode ) {
		
		if ( $mode === 'confirm' ) {
			$p = htmlspecialchars( $p, ENT_QUOTES, 'UTF-8' );
		}
		
		return $p;
		
	}
	
	
	
	
	// public already_cookie_check
	public function already_cookie_check() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/csv-record/include/already-cookie-check.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/csv-record/include/already-cookie-check.php' );
		}
		
	}
	
	
	
	
	// public already_ip_check
	public function already_ip_check() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/csv-record/include/already-ip-check.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/csv-record/include/already-ip-check.php' );
		}
		
	}
	
	
	
	
	// public post_check
	public function post_check( $mode ) {
		
		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$this->referer = $this->sanitize_post( $_SERVER['HTTP_REFERER'], $mode );
		}
		
		
		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$this->addr = $this->sanitize_post( $_SERVER['REMOTE_ADDR'], $mode );
		}
		
		
		if ( isset( $_SERVER['REMOTE_HOST'] ) ) {
			$this->host = $this->sanitize_post( $_SERVER['REMOTE_HOST'], $mode );
		} else {
			$this->host = $this->sanitize_post( gethostbyaddr( $_SERVER['REMOTE_ADDR'] ), $mode );
		}
		
		
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$this->agent = $this->sanitize_post( $_SERVER['HTTP_USER_AGENT'], $mode );
		}
		
		
		if ( isset( $_POST['javascript_action'] ) && $_POST['javascript_action'] === 'true' ) {
			$this->javascript_comment = '送信前の入力チェックは正常に動作しました。';
		}
		
		
		if ( isset( $_POST['now_url'] ) && $_POST['now_url'] !== '' ) {
			$this->now_url = $this->sanitize_post( $_POST['now_url'], $mode );
			$this->now_url = mb_convert_kana( $this->now_url, 'as' );
		}
		
		
		if ( isset( $_POST['before_url'] ) && $_POST['before_url'] !== '' ) {
			$this->before_url = $this->sanitize_post( $_POST['before_url'], $mode );
			$this->before_url = mb_convert_kana( $this->before_url, 'as' );
		}
		
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/writing-time/post-check.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/writing-time/post-check.php' );
		}
		
		
		if ( isset( $_POST['order_count'] ) && $_POST['order_count'] !== '' ) {
			$this->order_count = $this->sanitize_post( $_POST['order_count'], $mode );
			$this->order_count = mb_convert_kana( $this->order_count, 'KVa' );
		}
		
		
		for ( $i = 1; $i < $this->order_count + 1; $i++ ) {
			
			if ( isset( $_POST['order_'.$i] ) && $_POST['order_'.$i] !== '' ) {
				$this->order_isset[$i] = $this->sanitize_post( $_POST['order_'.$i], $mode );
				$this->order_isset[$i] = mb_convert_kana( $this->order_isset[$i], 'KVa' );
				$this->order_isset[$i] = explode( ',', $this->order_isset[$i] );
			}
			
			
			if ( $this->order_isset[$i][0] === 'checkbox' ) {
				
				if ( isset( $_POST[$this->order_isset[$i][1]] ) && $_POST[$this->order_isset[$i][1]] !== '' ) {
					foreach( $_POST[$this->order_isset[$i][1]] as $key => $value ) {
						$this->post_isset[$i][] = $this->sanitize_post( $_POST[$this->order_isset[$i][1]][$key], $mode );
					}
					$this->post_isset[$i] = implode( '、', $this->post_isset[$i] );
				} else {
					$this->post_isset[$i] = '';
				}
				
			} else if ( $this->order_isset[$i][0] === 'email' ) {
				
				if ( isset( $_POST[$this->order_isset[$i][1]] ) && $_POST[$this->order_isset[$i][1]] !== '' ) {
					$this->post_isset[$i] = $this->sanitize_post( $_POST[$this->order_isset[$i][1]], $mode );
					$this->post_isset[$i] = mb_convert_kana( $this->post_isset[$i], 'KVa' );
					
					$this->mail_address_check( $this->order_isset[$i][1], $this->post_isset[$i], true );
					$this->reply_address_set( $this->order_isset[$i][1], $this->post_isset[$i] );
				} else {
					$this->post_isset[$i] = '';
				}
				
			} else if ( $this->order_isset[$i][0] === 'file' ) {
				
				if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/post-check.php' ) ) {
					include( dirname( __FILE__ ) .'/../addon/attachment/post-check.php' );
				}
				
			} else {
				
				if ( isset( $_POST[$this->order_isset[$i][1]] ) && $_POST[$this->order_isset[$i][1]] !== '' ) {
					$this->post_isset[$i] = $this->sanitize_post( $_POST[$this->order_isset[$i][1]], $mode );
					$this->post_isset[$i] = mb_convert_kana( $this->post_isset[$i], 'KVa' );
					
					// for Kaspersky fix
					$this->mail_address_check( $this->order_isset[$i][1], $this->post_isset[$i], false );
					$this->reply_address_set( $this->order_isset[$i][1], $this->post_isset[$i] );
				} else {
					$this->post_isset[$i] = '';
				}
				
			}
			
		}
		
	}
	
	
	
	
	// public block_ip
	public function block_ip() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/block/block-ip.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/block/block-ip.php' );
		}
		
	}
	
	
	
	
	// public block_word
	public function block_word() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/block/block-word.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/block/block-word.php' );
		}
		
	}
	
	
	
	
	// public block_address
	public function block_address() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/block/block-address.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/block/block-address.php' );
		}
		
	}
	
	
	
	
	// public mail_set
	public function mail_set( $set ) {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/dear-name/variable-init.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/dear-name/variable-init.php' );
		}
		
		$send_date = date( 'Y年m月d日　H時i分s秒' );
		
		$set_body  = PHP_EOL;
		$set_body .= '-----------------------------------------------------------------------------------'.PHP_EOL;
		$set_body .= PHP_EOL;
		$set_body .= '【送信時刻】'.PHP_EOL;
		$set_body .= $send_date;
		
		for ( $i = 1; $i < $this->order_count + 1; $i++ ) {
			
			if ( file_exists( dirname( __FILE__ ) .'/../addon/dear-name/name-get.php' ) ) {
				include( dirname( __FILE__ ) .'/../addon/dear-name/name-get.php' );
			}
			
			if ( $this->post_isset[$i] !== '' ) {
				if ( $this->order_isset[$i][2] === 'false' ) {
					
					$set_body .= PHP_EOL;
					$set_body .= PHP_EOL;
					$set_body .= '【'.$this->order_isset[$i][3].'】'.PHP_EOL;
					$set_body .= $this->post_isset[$i];
					
				} else {
					
					if ( $this->post_isset[$i - 1] !== '' ) {
						$set_body .= '　'.$this->post_isset[$i];
					} else {
						
						$set_body .= PHP_EOL;
						$set_body .= PHP_EOL;
						$set_body .= '【'.$this->order_isset[$i][3].'】'.PHP_EOL;
						$set_body .= $this->post_isset[$i];
						
					}
					
				}
			}
			
		}
		
		
		if ( $set === 'send' ) {
			
			$set_body .= PHP_EOL;
			$set_body .= PHP_EOL;
			$set_body .= '-----------------------------------------------------------------------------------'.PHP_EOL;
			$set_body .= PHP_EOL;
			$set_body .= '【送信者のIPアドレス】'.PHP_EOL;
			$set_body .= $this->addr.''.PHP_EOL;
			$set_body .= PHP_EOL;
			$set_body .= '【送信者のホスト名】'.PHP_EOL;
			$set_body .= $this->host.''.PHP_EOL;
			$set_body .= PHP_EOL;
			$set_body .= '【送信者のブラウザ】'.PHP_EOL;
			$set_body .= $this->agent.''.PHP_EOL;
			$set_body .= PHP_EOL;
			$set_body .= '【送信前の入力チェック】'.PHP_EOL;
			$set_body .= $this->javascript_comment.''.PHP_EOL;
			$set_body .= PHP_EOL;
			$set_body .= '【メールフォームのURL】'.PHP_EOL;
			$set_body .= $this->now_url.''.PHP_EOL;
			$set_body .= PHP_EOL;
			$set_body .= '【メールフォームのページの直前に見たURL】'.PHP_EOL;
			$set_body .= $this->before_url.''.PHP_EOL;
			$set_body .= PHP_EOL;
			
			if ( file_exists( dirname( __FILE__ ) .'/../addon/writing-time/writing-ok.php' ) ) {
				include( dirname( __FILE__ ) .'/../addon/writing-time/writing-ok.php' );
			}
			
		} else {
			
			$set_body .= PHP_EOL;
			$set_body .= PHP_EOL;
			$set_body .= '-----------------------------------------------------------------------------------'.PHP_EOL;
			$set_body .= $this->thanks_body_signature;
			
		}
		
		
		if ( $set === 'send' ) {
			$this->send_body   .= $set_body;
		} else {
			
			if ( file_exists( dirname( __FILE__ ) .'/../addon/dear-name/dear-set.php' ) ) {
				include( dirname( __FILE__ ) .'/../addon/dear-name/dear-set.php' );
			}
			
			$this->thanks_body .= $set_body;
			
		}
		
	}
	
	
	
	
	// public csv_check
	public function csv_check() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/csv-record/include/csv-check.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/csv-record/include/csv-check.php' );
		}
		
	}
	
	
	
	
	// public csv_write
	public function csv_write() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/csv-record/include/csv-write.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/csv-record/include/csv-write.php' );
		}
		
	}
	
	
	
	
	// public already_cookie_set
	public function already_cookie_set() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/csv-record/include/already-cookie-set.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/csv-record/include/already-cookie-set.php' );
		}
		
	}
	
	
	
	
	// public already_ip_set
	public function already_ip_set() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/csv-record/include/already-ip-set.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/csv-record/include/already-ip-set.php' );
		}
		
	}
	
	
	
	
	// public enquete_result
	public function enquete_result() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/csv-record/include/enquete-result.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/csv-record/include/enquete-result.php' );
		}
		
	}
	
	
	
	
	// public mail_send
	public function mail_send() {
		
		$send_address_all = implode( ',', $this->send_address );
		
		
		if ( $this->reply_mail_address === true ) {
			$additional_headers = "From: ".$this->mail_address;
			$additional_params  = '-f'.$this->mail_address;
		} else {
			$additional_headers = "From: ".$this->send_address[0];
			$additional_params  = '-f'.$this->send_address[0];
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/carbon-copy/carbon-headers.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/carbon-copy/carbon-headers.php' );
		}
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/attachment/mail-multipart.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/attachment/mail-multipart.php' );
		}
		
		$this->my_result = mb_send_mail( $send_address_all, $this->send_subject, $this->send_body, $additional_headers, $additional_params );
		
		
		if ( $this->reply_mail === 1 ) {
			
			$this->send_name           = mb_encode_mimeheader( $this->send_name, 'UTF-8' );
			$thanks_additional_headers = "From: ".$this->send_name." <".$this->send_address[0].">";
			$thanks_additional_params  = '-f'.$this->send_address[0];
			
			if ( $this->reply_mail_address === true ) {
				$this->you_result = mb_send_mail( $this->mail_address, $this->thanks_subject, $this->thanks_body, $thanks_additional_headers, $thanks_additional_params );
			} else {
				$this->you_result = true;
			}
			
		}
		
	}
	
	
	
	
	// public mail_result
	public function mail_result() {
		
		if ( $this->reply_mail === 1 && $this->reply_mail_address === true ) {
			
			if ( $this->my_result && $this->you_result ) {
				echo 'send_success,' . $this->thanks_page_url;
			} else {
				echo 'send_failed,エラーが起きました。<br />ご迷惑をおかけして大変申し訳ありません。';
			}
			
		} else {
			
			if ( $this->my_result ) {
				echo 'send_success,' . $this->thanks_page_url;
			} else {
				echo 'send_failed,エラーが起きました。<br />ご迷惑をおかけして大変申し訳ありません。';
			}
			
		}
		
	}
	
	
	
	
	// public config_get
	public function config_get() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/confirm/config-get.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/confirm/config-get.php' );
		}
		
	}
	
	
	
	
	// public confirm_set
	public function confirm_set() {
		
		if ( file_exists( dirname( __FILE__ ) .'/../addon/confirm/confirm-set.php' ) ) {
			include( dirname( __FILE__ ) .'/../addon/confirm/confirm-set.php' );
		}
		
	}
	
}

?>