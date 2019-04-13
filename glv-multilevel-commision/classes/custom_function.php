<?php
// add bank tab and prodile in add bank detail comment line number  25 ,26 , 47
// my account- referral.php on 17to 20 line comments
if ( ! class_exists( 'mrp_custom_function' ) ) {
	class mrp_custom_function extends WooCommerce_Multilevel_Referal {
		function __construct() {
			//INIT
			add_action( 'init', array( $this, 'mrp_custom_init_function' ) );
			add_action( 'admin_init', array( $this, 'mrp_admin_custom_init_function' ) );

			//ENQUEUE SCRIPTS
			add_action( 'wp_enqueue_scripts', array( $this, 'mrp_enqueue_scrips_custom' ) );

			// MENU ENDPOINT POINT CREATE
			add_action( 'woocommerce_account_my-referral_endpoint', array(
				$this,
				'mrp_my_affliates_endpoint_content'
			) );
			$withdrawal_feature = get_option( 'wmc_withdrawal_features', true );
			if ( ! $withdrawal_feature || $withdrawal_feature != 'no' ) {
				add_action( 'woocommerce_account_account-statements_endpoint', array(
					$this,
					'mrp_account_statements_endpoint_content'
				) );
				//add_action( 'woocommerce_before_my_account', array($this, 'user_front_header_template' ));
				add_action( 'woocommerce_account_redeem-points_endpoint', array(
					$this,
					'mrp_redeem_points_endpoint_content'
				) );
			}
			//MY-ACCOUNT IN CHANGE NAME AND ADD ACCOUNT MENUS 
			add_filter( 'woocommerce_account_menu_items', array( $this, 'mrp_remove_my_account_links' ), 99 );

			// EDIT ACCOUNT FORM IN ADD BANK ACCOUNT DETAILS FIELDS
			//add_action('woocommerce_edit_account_form',array($this , 'mrp_add_bank_acount_information_call') );

			//EDIT USER PROFILE UPDATE AND VALIDATION
			//add_action('woocommerce_save_account_details_required_fields',array($this , 'mrp_save_user_fields_validation') ,99);
			//add_action('woocommerce_save_account_details',array($this , 'mrp_save_user_fields') ,99);

			//ADD SHORTCODE FOR REDEEM PAYMENT
			add_shortcode( 'redeem_points_payout', array( $this, 'mrp_redeem_point_callback' ) );

			// MY ACCOUNT PAGE TITLE CHANGE
			add_filter( 'the_title', array(
				$this,
				'maybe_change_wp_title_ver'
			), 99, 2 ); //99 is set as priority (read comments)

			// ADD SHORT CODE FOR REDEEM TRANSACTION
			add_shortcode( 'wmc_show_redeem_info', array( $this, 'mrp_wmc_show_redeem_info_callback' ) );
			// CUSTOM PAYMENT METHOD ADD BACKEND
		}

		function mrp_wmc_show_redeem_info_callback() {
			ob_start();
			global $wpdb;
			$tbl_name_ref_pro = $wpdb->prefix . 'referal_program';
			$tbl_name_redeem  = $wpdb->prefix . 'redeem_history';
			if ( is_user_logged_in() ) {
				$obj_referal_users   = new Referal_Users();
				$obj_referal_program = new Referal_Program();
				$current_user_id     = get_current_user_ID();
				$data                = array(
					'referral_code'   => $obj_referal_users->referral_user( 'referral_code', 'user_id', $current_user_id ),
					'total_points'    => $obj_referal_program->available_credits( $current_user_id ),
					'total_followers' => $obj_referal_program->no_of_followers( $current_user_id )
				);

				$user_id = get_current_user_ID();

				$count_sql = "SELECT COUNT(id) from $tbl_name_redeem WHERE transaction_id != '' AND user_id=" . $current_user_id;
				$query_sql = "select * from $tbl_name_redeem WHERE";

				if ( isset( $_GET['search_start_date'] ) && $_GET['search_start_date'] != '' && isset( $_GET['search_end_date'] ) && $_GET['search_end_date'] != '' ) {
					$count_sql .= " AND (date BETWEEN '" . $_GET['search_start_date'] . "' AND '" . $_GET['search_end_date'] . ' 23:59:59' . "')";
					$query_sql .= " (date BETWEEN '" . $_GET['search_start_date'] . "' AND '" . $_GET['search_end_date'] . ' 23:59:59' . "') AND";
				}

				$page_num     = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
				$limit        = 10; // Number of rows in page
				$offset       = ( $page_num - 1 ) * $limit;
				$total        = $wpdb->get_var( $count_sql );
				$num_of_pages = ceil( $total / $limit );

				$query_sql .= " user_id=$user_id AND transaction_id != ''  order by id DESC LIMIT $offset,$limit";
				$results   = $wpdb->get_results( $query_sql );
				?>

                <div class="show_reddem_table">
                    <h2><?php _e( 'Withdrawn History', 'wmc' ); ?></h2>
                    <div class="widthraw_filter">
                        <form method="get" id="widraw_filter_id">
                            <div class="withraw_main_label">
                                <label><?php _e( 'Filter by Date Range', 'woocommerce-extension' ); ?></label></div>
                            <div class="withdraw_filters">
								<?php
								echo '<label>' . __( 'From', 'woocommerce-extension' ) . '</label>:<label class="start_date"><input type="text" name="search_start_date" placeholder="YYYY/MM/DD" value="' . ( isset( $_REQUEST['search_start_date'] ) ? $_REQUEST['search_start_date'] : '' ) . '" autocomplete="off" /></label>';
								echo '<label>' . __( 'To:', 'woocommerce-extension' ) . '</label><label class="end_date"><input type="text" name="search_end_date" placeholder="YYYY/MM/DD" value="' . ( isset( $_REQUEST['search_end_date'] ) ? $_REQUEST['search_end_date'] : '' ) . '" autocomplete="off" /></label>';
								?>
                                <input type="submit" name="" value="Apply">
                                <input type="reset" name="" value="Reset" id="reset_widthraw">
                            </div>
                        </form>
                    </div>
                    <table class="shop_table shop_table_responsive">
                        <thead>
                        <tr>

                            <th><?php _e( 'Mobile No.', 'woocommerce-extension' ); ?></th>
                            <th style="display:none;"><?php _e( 'Order Id', 'woocommerce-extension' ); ?></th>
                            <th><?php _e( 'Transaction ID', 'woocommerce-extension' ); ?></th>
                            <th><?php _e( 'Amount', 'woocommerce-extension' ); ?></th>
                            <th><?php _e( 'Status', 'woocommerce-extension' ); ?></th>
                            <th><?php _e( 'Message', 'woocommerce-extension' ); ?></th>
                            <!-- <th><?php //_e('Payment Method','woocommerce-extension');
							?></th> -->
                            <th><?php _e( 'Date', 'woocommerce-extension' ); ?></th>

                        </tr>
                        </thead>
                        <tbody>
						<?php if ( $results ) {

							foreach ( $results as $key => $value ) {
								if ( $value->transaction_id == '' ) {
									$value->transaction_id = '-';
								}
								if ( $value->mobile_number == '' ) {
									$value->mobile_number = '-';
								}
								echo "<tr>";
								echo "<td align='center'>" . $value->mobile_number . "</td>";
								echo "<td style='display:none;'>>" . $value->merchant_order_id . "</td>";
								echo "<td align='center'>" . $value->transaction_id . "</td>";
								echo "<td>" . get_woocommerce_currency_symbol() . ' ' . number_format( $value->amount, 2 ) . "</td>";
								echo "<td>" . $value->status . "</td>";
								echo "<td>" . $value->message . "</td>";
								// echo "<td>".$value->payment_method."</td>";
								echo "<td>" . $value->date . "</td>";

								echo "</tr>";
							}
						} else {
							echo "<tr><td colspan='6'>" . __( 'Transaction not available.', 'woocommerce-extension' ) . "</td></tr>";
						} ?>
                        </tbody>
                    </table>
					<?php
					echo $page_links = paginate_links( array(
						'base'      => add_query_arg( 'pagenum', '%#%' ),
						'format'    => '',
						'prev_text' => __( '«', 'text-domain' ),
						'next_text' => __( '»', 'text-domain' ),
						'total'     => $num_of_pages,
						'current'   => $page_num
					) );
					?>
                </div>
				<?php

			}
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}

		function get_referral_user_list_custom( $user_id, $filter ) {
			global $wpdb;

			$sql = 'SELECT a.user_id, a.meta_value as first_name, b.meta_value as last_name, followers_count(a.user_id, \'count\') as followers, c.active, c.join_date
			FROM ' . $wpdb->usermeta . ' AS a
			JOIN ' . $wpdb->usermeta . ' AS b on a.user_id = b.user_id
			JOIN ' . $wpdb->prefix . 'referal_users AS c on a.user_id = c.user_id
			WHERE a.meta_key = "first_name" AND b.meta_key = "last_name" AND c.active = 1 AND c.referral_parent = ' . $user_id;


			if ( isset( $_GET['filter'] ) && $_GET['filter'] != '' ) {
				$get_filter_date  = $_GET['filter'];
				$month_start_date = date( 'y-m-d', strtotime( "$get_filter_date first day of this month" ) );
				$month_last_date  = date( 'y-m-d', strtotime( "$get_filter_date last day of this month" ) );
				$sql              .= ' AND c.join_date BETWEEN STR_TO_DATE("' . $month_start_date . '","%Y-%m-%d") AND STR_TO_DATE("' . $month_last_date . '","%Y-%m-%d")';
			}
			if ( isset( $_GET['orderby'] ) && $_GET['orderby'] == 'desc' ) {
				$sql .= ' order by c.join_date DESC';
			} else {
				$sql .= ' order by c.join_date ASC';
			}


			// if($filter == 'month')
			// {
			// 	$last_date  = date('Y-m-d H:i:s', strtotime('-1 months'));
			// 	$sql .= ' AND c.join_date BETWEEN "'.$last_date.'" AND "'.$date.'" ';
			// }else if($filter == '3month')
			// {
			// 	$last_date  = date('Y-m-d H:i:s', strtotime('-3 months'));
			// 	$sql .= ' AND c.join_date BETWEEN "'.$last_date.'" AND "'.$date.'" ';
			// }else if($filter == 'year')
			// {	
			// 	$year = date('Y'); 
			// 	$last_date  = date('Y', strtotime('-1 year'));
			// 	$sql .= ' AND c.join_date BETWEEN "'.$last_date.'" AND "'.$year.'" ';
			// }	

			$referral_result = $wpdb->get_results( $sql );

			//print_r($wpdb->last_query);
			return $referral_result;
		}

		// MY ACCOUNT PAGE TITLE CHANGE FUNCTION
		function maybe_change_wp_title_ver( $title, $sep ) {
			$myaccount_page = get_option( 'woocommerce_myaccount_page_id' );
			if ( is_user_logged_in() && in_the_loop() && is_page( $myaccount_page ) ) {

				$user_data = wp_get_current_user();
				$title     = $user_data->user_firstname . " " . $user_data->user_lastname;
			}

			return $title;
		}

		// REDEEM POINT SHORTCODE CALLBACK FUNCTION
		function mrp_redeem_point_callback() {

			ob_start();

			if ( is_user_logged_in() ) {
				$current_user_id     = get_current_user_ID();
				$get_mobile_num      = get_user_meta( $current_user_id, 'billing_phone', true );
				$obj_referal_program = new Referal_Program();

				$obj_referal_users = new Referal_Users();
				$user_id           = get_current_user_ID();
				$active            = array();
				$action[1]         = '';
				$action[2]         = '';
				$action[3]         = '';
				if ( isset( $_POST['payment_method'] ) ) {
					if ( $_POST['payment_method'] == 'Paytm' ) {
						$action[1] = 'checked';
					} else if ( $_POST['payment_method'] == 'bank_transfer' ) {
						$action[3] = 'checked';
					} else {
						$action[2] = 'checked';
					}
				} else {
					$action[1] = 'checked';
				}
				$total_point = $obj_referal_program->available_credits( $current_user_id );

				?>
                <div class="redeem_point_main">

                    <form method="post">
                        <h2><?php _e( 'Withdraw', 'woocommerce-extension' ); ?></h2>
                        <div class="withdraw_limit">
                            <label><?php
								$withdraw_lmt_get = get_option( 'wmc_conversion_rate_cal', true );

								if ( $withdraw_lmt_get == '' ) {
									$withdraw_lmt_get = 1;
								}
								$total_redeem = $withdraw_lmt_get * $total_point;

								echo __( 'Your maximum withdraw limit is ', 'woocommerce-extension' ) . "<span class='limit_amount'>" . wc_price( $total_redeem ) . "</span>";
								?>
                            </label>
                        </div>
                        <div class="redeem_payment_methds_select">
                            <div class="paytm">
                                <label>
                                    <input id="paytm_radio" type="radio" name="payment_method"
                                           value="Paytm" <?php echo $action[1]; ?>>
									<?php _e( 'PayTM', 'woocommerce-extension' ); ?>
                                </label>
                                <input type="hidden" id="conversion_rate_id" value="<?php echo $withdraw_lmt_get; ?>"
                                       data="<?php echo get_woocommerce_currency_symbol(); ?>">
                                <div class="content_payment" style="<?php if ( $action[1] == '' ) {
									echo 'display:none';
								} ?>">
                                    <table>
                                        <tr>
                                            <th><label><?php _e( 'Mobile Number', 'woocommerce-extension' ) ?></label>
                                            </th>
                                            <td><input type="text" name="paytm_id"
                                                       placeholder="Enter 10 Digit mobile number"
                                                       value="<?php echo isset( $_POST['paytm_id'] ) ? $_POST['paytm_id'] : $get_mobile_num; ?>"
                                                       maxlength="10"></td>
                                        </tr>
                                        <tr>
                                            <th><label><?php _e( 'Enter points', 'woocommerce-extension' ); ?> </label>
                                            </th>
                                            <td>
                                                <input type="text" name="paytm_amount" placeholder="ex. 100"
                                                       value="<?php echo isset( $_POST['paytm_amount'] ) ? $_POST['paytm_amount'] : ''; ?>">
                                                <br><span
                                                        class="convet_amount"><?php _e( 'Calulcated Withdraw amount', 'woocommerce-extension' ); ?><strong> <label
                                                                class="show_amount"></label></strong></span>
                                            </td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                            <div class="newpurchase" style="display:none;">
                                <label>
                                    <input id="newpurchase_radio" type="radio" name="payment_method"
                                           value="new_purchase" <?php echo $action[2]; ?>>
									<?php _e( 'New Purchase', 'woocommerce-extension' ); ?>
                                </label>
                                <div class="content_payment" style="<?php if ( $action[2] == '' ) {
									echo 'display:none';
								} ?>">
                                </div>
                            </div>
                            <div class="bank_tarnsfer" style="display:none;">
                                <label>
                                    <input id="bank_transfer_radio" type="radio" name="payment_method"
                                           value="bank_transfer" <?php echo $action[3]; ?>>
									<?php _e( 'Bank Transfer Request', 'woocommerce-extension' ); ?>
                                </label>
                                <div class="content_payment" style="<?php if ( $action[3] == '' ) {
									echo 'display:none';
								} ?>">
									<?php
									$myaccount_page = get_option( 'woocommerce_myaccount_page_id' );
									if ( $myaccount_page ) {
										$myaccount_page_url = get_permalink( $myaccount_page ) . "edit-account/";
										echo "<a href='" . $myaccount_page_url . "'><span>" . __( 'Add New Bank Detail', 'woocommerce-extension' ) . "</span></a>";
									}
									if ( is_user_logged_in() ) {
										$user_id = get_current_user_ID();
										$this->mrp_saved_card_information( $user_id, 'redeem' );

									}
									?>
                                </div>
                            </div>
							<?php wp_nonce_field( 'redeem_amount' . $user_id, 'redeem_nonce' ); ?>
                            <input type="submit" name="send_money"
                                   value="<?php _e( 'Withdraw', 'woocommerce-extension' ); ?>"></th>
                        </div>
                    </form>
                </div>
				<?php
			}

			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}

		// ENQUEUE SCRIPTS STYLES
		function mrp_enqueue_scrips_custom() {

			wp_enqueue_script( 'mrp_custom_new_js', WMC_URL . 'js/custom.js', array(
				'jquery',
				'jquery-ui-datepicker'
			) );
			wp_register_style( 'jquery-ui2', WMC_URL . 'css/jquery-ui.css' );
			wp_register_style( 'wmc-fontAwesome', WMC_URL . 'css/font-awesome.min.css' );
			wp_enqueue_style( 'jquery-ui2' );
			wp_enqueue_style( 'wmc-fontAwesome' );
			wp_enqueue_style( 'jquery-ui-datepicker' );

		}

		function mrp_save_user_fields( $user_id ) {
			$get_user_data = get_user_meta( $user_id, 'woocommerce_saved_account_details', true );

			if ( ! wp_verify_nonce( $_POST['save-account-details-nonce'], 'save_account_details' ) ) {
				return;
			}
			if ( isset( $_POST['action_edit'] ) && $_POST['action_edit'] == 'edit' && isset( $_POST['edit_id'] ) ) {
				$id = $_POST['edit_id'];
				if ( isset( $_POST['payee_name_edit'] ) ) {
					$get_user_data[ $id ]['payee_name'] = trim( $_POST['payee_name_edit'] );
				}
				if ( isset( $_POST['payee_account_number_edit'] ) ) {
					$get_user_data[ $id ]['payee_account_number'] = trim( $_POST['payee_account_number_edit'] );
				}
				if ( isset( $_POST['bank_name_edit'] ) ) {
					$get_user_data[ $id ]['bank_name'] = trim( $_POST['bank_name_edit'] );
				}
				if ( isset( $_POST['bank_branch_edit'] ) ) {
					$get_user_data[ $id ]['bank_branch'] = trim( $_POST['bank_branch_edit'] );
				}
				if ( isset( $_POST['bank_ifsc_edit'] ) ) {
					$get_user_data[ $id ]['bank_ifsc'] = trim( $_POST['bank_ifsc_edit'] );
				}
				if ( isset( $_POST['sel_account_type_edit'] ) ) {
					$get_user_data[ $id ]['sel_account_type'] = trim( $_POST['sel_account_type_edit'] );
				}
				wc_add_notice( __( 'Bank Details updated.', 'woocommerce-extension' ), 'success' );
			}
			$bank_details = array();
			if ( isset( $_POST['add_account_detail'] ) && $_POST['add_account_detail'] == 'on' ) {

				if ( isset( $_POST['payee_name'] ) ) {
					$bank_details['payee_name'] = trim( $_POST['payee_name'] );
				}
				if ( isset( $_POST['payee_account_number'] ) ) {
					$bank_details['payee_account_number'] = trim( $_POST['payee_account_number'] );
				}
				if ( isset( $_POST['bank_name'] ) ) {
					$bank_details['bank_name'] = trim( $_POST['bank_name'] );
				}
				if ( isset( $_POST['bank_branch'] ) ) {
					$bank_details['bank_branch'] = trim( $_POST['bank_branch'] );
				}
				if ( isset( $_POST['bank_ifsc'] ) ) {
					$bank_details['bank_ifsc'] = trim( $_POST['bank_ifsc'] );
				}
				if ( isset( $_POST['sel_account_type'] ) ) {
					$bank_details['sel_account_type'] = trim( $_POST['sel_account_type'] );
				}
			}
			if ( ! empty( $bank_details ) ) {
				if ( $get_user_data ) {
					$get_user_data[] = $bank_details;
				} else {
					$get_user_data[] = $bank_details;
				}
				wc_add_notice( __( 'Bank Details Added.', 'woocommerce-extension' ), 'success' );
			}
			update_user_meta( $user_id, 'woocommerce_saved_account_details', $get_user_data );

		}

		// SAVE ACCOUNT FIELDS HANDLE AND STORE
		function mrp_save_user_fields_validation( $arr ) {
			if ( ! is_user_logged_in() ) {
				return;
			}
			$user_id       = get_current_user_ID();
			$get_user_data = get_user_meta( $user_id, 'woocommerce_saved_account_details', true );

			if ( isset( $_POST['add_account_detail'] ) && $_POST['add_account_detail'] == 'on' ) {
				if ( array_search( $_POST['payee_account_number'], array_column( $get_user_data, 'payee_account_number' ) ) ) {
					wc_add_notice( __( 'This Account number bank detail already exist.', 'woocommerce-extension' ), 'error' );
				}
				if ( ! isset( $_POST['payee_name'] ) || empty( $_POST['payee_name'] ) ) {
					wc_add_notice( __( 'Please Enter Payee name.', 'woocommerce-extension' ), 'error' );
				}
				if ( ! isset( $_POST['payee_account_number'] ) || empty( $_POST['payee_account_number'] ) ) {
					wc_add_notice( __( 'Please Enter Account Number.', 'woocommerce-extension' ), 'error' );
				}
				if ( ! isset( $_POST['bank_name'] ) || empty( $_POST['bank_name'] ) ) {
					wc_add_notice( __( 'Please Enter Bank name.', 'woocommerce-extension' ), 'error' );
				}
				if ( ! isset( $_POST['bank_branch'] ) || empty( $_POST['bank_branch'] ) ) {
					wc_add_notice( __( 'Please Enter Bank branch.', 'woocommerce-extension' ), 'error' );
				}
				if ( ! isset( $_POST['bank_ifsc'] ) || empty( $_POST['bank_ifsc'] ) ) {
					wc_add_notice( __( 'Please Enter Bank IFSC CODE.', 'woocommerce-extension' ), 'error' );
				}
				if ( ! isset( $_POST['sel_account_type'] ) || empty( $_POST['sel_account_type'] ) ) {
					wc_add_notice( __( 'Please Select Account type.', 'woocommerce-extension' ), 'error' );
				}

			}
			if ( isset( $_POST['action_edit'] ) && $_POST['action_edit'] == 'edit' && isset( $_POST['edit_id'] ) ) {
				$new_check_num = $get_user_data;
				unset( $new_check_num[ $_POST['edit_id'] ] );
				if ( array_search( $_POST['payee_account_number_edit'], array_column( $new_check_num, 'payee_account_number' ) ) ) {
					wc_add_notice( __( 'This Account number bank detail already exist.', 'woocommerce-extension' ), 'error' );
				}
				if ( ! isset( $_POST['payee_name_edit'] ) || empty( $_POST['payee_name_edit'] ) ) {
					wc_add_notice( __( 'Please Enter Edit Payee name.', 'woocommerce-extension' ), 'error' );
				}
				if ( ! isset( $_POST['payee_account_number_edit'] ) || empty( $_POST['payee_account_number_edit'] ) ) {
					wc_add_notice( __( 'Please Enter Edit Account Number.', 'woocommerce-extension' ), 'error' );
				}
				if ( ! isset( $_POST['bank_name_edit'] ) || empty( $_POST['bank_name_edit'] ) ) {
					wc_add_notice( __( 'Please Enter Edit Bank name.', 'woocommerce-extension' ), 'error' );
				}
				if ( ! isset( $_POST['bank_branch_edit'] ) || empty( $_POST['bank_branch_edit'] ) ) {
					wc_add_notice( __( 'Please Enter Edit Bank branch.', 'woocommerce-extension' ), 'error' );
				}
				if ( ! isset( $_POST['bank_ifsc_edit'] ) || empty( $_POST['bank_ifsc_edit'] ) ) {
					wc_add_notice( __( 'Please Enter Edit Bank IFSC CODE.', 'woocommerce-extension' ), 'error' );
				}
				if ( ! isset( $_POST['sel_account_type_edit'] ) || empty( $_POST['sel_account_type_edit'] ) ) {
					wc_add_notice( __( 'Please Select Edit Account type.', 'woocommerce-extension' ), 'error' );
				}
			}

			return $arr;

		}

		function bank_add_account( $data ) {

			if ( is_user_logged_in() ) {
				$user_id = get_current_user_ID();
				if ( $data == 'edit' && isset( $_GET['action'] ) && $_GET['action'] == 'edit' && isset( $_GET['id'] ) ) {
					$get_user_data = get_user_meta( $user_id, 'woocommerce_saved_account_details', true );
					$_POST         = $get_user_data[ $_GET['id'] ];
				}
			}

			?>
            <div class="add_bank_details_div">
				<?php if ( $data != 'edit' ) { ?>
                    <span>
						<input type="checkbox" name="add_account_detail"
                               id="add_account_detail" <?php echo isset( $_POST['add_account_detail'] ) ? 'checked' : ''; ?>>
						<?php _e( 'Add Bank detail', 'woocommerce-extension' ) ?>
					</span>
				<?php } ?>
                <div class="fields_groups"
                     style="display:<?php echo( isset( $_POST['add_account_detail'] ) || $data == 'edit' ? 'block' : 'none' ); ?>;">
					<?php
					$name = '';
					if ( $data != 'edit' ) {
						echo "<h3>" . __( 'Add Account detail', 'woocommerce-extension' ) . "</h3>";
					} else {
						$name = '_edit';
						echo "<h3>" . __( 'Edit Account detail', 'woocommerce-extension' ) . "</h3>";
					}
					?>
                    <p>
                        <label> <?php _e( 'Payee Name', 'woocommerce-extension' ); ?>
                            <input type="text" name="payee_name<?php echo $name; ?>"
                                   value="<?php echo isset( $_POST['payee_name'] ) ? $_POST['payee_name'] : ''; ?>"
                                   placeholder="">
                        </label>
                    </p>
                    <p>
                        <label> <?php _e( 'Payee Account number', 'woocommerce-extension' ); ?>
                            <input type="text" name="payee_account_number<?php echo $name; ?>"
                                   value="<?php echo isset( $_POST['payee_account_number'] ) ? $_POST['payee_account_number'] : ''; ?>"
                                   placeholder="">
                        </label>
                    </p>
                    <p>
                        <label> <?php _e( 'Bank Name', 'woocommerce-extension' ); ?>
                            <input type="text" name="bank_name<?php echo $name; ?>"
                                   value="<?php echo isset( $_POST['bank_name'] ) ? $_POST['bank_name'] : ''; ?>"
                                   placeholder="">
                        </label>
                    </p>
                    <p>
                        <label> <?php _e( 'Branch', 'woocommerce-extension' ); ?>
                            <input type="text" name="bank_branch<?php echo $name; ?>"
                                   value="<?php echo isset( $_POST['bank_branch'] ) ? $_POST['bank_branch'] : ''; ?>"
                                   placeholder="">
                        </label>
                    </p>
                    <p>
                        <label> <?php _e( 'IFSC CODE', 'woocommerce-extension' ); ?>
                            <input type="text" name="bank_ifsc<?php echo $name; ?>"
                                   value="<?php echo isset( $_POST['bank_ifsc'] ) ? $_POST['bank_ifsc'] : ''; ?>"
                                   placeholder="">
                        </label>
                    </p>
                    <p>
                        <label> <?php _e( 'Account type', 'woocommerce-extension' ); ?>
                            <select name="sel_account_type<?php echo $name; ?>">
                                <option value=""><?php _e( 'Select type', 'woocommerce-extension' ) ?></option>
                                <option value="saving" <?php echo isset( $_POST['sel_account_type'] ) && $_POST['sel_account_type'] == 'saving' ? 'selected' : ''; ?>><?php _e( 'Saving', 'woocommerce-extension' ); ?></option>
                                <option value="current" <?php echo isset( $_POST['sel_account_type'] ) && $_POST['sel_account_type'] == 'current' ? 'selected' : ''; ?>><?php _e( 'Current', 'woocommerce-extension' ); ?></option>
                            </select>
                        </label>
                    </p>
                    <p>
						<?php if ( $data == 'edit' ) {
							echo "<input type='hidden' name='action_edit' value='edit'>";
							echo "<input type='hidden' name='edit_id' value='" . $_GET['id'] . "'>";
						}
						?>
                    </p>
                </div>
            </div>
			<?php
		}

		function mrp_saved_card_information( $user_id, $var ) {
			?>
            <table>
                <tr>
					<?php
					if ( $var == 'redeem' ) {
						echo "<td></td>";
					}
					?>
                    <th><?php _e( 'Payee Name', 'woocommerce-extension' ) ?></th>
                    <th><?php _e( 'Bank Name', 'woocommerce-extension' ) ?></th>
                    <th><?php _e( 'Payee Account number', 'woocommerce-extension' ); ?></th>
                    <th><?php _e( 'Branch', 'woocommerce-extension' ); ?></th>
                    <th><?php _e( 'IFSC CODE', 'woocommerce-extension' ); ?></th>
                    <th><?php _e( 'Account type', 'woocommerce-extension' ); ?></th>
                </tr>
				<?php
				$user_card_data = get_user_meta( $user_id, 'woocommerce_saved_account_details', true );
				if ( ! empty( $user_card_data ) ) {
					$myaccount_page = get_option( 'woocommerce_myaccount_page_id' );
					if ( $myaccount_page ) {
						$myaccount_page_url = get_permalink( $myaccount_page );
					}

					foreach ( $user_card_data as $key => $value ) {
						echo "<tr>";
						if ( $var == 'redeem' ) {
							echo "<th><input type='radio' name='bank_transaction_select' value='" . $key . "'></th>";
						}
						echo "<td>" . $value['payee_name'] . "</td>";
						echo "<td>" . $value['bank_name'] . "</td>";
						echo "<td>" . $value['payee_account_number'] . "</td>";
						echo "<td>" . $value['bank_branch'] . "</td>";
						echo "<td>" . $value['bank_ifsc'] . "</td>";
						echo "<td>" . $value['sel_account_type'] . "</td>";
						if ( $var == 'show' ) {
							echo "<td><a class='edit_bank_info' href='" . $myaccount_page_url . "edit-account/?action=edit&id=" . $key . "'><span>" . __( 'edit', 'woocommerce-extension' ) . "</span></a> ";
							echo "<a class='remove_bank_info' href='" . $myaccount_page_url . "edit-account/?action=delete&id=" . $key . "'><span>" . __( 'remove', 'woocommerce-extension' ) . "</span></a> </td>";
						}
						echo "</tr>";
					}
				} else {
					echo "<td></td><td colspan='5'>" . __( 'Bank details not found.', 'woocommerce-extension' ) . "<td>";
				} ?>
            </table>
		<?php }

		//edit account form function
		function mrp_add_bank_acount_information_call() {

			?>
            <div class="bank_account_user_info">

				<?php
				$data = 'new';
				$this->bank_add_account( $data );
				?>
                <div class="saved_bank_details_info">
                    <span><?php _e( 'Saved Bank details', 'woocommerce-extension' ) ?></span>
					<?php
					if ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && isset( $_GET['id'] ) ) {
						$data = 'edit';
						$this->bank_add_account( $data );
					}
					if ( is_user_logged_in() ) {
						$user_id = get_current_user_ID();
						$this->mrp_saved_card_information( $user_id, 'show' );
					}
					?>
                </div>
            </div>
			<?php
		}

		//MY-AFFILLIATE MENU PAGE CALLBACK FUNCTION
		function mrp_my_affliates_endpoint_content() {
			$var = "my_affiliate";
			$this->user_front_header_template( $var );
		}

		//ACCOUNT STATEMENT MENU PAGE CALLBACK FUNCTION
		function mrp_account_statements_endpoint_content() {
			$var = "redeem_statement";
			$this->user_front_header_template( $var );
		}

		function user_front_header_template( $var ) {

			if ( is_user_logged_in() ) {
				$myaccount_page      = get_option( 'woocommerce_myaccount_page_id' );
				$current_user_id     = get_current_user_ID();
				$obj_referal_program = new Referal_Program();
				$obj_referal_users   = new Referal_Users();
				$data                = array(
					'referral_code'    => $obj_referal_users->referral_user( 'referral_code', 'user_id', $current_user_id ),
					'total_points'     => $obj_referal_program->available_credits( $current_user_id ),
					'total_followers'  => $obj_referal_program->no_of_followers( $current_user_id ),
					'total_withdraw'   => $obj_referal_program->total_withdraw_credit( $current_user_id ),
					'total_earn_point' => $obj_referal_program->total_earn_credit( $current_user_id ),

				);
				if ( $var == 'redeem_point' ) {
					$data['active']  = "amount_tab";
					$data['content'] = do_shortcode( '[redeem_points_payout]', true );

				}
				if ( $var == 'redeem_statement' ) {
					$data['active']  = "amount_tab";
					$data['content'] = do_shortcode( '[wmc_show_redeem_info]', true );
				}
				if ( $var == 'my_affiliate' ) {
					$data['active']  = "referral_tab";
					$data['content'] = do_shortcode( '[wmc_show_affiliate_info]', true );
//					$data['content'] .= do_shortcode( '[wmc_show_credit_info]', true );
				}
				echo self::render_template( 'front/myaccount-referral.php', array( 'data' => $data ) );
			}

		}

		//REDEEM POINTS MENU PAGE CALLBACK FUNCTION
		function mrp_redeem_points_endpoint_content() {
			$var = "redeem_point";
			$this->user_front_header_template( $var );
		}

		// ADMIN FUNCTION
		function mrp_admin_custom_init_function() {
			global $wpdb;
			require WMC_DIR . 'lib/config_paytm.php';
			require WMC_DIR . 'lib/encdec_paytm.php';
			if ( isset( $_GET['page'] ) && $_GET['page'] == "wc_referral" && isset( $_GET['tab'] ) && $_GET['tab'] == "withdraw_history" ) {
				$table_redeem_history = $wpdb->prefix . "redeem_history";
				$pending_ids          = $wpdb->get_results( "select * from $table_redeem_history where status = 'PENDING'" );
				foreach ( $pending_ids as $key => $value ) {
					$this->mrp_alogi_status_check( $value->transaction_id, $value->user_id );
				}
			}
		}

		//INIT FUNCTION
		function mrp_custom_init_function() {
			global $wpdb;

			$obj_referal_program = new Referal_Program();
			$redeem_history_tbl  = $wpdb->prefix . "redeem_history";
			$checkSQL_reddem_tbl = "show tables like '$redeem_history_tbl'";
			if ( $wpdb->get_var( $checkSQL_reddem_tbl ) != $redeem_history_tbl ) {
				$create_table = "CREATE TABLE $redeem_history_tbl (
                            id bigint(20) NOT NULL AUTO_INCREMENT,
                            user_id bigint(20),
                            merchant_order_id varchar(255),
                            transaction_id varchar(255),
                            amount varchar(255),
                            status varchar(20),
                            message varchar(255),
                            payment_method varchar(255),
                            date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (id)
                            );";
				require_once( ABSPATH . "wp-admin/includes/upgrade.php" );
				dbDelta( $create_table );
			}

			$check_fields1 = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '" . $redeem_history_tbl . "' AND COLUMN_NAME = 'mobile_number' AND TABLE_SCHEMA = '" . DB_NAME . "' ";
			if ( ! $wpdb->get_var( $check_fields1 ) ) {
				$wpdb->query( "ALTER TABLE $redeem_history_tbl ADD `mobile_number` BIGINT(20) NOT NULL AFTER `user_id`" );
			}

			$referal_tbl  = $wpdb->prefix . 'referal_program';
			$check_fields = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$referal_tbl' AND COLUMN_NAME = 'type' OR COLUMN_NAME = 'redeem_id' AND TABLE_SCHEMA = '" . DB_NAME . "' ";

			if ( ! $wpdb->get_var( $check_fields ) ) {
				$wpdb->query( "ALTER TABLE $referal_tbl ADD `type` VARCHAR(255) NOT NULL AFTER `date`, ADD `redeem_id` BIGINT(20) NOT NULL AFTER `type`" );
			}

			if ( is_user_logged_in() ) {
				$user_id = get_current_user_ID();
				if ( isset( $_POST['redeem_nonce'] ) && wp_verify_nonce( $_POST['redeem_nonce'], 'redeem_amount' . $user_id ) ) {

					if ( isset( $_POST['payment_method'] ) && $_POST['payment_method'] == 'Paytm' ) {
						$check_con = true;
						if ( empty( $_POST['paytm_id'] ) ) {
							wc_add_notice( __( 'PayTm Mobile Number field is empty.', 'woocommerce-extension' ), 'error' );
							$check_con = false;
						}
						if ( empty( $_POST['paytm_amount'] ) ) {
							wc_add_notice( __( 'Redeem amount field is empty.', 'woocommerce-extension' ), 'error' );
							$check_con = false;
						}
						if ( $check_con ) {
							if ( isset( $_POST['paytm_id'] ) && $_POST['paytm_amount'] ) {
								$total_points     = $obj_referal_program->available_credits( $user_id );
								$withdraw_lmt_get = get_option( 'wmc_conversion_rate_cal', true );
								if ( $withdraw_lmt_get == '' ) {
									$withdraw_lmt_get = 1;
								}
								$compare_price = $withdraw_lmt_get * $total_points;
								$get_amount    = $_POST['paytm_amount'] * $withdraw_lmt_get;

								if ( $_POST['paytm_amount'] > 0 && $get_amount <= $compare_price ) {
									$this->paytm_api_results( $_POST );
								} else {
									wc_add_notice( __( 'Please check your maximum withdraw limit.', 'woocommerce-extension' ), 'error' );
								}

							}
						}

					}
				}
			}

			add_rewrite_endpoint( 'my-referral', EP_PAGES );
			add_rewrite_endpoint( 'account-statements', EP_PAGES );
			add_rewrite_endpoint( 'redeem-points', EP_PAGES );

			if ( is_user_logged_in() ) {
				$user_id = get_current_user_ID();
				if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' && isset( $_GET['id'] ) ) {
					$get_user_data = get_user_meta( $user_id, 'woocommerce_saved_account_details', true );
					unset( $get_user_data[ $_GET['id'] ] );
					update_user_meta( $user_id, 'woocommerce_saved_account_details', $get_user_data );
					wc_add_notice( __( 'Bank record deleted.', 'woocommerce-extension' ), 'success' );
				}

				$myaccount_page = get_option( 'woocommerce_myaccount_page_id' );
				$post_data      = get_post( $myaccount_page );
				if ( isset( $post_data->post_name ) ) {
					$slug = $post_data->post_name;
				} else {
					$slug = '';
				}
				$myacc_link = get_permalink( $myaccount_page ) . 'account-statements';
				$link       = $_SERVER['REQUEST_URI'];
				$slug_arr   = explode( $slug . '/', $link );

				if ( isset( $slug_arr[1] ) && ( $slug_arr[1] == 'account-statements' || $slug_arr[1] == 'account-statements/' ) ) {
					$this->mrp_paytm_status_check_function();
				}
			}

		}

		function paytm_api_results( $posts ) {
			$obj_referal_program = new Referal_Program();
			$user_id             = get_current_user_ID();
			$merchant_order_id   = 'order' . time() . mt_rand( 0, 999 );
			require WMC_DIR . 'lib/config_paytm.php';
			require WMC_DIR . 'lib/encdec_paytm.php';

			$data = array(
				"request"       => array(
					"requestType"       => null,
					"merchantGuid"      => PAYTM_MERCHANT_GUID,
					"merchantOrderId"   => $merchant_order_id,
					"salesWalletName"   => null,
					"salesWalletGuid"   => PAYTM_SALES_WALLET_GUID,
					"payeeEmailId"      => null,
					"payeePhoneNumber"  => $_POST['paytm_id'],
					"payeeSsoId"        => "",
					"appliedToNewUsers" => "Y",
					"amount"            => $_POST['paytm_amount'],
					"currencyCode"      => PAYTM_CURRENCY_CODE,
				),
				"metadata"      => "Paytm Transaction",
				"ipAddress"     => "127.0.0.1",
				"platformName"  => "PayTM",
				"operationType" => "SALES_TO_USER_CREDIT"
			);

			$requestData = json_encode( $data );


			$Checksumhash = getChecksumFromString( $requestData, PAYTM_MERCHANT_KEY );
			$headerValue  = array(
				'Content-Type:application/json',
				'mid:' . PAYTM_MERCHANT_GUID,
				'checksumhash:' . $Checksumhash
			);
			// echo "<pre>";
			// print_r( $headerValue);

			$ch = curl_init( PAYTM_GRATIFICATION_URL );
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $requestData );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // return the output in string format
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerValue );
			$info   = curl_getinfo( $ch );
			$result = curl_exec( $ch );

			// echo '<br/><br/><b>INFO :</b>';
			// print_r($info);
			// echo '<br/><br/><b>RESULT :</b> ' ;
			$responce = json_decode( $result );

			$conversion_rate = get_option( 'wmc_conversion_rate_cal', true );
			if ( $conversion_rate == '' ) {
				$conversion_rate = 1;
			}
			if ( $responce->response->walletSysTransactionId != '' ) {
				$this->mrp_paytm_status_check_function( $responce->response->walletSysTransactionId );
			}

			if ( $responce->status == 'SUCCESS' ) {
				$data                      = array();
				$data['merchant_order_id'] = $responce->orderId;
				$data['transaction_id']    = $responce->response->walletSysTransactionId;
				$data['status']            = $responce->status;
				$data['statusMessage']     = $responce->statusMessage;
				$data['payment_method']    = $_POST['payment_method'];
				$data['mobile_number']     = $_POST['paytm_id'];

				$data['user_id'] = $user_id;
				$data['amount']  = $_POST['paytm_amount'] * $conversion_rate;
				$data['redeems'] = $_POST['paytm_amount'];
				$data['type']    = 1;
				$obj_referal_program->insert_redeem( $data );

				$myaccount_page = get_option( 'woocommerce_myaccount_page_id' );

				wc_add_notice( __( 'Amount successfully added to your paytm wallet.', 'woocommerce-extension' ), 'success' );

				wp_redirect( get_permalink( $myaccount_page ) . "redeem-points/" );
				exit();
			} else {
				$data                      = array();
				$data['merchant_order_id'] = $responce->orderId;
				$data['transaction_id']    = $responce->response->walletSysTransactionId;
				$data['status']            = $responce->status;
				$data['payment_method']    = $_POST['payment_method'];
				$data['statusMessage']     = $responce->statusMessage;
				$data['user_id']           = $user_id;
				$data['mobile_number']     = $_POST['paytm_id'];
				$data['amount']            = $_POST['paytm_amount'] * $conversion_rate;

				$obj_referal_program->insert_redeem( $data );
				wc_add_notice( __( $responce->statusMessage, 'woocommerce-extension' ), 'error' );
			}

		}

		// PAYTM STATUS CHECK FUNCTION
		function mrp_paytm_status_check_function( $transaction_id = '' ) {
			global $wpdb;

			$tbl_redeem = $wpdb->prefix . "redeem_history";
			if ( ! is_user_logged_in() ) {
				return;
			}
			$user_id = get_current_user_ID();
			if ( $transaction_id != '' ) {
				$this->mrp_alogi_status_check( $transaction_id, $user_id );
			} else {
				require WMC_DIR . 'lib/config_paytm.php';
				require WMC_DIR . 'lib/encdec_paytm.php';

				$results_tx_ids = $wpdb->get_col( "select transaction_id from $tbl_redeem where user_id = $user_id AND status = 'PENDING' " );
				if ( $results_tx_ids ) {

					foreach ( $results_tx_ids as $key => $value ) {
						$this->mrp_alogi_status_check( $value, $user_id );
					}
				}
			}
		}

		function mrp_alogi_status_check( $txn_id, $user_id ) {
			global $wpdb;
			$tbl_redeem = $wpdb->prefix . "redeem_history";
			header( "Pragma: no-cache" );
			header( "Cache-Control: no-cache" );
			header( "Expires: 0" );

			// following files need to be included


			$ORDER_ID          = "";
			$requestParamList  = array();
			$responseParamList = array();
			$constants         = get_defined_constants( true );
			$data              = array(
				"request"       => array(
					'requestType'  => 'wallettxnid',
					"txnType"      => "SALES_TO_USER_CREDIT",
					"txnId"        => $txn_id,
					'merchantGuid' => PAYTM_MERCHANT_GUID,
				),
				"platformName"  => "PayTM",
				"operationType" => "CHECK_TXN_STATUS",
			);

			$requestData  = json_encode( $data );
			$Checksumhash = getChecksumFromString( $requestData, PAYTM_MERCHANT_KEY );
			$headerValue  = array(
				'Content-Type:application/json',
				'mid:' . PAYTM_MERCHANT_GUID,
				'checksumhash:' . $Checksumhash
			);
			// echo "<pre>";
			// print_r( $headerValue);
			$ch = curl_init( PAYTM_CHECK_STATUS_URL );
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $requestData );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); // return the output in string format
			curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headerValue );
			$info          = curl_getinfo( $ch );
			$result        = curl_exec( $ch );
			$result_output = json_decode( $result );
			if ( $result_output ) {
				$conversion_rate = get_option( 'wmc_conversion_rate_cal', true );
				if ( $conversion_rate == '' ) {
					$conversion_rate = 1;
				}
				if ( isset( $result_output->response->txnList[0]->status ) && $result_output->response->txnList[0]->status == 1 ) {
					$results_tx_ids = $wpdb->get_var( "select id from $tbl_redeem where user_id = $user_id AND status = 'PENDING' && transaction_id = '$txn_id' " );
					if ( $results_tx_ids ) {
						$data                      = array();
						$data['merchant_order_id'] = $result_output->response->txnList[0]->merchantOrderId;
						$data['transaction_id']    = $result_output->response->txnList[0]->txnGuid;
						$data['status']            = 'SUCCESS';
						$data['statusMessage']     = $result_output->response->txnList[0]->message;
						$data['payment_method']    = 'PayTM';
						$data['user_id']           = $user_id;
						$data['amount']            = $result_output->response->txnList[0]->txnAmount * $conversion_rate;
						$data['redeems']           = $result_output->response->txnList[0]->txnAmount;
						$data['type']              = 1;
						$data['id']                = $results_tx_ids;
						$this->update_redeem( $data );
					}
				}
			}

		}

		function update_redeem( $data ) {
			global $wpdb;
			$wpdb->update(
				$wpdb->prefix . "redeem_history",
				array(
					'status'  => $data['status'],
					'message' => $data['statusMessage'],
				),
				array(
					'user_id'        => $data['user_id'],
					'transaction_id' => $data['transaction_id']
				)
			);

			$wpdb->insert(
				$wpdb->prefix . "referal_program",
				array(
					'order_id'  => isset( $data['order_id'] ) ? $data['order_id'] : 0,
					'user_id'   => $data['user_id'],
					'credits'   => isset( $data['credits'] ) ? $data['credits'] : 0,
					'redeems'   => isset( $data['redeems'] ) ? $data['redeems'] : 0,
					'type'      => isset( $data['type'] ) ? $data['type'] : 0,
					'redeem_id' => isset( $data['id'] ) ? $data['id'] : 0,
				)
			);
		}

		// MY-ACCOUNT MENUS FILTER FUNCTION
		function mrp_remove_my_account_links( $menu_links ) {
			$new_array                  = array();
			$menu_links['orders']       = __( 'My orders', 'woocommerce-extension' );
			$menu_links['edit-account'] = __( 'Profile', 'woocommerce-extension' );
			//unset( $menu_links['downloads'] );
			$withdrawal_feature = get_option( 'wmc_withdrawal_features', true );
			foreach ( $menu_links as $key => $value ) {
				$new_array[ $key ] = $value;
				if ( $key == 'referral' ) {
//					$new_array['my-referral'] = __( 'List Referrer', 'woocommerce-extension' );

					//if($withdrawal_feature != 'no' || )
					if ( ! $withdrawal_feature || $withdrawal_feature != 'no' ) {
						$new_array['redeem-points']      = __( 'Withdraw', 'woocommerce-extension' );
						$new_array['account-statements'] = __( 'Withdrawn History', 'woocommerce-extension' );
					}
				}
			}

			return $new_array;
		}

	}

	new mrp_custom_function();
}

?>