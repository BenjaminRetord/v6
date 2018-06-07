<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Note. It is important to remove spaces between elements.
?>

<style>
    .dropdown-header {
        display: block;
        padding: 3px 20px;
        font-size: 12px;
        line-height: 1.42857143;
        color: #777;
        white-space: nowrap;
    }

    .dropdown-menu-right {
        right: 0;
        left: auto;
    }

    #userDropdownIcon {
        background-color: #<?php echo $primary_color; ?>;
        border: solid 1px white;
        color: #<?php echo $secondary_color; ?>;
    }

    #userDropdownIcon:hover,
    #userDropdownIcon.active {
        border: 1px solid;
        box-shadow: inset 0 0 20px rgba(255, 255, 255, .5), 0 0 20px rgba(255, 255, 255, .2);
        outline-color: rgba(255, 255, 255, 0);
        outline-offset: 15px;
        background-color: #<?php echo $secondary_color; ?>;
        color: #fff;
    }

    #userDropdownMenu li>a:hover,
    #userDropdownMenu .active>a {
        background: #<?php echo $secondary_color; ?>; none;
    }
</style>

<!-- Button which opens up the dropdown menu. -->
<div class='dropdown' id="userDropdown" style="float: right;">
    <div class="em-user-dropdown-button" id="userDropdownLabel" aria-haspopup="true" aria-expanded="false">
        <i class="big circular user outline icon" id="userDropdownIcon"></i>
    </div>
    <ul class="dropdown-menu dropdown-menu-right" id="userDropdownMenu" aria-labelledby="userDropdownLabel">
        <li class="dropdown-header"><?php echo $user->name; ?></li>
        <li class="dropdown-header"><?php echo $user->email; ?></li>
        <?php if (!empty($list)) :?>
            <li role="separator" class="divider"></li>
            <?php foreach ($list as $i => $item) :?>
                <li class="<?php echo ($item->id == $active_id)?'active':''; ?>"><a href="<?php echo $item->flink ?>"><?php echo $item->title; ?></a></li>
            <?php endforeach; ?>
        <?php endif; ?>
        <li role="separator" class="divider"></li>
        <?php
            $userToken = JSession::getFormToken();
            echo '<li><a href="index.php?option=com_users&task=user.logout&' . $userToken . '=1">'.JText::_('LOGOUT').'</a></li>';
        ?>
    </ul>
</div>

<script>
    // This counters all of the issues linked to using BootstrapJS.
    document.getElementById('userDropdownLabel').onclick = function(e) {
        e.stopPropagation();
        var dropdown = document.getElementById('userDropdown');
        var icon = document.getElementById('userDropdownIcon');

        if (dropdown.hasClass('open')) {
            dropdown.removeClass('open');
            icon.removeClass('active');
        } else {
            dropdown.addClass('open');
            icon.addClass('active');
        }
    };

    document.onclick = function (e) {
        e.stopPropagation();
        var dropdown = document.getElementById('userDropdown');
        var icon = document.getElementById('userDropdownIcon');

        if (dropdown.hasClass('open')) {
            dropdown.removeClass('open');
            icon.removeClass('active');
        }
    };
</script>

