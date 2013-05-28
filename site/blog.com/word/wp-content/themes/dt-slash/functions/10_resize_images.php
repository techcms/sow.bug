<?php
/*
   function default_attachment($att=null) {
	   if ($att)
	   {
		  if ($att[0])
			 return $att;
	   }
	   $file = "/images/noimage.jpg";
	   $att[0] = get_template_directory_uri().$file;
	   $fname = dirname(__FILE__)."/../".$file;
	   $size = @getimagesize($fname);
	   $att[1] = $size[0];
	   $att[2] = $size[1];
	   return $att;
	}
*/
   function filemtime_compare($a, $b)
   {
       return filemtime($a) - filemtime($b);    
   }

   function image_src($arr = false)
   {
      if ($arr && is_array($arr))
         extract($arr);

      if ( isset($src) && is_array($src) )
         $src = $src[0];

      $is_file = 0;
      
      if ( isset($src) && $src && !is_numeric($src) )
      {
         $src = str_replace(site_url(), '', $src);
         $img_id = $src;
         $is_file = 1;
      }
      else
      {
         if (!isset($post_id) || !$post_id)
            $post_id = get_the_ID();  
         if (!isset($img_id) || !$img_id)
            $img_id = get_post_thumbnail_id( $post_id );
      }
         
      if ( !isset($noimage) )
         $noimage = 0;
         
      if (!$img_id && !$noimage)
         return;
      
      if (!$is_file)
      {
         $orig_image = wp_get_attachment_image_src($img_id, 'large' );
         
         $img_id = $orig_image[0];
         $img_id = str_replace(site_url(), '', $img_id);
         
         if (!$orig_image && !$noimage)
            return false;
      }
   
      $options = get_option(LANGUAGE_ZONE.'_theme_options');
      if ($options["resize_method"] == "ext")
         $src = get_template_directory_uri().'/resize.php?get_image='.urlencode($img_id);
      else
         $src = site_url().'/?get_image='.urlencode($img_id);
      
      if (isset($w) && $w)
         $src .= '&amp;w='.$w;
      if (isset($h) && $h)
         $src .= '&amp;h='.$h;
      if (isset($noimage) && $noimage)
         $src .= '&amp;noimage=1';
      
      //$src = "about:blank";
      
      return $src;
   }

   function cleanCache()
   {
      $files = glob(TMP_DIR."/*", GLOB_BRACE);

      if (count($files) > 0)
      {
         $yesterday = time() - (24 * 60 * 60);
         usort($files, 'filemtime_compare');
         $i = 0;
         if (count($files) > 300)
         {
            foreach ($files as $file)
            {
               if (is_dir($file))
                  continue;
               $i ++;
               if ($i >= 5);
                  return;
               if (@filemtime($file) > $yesterday)
                  return;
               if (file_exists($file))
                  @unlink($file);
            }            
         }
      }
   }

   /***************************************************************************************/

   if ( !isset($_GET['get_image']) ) 
      return;
   
   ///////////////////////////////////////
   ///////////////////////////////////////
   
   // check if image is cached
   $tmp_name = TMP_DIR.md5($_SERVER["REQUEST_URI"]).".jpg";
   
   if ( isset($_GET['nocache']) && $_GET['nocache'] && file_exists($tmp_name) )
      @unlink($tmp_name);
   
   if ( @file_exists($tmp_name) && !isset($_GET['nocache']) )
   {   
      $gmdate_mod = gmdate("D, d M Y H:i:s", filemtime($tmp_name));

      if(!strstr($gmdate_mod, "GMT")) {
         $gmdate_mod .= " GMT";
      }

      if (isset($_SERVER["HTTP_IF_MODIFIED_SINCE"])) {

         $if_modified_since = preg_replace ("/;.*$/", "", $_SERVER["HTTP_IF_MODIFIED_SINCE"]);
         if ($if_modified_since == $gmdate_mod) {
             header("HTTP/1.1 304 Not Modified");
             die();
         }
      }
      
      $fileSize = filesize($tmp_name);
      header ("Content-Type: image/jpeg");
      header ('Accept-Ranges: bytes');
      header ('Last-Modified: ' . $gmdate_mod);
      header ('Content-Length: ' . $fileSize);
      header ('Cache-Control: max-age=9999, must-revalidate');
      header ('Expires: ' . $gmdate_mod);
      echo @file_get_contents($tmp_name);
     
      exit;
   }
   
      
   $img_id = $_GET['get_image'];

   /*   
   if (!$img_id) 
      return;
   */
   
   if ($img_id && !is_numeric($img_id))
   {
      if (!preg_match('/^http:\/\//', $img_id))
      {
         if ( preg_match('/\/wp-content\//', $img_id) )
         {
            $img_id = site_url()."/".$img_id;
         }
         else
         {
            $img_id = get_template_directory_uri()."/".$img_id;
		 }
      }
      //echo $img_id; exit;
      $orig_image = array(
         $img_id
      );
   }
   else
   {
      $orig_image = wp_get_attachment_image_src($img_id, 'large' );
      if (!$orig_image[0])
      {
         if (isset($_GET['debug']) && $_GET['debug'])
            die("Image $img_id not found");   
         $orig_image = default_attachment();
      }
   }
   
   $src_image = $orig_image[0];
   $src_image = ABSPATH.str_replace(site_url(), '', $src_image);
   $src_image = preg_replace('/\/{1,}/', '/', $src_image);
   
   //echo $src_image;
   
   if (!file_exists($src_image))
   { 
	  if ( isset($_GET['debug']) && $_GET['debug'] )
         die('Image '.$src_image.' not found');
      $orig_image = default_attachment();
      $src_image = $orig_image[0];
      $src_image = ABSPATH.str_replace(site_url(), '', $src_image);      
   }
   
   // get mime type
   $mime_type = 'image/png';
   $fileDetails = pathinfo($src_image);
   $ext = strtolower($fileDetails["extension"]);
   $types = array(
       'jpg'  => 'image/jpeg',
       'jpeg' => 'image/jpeg',
       'png'  => 'image/png',
       'gif'  => 'image/gif'
   );

   if (strlen($ext) && strlen($types[$ext])) {
      $mime_type = $types[$ext];
   } 
   
   $is_no_image = 0;
   if ( preg_match('/noimage\.jpg$/', $src_image) )
      $is_no_image = 1;
   
   //echo "resize $is_no_image"; exit;
   
   // open image
   $mime_type = strtolower($mime_type);
   if (stristr ($mime_type, 'gif'))
   {
      $image = imagecreatefromgif($src_image);
   }
   elseif (stristr($mime_type, 'jpeg'))
   {
      $image = imagecreatefromjpeg($src_image);
   }
   elseif (stristr ($mime_type, 'png'))
   {
      $image = imagecreatefrompng($src_image);
   }
   
   $src_image_w = imagesx($image);
   $src_image_h = imagesy($image);
   
   if (!$image)
      die("Image not created");
    
   // define function to ouput image  
   switch ($mime_type)
   {
     case 'image/gif':
         $quality = 100;
         $image_func = "imagegif";
         break;
   
     case 'image/jpeg':
         $quality = 90;
         $image_func = "imagejpeg";
         break;
     
     default :
         $quality = 9;
         $image_func = "imagepng";
   }
      
   // define resize image params
   $w = (isset($_GET["w"]) ? $_GET["w"] : 0);
   $h = (isset($_GET["h"]) ? $_GET["h"] : 0);
   
   if ($w >= LARGE_SIZE && !isset($_GET['no_limits']))
   {
      if (!$h && $src_image_w < $src_image_h)
      {
         $h = $w;
         $w = 0;
      }
      if (
         ($w > $src_image_w && $w)
         ||
         ($h > $src_image_h && $h)
      )
      {
         $h = $src_image_h;
         $w = $src_image_w;
      }
   }
   
   if ($w && !$h)
   {
      $k = $src_image_w / $src_image_h;
      $h = $w / $k;
   }

   if (!$w && $h)
   {
      $k = $src_image_w / $src_image_h;
      $w = $h * $k;
   }
   
   $w = intval($w);
   $h = intval($h);
   $src_w = $src_image_w;
   $src_h = $src_image_h;
   $src_x = 0;
   $src_y = 0;
   // resize image
   if ($w>0 && $h>0)
   {
      // where should we start cropping
      $new_w = $w;
      $new_h = $h;
      if ( ($src_w / $src_h) <= ($w / $h) )
      {
         $new_h = intval( $w / $src_w * $src_h );
         $src_y = intval( ($new_h - $h) / 2 );
      }
      else
      {
         $new_w = intval( $h * $src_w / $src_h );
         $src_x = intval( ($new_w - $w) / 2 );
      }
      
      $old_img = $image;
      $image = imagecreatetruecolor($w, $h);
      
      // set transparency
      if ($mime_type == 'image/png')
      {
         imagealphablending($image, false);
         imagesavealpha($image, true);
         $trans_colour = imagecolorallocatealpha($image, 0, 0, 0, 127);
         imagefill($image, 0, 0, $trans_colour);
      }
      
      // copy resampled image
      $tmp_im = imagecreatetruecolor($new_w, $new_h);

      // set transparency
      if ($mime_type == 'image/png')
      {
         imagealphablending($tmp_im, false);
         imagesavealpha($tmp_im, true);
         $trans_colour = imagecolorallocatealpha($tmp_im, 0, 0, 0, 127);
         imagefill($tmp_im, 0, 0, $trans_colour);
      }
      
      imagecopyresampled(
         $tmp_im, $old_img,
         0, 0,
         0, 0,
         $new_w, $new_h,
         $src_w, $src_h
      );
      imagedestroy($old_img);
      
      imagecopy(
         $image, $tmp_im,
         0, 0,
         $src_x, $src_y,
         $w, $h
      );
      
      @imagedestroy($tmp_im);
   }
   
   if ( !$is_no_image )
      @$image_func($image, $tmp_name, $quality);
      
   cleanCache();
   // output image
   Header("Content-Type: ".$mime_type);
   $image_func($image, null, $quality);
   @imagedestroy($image);
   

   
   exit;