<?php
/**
 * @package	HikaShop for Joomla!
 * @version	4.0.1
 * @author	hikashop.com
 * @copyright	(C) 2010-2018 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="hikashop_product_contact_<?php echo hikaInput::get()->getInt('cid');?>_page" class="hikashop_product_contact_page">
	<fieldset>
		<div class="" style="float:left">
			<h1><?php
if(!empty($this->product->product_id)) {
	$doc = JFactory::getDocument();
	$doc->setMetaData( 'robots', 'noindex' );
	if(!empty($this->product->images)) {
		$image = reset($this->product->images);
		$img = $this->imageHelper->getThumbnail($image->file_path, array(50,50), array('default' => true), true);
		if($img->success) {
			echo '<img src="'.$img->url.'" alt="" style="vertical-align:middle"/> ';
		}
	}
	echo @$this->product->product_name;
} else {
	echo @$this->title;
}
			?></h1>
		</div>
		<div class="toolbar" id="toolbar" style="float: right;">
			<button class="hikabtn hikabtn-success" type="button" onclick="checkFields();"><i class="fa fa-check"></i> <?php echo JText::_('OK'); ?></button>
<?php if(hikaInput::get()->getCmd('tmpl', '') != 'component') { ?>
			<button class="hikabtn hikabtn-danger" type="button" onclick="history.back();"><i class="fa fa-times"></i> <?php echo JText::_('HIKA_CANCEL'); ?></button>
<?php } ?>
		</div>
		<div style="clear:both"></div>
	</fieldset>
<?php
	$formData = hikaInput::get()->getVar('formData','');
	if(empty($formData))
		$formData = new stdClass();
	if(isset($this->element->name) && !isset($formData->name)){
		$formData->name = $this->element->name;
	}
	if(isset($this->element->email) && !isset($formData->email)){
		$formData->email = $this->element->email;
	}
?>
	<form action="<?php echo hikashop_completeLink('product'); ?>" id="hikashop_contact_form" name="hikashop_contact_form" method="post">
		<dl>
			<dt id="hikashop_contact_name_name" class="hikashop_contact_item_name">
				<label for="data[contact][name]"><?php echo JText::_( 'HIKA_USER_NAME' ); ?> <span class="hikashop_field_required_label">*</span></label>
			</dt>
			<dd id="hikashop_contact_value_name" class="hikashop_contact_item_value">
				<input id="hikashop_contact_name" type="text" name="data[contact][name]" size="40" value="<?php echo $this->escape(@$formData->name);?>" />
			</dd>
			<dt id="hikashop_contact_name_email" class="hikashop_contact_item_name">
				<label for="data[contact][email]"><?php echo JText::_( 'HIKA_EMAIL' ); ?> <span class="hikashop_field_required_label">*</span></label>
			</dt>
			<dd id="hikashop_contact_value_email" class="hikashop_contact_item_value">
				<input id="hikashop_contact_email" type="text" name="data[contact][email]" size="40" value="<?php echo $this->escape(@$formData->email);?>" />
			</dd>
<?php
	if(!empty($this->contactFields)){
?>
		</dl>
<?php
		foreach ($this->contactFields as $fieldName => $oneExtraField) {
			$itemData = @$formData->$fieldName;
?>
		<dl id="hikashop_contact_<?php echo $oneExtraField->field_namekey; ?>">
			<dt id="hikashop_contact_item_name_<?php echo $oneExtraField->field_id;?>" class="hikashop_contact_item_name">
				<label for="data[contact][<?php echo $oneExtraField->field_namekey; ?>]">
					<?php echo $this->fieldsClass->getFieldName($oneExtraField, true);?>
				</label>
			</dt>
			<dd id="hikashop_contact_item_value_<?php echo $oneExtraField->field_id;?>" class="hikasho_contact_item_value"><?php
					$onWhat='onchange';
					if($oneExtraField->field_type=='radio')
						$onWhat='onclick';
					$oneExtraField->product_id = hikaInput::get()->getInt('cid');
					echo $this->fieldsClass->display(
						$oneExtraField,$itemData,
						'data[contact]['.$oneExtraField->field_namekey.']',
						false,
						' '.$onWhat.'="hikashopToggleFields(this.value,\''.$fieldName.'\',\'contact\',0);"',
						false,
						null,
						null,
						false
					);
				?>
			</dd>
		</dl>
<?php
		}
?>
		<dl>
<?php
	}
	if(!empty($this->extra_data['fields'])) {
		foreach($this->extra_data['fields'] as $key => $value) {
?>			<dt id="hikashop_contact_<?php echo $key; ?>_email" class="hikashop_contact_item_name">
				<label><?php echo JText::_($value['label']); ?></label>
			</dt>
			<dd id="hikashop_contact_<?php echo $key; ?>_email" class="hikashop_contact_item_value">
				<?php echo $value['content']; ?>
			</dd>
<?php
		}
	}
?>
			<dt id="hikashop_contact_name_altbody" class="hikashop_contact_item_name">
				<label for="data[contact][altbody]"><?php echo JText::_( 'ADDITIONAL_INFORMATION' ); ?> <span class="hikashop_field_required_label">*</span></label>
			</dt>
			<dd id="hikashop_contact_value_altbody" class="hikashop_contact_item_value">
				<textarea id="hikashop_contact_altbody" cols="60" rows="10" name="data[contact][altbody]" style="width:100%;"><?php
					if(isset($formData->altbody)) echo $formData->altbody;
				?></textarea>
			</dd>
		</dl>
		<input type="hidden" name="data[contact][product_id]" value="<?php echo hikaInput::get()->getInt('cid');?>" />
		<input type="hidden" name="cid" value="<?php echo hikaInput::get()->getInt('cid');?>" />
		<input type="hidden" name="option" value="<?php echo HIKASHOP_COMPONENT; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="ctrl" value="product" />
		<input type="hidden" name="redirect_url" value="<?php $redirect_url = hikaInput::get()->getString('redirect_url', ''); echo $this->escape($redirect_url); ?>" />
<?php
	if(!empty($this->extra_data['hidden'])) {
		foreach($this->extra_data['hidden'] as $key => $value) {
			echo "\t\t" . '<input type="hidden" name="'.$this->escape($key).'" value="'.$this->escape($value).'" />' . "\r\n";
		}
	}
	if(hikaInput::get()->getVar('tmpl', '') == 'component') {
?>		<input type="hidden" name="tmpl" value="component" />
<?php
	}
	echo JHTML::_( 'form.token' );
?>
	</form>
</div>
