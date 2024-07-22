<div class="os-categories-ordering-w" data-category-order-update-route="<?php echo OsRouterHelper::build_route_name('service_categories', 'update_order_of_categories'); ?>">
	<div class="os-category-children">
	<?php OsServiceHelper::generate_service_categories_list(); ?>
	<?php if(is_array($uncategorized_services)){
		foreach($uncategorized_services as $service){
			echo '<div class="item-in-category-w status-'.$service->status.'" data-id="'.$service->id.'">
				<div class="os-category-item-drag"></div>
				<div class="os-category-item-name">'.$service->name.'</div>
				<div class="os-category-item-meta">'.__('ID: ', 'latepoint').'<span>'.$service->id.'</span></div>
			</div>';
		}
	} ?>
	</div>
	<div class="add-item-category-box add-item-category-trigger">
		<div class="add-item-category-graphic-w">
			<div class="add-item-category-plus"><i class="latepoint-icon latepoint-icon-plus4"></i></div>
		</div>
		<div class="add-item-category-label"><?php _e('Create New Category', 'latepoint'); ?></div>
	</div>
	<div class="os-form-w os-category-w editing os-new-item-category-form-w" style="display:none;">
		<div class="os-category-head">
			<div class="os-category-name"><?php _e('Create New Service Category', 'latepoint'); ?></div>
		</div>
		<div class="os-category-body">
			<?php 
			$service_category = new OsServiceCategoryModel();
			include('_form.php'); ?>
		</div>
	</div>
</div>