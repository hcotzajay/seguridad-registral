<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <link rel="icon" type="image/png" href="<?php echo e(logo_icon()); ?>">

    <title><?php echo e(env('APP_NAME')); ?></title>
</head>
<body>
<div id="app">
    <?php (session_start()); ?>

    <?php if(!isset($_SESSION['id_usuario'])): ?>
        <login
            app-name="<?php echo e(env('APP_NAME')); ?>"
            name-server-aplicaciones="<?php echo e(env('NAME_SERVER_APLICACIONES')); ?>"
            app-env-color="<?php echo e(env('APP_ENV_COLOR')); ?>"
            logo="<?php echo e(logo_login()); ?>"
        ></login>
    <?php else: ?>
        <work-area
            app-name="<?php echo e(env('APP_NAME')); ?>"
            name-server-aplicaciones="<?php echo e(env('NAME_SERVER_APLICACIONES')); ?>"
            app-env-color="<?php echo e(env('APP_ENV_COLOR')); ?>"
            logo="<?php echo e(logo_work_area()); ?>"
        ></work-area>
    <?php endif; ?>
    <vue-progress-bar></vue-progress-bar>
</div>
<script src="<?php echo e(mix('js/app.js')); ?>"></script>
</body>
</html>
<?php /**PATH /var/www/seguridad_registral/resources/views/spa.blade.php ENDPATH**/ ?>