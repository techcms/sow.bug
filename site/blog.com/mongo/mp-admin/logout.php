<?php
require_once(dirname(dirname(__FILE__)).'/mp-includes/includes.php');
$mp = mongopress_load_mp();
$options = $mp->options();
if(isset($_SERVER['HTTP_REFERER'])){
    $referrer = $_SERVER['HTTP_REFERER'];
}else{
    $referrer = false;
} $referrer_url = $options['full_url'].'admin/';
if($referrer = $referrer_url) { $referrer = false; }
?>
<!doctype html>
<html class="" lang="en">
    <head><title><?php _e('Logging-out and re-directing...'); ?></title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script>
        <script>
            var mp_root_url = '<?php echo 'http://'.$_SERVER['HTTP_HOST'].$options['root_url']; ?>';
            $(document).ready(function(){
                jQuery.ajax({
                    url:mp_root_url+'mp-includes/ajax/logout.php',
                    type: "POST",
                    dataType: 'json',
                    success: function(result){
                        //-> FOR DEBUGGING: return; // temp - stay on logout
                        <?php if($referrer){ ?>
                            window.location = '<?php echo $referrer; ?>';
                        <?php }else{ ?>
                            window.location = result.referrer;
                        <?php } ?>
                    }
                });
            });
        </script>
    </head>
    <body></body>
</html>
