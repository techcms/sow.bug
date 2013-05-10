<?php
class Bootstrap extends \Sow\sys\Bootstrap
{
    //为本地目录lib下的文件注册空间名
    public function _initDefine() {
        define( 'WP_USE_THEMES', true );
        define( 'AUTH_KEY',         'kZh+|=P_1/wO2bON3bO?a}GN%b=1(<QYoLwW}V]o~a 9PVn4cn|[ ZecsP&,Du0u' );
        define( 'SECURE_AUTH_KEY',  'A9+XpAAclXO2``)c+m832xN{3L0pmYKH/EfoCGx|zRc+.0UK-Bo0^F._+ipWe/S-' );
        define( 'LOGGED_IN_KEY',    '[ls22DSIGu94>n<yjmAbBPAoIi,qQEW@,1|EWjSy4@nux+-#874G6 ;2qz,^rO+Z' );
        define( 'NONCE_KEY',        '1kfURPmt|]yO>VQvRT?:a9Y;n=gbj_X4(yS L^K? /yU{-H^a.C  &xfsX$|xakx' );
        define( 'AUTH_SALT',        '{-;Z0Euq|mgkTp3($8_x,{sI7_hgqo%R=e;#6-LRL+E!v!J7K8XQcV_(5B=gmJkN' );
        define( 'SECURE_AUTH_SALT', '[_krGZ>mkXEN#).j:)I_pArzed5WDYs$b%VD{a?o&fhM5/1RGdi]`@=X/%<`^^3>' );
        define( 'LOGGED_IN_SALT',   '`.HFy]~o=z,0^eHgg*wP%DV 901%!STiJ$Kj^hGE,Al_,C=~DRY9#*{XV$k5|POu' );
        define( 'NONCE_SALT',       ']!Yl``k!F&R9 1tQ%oA8s[kkiVsd ^kby+0SE9ufw1B.C<e+MJN|7=GpyIxx944?' );
        define( 'WPLANG', 'zh_CN' );
        define( 'WP_DEBUG', false );
        define( 'ABSPATH', dirname( __FILE__ ) . '/' );
        define( 'WPINC', 'wp-includes' );

    }
    public function _initIniSet() {
        @ini_set( 'magic_quotes_runtime', 0 );
        @ini_set( 'magic_quotes_sybase',  0 );

    }


}
