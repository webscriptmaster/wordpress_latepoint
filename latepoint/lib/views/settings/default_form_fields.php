<div class="os-section-header"><h3><?php _e('Default Fields', 'latepoint'); ?></h3></div>
<?php OsSettingsHelper::generate_default_form_fields($default_fields); ?>
<div class="os-section-header"><h3><?php _e('Custom Fields', 'latepoint'); ?></h3></div>
<?php echo OsAddonsHelper::generate_missing_addon_link(__('To create more fields install Custom Fields addon', 'latepoint')); ?>
