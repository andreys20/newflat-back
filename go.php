<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Expires: " . date("r"));
$query = $_REQUEST;
unset($query['q']);

if(!empty($query) && isset($query)) {
	$url = iconv('windows-1251','UTF-8', $_SERVER['REQUEST_URI']);
	$ref = explode('?ref=', $url)[1];
}
?>

<script type="text/javascript">
	localStorage.setItem('ref', '<?php echo $ref;?>');
	window.location = window.location.origin
</script>