<?php
/**
 * Created by PhpStorm.
 * User: imacemundus
 * Date: 2018-12-20
 * Time: 14:04
 */

?>


<div class="user-list-menu">
    <div class="content">
        <ul>
            <?php if (!empty($list)) :?>
                <?php foreach ($list as $i => $item) :?>
                    <li class="<?php echo ($item->id)?'menu-item':''; echo ($item->id == $active_id)?' menu-item-active':''; ?>"><a href="<?php echo $item->flink ?>"><?php echo $item->title; ?></a></li>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php
            $userToken = JSession::getFormToken();
            echo '<li class="user-logout"><a href="index.php?option=com_users&task=user.logout&' . $userToken . '=1">'.JText::_('LOGOUT').'</a></li>';
            ?>
        </ul>
    </div>
</div>