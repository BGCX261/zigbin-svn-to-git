<?php
class animated_captcha {

	var $secret;
	var $auto_correction;
	var $auto_lower;
	var $auto_upper;
	var $background_array;
	var $background_dir;
	var $background_regexp;
	var $background_selected;
	var $color;
	var $distortion_type;
	var $distortion_type_array;
	var $frame_delay;
	var $frame_number;
	var $font_array;
	var $font_dir;
	var $font_size;
	var $grid_density;
	var $image_type;
	var $magic_words;
	var $overlap_text;
	var $overlap_text_factor;
	var $randomAngle_factor;
	var $random_y_factor;
	var $randomize_grid;
	var $session_name;
	var $string_length;
	var $temp_dir;
	var $text_space;
	var $use_background;
	var $use_distortion;
	var $use_grid;

	function animated_captcha() {
		$my_dir = dirname(__FILE__);
		$this->auto_correction = true;
		$this->auto_lower = false;
		$this->auto_upper = true;
		$this->background_array = array();
		$this->background_dir = $my_dir.'/animatedcaptcha_background/';
		$this->background_regexp = '/\.gif+$/i';
		$this->background_selected = '';
		$this->color = array();
		$this->color['background'] = array('#FEFDCF','#DFFEFF','#FFEEE1','#E1F4FF');
		$this->color['grid'] = array('000000');
		$this->color['text'] = array('000000');//#000000 =>black
		$this->distortion_type = 'normal';
		$this->distortion_type_array = array('normal', 'slice', 'wave');
		$this->font_array = array();
		$this->font_dir = $my_dir.'/animatedcaptcha_fonts/';
		$this->font_size = 16;
		$this->frame_delay = 80;
		$this->frame_number = 3;
		$this->grid_density = 2;
		$this->image_type = 'gif';
		$this->magic_words = '';
		$this->overlap_text = true;
		$this->overlap_text_factor = array(0, 2);
		$this->random_angle_factor = 10;
		$this->random_y_factor = 3;
		$this->randomize_grid = true;
		$this->session_name = 'turing_string';
		$this->string_length = 6;
		$this->temp_dir = $my_dir.'/animatedcaptcha_temp/';
		$this->text_space = 10;
		$this->use_background = true;
		$this->use_distortion = true;
		$this->use_grid = false;
		mt_srand( ((int)((double)microtime()*1000003)) );
	}

	function background_color($ar) {
		if (is_array($ar)) {
			$this->color['background'] = $ar;
		}
	}

	function distortion_type($s) {
		if ($s != '') {
			$this->distortion_type = $s;
		}
	}

	function draw_ellipse($b=true) {
	}

