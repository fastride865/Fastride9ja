<div class="modal fade" id="examplePositionSidebar" aria-labelledby="examplePositionSidebar"
     role="dialog" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-simple modal-sidebar modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if(!empty($info_setting) && $info_setting->$page_name != ""): ?>
                    <?php echo translateLocalContent($info_setting->$page_name, strtolower(App::getLocale())); ?>

                <?php else: ?>
                    <p>No information content found...</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div><?php /**PATH /home/msprojectsappori/public_html/multi-service-v1/resources/views/merchant/shared/info-setting.blade.php ENDPATH**/ ?>