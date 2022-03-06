<?php

// Prevent direct access
if (!isset($include)) {
	header("Location: ../../");
}
?>
<div id="contextMenu" style="display:none;">
	<ul class="dropdown-menu dropdown-menu-dark mx-0 border-0 shadow">
		<li><a class="dropdown-item copy-link" data-link="<?php echo $currentLink; ?>"><i class="fas fa-link ps-2" style="width:19px;"></i><span>Copy link</span></a></li>
		<li><a class="dropdown-item view-raw" data-link="<?php echo $currentLink; ?>/raw"><i class="fas fa-external-link-square ps-2" style="width:19px;"></i><span>View raw</span></a></li>
		<li><hr class="dropdown-divider"></li>
		<li><a class="dropdown-item download" data-link="<?php echo $currentLink; ?>/download"><i class="fas fa-cloud-download ps-2" style="width:19px;"></i><span>Download</span></a></li>
	</ul>
</div>
