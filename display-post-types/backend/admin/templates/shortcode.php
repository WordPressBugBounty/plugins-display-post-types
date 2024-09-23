<?php
/**
 * Display Post Types options shortcode page
 *
 * @package Display Post Types
 * @since 2.6.0
 */

?>
<div class="dpt-shortcode-wrapper">
    <div class="dpt-shortcode-header">
        <h3><?php printf( 'Shortcode Generator', 'display-post-types' ); ?></h3>
        <div class="dpt-shortcode-action">
            <button id="dpt-shortcode-generator-btn" class="button button-primary">Create New Shortcode</button>
            <?php if ( ! empty( $shcode_gen->shortcode_settings ) ) : ?>
                <span class="dpt-separator">or</span>
                <?php echo $shcode_gen->dropdown(); ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="dpt-shortcode-generator">
        <div class="dpt-shortcode-result"></div>
        <div class="dpt-shortcode-workspace">
            <div id="dpt-shortcode-form" class="dpt-shortcode-form"></div>
            <div id="dpt-shortcode-preview" class="dpt-shortcode-preview">
                <div style="padding: 20px; font-size: 20px; color: #aaa;">
					<span>Create a </span>
					<span style="color: #333;">New Shortcode</span>
					<span> or </span>
                    <span style="color: #333;">Edit an Existing</span>
                    <span> Shortcode using the menu above.</span>
				</div>
            </div>
        </div>
    </div>
</div>