	function generate() {
		$this->no_cache();
		$this->check();
		$filename = array();
		$frame_delays = array();

		$image_string = $this->getImageString();

		if ($this->use_background) {
			$this->background_selected = $this->randomArrayEl($this->background_array);
		}

		$ar_char_color = array();
		for ($i=0;$i<$this->string_length;$i++) {
			$ar_char_color[] = $this->getRGBCode($this->randomArrayEl($this->color['text']));
		}

		$waveDirection = (0 == mt_rand(0, 1)) ? 1 : -1;

		for ($frame_count = 1;$frame_count <= $this->frame_number; $frame_count++) {

			$random_font = $this->randomFont();
			if ($random_font) {
				$x = (@imagefontwidth($random_font)+4+$this->text_space)*($this->string_length);
				if (!$this->use_background) {
					$height = (int) (@imagefontheight($random_font)*$this->font_size/4)+($this->random_y_factor*2);
				}
			} else {
				$x= (imagefontwidth($this->font_size)+$this->text_space)*$this->string_length;
				if (!$this->use_background) {
					$height = @imagefontheight($this->font_size)+10+($this->random_y_factor*2);
				}
			}

			if (!$this->use_background) {
				$width = $x + 20;
			}

			if ($this->use_background) {
				if (preg_match('/\.gif+$/', $this->background_selected)) {
					$this->image_type = 'gif';
					$image = imagecreatefromgif($this->background_selected);
				} else if (preg_match('/\.(jpeg|jpg)+$/', $this->background_selected)) {
					$this->image_type = 'jpeg';
					$image = imagecreatefromjpeg($this->background_selected);
				} else if (preg_match('/\.png+$/', $this->background_selected)) {
					$this->image_type = 'png';
					$image = imagecreatefrompng($this->background_selected);
				}
				$a = getimagesize($this->background_selected);
				$width = $a[0];
				$height = $a[1];
				unset($a);

				if ($this->use_distortion) {
					$image = imagecreate($width, $height);
					$tempBgColor = imagecolorallocate($image, 255, 255, 255);
					imagefilledrectangle($image, 0, 0, $width, $height, $tempBgColor);
				}
			} else {
				$ar_background_color = array();
				$ar_background_color[] = $this->getRGBCode($this->randomArrayEl($this->color['background']));

				$image = imagecreate($width, $height);
				$background_color = imagecolorallocate($image, $ar_background_color[0][0], $ar_background_color[0][1], $ar_background_color[0][2]);
				$border_color = imagecolorallocate($image, 0, 0, 0);
				imagerectangle($image,0,0,$width-1,$height-1,$border_color);
				imagefilledrectangle($image, 1, 1, $width-1, $height-1, $background_color);
			}

			if ($this->use_grid) {
				$ar_grid_color = array();
				$ar_grid_color[] = $this->getRGBCode($this->randomArrayEl($this->color['grid']));

				$grid_color = imagecolorallocate($image,$ar_grid_color[0][0],$ar_grid_color[0][1],$ar_grid_color[0][2]);

				for ($i=0;$i<$this->grid_density;$i++) {
					if ($this->randomize_grid) {
						$i_factor = (mt_rand(1,100) + microtime()) % mt_rand(2,100);
						$i_factor++;
						$i_factor2 = (mt_rand(1,100) + microtime()) % mt_rand(2,100);
						$i_factor2++;
					} else {
						$i_factor = 3;
						$i_factor2 = 3;
					}
					imagesetthickness($image, mt_rand(0,2));
					imageline($image, $i*($width/$i_factor),2,$i*($width/$i_factor2),$height,$grid_color);
				}
				if ($this->randomize_grid) {
					$i_factor = (mt_rand(1,100) + microtime()) % mt_rand(2,100);
					$i_factor++;
					$i_factor2 = (mt_rand(1,100) + microtime()) % mt_rand(2,100);
					$i_factor2++;
				} else {
					$i_factor=5;
					$i_factor2=5;
				}
				imageline($image,0,$i*($height/$i_factor),$width,$i*($height/$i_factor2),$grid_color);
				if ($this->randomize_grid) {
					imageline($image,0,$i*($height/3),$width,$i*($height/3),$grid_color);
				}
			}

			$x = sprintf("%d",($width-$x)/2);
			if ($random_font) {
				$y = ($height-imagefontheight($random_font))/2;
			} else {
				$y = ($height-imagefontheight($this->font_size))/2;
			}
			$offset_x = 0;
			$x += mt_rand(0,2);

			for ($i=0;$i<$this->string_length;$i++) {
				$char = substr($image_string, $i, 1);
				$x2 = $x + $offset_x - 1 + mt_rand(0,1);
				$y2 = $y - $this->random_y_factor + mt_rand(0, $this->random_y_factor * 2);
				$y2 = $y + 10;
				$foreground_color = imagecolorallocate($image, $ar_char_color[$i][0], $ar_char_color[$i][1], $ar_char_color[$i][2]);
				if ($random_font) {
					$angle = $this->randomAngle();

					if ($this->overlap_text) {
						if ($this->use_background) {
							imagettftext($image, $this->font_size, $angle + mt_rand(0,1), $x2 + $this->randomOverlapFactor(), $y2 + $this->randomOverlapFactor(), $foreground_color, $this->randomFont(),$char);
						} else {
							if (mt_rand(0,10) <= 9) {
								$comp = $this->getColorComplement($ar_char_color[$i], mt_rand(1,13), mt_rand(1,13), mt_rand(1,13));
								$comp_color = imagecolorallocate($image,$comp[0],$comp[1],$comp[2]);
							} else {
								$comp_color = $foreground_color;
							}
							imagettftext($image, $this->font_size, $angle + mt_rand(0,1), $x2 + $this->randomOverlapFactor(), $y2 + $this->randomOverlapFactor(), $comp_color, $this->randomFont(),$char);
						}
					}
					imagettftext($image, $this->font_size, $angle, $x2, $y2, $foreground_color, $this->randomFont(), $char);
					$offset_x += (imagefontwidth($random_font) * $this->font_size / 8) + $this->text_space;
				} else {
					imagestring($image, $this->font_size, $x2, $y2, $char, $foreground_color);
					$offset_x += imagefontwidth($this->font_size) + $this->text_space;
				}
			}

			if ($this->use_grid) {
				$ar_grid_color = array();
				$ar_grid_color[] = $this->getRGBCode($this->randomArrayEl($this->color['grid']));

				$grid_color = imagecolorallocate($image,$ar_grid_color[0][0],$ar_grid_color[0][1],$ar_grid_color[0][2]);

				for ($i=0;$i<2;$i++) {
					if ($this->randomize_grid) {
						$i_factor = (mt_rand(1,100) + microtime()) % mt_rand(2,100);
						$i_factor++;
						$i_factor2 = (mt_rand(1,100) + microtime()) % mt_rand(2,100);
						$i_factor2++;
					} else {
						$i_factor = 3;
						$i_factor2 = 3;
					}
					imagesetthickness($image, mt_rand(1,2));
					imageline($image, $i*($width/$i_factor),2,$i*($width/$i_factor2),$height,$grid_color);
				}
				for ($i=0;$i<1;$i++) {
					if ($this->randomize_grid) {
						$i_factor = (mt_rand(1,100) + microtime()) % mt_rand(2,100);
						$i_factor++;
						$i_factor2 = (mt_rand(1,100) + microtime()) % mt_rand(2,100);
						$i_factor2++;
					} else {
						$i_factor=5;
						$i_factor2=5;
					}
					imageline($image,0,$i*($height/$i_factor),$width,$i*($height/$i_factor2),$grid_color);
				}
			}

			if ($this->use_distortion) {
				if ('normal' == $this->distortion_type) {
					$image = $this->distortImage($image);
				} else if ('slice' == $this->distortion_type) {
					$image = $this->distortImageSlice($image);
				} else if ('wave' == $this->distortion_type) {
					$image = $this->distortImageWave($image, $waveDirection);
				}
			}

			if ($this->frame_number > 1) {
				$rand_filename = $this->temp_dir.md5(microtime().getenv('REMOTE_ADDR')).mt_rand(1, 999).'.txt';
				$fp = fopen($rand_filename, 'w+');
				if ($fp) {
					flock($fp, 2);
					fwrite($fp, '');
					flock($fp, 3);
					fclose($fp);
					imagegif($image, $rand_filename);
					imagedestroy($image);
					$filename[] = $rand_filename;
					$frame_delays[] = $this->frame_delay;
					unset($image, $rand_filename);
				}
			}
		}

		if (!headers_sent()) {
			session_start();
			header('Content-type: image/gif'); 
			$_SESSION[$this->session_name] = $this->string_crypt($image_string);
		} else {
			@trigger_error('generate() was called after headers already sent.', E_USER_ERROR);
		}

		if (1 == $this->frame_number) {
			if ('gif' == $this->image_type) {
				imagegif($image);
			} else if ('jpeg' == $this->image_type) {
				imagejpeg($image);
			} else if ('png' == $this->image_type) {
				imagepng($image);
			}
			imagedestroy($image);
		} else if (count($filename)>0) {
			$angif = new GIFEncoder($filename,$frame_delays,0,2,0,0,0,'url');
			echo($angif->GetAnimation());

			foreach ($filename as $s) {
				@unlink($s);
			}

			unset($angif, $filename, $frame_delays, $s);
		}
	}

