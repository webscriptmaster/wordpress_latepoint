<?php
/* @var $jobs OsProcessJobModel[] */
?>
<?php
if($jobs){
  foreach ($jobs as $job): ?>
    <tr>
      <td><strong><?php echo \LatePoint\Misc\ProcessEvent::get_event_name_for_type($job->get_original_process_attribute('event_type')); ?></strong></td>
      <td>
	      <?php
	      $process_name = $job->get_original_process_attribute('name');
				$is_deleted = ($job->process->id != $job->process_id);
	      $process_name.= (!$is_deleted && ($job->process->name != $job->get_original_process_attribute('name'))) ? ' ['.__('Modified', 'latepoint').']' : '';
				$id_html = $is_deleted ? ' ['.__('Deleted', 'latepoint').']' : ' (ID:'.$job->process_id.')'; // deleted
	      echo '<a href="'.OsRouterHelper::build_link(['processes', 'index']).'" target="_blank">'.$process_name.$id_html.'</a>'; ?></td>
      <td><?php echo $job->get_link_to_object(); ?></td>
      <td><?php echo $job->get_actions_summary(); ?></td>
      <td>
	      <?php
	      if($job->status == LATEPOINT_JOB_STATUS_SCHEDULED){
					$atts = ' data-os-prompt="'.__('Are you sure you want to cancel this scheduled job?', 'latepoint').'"
										data-os-params="'. OsUtilHelper::build_os_params(['id' => $job->id]). '"
										data-os-after-call="reload_process_jobs_table"
										data-os-action="'.OsRouterHelper::build_route_name('process_jobs', 'cancel').'" ';
	      }else{
					$atts = '';
	      }
	      echo '<span class="os-column-status os-column-status-'.$job->status.'" '.$atts.'">'.OsProcessJobsHelper::get_nice_job_status_name($job->status).'</span>';
				?>
      </td>
      <td>
        <?php echo $job->to_run_after_utc; ?>
      </td>
	    <td>
	      <?php
	      echo '<span class="in-table-time-left">'.OsTimeHelper::time_left_to_datetime($job->to_run_after_utc, new DateTimeZone('UTC')).'</span>';
        if($job->run_result){
					echo ' <a href="#" 
					data-os-params="' . http_build_query(['id' => $job->id]) . '" 
			    data-os-action="' . OsRouterHelper::build_route_name( 'process_jobs', 'view_job_run_result' ) . '" 
			    data-os-lightbox-classes="width-800"
			    data-os-after-call="latepoint_init_json_view"
			    data-os-output-target="lightbox"><i class="latepoint-icon latepoint-icon-file-text"></i></a>';
        }
				?>
      </td>
      <td>
        <a class="latepoint-link" data-os-after-call="reload_process_jobs_table" href="#" data-os-prompt="<?php _e('Are you sure you want to run this job?', 'latepoint'); ?>" data-os-action="<?php echo OsRouterHelper::build_route_name('process_jobs', 'run_job'); ?>" data-os-params="<?php echo OsUtilHelper::build_os_params(['job_id' => $job->id]) ?>">
          <?php if(in_array($job->status, [LATEPOINT_JOB_STATUS_COMPLETED, LATEPOINT_JOB_STATUS_ERROR])){ ?>
            <i class="latepoint-icon latepoint-icon-refresh-cw"></i>
            <span><?php _e('Run Again','latepoint'); ?></span>
          <?php }elseif($job->status == LATEPOINT_JOB_STATUS_SCHEDULED){ ?>
            <i class="latepoint-icon latepoint-icon-play-circle"></i>
            <span><?php _e('Run Now','latepoint'); ?></span>
          <?php }else{ ?>
            <i class="latepoint-icon latepoint-icon-play-circle"></i>
            <span><?php _e('Run','latepoint'); ?></span>
          <?php } ?>
        </a>
      </td>
    </tr>

    <?php
  endforeach;
}
?>