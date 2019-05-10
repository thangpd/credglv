<?php

namespace credglv\models;

use credglv\core\Model;
use credglv\core\interfaces\ModelInterface;

class ReferrerModel extends Model implements ModelInterface {


	const POST_TYPE = 'credglv_referrer';
	const POST_META = 'course_meta';
	const BUNDLE_ID = 'referrer_id';
	const SALE_PRICE = 'credglv_sale_price';
	const REGULAR_PRICE = 'total_price';


	public static function getPosttypeConfig() {
		// TODO: Implement getPosttypeConfig() method.
		$labels = array(
			'name'               => _x( 'Referrer', 'post type general name', 'credglv' ),
			'singular_name'      => _x( 'Referrer', 'post type singular name', 'credglv' ),
			'menu_name'          => _x( 'Referrer', 'admin menu', 'credglv' ),
			'name_admin_bar'     => _x( 'Referrer', 'add new on admin bar', 'credglv' ),
			'add_new'            => _x( 'Add Referrer', 'Course', 'credglv' ),
			'add_new_item'       => __( 'Add New Referrer', 'credglv' ),
			'new_item'           => __( 'New Referrer', 'credglv' ),
			'edit_item'          => __( 'Edit Referrer', 'credglv' ),
			'view_item'          => __( 'View Referrer', 'credglv' ),
			'all_items'          => __( 'Referrers', 'credglv' ),
			'search_items'       => __( 'Search Referrer', 'credglv' ),
			'parent_item_colon'  => __( 'Parent Referrer:', 'credglv' ),
			'not_found'          => __( 'No Referrer found.', 'credglv' ),
			'not_found_in_trash' => __( 'No Referrer found in Trash.', 'credglv' )
		);

		$args = array(
			'labels'          => $labels,
			'description'     => __( 'Description.', 'credglv' ),
			'public'          => true,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'capability_type' => 'post',
			'has_archive'     => false,
			'hierarchical'    => false,
			'supports'        => array( 'title', 'excerpt', 'thumbnail' )
		);

		return [
			'post' => [
				'name' => 'credglv_referrer',
				'args' => $args
			]
		];
	}

	public function getId() {
		return parent::getId(); // TODO: Change the autogenerated stub
	}

	public function init() {
		parent::init(); // TODO: Change the autogenerated stub
	}

	public function getName() {
		return self::POST_TYPE;
	}

	public function save( $postData = [] ) {
		try {
			$defaultPostData = [
				'post_title'  => '#Referrer ',
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish'
			];
			if ( $this->isNew ) {
				$postId = wp_insert_post( array_merge( $defaultPostData, $postData ) );

				return $postId;
			} else {
				return wp_update_post( $this->post );
			}
		} catch ( \Exception $e ) {
		}

		return false;
	}

	public function saveMetaData( $post_id, $data_meta ) {
		if ( isset( $data_meta['instructors'] ) && ! empty( $data_meta['instructors'] ) ) {

			if ( is_array( $data_meta['instructors'] ) ) {
				foreach ( $data_meta['instructors'] as $ins_id ) {
					add_user_meta( $ins_id, Instructor::instructorMetaKey(), $post_id );
				}
			}
		}
		$attrs = $this->getAttributes();
		foreach ( $attrs as $key => $data ) {
			if ( isset( $data_meta[ $key ] ) ) {
				update_post_meta( $post_id, $key, $data_meta[ $key ] );
			} else {
				update_post_meta( $post_id, $key, '' );
			}
		}
	}

	public function getAttributes() {

		return array(
			'instructors'           => '',
			self::SALE_PRICE        => '',
			self::REGULAR_PRICE     => '',
			'best_selling'          => '',
			'credglv_subtitle_text'    => '',
			'credglv_about_area'       => '',
			'credglv_project_area'     => '',
			'created_by_conditinal' => '',
			'creator_by_conditinal' => '',
			'credglv_faq_repeator'     => '',
		);


	}

	public static function get_list_course_referrer( $post_id ) {

		$referrer_item = get_post_meta( $post_id, self::POST_META );

		if ( ! empty( $referrer_item ) && is_array( $referrer_item ) ) {
			return $referrer_item;

		} else {
			return array();
		}

	}

	/**
	 * Get instructors list who assigned to this course
	 *
	 * @param int $postId
	 *
	 * @return array
	 */
	public function getInstructors( $postId = false, $object = '', $limit = - 1 ) {

		$instructors = [];
		if ( $postId == false ) {
			return $instructors;
		}
		$user_query = new \WP_User_Query(
			array(
				'meta_key'   => Instructor::instructorMetaKey(),
				'meta_value' => $postId ? $postId : $this->getId()
			)
		);
		/*Array
		(
            [0] => Array
            (
                [0] => 4
                [1] => 5
            ))*/
		$instructs_geted      = get_post_meta( $postId, 'instructors' );
		$post_meta_instructor = array_shift( $instructs_geted );
		$limit                = intval( $limit );
		$users                = $user_query->get_results();
		foreach ( $users as $i => $user ) {
			/** @var \WP_User $user */
			if ( is_array( $post_meta_instructor ) && in_array( $user->ID, $post_meta_instructor ) ) {
				if ( $object !== '' ) {
					$instructors[ $user->ID ] = $user;
				} else {
					$instructors[ $user->ID ] = $user->display_name;
				}
			} else {
				delete_user_meta( $user->ID, Instructor::instructorMetaKey(), $postId );
			}

			if ( $limit > 0 && $i == $limit - 1 ) {
				break;
			}
		}

		return $instructors;
	}

	public static function getAll() {

		$args  = array(
			'post_type'   => ReferrerModel::POST_TYPE,
			'post_status' => 'publish'
		);
		$query = new \WP_Query( $args );

		return $query->posts;

	}

	public function delete() {
	}
	//Add
	public function getViewNumber( $courseID = false ) {

		$view_count = 0;
		if ( $courseID ) {
			$course_view_count = get_post_meta( $courseID, 'referrer_view_count', true );
			$view_count        = ! empty( $course_view_count ) ? $course_view_count : $view_count;
		}

		return $view_count;
	}
}