	function font_size($i) {
		$this->font_size = intval($i);
	}

	function frame_delay($i) {
		$i = intval($i);
		if ($i >= 1) {
			$this->frame_delay = $i;
		}
	}

	function frame_number($i) {
		$i = intval($i);
		if ($i >= 1) {
			$this->frame_number = $i;
		}
	}

	function grid_color($ar) {
		if (is_array($ar)) {
			$this->color['grid'] = $ar;
		}
	}

	function grid_density($i) {
		$this->grid_density = intval($i);
	}

	function magic_words($s) {
		$this->magic_words = trim($s);
	}

	function overlap_text($b=true) {
		$this->overlap_text = (boolean)$b;
	}

	function overlap_text_factor($i1,$i2) {
		$this->overlap_text_factor = array(intval($i1),intval($i2));
	}

	function random_y_factor($i) {
		$this->random_y_factor = intval($i);
	}

	function randomize_grid($b=true) {
		$this->randomize_grid = (boolean)$b;
	}

	function string_length($i) {
		$this->string_length = intval($i);
	}

	function text_color($ar) {
		if (is_array($ar)) {
			$this->color['text'] = $ar;
		}
	}

	function text_space($i) {
		$this->text_space = intval($i);
	}

	function use_background($b = true) {
		$this->use_background = (boolean)$b;
	}

