<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\core;


use credglv\core\components\Script;
use credglv\core\components\Style;
use credglv\core\interfaces\CacheableInterface;
use credglv\core\interfaces\CacheableShortcodeInterface;
use credglv\core\interfaces\ResourceInterface;
use credglv\core\interfaces\ShortcodeInterface;

abstract class Shortcode extends BaseObject implements ShortcodeInterface {
	/**
	 * @var string
	 */
	public $shortcodeBaseUrl = '';
	/**
	 * Default layout file
	 * @var string
	 */
	protected $layoutView = 'layout';

	/**
	 * @var string
	 */
	protected $contentView = 'content';
	/**
	 * @var string Id of shortcode
	 */
	public $id;

	/**
	 * @var string Name of shortcode
	 */
	public $name;

	/**
	 * @var string class name
	 */
	public $class;

	/**
	 * @var array
	 */
	public $scripts = [];

	/**
	 * @var array
	 */
	public $styles = [];


	/**
	 * @var array
	 */
	public $dependencies = [];

	/**
	 * @var array
	 */
	public $attributes = [];


	/**
	 * Priority number
	 * which defined the order of ajax load when lazyLoad config is enable
	 * @var int
	 */
	public $priority = 0;

	/**
	 * Default config
	 * @var array
	 */
	private $config = [];

	/**
	 * Extra params
	 * which supports for some page builders
	 * @var array
	 */
	public $extras = [];

	/**
	 * Data that user passed from shortcode
	 * @var array
	 */
	private $data = [];

	/**
	 * Shortcode constructor.
	 *
	 * @param array $configs
	 */
	public final function __construct( $configs = [] ) {
		parent::__construct( $configs );
		$this->config = $configs;
		$this->init();
	}


	/**
	 * Find shortcode content in cache
	 * if it exists just return result
	 *
	 * @param array $data
	 * @param array $param
	 *
	 * @return mixed|null|string
	 */
	public function findInCache( $data = [], $param = [], $key = '' ) {
		if ( $this instanceof CacheableShortcodeInterface ) {
			if ( empty( $key ) ) {
				$key = $this->getId() . md5( json_encode( $data ) );
			}
			$this->data = $data;

			if ( ! empty( $content ) ) {
				credglv()->hook->registerHook( 'before_shortcode_render', $this );
				credglv()->hook->registerHook( 'after_shortcode_render', $this, $content );
				foreach ( $this->getResources() as $resource ) {
					/** ResourceInterface $resource */
					if ( $resource instanceof Script ) {
						wp_enqueue_script( $resource->getId() );
					} else {
						wp_enqueue_style( $resource->getId() );
					}
				}
				credglv()->hook->listenFilter( 'credglv_shortcode_selfdo', [ $this, 'runJsShortcode' ] );

				return $content;
			}
		}

		//Render shortcode content
		return $this->getShortcodeContent( $data, $param );
	}


	/**
	 * Get default config
	 * @return array
	 */
	public function getDefaultConfig() {
		return $this->config;
	}

	/**
	 * Init Shortcode
	 */
	public final function init() {
		parent::init(); // TODO: Change the autogenerated stub
		/**
		 * Sometime we need to flush the cache to refresh data
		 */
		credglv()->wp->add_action( 'shortcode_' . $this->getId() . '_flushcache', [ $this, 'flushCache' ] );
		if ( method_exists( $this, '_init' ) ) {
			$this->_init();
		}
	}

	/**
	 * @param string $file
	 *
	 * @return string
	 */
	public function getShortcodeUrl( $file = '' ) {
		if ( preg_match( "/^http/i", $file ) ) {
			return $file;
		}
		$shortcodePath    = $this->getPath();
		$relativePath     = substr( $shortcodePath, strpos( $shortcodePath, credglv()->helpers->general->fixPath( ABSPATH ) ) );
		$shortcodeBaseUrl = credglv()->wp->site_url() . '/' . str_replace( "\\", '/', str_replace( credglv()->helpers->general->fixPath( ABSPATH ), '', $shortcodePath ) ) . '/';

		if ( ! empty( $file ) ) {
			return $shortcodeBaseUrl . '/' . $file;
		}

		return $shortcodeBaseUrl;
	}

