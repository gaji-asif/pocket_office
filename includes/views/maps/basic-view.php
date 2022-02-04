<?php

if(empty($zoom))
{
	$zoom = 12;
}

?>
<div id="<?=@$map_id?>"></div>
<script>
	$(window).on('load', function() {
		var map = $('#<?=@$map_id?>');
		map.jHERE({
			enable: ['behavior', 'typeselector', 'zoombar', 'scalebar', 'contextmenu'],
			center: [<?=@$latitude?>, <?=@$longitude?>],
			zoom: <?=$zoom?>
		}).jHERE('marker', [<?=@$latitude?>, <?=@$longitude?>], {
			icon: '<?=IMAGES_DIR?>/icons/map/center-marker.png',
			anchor: {x: 24, y: 24}
		});
	});
</script>