<?php

// Prevent direct access
if (!isset($include)) {
	header("Location: ../../");
}

// Include files
require_once 'includes/utils/post.php';

class RowItem {
	// Function to list range of uploads from set uid
	public static function fetchUploads($token, $start, $sort, $order) {
		$c = $start+1;
		// Get number of uploads from user
		$arr = array("token"=>"$token","action"=>"get_upload_count");
		$res = post('http://localhost/api/v1/user.php', $arr);
		$uploads_count = json_decode($res, true)['data'];

		// If number of uploads is > 0, get uploads
		$arr = array("token"=>"$token","action"=>"get_uploads","start"=>"$start","sort"=>"$sort","order"=>"$order");
		$res = post('http://localhost/api/v1/user.php', $arr);
		$uploads = json_decode($res, true)['data'];

		// If no uploads, return empty array
		if (empty($uploads) || count($uploads) < 1) {
			return array();
		}
		else {
			return array(
				"start" => $start,
				"end" => $start+10,
				"uploads_count" => $uploads_count,
				"uploads" => $uploads,
			);
		}
	}
	// Function to print all upload items from array
	public static function printUploads($items) {
		$c = $items['start']+1;
		$uploads = $items['uploads'];
		// Loop through each upload
		foreach ($uploads as $upload) {
			// Create array of data
			$item = array(
				"item_num" => $c,
				"item_id" => $upload['id'],
				"item_og_name" => $upload['og_name'],
				"item_ul_name" => $upload['ul_name'],
				"item_type" => $upload['ext'],
				"item_timestamp" => $upload['time'],
				"item_size" => $upload['size'],
				"item_visibility" => $upload['vis'],
			);
			// Convert unix timestamp to readable format
			$timestamp = $item['item_timestamp'];
			$tz = new DateTimeZone('America/Halifax');
			$date = (new DateTimeImmutable('@'.$timestamp, $tz))->setTimeZone($tz);
			$item['item_time'] = $date->format('H:i:s');
			$item['item_date'] = $date->format('Y-m-d');
			$item['item_size'] = round($item['item_size']/1000, 2);
			// Convert size from bytes to KB, MB, GB
			if ($item['item_size'] > 1000) {
				$item['item_size'] = round($item['item_size']/1000, 2);
				if ($item['item_size'] > 1000) {
					$item['item_size'] = round($item['item_size']/1000, 2);
					$item['item_size'] = $item['item_size'].' GB';
				}
				else {
					$item['item_size'] .= ' MB';
				}
			}
			else {
				$item['item_size'] .= ' KB';
			}
			// Print item
			self::printItem($item);
			$c++;
		}
	}
	// Function to print individual upload item
	private static function printItem($item) {
		$currentLink = 'http://'.$_SERVER['HTTP_HOST'];
		echo '
			<div class="row d-flex justify-content-between flex-nowrap row-item bg-dark text-light" id="'.$item['item_id'].'">
				<div class="col-1 col-num">'.$item['item_num'].'</div>
				<div class="col col-md col-name text-truncate">'.htmlspecialchars($item['item_og_name']).'</div>
				<div class="col-1 d-none col-size d-md-block">'.$item['item_size'].'</div>
				<div class="col-2 d-none col-date d-md-block" data-bs-toggle="tooltip" data-bs-position="top" title="'.$item['item_time'].'">'.$item['item_date'].'</div>
				<div class="col d-flex col-actions justify-content-between btn-group" role="group">
					<button type="button" data-link="'.$currentLink.'/file/'.$item['item_ul_name'].'" class="btn btn-action-dark-cyan btn-group-child fileButtonOpen" data-bs-toggle="tooltip" data-bs-placement="top" title="Open"><i class="fas fa-external-link-square"></i></button>
					<button type="button" data-link="'.$currentLink.'/file/'.$item['item_ul_name'].'" class="btn btn-action-dark-cyan btn-group-child fileButtonCopy" data-bs-toggle="tooltip" data-bs-placement="top" title="Copy link"><i class="fas fa-link"></i></button>
					<button type="button" data-id="'.$item['item_id'].'" class="btn btn-action-dark-cyan btn-group-child fileButtonVis" data-bs-toggle="tooltip" data-bs-placement="top" title="Change visibility"><i class="fas '; if ($item['vis'] == 0) echo 'fa-eye-slash'; else echo 'fa-eye'; echo '"></i></button>
					<button type="button" data-link="'.$currentLink.'/file/'.$item['item_ul_name'].'/download" class="btn btn-action-dark-cyan btn-group-child fileButtonDownload" data-bs-toggle="tooltip" data-bs-placement="top" title="Download"><i class="fas fa-download"></i></button>
					<button type="button" data-id="'.$item['item_id'].'" class="btn btn-action-dark-danger btn-group-child fileButtonDelete" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><i class="fas fa-trash"></i></button>
				</div>
			</div>
		';
	}
}

// Check URL for start index
if (isset($_GET['i'])) {
	$start = $_GET['i'];
}
else {
	$start = 0;
}
// Check URL for sort pattern
if (isset($_GET['s'])) {
	$sort = $_GET['s'];
}
else {
	$sort = 'time';
}
// Check URL for order
if (isset($_GET['o'])) {
	$order = $_GET['o'];
}
else {
	$order = 'd';
}
// Fetch token from cookie
$token = $_COOKIE['shadow_login_token'];
// Fetch uploads
$rowItems = RowItem::fetchUploads($token, $start, $sort, $order);