	/**
	 * Get user defined data
	 *
	 * @param $attrs
	 *
	 * @return array
	 */
	protected function getData( $attrs ) {
		$this->data       = $attrs;
		$this->attributes = $this->getAttributes();

		$this->attributes = credglv()->hook->registerFilter( "credglv_attributes_{$this->getId()}", $this->attributes );
		foreach ( $this->attributes as $key => &$defaultValue ) {
			$configName         = "credglv_{$key}";
			$defaultConfigValue = credglv()->config->$configName;
			if ( ! empty( $defaultConfigValue ) ) {
				$defaultValue = $defaultConfigValue;
			}
		}
		$data = credglv()->wp->shortcode_atts( $this->attributes, $attrs, $this->getId() );
		$data = credglv()->hook->registerFilter( "credglv_shortcode_{$this->getId()}_data", $data );


		//}
		return [
			'data' => $data
		];
	}

	/**
	 * Render shortcode view
	 * based on phpRenderFile of General helper
	 *
	 * @param $view
	 * @param array $data
	 * @param boolean $registerAsset
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	public function render( $view, $data = [], $registerAsset = false, $cacheKey = '' ) {
		if ( ! preg_match( '/\.php$/i', $view ) ) {
			$view .= '.php';
		}
		if ( ! file_exists( $view ) ) {
			$view = $this->getPath() . "/views/{$view}";
			$view = apply_filters( 'credglv_pre_get_path_shortcode', $view, $this, $data );
		}

		if ( ! file_exists( realpath( $view ) ) ) {
			throw new RuntimeException( __( "The view {$view} can not be found", 'credglv' ) );
		}
		$data['context'] = $this;
		credglv()->hook->registerHook( 'before_shortcode_render', $this );
		$result = credglv()->helpers->general->renderPhpFile( $view, $data );
		credglv()->hook->registerHook( 'after_shortcode_render', $this, $result );

		//Not used, just keep it to restructure later
		foreach ( $this->getResources() as $resource ) {
			/** ResourceInterface $resource */
			//credglv()->resourceManager->registerResource($resource);
			if ( $resource instanceof Script ) {
				wp_enqueue_script( $resource->getId() );
			} else {
				wp_enqueue_style( $resource->getId() );
			}
		}
		if ( ! CREDGLV_DEBUG ) {
			credglv()->hook->listenFilter( 'credglv_shortcode_selfdo', [ $this, 'runJsShortcode' ] );
		}
		if ( ! WP_DEBUG ) {
			$result = credglv()->helpers->general->minifyHtml( $result );
		}
		//credglv()->resourceManager->release();
		if ( empty( $cacheKey ) ) {
			$cacheKey = $this->getId() . md5( json_encode( $this->data ) );
		}


		return $result;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function runJsShortcode( $data = [] ) {
		if ( ! isset( $data['credglv_do_shortcode'] ) ) {
			$data['credglv_do_shortcode'] = [];
		}
		$parents = class_parents( $this, true );
		if ( ! empty( $parents ) ) {
			foreach ( $parents as $parent ) {
				if ( defined( "$parent::SHORTCODE_ID" ) ) {
					$parentId = $parent::SHORTCODE_ID;
					if ( ! in_array( $parentId, $data['credglv_do_shortcode'] ) ) {
						$data['credglv_do_shortcode'][] = $parentId;
						/** @var ShortcodeInterface $parentShortcode */
						$parentShortcode = $parent::getInstance();
						$statics         = $parentShortcode->getStatic();
						foreach ( $statics as $static ) {
							switch ( $static['type'] ) {
								case 'style' :
									wp_enqueue_style( $static['id'] );
									break;
								case 'script' :
									wp_enqueue_script( $static['id'] );
									break;
							}
						}
					}

				}
			}
		}
		if ( $this instanceof CacheableShortcodeInterface ) {
			$shortcodes = $this->getChildren();
			if ( ! empty( $shortcodes ) ) {
				foreach ( $shortcodes as $shortcode ) {
					if ( ! in_array( $shortcode, $data['credglv_do_shortcode'] ) ) {
						$data['credglv_do_shortcode'][] = $shortcode;
						$shortcode                   = credglv()->shortcodeManager->getShortcodeById( $shortcode );
						$statics                     = $shortcode->getStatic();
						if ( ! empty( $statics ) ) {
							foreach ( $statics as $static ) {
								switch ( $static['type'] ) {
									case 'style' :
										wp_enqueue_style( $static['id'] );
										break;
									case 'script' :
										wp_enqueue_script( $static['id'] );
										break;
								}
							}
						}
					}
				}
			}
		}
		if ( ! in_array( $this->getId(), $data['credglv_do_shortcode'] ) ) {
			$data['credglv_do_shortcode'][] = $this->getId();
		}

		return $data;
	}

	/**
	 * Response json data to client
	 * with following structure
	 * {
	 *      "code" : HTTP_CODE,
	 *      "message" : ACTION_MESSAGE,
	 *      "data" : RESPONSE_DATA
	 *
	 * }
	 * @param $data
	 */
	public function responseJson( $data ) {
		$_data = [
			'code'    => 200,
			'message' => '',
			'data'    => []
		];
		$data  = array_merge( $_data, $data );
		header( 'Content-Type: application/json' );
		print json_encode( $data, JSON_PRETTY_PRINT );
		exit;
	}


	/**
	 * Get resources of shortcode
	 *
	 * @return ResourceInterface[];
	 */
	public function getResources() {
		$resources = [];
		$statics   = $this->getStatic();
		if ( ! empty( $statics ) ) {
			foreach ( $statics as $static ) {
				if ( isset( $static['url'] ) ) {
					$static['url'] = $this->getShortcodeUrl( $static['url'] );
				}
				if ( $static['type'] == 'script' ) {
					$resources[] = new Script( $static );
				} else {
					$resources[] = new Style( $static );
				}
			}
		}

		return $resources;
	}

	/**
	 * Get shortcode dependencies
	 * The dependency shortcode need to be registered before this shortcode
	 *
	 * @return ShortcodeInterface[]
	 */
	public function getDependencies() {
		return $this->dependencies;
	}


	/**
	 * @param array $data
	 *
	 * @return string
	 */
	public function generateAttrbuteHtml( $data = [] ) {
		$id   = 'sc-' . $this->getId() . '-' . credglv()->helpers->general->getRandomString( 6 );
		$html = " id='{$id}' ";
		if ( ! empty( $data ) ) {
			foreach ( $data as $key => $value ) {
				$value = str_replace( "'", '"', $value );
				$html  .= ( " data-{$key}='{$value}' " );
			}
		}

		return $html;
	}

	/**
	 * @return string
	 */
	public function getCahename() {
		// TODO: Implement getCahename() method.
		return "shortcode-{$this->id}-cache";
	}

	/**
	 * Flush owner cache to refresh data
	 * @return mixed
	 */
	public function flushCache() {

	}

	/**
	 * Get shortcode layout
	 * @return string
	 */
	public function getLayout( $attrs = [] ) {
		$this->layoutView = CREDGLV_PATH . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'layout.php';

		return $this->render( $this->layoutView, $this->getData( $attrs ) );
	}

	/**
	 * Get full content of this shortcode
	 * @return string
	 */
	public function getShortcodeContent( $data = [], $params = [], $key = '' ) {
		if ( ! is_array( $params ) ) {
			$params = [];
		}
		$data = $this->getData( $data );

		return $this->render( $this->contentView, array_merge( $data, $params ), true, $key );
	}

	/**
	 * Get child shortcode content
	 *
	 * @param $shortcode
	 * @param array $params
	 *
	 * @return string
	 */
	public function getChildShortcode( $shortcode, $params = [] ) {
		if ( ! preg_match( '/^\[(.*?)\]$/', $shortcode ) ) {
			$options = '';
			foreach ( $params as $key => $value ) {
				$options .= ( " {$key}=\"{$value}\" " );
			}
			$shortcode = "[{$shortcode} {$options}]";
		}

		return credglv_do_shortcode( $shortcode );
	}

	/**
	 * Array of default value of all shortcode options
	 * @return array
	 */
	public function getAttributes() {
		return [];
	}

	/**
	 * Register static resource
	 */
	public function getStatic() {
		return [];
	}


	/**
	 * @return string
	 */
	public function getPath() {
		$reflector = new \ReflectionClass( get_called_class() );
		$fn        = $reflector->getFileName();
		$path      = dirname( $fn );
		if ( strpos( $path, ABSPATH ) === false ) {
			//This is symlink
			$path = ABSPATH . 'wp-content/plugins/' . CREDGLV_NAME . '/' . substr( $path, strpos( $path, 'shortcodes' ) );
			$path = credglv()->helpers->general->fixPath( $path );
		}

		return credglv()->helpers->general->fixPath( $path );
	}

	/**
	 * @param string $class
	 *
	 * @return string
	 */
	public function defineShortcodeBlock( $is_echo = true ) {
		$class = ' credglv-shortcode-block credglv-shortcode-' . $this->getId();

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$class .= ' loaded ';
		}

		$class = apply_filters( 'credglv-shortcode-block-class', $class, $this->getId(), $this->data );

		if ( $is_echo ) {
			echo $class;
		} else {
			return $class;
		}
	}
}
