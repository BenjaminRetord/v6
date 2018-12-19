<?php
/**
 * @package   Joomla.Site
 * @subpackage  eMundus
 * @copyright Copyright (C) 2018 emundus.fr. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;
//var_dump($user->fnums); echo "<hr>"; var_dump($applications);
echo $description;
?>

<?php if (!empty($applications)) : ?>
    <div class="<?php echo $moduleclass_sfx ?>">

        <div class="row" id="em-applications">
            <div class="col-md-8">
            </div>

            <div class="col-md-2">
                <strong>Session réservée</strong>
            </div>

            <div class="col-md-2">
                <strong><?php echo JText::_('STATUS'); ?></strong>
            </div>
        </div>

        <?php foreach($applications as $application) : ?>
            <div class="row" id="row<?php echo $application->fnum; ?>">
                <div class="col-md-8 main-page-application-title">
                    <p class="">
                        <a href="<?php echo JRoute::_(JURI::base().'index.php?option=com_emundus&task=openfile&fnum='.$application->fnum.'&Itemid='.$Itemid.'#em-panel'); ?>" >
                            <?php
                            echo (!empty($user->fnum) && $application->fnum == $user->fnum)?'<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> <b>'.$application->label.'</b>':$application->label;
                            ?>
                        </a>
                </div>

                <div class="col-md-2 main-page-file-progress">
                    <div class="main-page-file-progress-label">

                            <?php
                                setlocale(LC_ALL, 'fr_FR.utf8');
                                $start_day = date('d',strtotime($application->date_start));
                                $end_day = date('d',strtotime($application->date_end));
                                $start_month = date('m',strtotime($application->date_start));
                                $end_month = date('m',strtotime($application->date_end));
                                $start_year = date('y',strtotime($application->date_start));
                                $end_year = date('y',strtotime($application->date_end));

    
                                if ($start_day == $end_day && $start_month == $end_month && $start_year == $end_year)
                                    echo strftime('%e',strtotime($application->date_start)) . " " . strftime('%B',strtotime($application->date_end)) . " " . date('Y',strtotime($application->date_end));
                                elseif ($start_month == $end_month && $start_year == $end_year)
                                    echo strftime('%e',strtotime($application->date_start)) . " au " . strftime('%e',strtotime($application->date_end)) . " " . strftime('%B',strtotime($application->date_end)) . " " . date('Y',strtotime($application->date_end));
                                elseif ($start_month != $end_month && $start_year == $end_year)
                                    echo strftime('%e',strtotime($application->date_start)) . " " . strftime('%B',strtotime($application->date_start)) . " au " . strftime('%e',strtotime($application->date_end)) . " " . strftime('%B',strtotime($application->date_end)) . " " . date('Y',strtotime($application->date_end));
                                elseif (($start_month != $end_month && $start_year != $end_year) || ($start_month == $end_month && $start_year != $end_year))
                                    echo strftime('%e',strtotime($application->date_start)) . " " . strftime('%B',strtotime($application->date_start)) . " " . date('Y',strtotime($application->date_start)) . " au " . strftime('%e',strtotime($application->date_end)) . " " . strftime('%B',strtotime($application->date_end)) . " " . date('Y',strtotime($application->date_end));
                            ?>
                    </div>
                </div>

                <div class="col-md-2 main-page-file-progress">
                    <div class="main-page-file-progress-label">

                        <span class="label label-<?php echo $application->class; ?>">
                            <?php echo $application->value; ?>
                        </span>
                    </div>
                </div>


            </div>
            <hr>
        <?php endforeach;  ?>
    </div>
<?php else :
    echo JText::_('NO_FILE');
    ?>
<?php endif; ?>

<script type="text/javascript">
    function deletefile(fnum){
        if (confirm("<?php echo JText::_('CONFIRM_DELETE_FILE'); ?>")) {
            document.location.href="<?php echo JRoute::_(JURI::base().'index.php?option=com_emundus&task=deletefile&fnum='); ?>"+fnum;
        }
    }

</script>
