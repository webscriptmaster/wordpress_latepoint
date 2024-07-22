<div class="step-agents-w latepoint-step-content <?php echo ($booking->agent_id) ? 'is-hidden' : ''; ?>" data-step-name="agents" data-clear-action="clear_step_agents">
  <div class="os-agents os-animated-parent os-items os-selectable-items os-as-grid os-three-columns">
    <?php $show_agent_bio = OsSettingsHelper::steps_show_agent_bio(); ?>
    <?php if(OsSettingsHelper::is_on('allow_any_agent')){ ?>
      <div class="os-animated-child os-item os-selectable-item"
          data-summary-field-name="agent" 
          data-summary-value="<?php _e('Any Agent', 'latepoint'); ?>" 
          data-id-holder=".latepoint_agent_id"
          data-item-id="<?php echo LATEPOINT_ANY_AGENT; ?>">
        <div class="os-animated-self os-item-i">
          <div class="os-item-img-w os-with-avatar"><div class="os-avatar" style="background-image: url(<?php echo LATEPOINT_IMAGES_URL . 'default-avatar.jpg'; ?>);"></div></div>
          <div class="os-item-name-w">
            <div class="os-item-name"><?php _e('Any Agent', 'latepoint'); ?></div>
          </div>
        </div>
      </div>
    <?php } ?>
    <?php foreach($agents as $agent){ ?>
      <div class="os-animated-child os-item os-selectable-item <?php echo $show_agent_bio ? 'with-details' : ''; ?>"
          data-summary-field-name="agent" 
          data-summary-value="<?php echo esc_attr($agent->name_for_front); ?>" 
          data-id-holder=".latepoint_agent_id"
          data-item-id="<?php echo $agent->id; ?>">
        <div class="os-animated-self os-item-i">
          <div class="os-item-img-w os-with-avatar"><div class="os-avatar" style="background-image: url(<?php echo $agent->avatar_url; ?>);"></div></div>
          <div class="os-item-name-w">
            <div class="os-item-name"><?php echo $agent->name_for_front; ?></div>
          </div>
          <?php if($show_agent_bio){ ?>
            <div class="os-item-details-popup-btn os-trigger-item-details-popup" data-item-details-popup-id="osItemDetailsPopupAgent_<?php echo $agent->id; ?>"><span><?php _e('Learn More', 'latepoint'); ?></span></div>
          <?php } ?>
        </div>
      </div>
    <?php } ?>
  </div>
  <?php if($show_agent_bio){ ?>
    <?php foreach($agents as $agent){ ?>
      <?php OsAgentHelper::generate_bio($agent); ?>
    <?php } ?>
  <?php } ?>
  <?php 
    echo OsFormHelper::hidden_field('booking[agent_id]', $booking->agent_id, [ 'class' => 'latepoint_agent_id', 'skip_id' => true]);
  ?>
</div>