	function use_distortion($b = true) {
		$this->use_distortion = (boolean)$b;
	}

	function use_grid($b = true) {
		$this->use_grid = (boolean)$b;
	}

	function validate($image_string='') {
		if ('' == session_id()) {
			if (headers_sent()) {
				@trigger_error('&quot;validate()&quot; was called after headers already sent.',E_USER_ERROR);
				return(false);
			} else {
				session_start();
			}
		}

		if ($this->auto_correction) {
			$image_string = str_replace('i', '1', $image_string);// no i, just 1
			$image_string = str_replace('l', '1', $image_string);// no l, just 1
			$image_string = str_replace('o', '0', $image_string);// no o, just 0
			$image_string = str_replace('s', '5', $image_string);// no s, just 5
		}

		if ($this->auto_lower) {
			$image_string = strtolower($image_string);
		}
		if ($this->auto_upper) {
			$image_string = strtoupper($image_string);
		}
		$session_string = strtoupper($_SESSION[$this->session_name]);
		$comparison = $this->string_crypt($image_string);
		return (boolean)($session_string == $comparison);
	}

	function check() {
		if ('' == $this->session_name) {
			$this->session_name = 'turing_image';
		}
		$this->grid_density = intval($this->grid_density);
		$this->string_length = intval($this->string_length);
		if ($this->string_length < 3) {
			$this->string_length = 3;
		} else if ($this->string_length > 20) {
			$this->string_length = 20;
		}
		if ($this->text_space < 1) {
			$this->text_space = 1;
		}

		$this->cleanColor($this->color['grid']);
		$this->cleanColor($this->color['text']);

		if (!is_dir($this->temp_dir)) {
			@trigger_error($this->temp_dir.' is not a directory.',E_USER_ERROR);
		} else if (!preg_match('/(\\\\|\/)+$/',$this->temp_dir)) {
			$this->temp_dir .= '/';
		}

		if ($this->use_distortion && 'random' == $this->distortion_type) {
			$this->distortion_type = $this->randomArrayEl($this->distortion_type_array);
		}

		if ($this->use_background) {
			$this->background_array = $this->scanDirectory($this->background_dir, $this->background_regexp);
			if (count($this->background_array) == 0) {// no GIF file
				$this->use_background = false;
			} else {
				$this->overlap_text = false;// must be turned off
			}
		} else {
			$this->use_distortion = false;
		}
		$this->font_array = $this->scanDirectory($this->font_dir, '/\.ttf+$/i');
	}

	function cleanCaptchaString(&$s) {
		$s = preg_replace('/4/i','y',$s);
		$s = preg_replace('/6/i','u',$s);
		$s = preg_replace('/9/i','t',$s);
		$s = preg_replace('/b/i','x',$s);
		$s = preg_replace('/f/i','h',$s);
	}

