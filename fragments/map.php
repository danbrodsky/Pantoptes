<?php
/**
 * Created by PhpStorm.
 * User: agott
 * Date: 2018-11-17
 * Time: 15:47
 */
?>
<script src='https://api.mapbox.com/mapbox-gl-js/v0.51.0/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v0.51.0/mapbox-gl.css' rel='stylesheet' />
<div class="my-4 w-100" id='map'></div>
<script>
    mapboxgl.accessToken = 'pk.eyJ1IjoiYWdvdHRhcmRvIiwiYSI6ImlQNEYtcWcifQ.2GSJXDBB7oMK61Ey9Dtzww';
    var map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/dark-v9', //hosted style id
        center: [-123.1, 49.25],
        zoom: 5
    });
    map.addControl(new mapboxgl.NavigationControl());
</script>