<?php

// Prevent direct access
if (!isset($include)) {
	header("Location: ../../");
}

// Include files
require_once 'includes/utils/post.php';

class RowItem {
	// Constructor
	public function __construct($uid) {
		$this->uid = $uid;
	}
	// Function to fetch all uploads from set uid
	public function listUploads() {
		$uploads = array();
	}
	// Function to list range of uploads from set uid
	public function listUploadsRange($start, $end) {
		$uploads = array();
	}
	private function printItem($upload) {
		$item_num = $upload['item_num'];
		$item_name = $upload['item_name'];
		$item_size = $upload['item_size']; // In bytes
		$item_type = $upload['item_type'];
		$item_date = $upload['item_time']; // DateTimeImmutable object timestamp
		$item_status = $upload['item_status'];
		echo '
			<div class="row row-item bg-dark text-light">
				<div class="col-1">1</div>
				<div class="col-3 col-md">sdfgh.jpg</div>
				<div class="col-1 d-none d-md-block">2.5MB</div>
				<div class="col-2 d-none d-md-block">03/03/22</div>
				<div class="col d-flex justify-content-between btn-group" role="group">
					<button type="button" class="btn btn-action-dark-cyan btn-group-first" data-bs-toggle="tooltip" data-bs-placement="top" title="Open"><i class="fas fa-external-link-square"></i></button>
					<button type="button" class="btn btn-action-dark-cyan" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy link"><i class="fas fa-link"></i></button>
					<button type="button" class="btn btn-action-dark-cyan" data-bs-toggle="tooltip" data-bs-placement="top" title="Change visibility"><i class="fas fa-eye-slash"></i></button>
					<button type="button" class="btn btn-action-dark-cyan" data-bs-toggle="tooltip" data-bs-placement="top" title="Download"><i class="fas fa-download"></i></button>
					<button type="button" class="btn btn-action-dark-danger btn-group-last" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><i class="fas fa-trash"></i></button>
				</div>
			</div>
		';
	}
}
?>

<div class="row-fluid px-5" id="uploads-table-container">
	<div class="col-fluid" id="uploads-table">
		<div class="row row-header text-light fw-bold pb-3">
			<div class="col-1">#</div>
			<div class="col-3 col-md">Name</div>
			<div class="col-1 d-none d-md-block">Size</div>
			<div class="col-2 d-none d-md-block">Date</div>
			<div class="col">Actions</div>
		</div>
		<div class="row row-item bg-dark text-light">
			<div class="col-1">1</div>
			<div class="col-3 col-md">sdfgh.jpg</div>
			<div class="col-1 d-none d-md-block">2.5MB</div>
			<div class="col-2 d-none d-md-block">03/03/22</div>
			<div class="col d-flex justify-content-between btn-group" role="group">
				<button type="button" class="btn btn-action-dark-cyan btn-group-first" data-bs-toggle="tooltip" data-bs-placement="top" title="Open"><i class="fas fa-external-link-square"></i></button>
				<button type="button" class="btn btn-action-dark-cyan" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy link"><i class="fas fa-link"></i></button>
				<button type="button" class="btn btn-action-dark-cyan" data-bs-toggle="tooltip" data-bs-placement="top" title="Change visibility"><i class="fas fa-eye-slash"></i></button>
				<button type="button" class="btn btn-action-dark-cyan" data-bs-toggle="tooltip" data-bs-placement="top" title="Download"><i class="fas fa-download"></i></button>
				<button type="button" class="btn btn-action-dark-danger btn-group-last" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><i class="fas fa-trash"></i></button>
			</div>
		</div>
		<div class="row row-item bg-dark text-light">
			<div class="col-1">2</div>
			<div class="col-3 col-md">fdshsdf.jpg</div>
			<div class="col-1 d-none d-md-block">2.5MB</div>
			<div class="col-2 d-none d-md-block">06/03/22</div>
			<div class="col d-flex justify-content-between btn-group" role="group">
				<button type="button" class="btn btn-action-dark-cyan btn-group-first" data-bs-toggle="tooltip" data-bs-placement="top" title="Open"><i class="fas fa-external-link-square"></i></button>
				<button type="button" class="btn btn-action-dark-cyan" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy link"><i class="fas fa-link"></i></button>
				<button type="button" class="btn btn-action-dark-cyan" data-bs-toggle="tooltip" data-bs-placement="top" title="Change visibility"><i class="fas fa-eye-slash"></i></button>
				<button type="button" class="btn btn-action-dark-cyan" data-bs-toggle="tooltip" data-bs-placement="top" title="Download"><i class="fas fa-download"></i></button>
				<button type="button" class="btn btn-action-dark-danger btn-group-last" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><i class="fas fa-trash"></i></button>
			</div>
		</div>
		<div class="row row-item bg-dark text-light">
			<div class="col-1">3</div>
			<div class="col-3 col-md">teafasdfst.jpg</div>
			<div class="col-1 d-none d-md-block">2.5MB</div>
			<div class="col-2 d-none d-md-block">06/03/22</div>
			<div class="col d-flex justify-content-between btn-group" role="group">
				<button type="button" class="btn btn-action-dark-cyan btn-group-first" data-bs-toggle="tooltip" data-bs-placement="top" title="Open"><i class="fas fa-external-link-square"></i></button>
				<button type="button" class="btn btn-action-dark-cyan" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy link"><i class="fas fa-link"></i></button>
				<button type="button" class="btn btn-action-dark-cyan" data-bs-toggle="tooltip" data-bs-placement="top" title="Change visibility"><i class="fas fa-eye-slash"></i></button>
				<button type="button" class="btn btn-action-dark-cyan" data-bs-toggle="tooltip" data-bs-placement="top" title="Download"><i class="fas fa-download"></i></button>
				<button type="button" class="btn btn-action-dark-danger btn-group-last" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><i class="fas fa-trash"></i></button>
			</div>
		</div>
	</div>
</div>