	function cleanColor(&$ar) {
		$ar2 = array();
		foreach ($ar as $a) {
			if (!stristr($a, 'ffffff')) {
				$ar2[] = $a;
			}
		}
		if (array() == $ar2) {
			$ar2 = array('#000000');
		}
		$ar = $ar2;
		unset($a, $ar2);
	}
	function distortImage($image) {
		$width = imagesx($image);
		$height = imagesy($image);
		$midHeight = floor($height / 2);
		$midWidth = floor($width / 2);
		$split1 = floor($height / 3);
		$split2 = $split1 * 2;

		$temp = imagecreatefromgif($this->background_selected);

		for ($ty = 0; $ty < $height ; $ty++) {
			for ($tx = 0; $tx < $width ; $tx++) {
				$tx2 = $tx + floor($ty % mt_rand(1, 2));
				$ty2 = $ty + floor($tx % mt_rand(1, 2));
				$index = imagecolorat($image, $tx, $ty);
				$colors = imagecolorsforindex($image, $index);

				if ($colors['red'] != 255 && $colors['green'] != 255 && $colors['blue'] != 255) {
					if ($colors['red'] > 40) {
						$colors['red'] -= mt_rand(0, 3);
					}
					if ($colors['green'] > 40) {
						$colors['green'] -= mt_rand(0, 3);
					}
					if ($colors['blue'] > 40) {
						$colors['blue'] -= mt_rand(0, 3);
					}
					$color = imagecolorresolve($temp, $colors['red'], $colors['green'], $colors['blue']);
					imagesetpixel($temp, $tx2, $ty2, $color);
					imagesetpixel($temp, $tx2-1, $ty2, $color);
				}
			}
		}
		return $temp;
	}

	function distortImageSlice($image) {
		$width = imagesx($image);
		$height = imagesy($image);
		$midHeight = floor($height / 2);
		$midWidth = floor($width / 2);
		$split1 = floor(0.41 * $height) + mt_rand(1, 4);

		$temp = imagecreatefromgif($this->background_selected);
		$pixX = mt_rand(2, 3);
		$pixY = 2;
		for ($ty = 0; $ty < $height ; $ty++) {
			for ($tx = 0; $tx < $width ; $tx++) {
				if ($split1 <= $ty) {
					$tx2 = $tx - $pixX;
					$ty2 = $ty;
				} else {
					$tx2 = $tx;
					$ty2 = $ty - $pixY;
				}
				$index = imagecolorat($image, $tx, $ty);
				$colors = imagecolorsforindex($image, $index);

				if ($colors['red'] != 255 && $colors['green'] != 255 && $colors['blue'] != 255) {
					$color = imagecolorresolve($temp, $colors['red'], $colors['green'], $colors['blue']);
					if ($tx %10 == 0) {
						$tx += mt_rand(0, 1);
					}
					imagesetpixel($temp, $tx2, $ty2, $color);
					imagesetpixel($temp, $tx2-1, $ty2, $color);
				}
			}
		}
		return $temp;
	}
	function distortImageWave($image, $dir = 1, $wave = 5.2) {
		$width = imagesx($image);
		$height = imagesy($image);
		$midHeight = floor($height / 2);
		$midWidth = floor($width / 2);

		$temp = imagecreatefromgif($this->background_selected);

		for ($ty = 0; $ty < $height ; $ty++) {
			for ($tx = 0; $tx < $width ; $tx++) {

				$xf = $wave * sin(2 * 3.1415 * $ty / 128) * $dir;
				$yf = $wave * cos(2 * 3.1415 * $tx / 128) * $dir;

				$tx2 = $tx - $xf;
				$ty2 = $ty - $yf;

				$index = imagecolorat($image, $tx, $ty);
				$colors = imagecolorsforindex($image, $index);

				if ($colors['red'] != 255 && $colors['green'] != 255 && $colors['blue'] != 255) {
					$color = imagecolorresolve($temp, $colors['red'], $colors['green'], $colors['blue']);
					imagesetpixel($temp, $tx2, $ty2, $color);
				}
			}
		}
		return $temp;
	}

