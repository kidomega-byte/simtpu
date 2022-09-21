<?php if (! $__env->hasRenderedOnce('4632e4ab-1ece-4be9-8dc1-f0b40a93f985')): $__env->markAsRenderedOnce('4632e4ab-1ece-4be9-8dc1-f0b40a93f985'); ?>
<?php $__env->startPush('plugin-css'); ?>
    <link rel="stylesheet" href="<?php echo mix('/plugins/fileinput/bootstrap-fileinput.min.css', '/assets/vendor/boilerplate'); ?>">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('plugin-js'); ?>
    <script src="<?php echo mix('/plugins/fileinput/bootstrap-fileinput.min.js', '/assets/vendor/boilerplate'); ?>"></script>
    <script src="/assets/vendor/boilerplate/plugins/fileinput/themes/fas/theme.min.js"></script>
    <script>$.fn.fileinput.defaults = $.extend({}, $.fn.fileinput.defaults, $.fn.fileinputThemes.fas);</script>
<?php if(App::getLocale() !== 'en'): ?>
    <script src="/assets/vendor/boilerplate/plugins/fileinput/locales/<?php echo e(App::getLocale()); ?>.js"></script>
    <script>$.fn.fileinput.defaults.language='<?php echo e(App::getLocale()); ?>';</script>
<?php endif; ?>
    <script>registerAsset('fileinput');</script>
<?php $__env->stopPush(); ?>
<?php endif; ?><?php /**PATH /Users/ilhamtaufiq/www/simtpuv2/vendor/sebastienheyd/boilerplate/src/resources/views/load/fileinput.blade.php ENDPATH**/ ?>