<?php
/* @var $templates array */
/* @var $heading string */
/* @var $action_type string */
/* @var $selected_template_id string */
?>
<div class="latepoint-lightbox-heading">
	<h2><?php echo $heading; ?></h2>
</div>
<div class="latepoint-lightbox-content no-padding">
	<div class="os-templates-wrapper">
		<div class="os-templates-list">
			<div class="template-type-selector-wrapper">
				<div class="template-type-selector is-selected" data-user-type="agent"><?php _e('To Agents', 'latepoint'); ?></div>
				<div class="template-type-selector" data-user-type="customer"><?php _e('To Customers', 'latepoint'); ?></div>
			</div>
			<div class="os-template-items">
			<?php
			foreach($templates as $template){
				echo '<div data-user-type="'.$template['to_user_type'].'" class="os-template-item '.($template['to_user_type']=='agent' ? '' : 'hidden').' '.($template['id'] == $selected_template_id ? 'selected' : '').'" data-id="'.$template['id'].'">';
					echo '<div class="os-template-name">'.$template['name'].'</div>';
				echo '</div>';
			} ?>
			</div>
		</div>
		<div class="os-template-previews">
			<?php
			foreach($templates as $template) {
				echo '<div class="os-template-preview type-'.$action_type.'" data-id="'.$template['id'].'" style="'.($template['id'] == $selected_template_id ? '' : 'display: none;').'">';
				switch($action_type){
					case 'send_email':
							echo '<div class="os-template-preview-subject"><span class="os-value">'.$template['subject'].'</span></div>';
							echo '<div class="os-template-preview-to"><span class="os-label">'.__('To:', 'latepoint').'</span><span class="os-value">'.OsReplacerHelper::stylize_vars($template['to_email']).'</span></div>';
							echo '<div class="os-template-preview-content">'.OsReplacerHelper::stylize_vars($template['content']).'</div>';
						break;
					case 'send_sms':
						echo '<div class="os-template-preview-content-wrapper">';
							echo '<div class="os-template-preview-to"><span class="os-label">'.__('To:', 'latepoint').'</span><span class="os-value">'.$template['to_phone'].'</span></div>';
							echo '<div class="os-template-preview-content">'.$template['content'].'</div>';
						echo '</div>';
						break;
				}

				/**
				 * Executed after each notification template preview
				 *
				 * @since 4.7.0
				 * @hook latepoint_after_notification_template_preview
				 *
				 * @param {string} $action_type Type of action being previewed
				 * @param {array} $template Array of template information being previewed
				 * @param {string} $selected_template_id ID of selected template for which preview is to be shown
				 */
                do_action('latepoint_after_notification_template_preview', $action_type, $template, $selected_template_id);
				echo '</div>';
			}
			?>
		</div>
	</div>
</div>
<div class="latepoint-lightbox-footer right-aligned">
	<button type="button" class="latepoint-btn latepoint-btn-primary latepoint-select-template-btn" data-action-type="<?php echo $action_type; ?>" data-action-id="<?php echo $action_id; ?>" data-route="<?php echo OsRouterHelper::build_route_name('processes', 'load_action_settings');?>"><?php _e('Load this template', 'latepoint'); ?></button>
</div>