	function getColorComplement($color, $factor1=0, $factor2=0, $factor3=0) {
		if (is_array($color)) {
			$comp = array();
			for ($i=0;$i<=2;$i++) {
				$x = 255 - intval($color[$i]);
				$com = sprintf('$x += $factor'.'%s'.';',$i+1);
				eval($com);
				if ($x < 0) {
					$x = 0;
				} else if ($x > 255) {
					$x = 255;
				}
				$comp[] = $x;
			}
		} else {
			$comp = array(0,0,0);
		}
		return $comp;
	}
	function getImageString() {
		$image_string = $this->microseconds() .
						date('r') . mt_rand(1, 1000) .
						$_SERVER['REMOTE_ADDR'] .
						$_SERVER['HTTP_X_FORWARDED_FOR'];
		$image_string .= md5($image_string);
		$image_string = md5($image_string);
		$image_string = substr($image_string, 1, $this->string_length);
		$this->cleanCaptchaString($image_string);

		if ($this->auto_lower) {
			$image_string = strtolower($image_string);
		} else if ($this->auto_upper) {
			$image_string = strtoupper($image_string);
		}

		return $image_string;
	}
	function getRGBCode($s) {
		$rgbArr = array();
		$s = str_replace('#','',$s);
		if (6 == strlen($s)) {
			$rgbArr[] = hexdec(substr($s, 0, 2));
			$rgbArr[] = hexdec(substr($s, 2, 2));
			$rgbArr[] = hexdec(substr($s, 4, 2));
		}
		return $rgbArr;
	}
	function microseconds() {
		return(substr(microtime(),2,8));
	}
	function no_cache() {
		if (!headers_sent()) {
			header('Cache-Control: private, no-store, no-cache, must-revalidate, pre-check=0, post-check=0, max-age=0');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: '.gmdate('D, d M Y H:i:s',time()-60).' GMT');
			header('Pragma: no-cache');
		}
	}
	function randomAngle() {
		return $this->random_angle_factor - mt_rand(0, $this->random_angle_factor * 2);
	}

	function randomArrayEl($ar) {
		if (count($ar) > 0) {
			return $ar[mt_rand(0, count($ar) - 1)];
		} else {
			trigger_error('No array element.', E_USER_ERROR);
			return false;
		}
	}

	function randomFont() {
		return $this->randomArrayEl($this->font_array);
	}

