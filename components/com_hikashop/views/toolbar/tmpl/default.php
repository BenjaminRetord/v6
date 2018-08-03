<?php
/**
 * @package	HikaShop for Joomla!
 * @version	3.5.1
 * @author	hikashop.com
 * @copyright	(C) 2010-2018 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if(!empty($this->title)) { ?>
<div class="header hikashop_header_title">
	<h1>
	<?php echo $this->title; ?>
	</h1>
</div>
<?php
}

$toolbar_classname = $this->config->get('front_toolbar_btn_classname', 'hikabtn');
if(empty($toolbar_classname))
	$toolbar_classname = 'hikabtn';

?>
<div class="hika_toolbar toolbar">
	<div class="hika_toolbar_btn hika_btn_32">
<?php
foreach($this->data as $key => $tool) {
	if(empty($tool['url']) && !empty($tool['sep'])) {
		echo '<div class="sep"></div>';
		continue;
	}

	$content = '';
	if(!empty($tool['icon'])) { $content .= '<span class="btnIcon icon-32-'.$tool['icon'].'"></span>'; }
	if(!empty($tool['name'])) { $content .= '<span class="btnName">' . $tool['name'] . '</span>'; }

	if(!empty($tool['url']) || !empty($tool['javascript'])) {
		if(empty($tool['popup'])) {
			if(empty($tool['url']))
				$tool['url'] = '#';
			if(empty($tool['linkattribs']))
				$tool['linkattribs'] = '';
			if(!empty($tool['javascript']))
				$tool['linkattribs'].= ' onclick="' . $tool['javascript'] . '"';
			echo '<a class="'.$toolbar_classname.'" href="'.$tool['url'].'" '.$tool['linkattribs'].'>' . $content . '</a>';
		} else {
			$attr = $this->popupHelper->getAttr(@$tool['linkattribs'], 'hikabtn');
			echo $this->popupHelper->display(
				$content,
				@$tool['name'],
				$tool['url'],
				$tool['popup']['id'],
				$tool['popup']['width'],
				$tool['popup']['height'],
				$attr, '', 'link'
			);
		}
	}elseif(!empty($tool['dropdown'])) {
		if(is_array($tool['dropdown'])) {
			$tool['dropdown']['options']['main_class'] = $toolbar_classname;
			echo $this->dropdownHelper->display(
				$tool['dropdown']['label'],
				$tool['dropdown']['data'],
				$tool['dropdown']['options']
			);
		}else {
			echo $tool['dropdown'];
		}
	}else {
		echo '<div class="'.$toolbar_classname.'">' . $content . '</div>';
	}
	unset($content);
}
?>
	</div>
	<div style="clear:both"></div>
</div>
