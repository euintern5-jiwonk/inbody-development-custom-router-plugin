<?php

namespace Essential_Addons_Elementor\Pro\Extensions;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use Essential_Addons_Elementor\Pro\Extensions\DynamicTags\Custom_Post_Types;
use Essential_Addons_Elementor\Pro\Extensions\DynamicTags\Posts;
use Essential_Addons_Elementor\Pro\Extensions\DynamicTags\Tags;
use Essential_Addons_Elementor\Pro\Extensions\DynamicTags\Terms;
use Essential_Addons_Elementor\Pro\Extensions\DynamicTags\Woo_Product_Categories;
use Essential_Addons_Elementor\Pro\Extensions\DynamicTags\Woo_Products;

class Advanced_Dynamic_Tags {
	
	public function __construct() {
		add_action( 'elementor/dynamic_tags/register', [ $this, 'register_dynamic_widgets' ] );
	}

	public function register_dynamic_widgets( $dynamic_tags_manager ){
		$dynamic_tags_manager->register_group(
			'eael-advanced-dynamic-tags',
			[
				'title' => __( 'EA Dynamic Tags', 'essential-addons-elementor' )
			]
		);

		$dynamic_tags_manager->register( new Posts() );
		$dynamic_tags_manager->register( new Woo_Products() );
		$dynamic_tags_manager->register( new Terms() );
		$dynamic_tags_manager->register( new Custom_Post_Types() );
	}
}
