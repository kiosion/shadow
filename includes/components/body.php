<?php

// Prevent direct access
if (!isset($include)) {
	header("Location: ../../");
}
?>
	<body class="text-center bg-black">
		<div class="d-flex flex-column min-vh-100">
			<?php if (isset($includeHeader)) require $includeHeader; ?>
			<main class="container-fluid my-auto pb-5">
				<?php include $includeBody; ?>
			</main>
			<?php if (isset($includeFooter)) require $includeFooter; ?>
		</div>
	<?php include_once 'includes/components/context-menu.php'; ?>
	</body>
	<?php require_once 'includes/scripts.php'; ?>
</html>
