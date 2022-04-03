<?php
if (!isset($include)) {
	header('Content-Type: application/json; charset=utf-8');
	include_once '../utils/res.php';
	echo Res::fail(403, 'Unauthorized');
	exit();
}
?>
<div class="contextMenu_obj nosel" style="display:none;">
	<ul class="dropdown-menu dropdown-menu-dark mx-0 border-0 shadow">
		<?php
			foreach ($contextMenu_objActions as $action) {
				if ($action['title'] == 'divider') echo '<li><hr class="dropdown-divider"></li>';
				else echo '<li id="dropdown-item-'.$action['class'].'"><a class="dropdown-item '.$action['class'].'" data-link="'.$action['data-link'].'"><i class="fas '.$action['icon'].' ps-2" style="width:19px;"></i><span>'.$action['title'].'</span></a></li>';
			}
		?>
	</ul>
</div>
<div class="contextMenu_link nosel" style="display:none;">
	<ul class="dropdown-menu dropdown-menu-dark mx-0 border-0 shadow">
		<?php
			foreach ($contextMenu_linkActions as $action) {
				if ($action['title'] == 'divider') echo '<li><hr class="dropdown-divider"></li>';
				else echo '<li id="dropdown-item-'.$action['class'].'"><a class="dropdown-item '.$action['class'].'" data-link="'.$action['data-link'].'"><i class="fas '.$action['icon'].' ps-2" style="width:19px;"></i><span>'.$action['title'].'</span></a></li>';
			}
		?>

	</ul>
</div>
