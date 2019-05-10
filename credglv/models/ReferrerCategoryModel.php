<?php
/**
 * @copyright © 2017 by Solazu Co.,LTD
 * @project Learn Master Plugin
 *
 * @since 1.0
 *
 */

namespace credglv\models;

use credglv\admin\controllers\ReferrerController;
use credglv\core\Model;

class ReferrerCategoryModel extends Model {


	const BUNDLECAT_SLUG = 'cat_referrer';

	/**
	 * Get Custom Meta Data
	 *
	 * @return array
	 */
	public function getAttributes() {
		return array(
			'is_feature'   => [
				'label' => esc_html__( 'Is Feature?', 'credglv' ),
				'form'  => [
					'label'       => esc_html__( 'Feature?', 'credglv' ),
					'description' => esc_html__( 'Choose if this category is feature category.', 'credglv' ),
					'type'        => 'checkbox',
					'class'       => 'field-category',
					'name'        => 'is_feature',
					'value'       => true,
					'checked'     => false,
				],
			],
			'icon_class'   => [
				'label' => esc_html__( 'Icon', 'credglv' ),
				'form'  => [
					'label'       => esc_html__( 'Icon Class', 'credglv' ),
					'description' => esc_html__( 'Enter icon class for this category. Ex: \'fa fa-user\'', 'credglv' ),
					'type'        => 'text',
					'class'       => 'field-category',
					'name'        => 'icon_class',
				],
			],
			'attach_image' => [
				'label' => esc_html__( 'Attach Image', 'credglv' ),
				'form'  => [
					'label' => esc_html__( 'Attach Image', 'credglv' ),
					'type'  => 'custom',
//					'renderer' => [ ReferrerController::getInstance(), 'renderImageField' ],
					'class' => 'field-category',
					'name'  => 'attach_image',
				]
			]
		);
	}

	/**
	 * Abstract function get name of table/model
	 * @return mixed
	 */
	public function getName() {
		return self::BUNDLECAT_SLUG;
	}

	/**
	 * Save object properties to database
	 * @return boolean
	 */
	public function save() {
	}

	/**
	 * Delete a object by primary key
	 *
	 *
	 * @return boolean
	 */
	public function delete() {
		// TODO: Implement delete() method.
	}


	/**
	 * @return mixed
	 */
	public static function getPosttypeConfig() {
		//taxonomy Cat
		$labels = array(
			'name'              => _x( 'Categories', 'taxonomy general name', 'credglv' ),
			'singular_name'     => _x( 'Category', 'taxonomy singular name', 'credglv' ),
			'search_items'      => __( 'Search Referrer Category', 'credglv' ),
			'all_items'         => __( 'All Categories', 'credglv' ),
			'parent_item'       => __( 'Parent Category', 'credglv' ),
			'parent_item_colon' => __( 'Parent Category :', 'credglv' ),
			'edit_item'         => __( 'Edit Category', 'credglv' ),
			'update_item'       => __( 'Update Category', 'credglv' ),
			'add_new_item'      => __( 'Add New Category', 'credglv' ),
			'new_item_name'     => __( 'New Category Name', 'credglv' ),
			'menu_name'         => __( 'Categories', 'credglv' ),
		);

		return [
			'taxonomy' => [
				'name'        => 'cat_referrer',
				'object_type' => [ 'credglv_referrer' ],
				'args'        => [
					'labels'            => $labels,
					'hierarchical'      => true,
					'show_ui'           => true,
					'show_admin_column' => true,
					'show_in_menu'      => true,
					'show_in_nav_menus' => true,
					'query_var'         => true,
				]
			],

		];
	}

	/**
	 * Delete a object by primary key
	 *
	 *
	 * @return boolean
	 */

	/**
	 * Get Data
	 *
	 * @param array $attrs
	 *
	 * @return object
	 */
	public function getData( $attrs = [] ) {
		$data = [];
		if ( ! empty( $this->post ) ) {
			if ( empty( $attrs ) ) {
//				$attrs = $this->attributes;
				$attrs = $this->getAttributes();
			}
			foreach ( $attrs as $attr => $params ) {
				$vAttr = get_term_meta( $this->post->term_id, $attr, true );
				if ( ! is_string( $vAttr ) ) {
					$vAttr = json_encode( $vAttr );
				}
				$this->{$attr} = $vAttr;
			}
		}

		return (object) $data;
	}