	function randomOverlapFactor() {
		return mt_rand($this->overlap_text_factor[0], $this->overlap_text_factor[1]);
	}
	function scanDirectory($dir, $fileregexp, $appendDir = true) {
		$ar = array();
		if (!is_dir($dir)) {
			@trigger_error($dir.' is not a directory.',E_USER_ERROR);
		} else {
			$dirRes = opendir($dir);
			if ($dirRes) {
				while ($file = readdir($dirRes)) {
					if ($file != '.' && $file != '..' && preg_match($fileregexp,$file)) {
						if ($appendDir) {
							$ar[] = $dir.$file;
						} else {
							$ar[] = $file;
						}
					}
				}
				closedir($dirRes);
				unset($dirRes, $file);
			}
		}
		return $ar;
	}
	function string_crypt($string) {
		$string .= $_SERVER['REMOTE_ADDR'];
		$string .= $_SERVER['HTTP_X_FORWARDED_FOR'];
		$string .= $this->magic_words;
		$string = strtoupper(md5($string));
		return $string;
	}
}
Class GIFEncoder {
	var $GIF = "GIF89a";		
	var $VER = "GIFEncoder V2.05";	

	var $BUF = Array ( );
	var $LOP =  0;
	var $DIS =  2;
	var $COL = -1;
	var $IMG = -1;

	var $ERR = Array (
		ERR00=>"Does not supported function for only one image!",
		ERR01=>"Source is not a GIF image!",
		ERR02=>"Unintelligible flag ",
		ERR03=>"Does not make animation from animated GIF source",
	);
	function GIFEncoder	(
							$GIF_src, $GIF_dly, $GIF_lop, $GIF_dis,
							$GIF_red, $GIF_grn, $GIF_blu, $GIF_mod
						) {
		if ( ! is_array ( $GIF_src ) && ! is_array ( $GIF_tim ) ) {
			printf	( "%s: %s", $this->VER, $this->ERR [ 'ERR00' ] );
			exit	( 0 );
		}
		$this->LOP = ( $GIF_lop > -1 ) ? $GIF_lop : 0;
		$this->DIS = ( $GIF_dis > -1 ) ? ( ( $GIF_dis < 3 ) ? $GIF_dis : 3 ) : 2;
		$this->COL = ( $GIF_red > -1 && $GIF_grn > -1 && $GIF_blu > -1 ) ?
						( $GIF_red | ( $GIF_grn << 8 ) | ( $GIF_blu << 16 ) ) : -1;

		for ( $i = 0; $i < count ( $GIF_src ); $i++ ) {
			if ( strToLower ( $GIF_mod ) == "url" ) {
				$this->BUF [ ] = fread ( fopen ( $GIF_src [ $i ], "rb" ), filesize ( $GIF_src [ $i ] ) );
			}
			else if ( strToLower ( $GIF_mod ) == "bin" ) {
				$this->BUF [ ] = $GIF_src [ $i ];
			}
			else {
				printf	( "%s: %s ( %s )!", $this->VER, $this->ERR [ 'ERR02' ], $GIF_mod );
				exit	( 0 );
			}
			if ( substr ( $this->BUF [ $i ], 0, 6 ) != "GIF87a" && substr ( $this->BUF [ $i ], 0, 6 ) != "GIF89a" ) {
				printf	( "%s: %d %s", $this->VER, $i, $this->ERR [ 'ERR01' ] );
				exit	( 0 );
			}
			for ( $j = ( 13 + 3 * ( 2 << ( ord ( $this->BUF [ $i ] { 10 } ) & 0x07 ) ) ), $k = TRUE; $k; $j++ ) {
				switch ( $this->BUF [ $i ] { $j } ) {
					case "!":
						if ( ( substr ( $this->BUF [ $i ], ( $j + 3 ), 8 ) ) == "NETSCAPE" ) {
							printf	( "%s: %s ( %s source )!", $this->VER, $this->ERR [ 'ERR03' ], ( $i + 1 ) );
							exit	( 0 );
						}
						break;
					case ";":
						$k = FALSE;
						break;
				}
			}
		}
		GIFEncoder::GIFAddHeader ( );
		for ( $i = 0; $i < count ( $this->BUF ); $i++ ) {
			GIFEncoder::GIFAddFrames ( $i, $GIF_dly [ $i ] );
		}
		GIFEncoder::GIFAddFooter ( );
	}
	function GIFAddHeader ( ) {
		$cmap = 0;

		if ( ord ( $this->BUF [ 0 ] { 10 } ) & 0x80 ) {
			$cmap = 3 * ( 2 << ( ord ( $this->BUF [ 0 ] { 10 } ) & 0x07 ) );

			$this->GIF .= substr ( $this->BUF [ 0 ], 6, 7		);
			$this->GIF .= substr ( $this->BUF [ 0 ], 13, $cmap	);
			$this->GIF .= "!\377\13NETSCAPE2.0\3\1" . GIFEncoder::GIFWord ( $this->LOP ) . "\0";
		}
	}
	function GIFAddFrames ( $i, $d ) {

		$Locals_str = 13 + 3 * ( 2 << ( ord ( $this->BUF [ $i ] { 10 } ) & 0x07 ) );

		$Locals_end = strlen ( $this->BUF [ $i ] ) - $Locals_str - 1;
		$Locals_tmp = substr ( $this->BUF [ $i ], $Locals_str, $Locals_end );

		$Global_len = 2 << ( ord ( $this->BUF [ 0  ] { 10 } ) & 0x07 );
		$Locals_len = 2 << ( ord ( $this->BUF [ $i ] { 10 } ) & 0x07 );

		$Global_rgb = substr ( $this->BUF [ 0  ], 13,
							3 * ( 2 << ( ord ( $this->BUF [ 0  ] { 10 } ) & 0x07 ) ) );
		$Locals_rgb = substr ( $this->BUF [ $i ], 13,
							3 * ( 2 << ( ord ( $this->BUF [ $i ] { 10 } ) & 0x07 ) ) );

		$Locals_ext = "!\xF9\x04" . chr ( ( $this->DIS << 2 ) + 0 ) .
						chr ( ( $d >> 0 ) & 0xFF ) . chr ( ( $d >> 8 ) & 0xFF ) . "\x0\x0";

		if ( $this->COL > -1 && ord ( $this->BUF [ $i ] { 10 } ) & 0x80 ) {
			for ( $j = 0; $j < ( 2 << ( ord ( $this->BUF [ $i ] { 10 } ) & 0x07 ) ); $j++ ) {
				if	(
						ord ( $Locals_rgb { 3 * $j + 0 } ) == ( ( $this->COL >> 16 ) & 0xFF ) &&
						ord ( $Locals_rgb { 3 * $j + 1 } ) == ( ( $this->COL >>  8 ) & 0xFF ) &&
						ord ( $Locals_rgb { 3 * $j + 2 } ) == ( ( $this->COL >>  0 ) & 0xFF )
					) {
					$Locals_ext = "!\xF9\x04" . chr ( ( $this->DIS << 2 ) + 1 ) .
									chr ( ( $d >> 0 ) & 0xFF ) . chr ( ( $d >> 8 ) & 0xFF ) . chr ( $j ) . "\x0";
					break;
				}
			}
		}
		switch ( $Locals_tmp { 0 } ) {
			case "!":
				$Locals_img = substr ( $Locals_tmp, 8, 10 );
				$Locals_tmp = substr ( $Locals_tmp, 18, strlen ( $Locals_tmp ) - 18 );
				break;
			case ",":
				$Locals_img = substr ( $Locals_tmp, 0, 10 );
				$Locals_tmp = substr ( $Locals_tmp, 10, strlen ( $Locals_tmp ) - 10 );
				break;
		}
		if ( ord ( $this->BUF [ $i ] { 10 } ) & 0x80 && $this->IMG > -1 ) {
			if ( $Global_len == $Locals_len ) {
				if ( GIFEncoder::GIFBlockCompare ( $Global_rgb, $Locals_rgb, $Global_len ) ) {
					$this->GIF .= ( $Locals_ext . $Locals_img . $Locals_tmp );
				}
				else {
					$byte  = ord ( $Locals_img { 9 } );
					$byte |= 0x80;
					$byte &= 0xF8;
					$byte |= ( ord ( $this->BUF [ 0 ] { 10 } ) & 0x07 );
					$Locals_img { 9 } = chr ( $byte );
					$this->GIF .= ( $Locals_ext . $Locals_img . $Locals_rgb . $Locals_tmp );
				}
			}
			else {
				$byte  = ord ( $Locals_img { 9 } );
				$byte |= 0x80;
				$byte &= 0xF8;
				$byte |= ( ord ( $this->BUF [ $i ] { 10 } ) & 0x07 );
				$Locals_img { 9 } = chr ( $byte );
				$this->GIF .= ( $Locals_ext . $Locals_img . $Locals_rgb . $Locals_tmp );
			}
		}
		else {
			$this->GIF .= ( $Locals_ext . $Locals_img . $Locals_tmp );
		}
		$this->IMG  = 1;
	}
	function GIFAddFooter ( ) {
		$this->GIF .= ";";
	}
	function GIFBlockCompare ( $GlobalBlock, $LocalBlock, $Len ) {

		for ( $i = 0; $i < $Len; $i++ ) {
			if	(
					$GlobalBlock { 3 * $i + 0 } != $LocalBlock { 3 * $i + 0 } ||
					$GlobalBlock { 3 * $i + 1 } != $LocalBlock { 3 * $i + 1 } ||
					$GlobalBlock { 3 * $i + 2 } != $LocalBlock { 3 * $i + 2 }
				) {
					return ( 0 );
			}
		}

		return ( 1 );
	}
	function GIFWord ( $int ) {

		return ( chr ( $int & 0xFF ) . chr ( ( $int >> 8 ) & 0xFF ) );
	}
	function GetAnimation ( ) {
		return ( $this->GIF );
	}
}
?>