if ($rowItems['uploads_count'] > 0) {

	$start = $rowItems['start'];
	$end = $rowItems['end'];
	$count = $rowItems['uploads_count'];

	// Table header
	echo '
		<div class="row-fluid px-5" id="uploads-table-container">
			<div class="col-fluid" id="uploads-table">
				<div class="row row-header ps-3 text-light fw-bold">
					<div class="col-1 col-num">#</div>
					<div class="col col-md col-name text-truncate">Name</div>
					<div class="col-1 d-none col-size d-md-block">Size</div>
					<div class="col-2 d-none col-date d-md-block">Date</div>
					<div class="col col-actions">Actions</div>
				</div>
				<div class="row-fluid row-container py-4">
	';

	// Print uploads
	RowItem::printUploads($rowItems);

	// Table footer
	echo '
				</div>
				<div class="row-fluid row-footer text-light fw-bold px-0">
					<div class="row d-flex justify-content-between flex-nowrap">
						<div class="col">
							Items: '.($start+1).' - '.$end.' of '.$count.'
						</div>
	';
	$itemsPerPage = 10;
	// Calculate number of pages to display given total items ($count) and items per page ($itemsPerPage)
	$pages = ceil($count / $itemsPerPage);
	// Calculate indexes required to display page buttons (prev, next)
	$prev = $start - $itemsPerPage;
	$next = $start + $itemsPerPage;
	// Create links for page buttons including other parameters
	$prevLink = 'http://'.$_SERVER['HTTP_HOST'].'?i='.$prev;
	if (isset($_GET['s'])) $prevLink .= '&s='.$sort;
	if (isset($_GET['o'])) $prevLink .= '&o='.$order;
	$nextLink = '?i='.$next;
	if (isset($_GET['s'])) $nextLink .= '&s='.$sort;
	if (isset($_GET['o'])) $nextLink .= '&o='.$order;

	// Create links for sort, order buttons including other parameters
	$sortLink = 'http://'.$_SERVER['HTTP_HOST'].'?i='.$start;
	if (isset($_GET['o'])) $sortLink .= '&o='.$order;
	$sortLinkName = $sortLink.'&s=n';
	$sortLinkDate = $sortLink.'&s=t';
	$sortLinkSize = $sortLink.'&s=s';
	$orderLink = 'http://'.$_SERVER['HTTP_HOST'].'?i='.$start;
	if (isset($_GET['s'])) $orderLink .= '&s='.$sort;
	$orderLinkAsc = $orderLink.'&o=a';
	$orderLinkDesc = $orderLink.'&o=d';

	// Sort, order buttons, pagination buttons
	echo '
						<div class="col-2 sortByDropdown">
							<div class="dropup">
								<a class="btn btn-secondary dropdown-toggle" role="button" id="dropdownSortBy" data-bs-toggle="dropdown" aria-expanded="false">Sort by</a>
								<ul class="dropdown-menu" aria-labelledby="dropdownSortBy">';
									if ($sort == 'n') echo '<li class="dropdown-item"><a class="dropdown-item active">Name</a></li>';
									else echo '<li class="dropdown-item"><a class="dropdown-item" href="'.$sortLinkName.'">Name</a></li>'; 
									if ($sort == 't') echo '<li class="dropdown-item active"><a class="dropdown-item">Date</a></li>';
									else echo '<li class="dropdown-item"><a class="dropdown-item" href="'.$sortLinkDate.'">Date</a></li>';
									if ($sort == 's') echo '<li class="dropdown-item"><a class="dropdown-item active">Size</a></li>';
									else echo '<li class="dropdown-item"><a class="dropdown-item" href="'.$sortLinkSize.'">Size</a></li>';
									echo'
								</ul>
							</div>
						</div>
						<div class="col-2 orderByDropdown">
							<div class="dropup">
								<a class="btn btn-secondary dropdown-toggle" role="button" id="dropdownSortBy" data-bs-toggle="dropdown" aria-expanded="false">Order</a>
								<ul class="dropdown-menu" aria-labelledby="dropdownSortBy">';
									if ($order == 'a') echo'<li class="dropdown-item active"><a class="dropdown-item">Ascending</a></li>';
									else echo '<li class="dropdown-item"><a class="dropdown-item" href="'.$orderLinkAsc.'">Ascending</a></li>';
									if ($order == 'd') echo '<li class="dropdown-item active"><a class="dropdown-item">Descending</a></li>';
									else echo '<li class="dropdown-item"><a class="dropdown-item" href="'.$orderLinkDesc.'">Descending</a></li>';
									echo '
								</ul>
							</div>
						</div>
						<div class="col-2 paginationButtons">
							<div class="btn-group" role="group">
									';if ($start == 0) {
										echo '<a class="btn btn-action-light btn-group-child" data-bs-toggle="tooltip" data-bs-placement="top" title="Previous"><i class="fas fa-angle-left"></i></a>';
									}
									else {
										$prev = 0;
										echo '<a href="'.$prevLink.'" class="btn btn-action-light btn-group-child" data-bs-toggle="tooltip" data-bs-placement="top" title="Previous"><i class="fas fa-angle-left"></i></a>';
									}
									echo '<a href="'.$nextLink.'" class="btn btn-action-light btn-group-child" data-bs-toggle="tooltip" data-bs-placement="top" title="Next"><i class="fas fa-angle-right"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	
	';
}
else {
	echo '
		<p class="fs-5 fw-bold my-5 text-light">No uploads to display!</p>
	';
}