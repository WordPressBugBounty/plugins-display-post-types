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
        <div class="dpt-shortcode-action">
            <button id="dpt-shortcode-generator-btn" class="button button-primary !rounded">Create New Shortcode</button>
            <?php if ( ! empty( $shcode_gen->shortcode_settings ) ) : ?>
                <span class="dpt-separator">or</span>
                <?php echo $shcode_gen->dropdown(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <label class="screen-reader-text" for="dpt-shortcode-list-search"><?php esc_html_e( 'Search shortcodes', 'display-post-types' ); ?></label>
                <input type="search" id="dpt-shortcode-list-search" class="dpt-shortcode-list-search dpt-hidden" placeholder="<?php esc_attr_e( 'Search shortcodes', 'display-post-types' ); ?>" />
				<button type="button" class="button button-secondary dpt-icon-button dpt-shortcode-search-toggle" aria-expanded="false" aria-controls="dpt-shortcode-list-search" aria-label="<?php esc_attr_e( 'Search shortcodes', 'display-post-types' ); ?>" title="<?php esc_attr_e( 'Search shortcodes', 'display-post-types' ); ?>">
                    <span class="dashicons dashicons-search" aria-hidden="true"></span>
                </button>
            <?php endif; ?>
        </div>
        <div class="dpt-shortcode-list-container">
            <?php echo $shcode_gen->list_table(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
<div id="dpt-shortcode-action-modal" class="dpt-shortcode-action-modal dpt-hidden">
    <div class="dpt-shortcode-action-wrapper">
        <h3><?php esc_html_e( 'Confirm Deletion', 'display-post-types' ); ?></h3>
        <p><?php esc_html_e( 'Are you sure you want to delete this shortcode?', 'display-post-types' ); ?></p>
        <button id="dpt-shortcode-deletion-btn" class="button button-primary" data-instance=""><?php esc_html_e( 'Delete Shortcode', 'display-post-types' ); ?></button>
        <button id="dpt-shortcode-deletion-cancel" class="button button-secondary"><?php esc_html_e( 'Cancel', 'display-post-types' ); ?></button>
    </div>
</div>
