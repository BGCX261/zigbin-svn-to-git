<?php
require_once(dirname(__FILE__).'/animatedcaptcha.class.php');

$img=new animated_captcha();

$img->session_name='my_session';

$img->magic_words('secret');

$img->grid_color(array('#63A595','#8FD67F'));

$img->text_color(array('#CD1B2D', '#950FC8', '#660033', '#006633', '#0D47B3', '#6600CC', '#000099'));

$img->frame_number(2);

$img->frame_delay(80);

$img->use_background(true);
$img->use_distortion(true);
$img->distortion_type('normal');
$img->use_grid(true);

$img->generate();
?>