<div class="os-form-w">
  <form action="" data-os-after-call="latepoint_wizard_item_editing_cancelled" data-os-output-target=".os-wizard-step-content-i" data-os-action="<?php echo OsRouterHelper::build_route_name('wizard', 'save_service'); ?>">
    <div class="os-row">
      <div class="os-col-lg-8">
        <?php echo OsFormHelper::text_field('service[name]', __('Service Name', 'latepoint'), $service->name); ?>
      </div>
      <div class="os-col-lg-4">
        <?php echo OsFormHelper::text_field('service[duration]', __('Duration (minutes)', 'latepoint'), $service->duration); ?>
      </div>
    </div>
    <?php 
      echo OsFormHelper::media_uploader_field('service[selection_image_id]', 0, __('Upload Image for Service', 'latepoint'), __('Remove Image', 'latepoint'), $service->selection_image_id);
      if(!$service->is_new_record()) echo OsFormHelper::hidden_field('service[id]', $service->id);
    ?>
    <?php if($agents){ ?>
      <h3 class="sub-header"><span><?php _e('Select Agents','latepoint'); ?></span></h3>
      <div class="os-agents-selector">
        <?php
        foreach($agents as $agent){
          $is_active_service = $service->is_new_record() ? true : $location->has_agent_and_service($agent->id, $service->id);
          $is_active_service_value = $is_active_service ? 'yes' : 'no';
          $active_class = $is_active_service ? 'active' : '';
          echo '<div class="agent '.$active_class.'">';
            echo '<div class="agent-avatar" style="background-image: url(' . $agent->get_avatar_url() . ')"></div>';
            echo '<div class="agent-name">' . $agent->full_name . '</div>';
            echo OsFormHelper::hidden_field('service[agents][agent_'.$agent->id.'][location_'.$location->id.'][connected]', $is_active_service_value, array('class' => 'connection-child-is-connected'));
          echo '</div>';
        } ?>
      </div>
    <?php } ?>
    <div class="side-by-side-buttons">
        <?php if(!isset($hide_cancel_btn)){ ?>
          <button type="button" data-os-after-call="latepoint_wizard_item_editing_cancelled" data-os-output-target=".os-wizard-step-content" data-os-params="current_step=services" data-os-action="<?php echo OsRouterHelper::build_route_name('wizard', 'load_step'); ?>" class="wizard-finished-editing-trigger latepoint-btn latepoint-btn-lg latepoint-btn-secondary">
	          <i class="latepoint-icon latepoint-icon-arrow-left"></i>
	          <span><?php _e('Cancel', 'latepoint'); ?></span>
          </button>
        <?php } ?>
          <button type="submit" class="latepoint-btn latepoint-btn-primary latepoint-btn-lg ">
            <span><?php echo ($service->is_new_record()) ? __('Save', 'latepoint') : __('Save', 'latepoint'); ?></span>
	          <i class="latepoint-icon latepoint-icon-check"></i>
          </button>
    </div>
  </form>
</div>