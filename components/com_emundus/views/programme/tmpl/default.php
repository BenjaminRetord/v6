<?php
defined('_JEXEC') or die('Restricted access');

JHTML::stylesheet('media/com_emundus/css/emundus.css' );

?>


    <?php if (empty($this->campaign)) { ?>
            <div class="alert alert-warning"><?php echo JText::_('NO_RESULT_FOUND') ?></div>
    <?php } else { ?>
            <h2 class="title"><?php echo $this->campaign['label'];?></h2>
                <div <?php if (!empty($this->com_emundus_programme_progdesc_class)) { echo "class=\"".$this->com_emundus_programme_progdesc_class."\""; } ?>>
                    <p> <?php if ($this->com_emundus_programme_showprogramme) { echo $this->campaign['notes']; }?> </p>

                    <?php if($this->com_emundus_programme_showlink) :?>
                        <p class="<?php echo !empty($this->com_emundus_programme_showlink_class) ? $this->com_emundus_programme_showlink_class : "";?>">
                            <a href="<?php echo $this->com_emundus_programme_showlink ;?>"><?php echo JText::_('MORE_INFO');?></a>
                        </p>
                    <?php endif; ?>
                    
                </div>
                 <div <?php if (!empty($this->com_emundus_programme_campdesc_class)) { echo "class=\"".$this->com_emundus_programme_campdesc_class."\""; } ?>>
                    <p> <?php if ($this->com_emundus_programme_showcampaign) {  echo $this->campaign['description']; } ?></p>
                </div>

            <fieldset class="apply-now-small">
                <legend><?php echo JText::_('CAMPAIGN_PERIOD'); ?></legend>
                <strong><i class="icon-time"></i> <?php echo JText::_('CAMPAIGN_START_DATE'); ?>:</strong>
                <?php echo date('d/m/Y H:i', strtotime($this->campaign['start_date'])); ?><br>
                <strong><i class="icon-time"></i> <?php echo JText::_('CAMPAIGN_END_DATE'); ?>:</strong>
                <?php echo date('d/m/Y H:i', strtotime($this->campaign['end_date'])); ?>
            </fieldset>

<?php } ?>

<script>
    jQuery(document).ready(function() {
        var titre = "<?php echo $this->campaign['label']; ?>";
        jQuery(document).prop('title', titre);
    });
</script>