	/**
	 * @param $id
	 *
	 * @return bool|Model
	 */
	public static function findOne( $id ) {
		$post = get_term( $id, 'cat_referrer' );
		if ( ! empty( $post ) ) {
			$modelClass = self::className();
			/** @var Model $model */
			$model       = new $modelClass();
			$model->post = $post;
			$model->getData();

			return $model;
		}

		return false;
	}

	/**
	 * Get Category by Referrer ID
	 *
	 * @param $post_id
	 *
	 * @return array|false|\WP_Error
	 */
	public function getCategoryReferrer( $post_id ) {
		$cat_name   = self::getName();
		$categories = get_the_terms( $post_id, $cat_name );

		return $categories;
	}

	/**
	 * Get Referrer Category By ID
	 *
	 * @param $id
	 *
	 * @return bool|ModelInterface|Model|null
	 */
	public function get( $id ) {
		$category = null;
		$term     = get_term( $id );
		if ( isset( $term ) && ! is_wp_error( $term ) ) {
			$category = self::findOne( $term );
		}

		return $category;
	}

	/**
	 * Get all Referrer Category
	 *
	 * @param $args
	 *
	 * @return array
	 */
	public function getAll( $args = array() ) {
		$args = array_merge(
			array(
				'taxonomy'   => $this->getName(),
				'hide_empty' => true,
			),
			$args
		);

		$categories = array();
		$terms      = get_terms( $args );
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$categories[] = self::findOne( $term->term_id );
			}
		}

		return $categories;
	}

	/**
	 * Get all Children Category
	 *
	 * @param null $parent_id
	 *
	 * @return array
	 */
	public function getChildren( $parent_id = null ) {
		if ( ! isset( $parent_id ) || ! is_int( $parent_id ) ) {
			$parent_id = $this->term_id;
		}

		return $this->getAll( array( 'parent' => $parent_id ) );
	}

	/**
	 * Check if category has child
	 *
	 * @param null $parent_id
	 *
	 * @return bool
	 */
	public function hasChildren( $parent_id = null ) {
		if ( ! isset( $parent_id ) || ! is_int( $parent_id ) ) {
			$parent_id = $this->term_id;
		}
		$childs = $this->getChildren( $parent_id );

		return ! empty( $childs );
	}

	/**
	 * Get referrer category permalink
	 *
	 * @return string|\WP_Error
	 */
	public function permalink() {
		$link = get_term_link( $this->term_id );
		if ( is_wp_error( $link ) ) {
			$link = null;
		}

		return $link;
	}

	/**
	 * Get Referrer Category Icon
	 *
	 * @param null $default
	 * @param bool $html
	 *
	 * @return mixed|null|string
	 */
	public function icon( $default = null, $html = false ) {
		$icon_class = $this->icon_class;
		$icon_class = apply_filters( 'credglv_referrer_category_get_icon_class', $this->term_id, $icon_class );
		if ( empty( $icon_class ) && ! empty( $default ) ) {
			$icon_class = $default;
		}
		$icon = $icon_class;
		if ( ! empty( $icon_class ) && $html ) {
			$icon = sprintf( '<i class="%s"></i>', $icon_class );
		}

		return $icon;
	}

	/**
	 * Check Referrer Category is featured
	 *
	 * @return bool
	 */
	public function is_feature() {
		$is_feature = apply_filters( 'credglv_referrer_category_is_feature', $this->term_id, $this->is_feature );

		return ! empty( $is_feature );
	}

	/**
	 * Get list of referrer related to this category
	 *
	 * @return bool
	 */
	public function getReferrers() {
		$referrers = false;
		if ( isset( $this->term_id ) && is_int( $this->term_id ) ) {
			$referrer_model = ReferrerModel::getInstance();
			$referrers      = $referrer_model->getAll( array(
				'tax_query' => array(
					array(
						'taxonomy'         => $this->getName(),
						'field'            => 'id',
						'terms'            => $this->term_id,
						'include_children' => true, // get child post
					)
				),
			) );
		}

		return $referrers;
	}

	/**
	 * After Save
	 *
	 * @param $postId
	 *
	 * @return mixed
	 */
	public function afterSave( $term_id, $post = null, $update = false ) {
		parent::afterSave( $term_id );
		if ( $term_id ) {
			credglv()->session->currentReferrerCategory = $term_id;
			credglv()->wp->do_action( 'after_referrer_category_save', $term_id );
		}
	}

	public function attach_image() {
		$attachment_id = apply_filters( 'credglv_referrer_category_attach_image', $this->attach_image );

		return $attachment_id;
	}


}