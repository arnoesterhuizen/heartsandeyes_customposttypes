<?php
if(!class_exists('HeartsAndEyes_CustomShortcodes'))
{
	class HeartsAndEyes_CustomShortcodes
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// register actions
			add_action('init', array(&$this, 'init'));
		} // END public function __construct

		/**
		 * hook into WP's admin_init action hook
		 */
		public function init()
		{
			add_filter( 'img_caption_shortcode', array(&$this, 'img_caption_shortcode', 10, 3 ) );

			add_shortcode( 'production', array(&$this, 'define_shortcode_productions' ) );
			add_shortcode( 'person',     array(&$this, 'define_shortcode_people' ) );
			add_shortcode( 'page',       array(&$this, 'define_shortcode_pages' ) );
			add_shortcode( 'extract',    array(&$this, 'define_shortcode_extract' ) );

			if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
				return;
			}

			if ( get_user_option('rich_editing') == 'true' ) {
				//add_filter( 'mce_external_plugins', 'hearts_and_eyes_add_plugin' );
				//add_filter( 'mce_buttons', 'hearts_and_eyes_register_buttons' );
			}
		} // END public static function init

		function define_shortcode_productions( $atts , $content = null ) {
			return $this->define_shortcode ($atts, $content, 'production');
		}
		function define_shortcode_people( $atts , $content = null ) {
			return $this->define_shortcode ($atts, $content, 'person');
		}
		function define_shortcode_pages( $atts , $content = null ) {
			return $this->define_shortcode ($atts, $content, 'page');
		}

		function define_shortcode_extract( $atts ) {
			$return = '';

			// Attributes
			extract( shortcode_atts(
				array(
					'id'   => null,
					'name' => null,
					'slug' => null,
					'type' => 'post'
				), $atts )
			);

			$page = null;

			// try a passed id parameter first
			if (null != $id && is_numeric($id)) {
				$page = get_post($id, ARRAY_A);
			}

			// try a passed name parameter second
			if (null == $page && null != $name && '' != $name) {
				$page = get_page_by_title($name, ARRAY_A, $type);
			}

			// try a passed slug parameter third
			if (null == $page && null != $slug && '' != $slug) {
				$page = get_page_by_path($slug, ARRAY_A, $type);
			}

			if (null != $page) {
				$anchor = '<a href="' . get_permalink($page['ID']) . '" title="' . esc_attr( $page['post_title'] ) . '">';

				$extract_thumbnail = get_the_post_thumbnail($page['ID'], array(64, 64), array('class' => 'attachment-64x64'));
				$extract_title = esc_attr( $page['post_title'] );

				//TODO: port this `twentyfourteen_get_the_subtitle` function over
				//$extract_subtitle = wptexturize( implode(', ', twentyfourteen_get_the_subtitle($page['ID']) ) );
				$extract_subtitle = '';

				$return .= '<aside class="wp-caption alignright aside-extract aside-' . $type . '">';

				if ('' != $extract_thumbnail) {
					$return .= $anchor . $extract_thumbnail . '</a>';
				}
				if ('' != $extract_title) {
					$return .= '<h1>' . $anchor . $extract_title . '</a></h1>';
				}
				if ('' != $extract_subtitle) {
					$return .= '<p>' . $anchor . $extract_subtitle . '. Read more&hellip;</a></p>';
				} else {
					$exclude_codes = 'caption';

					$extract_summary = wptexturize( strip_shortcodes( preg_replace( "~(?:\[/?)(?!(?:$exclude_codes))[^/\]]+/?\]~s", '', $page['post_content'] ) ) );

					$extract_summary = substr($extract_summary, 0, strpos($extract_summary, ' ', 50));
					$return .= '<p>' . $anchor . $extract_summary . '&hellip; Read more&hellip;</a></p>';
				}

				$return .= '</aside>';
			}

			return $return;
		}

		function define_shortcode ( $atts , $content = null , $shortcode ) {
			// Attributes
			extract( shortcode_atts(
				array(
					'id'   => null,
					'name' => null,
					'slug' => null
				), $atts )
			);

			$page = null;

			// try a passed id parameter first
			if (null != $id && is_numeric($id)) {
				$page = get_post($id, ARRAY_A);
			}

			// try a passed name parameter second
			if (null == $page && null != $name && '' != $name) {
				$page = get_page_by_title($name, ARRAY_A, $shortcode);
			}

			// try a passed slug parameter third
			if (null == $page && null != $slug && '' != $slug) {
				$page = get_page_by_path($slug, ARRAY_A, $shortcode);
			}

			// try the enclosed text as post name last
			if (null == $page) {
				$page = get_page_by_title($content, ARRAY_A, $shortcode);
			}

			if (null != $page) {
				$return  = '<a href="' . get_permalink($page['ID']) . '" title="' . esc_attr( $page['post_title'] ) . '" class="' . $shortcode . '">' . do_shortcode( $content ) . '</a>';
				return $return;
			}

			return '<span class="' . $shortcode . '">' . do_shortcode( $content ) . '</span>';
		}

		function img_caption_shortcode( $empty, $attr, $content = null ) {
			// New-style shortcode with the caption inside the shortcode with the link and image tags.
			if ( ! isset( $attr['caption'] ) ) {
				if ( preg_match( '#((?:<a [^>]+>\s*)?<img [^>]+>(?:\s*</a>)?)(.*)#is', $content, $matches ) ) {
					$content = $matches[1];
					$attr['caption'] = trim( $matches[2] );
				}
			}

			$atts = shortcode_atts( array(
				'id'      => '',
				'align'   => 'alignnone',
				'width'   => '',
				'caption' => ''
			), $attr, 'caption' );

			$atts['width'] = (int) $atts['width'];
			if ( $atts['width'] < 1 || empty( $atts['caption'] ) )
				return $content;

			if ( ! empty( $atts['id'] ) )
				$atts['id'] = 'id="' . esc_attr( $atts['id'] ) . '" ';

			$caption_width = $atts['width'];
			$caption_width = apply_filters( 'img_caption_shortcode_width', $caption_width, $atts, $content );

			$style = '';
			if ( $caption_width )
				$style = 'style="max-width: ' . (int) $caption_width . 'px" ';

			return '<div ' . $atts['id'] . $style . 'class="wp-caption ' . esc_attr( $atts['align'] ) . '">'
			. do_shortcode( $content ) . '<p class="wp-caption-text">' . do_shortcode( $atts['caption'] ) . '</p></div>';
		}
	} // END class HeartsAndEyes_CustomShortcodes
} // END if(!class_exists('HeartsAndEyes_CustomShortcodes'))
