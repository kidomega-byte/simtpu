<?php if (! $__env->hasRenderedOnce('8d30e549-638c-4b3f-804b-d92f57c5a1b5')): $__env->markAsRenderedOnce('8d30e549-638c-4b3f-804b-d92f57c5a1b5'); ?>
<?php
    $default = [
        'mode/xml/xml.js',
        'mode/css/css.js',
        'mode/javascript/javascript.js',
        'mode/htmlmixed/htmlmixed.js',
        'addon/edit/matchtags.js',
        'addon/edit/matchbrackets.js',
        'addon/edit/closetag.js',
        'addon/fold/xml-fold.js',
        'addon/selection/active-line.js'
    ];

    if (isset($js) && is_array($js)) {
        $default = array_merge($default, $js);
    }

    $js = array_unique($default);
?>
<?php $__env->startComponent('boilerplate::minify'); ?>
    <script>
        loadStylesheet('<?php echo e(mix('/plugins/codemirror/codemirror.min.css', '/assets/vendor/boilerplate')); ?>', () => {
            loadStylesheet('/assets/vendor/boilerplate/plugins/codemirror/theme/<?php echo e($theme ?? 'storm'); ?>.css', () => {
                loadScript('<?php echo e(mix('/plugins/codemirror/jquery.codemirror.min.js', '/assets/vendor/boilerplate')); ?>', () => {
                    <?php if(!empty($js)): ?>
                        <?php $__currentLoopData = $js; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $script): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            loadScript("/assets/vendor/boilerplate/plugins/codemirror/<?php echo e($script); ?>", () => {
                            <?php if($loop->last): ?>
                                registerAsset('CodeMirror', () => {
                                    $.fn.codemirror.defaults.theme = '<?php echo e($theme ?? 'storm'); ?>';
                                });
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $js; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $script): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            });
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                });
            });
        });
    </script>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php /**PATH /Users/ilhamtaufiq/www/simtpuv2/vendor/sebastienheyd/boilerplate/src/resources/views/load/async/codemirror.blade.php ENDPATH**/ ?>