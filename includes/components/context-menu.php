<?php
// Set current url as link to copy
$currentLink = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>
<div id="contextMenu" style="display:none;">
	<ul class="dropdown-menu dropdown-menu-dark mx-0 border-0 shadow">
		<li id="contextMenu-copyLink" data-link="<?php echo $currentLink; ?>"><a class="dropdown-item"><i class="fas fa-link ps-2" style="width:19px;"></i><span>Copy link</span></a></li>
		<li><a class="dropdown-item contextMenu-viewRaw"><i class="fas fa-external-link-square ps-2" style="width:19px;"></i><span>View raw</span></a></li>
		<li><hr class="dropdown-divider"></li>
		<li><a class="dropdown-item contextMenu-Download"><i class="fas fa-cloud-download ps-2" style="width:19px;"></i><span>Download</span></a></li>
	</ul>
</div>
