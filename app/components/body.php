<?php

// Prevent direct access
if (!isset($include)) {
	header("Location: ../../");
}
// Set page link
$requestURI = explode('/', $_SERVER['REQUEST_URI']);
if (!empty($requestURI[2])) $requestURI = '/'.$requestURI[1].'/'.$requestURI[2];
else $requestURI = '/'.$requestURI[1];
$currentLink = 'http://'.$_SERVER['HTTP_HOST'].$requestURI;
$currentHost = 'http://'.$_SERVER['HTTP_HOST'];
?>
	<body class="text-center bg-black" ondragstart="return false;">
		<div class="d-flex flex-column min-vh-100">
			<?php 
				if (isset($includeHeader)) require $includeHeader;
				include $includeBody;
				if (isset($includeFooter)) require $includeFooter; 
			?>
		</div>
	<?php 
		switch ($app_route) {
			default:
				$contextMenu_linkActions = array(
					'open' => array(
						'icon' => 'fa-external-link-square',
						'title' => 'Open in new tab',
						'class' => 'open-link',
						'data-link' => '',
						
					),
					'copy' => array(
						'icon' => 'fa-link',
						'title' => 'Copy link',
						'class' => 'copy-link',
						'data-link' => '',
						
					),
				);
				$contextMenu_objActions = array(
					'copy' => array(
						'icon' => 'fa-link',
						'title' => 'Copy link',
						'class' => 'copy-link',
						'data-link' => $currentLink,
					),
					'raw' => array(
						'icon' => 'fa-external-link-square',
						'title' => 'View raw',
						'class' => 'view-raw',
						'data-link' => $currentLink.'/raw',
					),
					'divider' => array(
						'title' => 'divider',
					),
					'download' => array(
						'icon' => 'fa-cloud-download',
						'title' => 'Download',
						'class' => 'download',
						'data-link' => $currentLink.'/download',
					),
				);
				break;
		}
		include_once 'app/components/context-menu.php'; 
	?>
	</body>
	<?php require_once 'app/scripts.php'; ?>
</html>
