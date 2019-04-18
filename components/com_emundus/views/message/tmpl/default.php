﻿<?php
/**
 * @package    Joomla
 * @subpackage emundus
 * @link       http://www.emundus.fr
 * @license    GNU/GPL
 * @author     Hugo Moracchini
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$current_user = JFactory::getUser();
$itemid 	= JRequest::getVar('Itemid', null, 'GET', 'none',0);
$view 		= JRequest::getVar('view', null, 'GET', 'none',0);
$task 		= JRequest::getVar('task', null, 'GET', 'none',0);
$tmpl 		= JRequest::getVar('tmpl', null, 'GET', 'none',0);

// Load the WYSIWYG editor used to edit the mail body.
$editor = JFactory::getEditor('tinymce');
$mail_body = $editor->display('mail_body', '[NAME], ', '100%', '400', '20', '20', false, 'mail_body', null, null, array('mode' => 'simple'));

$m_messages = new EmundusModelMessages();

// load all of the available messages, categories (to sort messages),attachments, letters.
$message_categories = $m_messages->getAllCategories();
$message_templates 	= $m_messages->getAllMessages();
$setup_attachments 	= $m_messages->getAttachments();
$setup_letters 		= $m_messages->getLetters();

$email_list = array();

?>

<!-- WYSIWYG Editor -->
<link rel="stylesheet" href="/components/com_jce/editor/libraries/css/editor.min.css" type="text/css">
<script data-cfasync="false" type="text/javascript" src="/media/editors/tinymce/tinymce.min.js"></script>
<script data-cfasync="false" type="text/javascript" src="/media/editors/tinymce/js/tinymce.min.js"></script>
<script data-cfasync="false" type="text/javascript">tinyMCE.init({menubar:false,statusbar: false})</script>

<div id="em-email-messages"></div>

<div class="em-modal-sending-emails" id="em-modal-sending-emails">
    <div id="em-sending-email-caption"><?php echo JText::_('SENDING_EMAILS') ;?></div>
    <img class="em-sending-email-img" id="em-sending-email-img" src="/images/emundus/sending-email.gif">
</div>

<form id="emailForm"  name="emailForm" style="padding:0px 15px;">
    <div class="em_email_block" id="em_email_block">

        <div class="form-inline row">

            <!-- Dropdown to select the email categories used. -->
            <div class="form-group col-md-6 col-sm-6">
                <label for="select_category" ><?php echo JText::_('SELECT_CATEGORY'); ?></label>
                <select name="select_category" class="form-control" onChange="setCategory(this);">
                    <?php if (!$message_categories) :?>
                        <option value="%"> <?php echo JText::_('NO_CATEGORIES_FOUND'); ?> </option>
                    <?php else: ?>
                        <option value="%"> <?php echo JText::_('SELECT_CATEGORY'); ?> </option>
                        <?php foreach ($message_categories as $message_category) :?>
                            <?php if (!empty($message_category)) :?>
                                <option value="<?php echo $message_category; ?>"> <?php echo $message_category; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Dropdown to select the email template used. -->
            <div class="form-group col-md-6 col-sm-6">
                <label for="select_template" ><?php echo JText::_('SELECT_TEMPLATE'); ?></label>
                <select name="select_template" id="message_template" class="form-control" onChange="getTemplate(this);">
                    <?php if (!$message_templates) :?>
                        <option value="%"> <?php echo JText::_('NO_TEMPLATES_FOUND'); ?> </option>
                    <?php else: ?>
                        <option value="%"> <?php echo JText::_('SELECT_TEMPLATE'); ?> </option>
                        <?php foreach ($message_templates as $message_template) :?>
                            <option value="<?php echo $message_template->id; ?>"> <?php echo $message_template->subject; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <input name="mail_from_id" type="hidden" class="inputbox" id="mail_from_id" value="<?php echo $current_user->id; ?>" /><br>
        <input name="fnums" type="hidden" class="inputbox" id="fnums" value="<?php echo implode(',',$this->fnums); ?>" />

        <!-- Add current user to Bcc -->
        <div class="checkbox">
            <label>
                <input type="checkbox" id="sendUserACopy"> <?php echo JText::_('SEND_COPY_TO_CURRENT_USER'); ?>
            </label>
        </div>

        <div class="form-group">
            <!-- List of users / their emails, gotten from the fnums selected. -->
            <div class="well well-sm" id="em-recipitents">
                <span class='label label-grey'><?php echo JText::_('TO'); ?>:</span>
                <?php foreach ($this->users as $user) : ?>

                    <?php if (!empty($user['email']) && !in_array($user['email'], $email_list)) : ?>
                        <?php $email_list[] = $user['email']; ?>
                        <span class="label label-grey em-email-label">
							<?php echo $user['name'].' <em>&lt;'.$user['email'].'&gt;</em>'; ?>
						</span>
                        <input type="hidden" name="ud[]" id="ud" value="<?php echo $user['id'] ?>"/>
                    <?php endif; ?>

                <?php endforeach; ?>
            </div>
        </div>
        <div class="form-group">
            <div class="inputbox input-xlarge form-control form-inline">
                <span class='label label-grey' for="mail_from" ><?php echo JText::_('FROM'); ?>:</span>
                <div class="form-group" style="display:inline-block !important;" id="mail_from_name" contenteditable="true"><?php echo $current_user->name; ?> </div>
                <strong> <div class="form-group" style="display:inline-block !important;" id="mail_from" contenteditable="true"><?php echo $current_user->email; ?></div> </strong>
            </div>
        </div>
        <div class="form-group">
            <div class="inputbox input-xlarge form-control form-inline">
                <span class='label label-grey' for="mail_from" ><?php echo JText::_('SUBJECT'); ?>:</span>
                <div class="form-group" style="display:inline-block !important;" id="mail_subject" contenteditable="true"><?php echo JFactory::getConfig()->get('sitename'); ?></div>
            </div>

            <!-- Email WYSIWYG -->
            <?php echo $mail_body; ?>
        </div>

        <div class="form-group">
            <br>
            <hr>
        </div>

        <div class="form-inline row">
            <div class="form-group col-sm-12 col-md-5">
                <label for="em-select_attachment_type" ><?php echo JText::_('SELECT_ATTACHMENT_TYPE'); ?></label>
                <select name="em-select_attachment_type" id="em-select_attachment_type" class="form-control  download" onChange="toggleAttachmentType(this);">
                    <option value=""> <?php echo JText::_('PLEASE_SELECT'); ?> </option>
                    <option value="upload"> <?php echo JText::_('UPLOAD'); ?> </option>
                    <?php if (EmundusHelperAccess::asAccessAction(4, 'r')) : ?>
                        <option value="candidate_file"> <?php echo JText::_('CANDIDATE_FILE'); ?> </option>
                    <?php endif; ?>
                    <?php if (EmundusHelperAccess::asAccessAction(4, 'c') && EmundusHelperAccess::asAccessAction(27, 'c')) : ?>
                        <option value="setup_letters"> <?php echo JText::_('SETUP_LETTERS_ATTACH'); ?> </option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group col-sm-12 col-md-7">
                <!-- Upload a file from computer -->
                <div class="hidden upload-file" id="upload_file">

                    <div class="file-browse">
                        <span id="em-filename"><?php echo JText::_('FILE_NAME'); ?></span>

                        <label for="em-file_to_upload" type="button"><?php echo JText::_('SELECT_FILE_TO_UPLOAD') ?>
                            <input type="file" id="em-file_to_upload" onChange="addFile();">
                        </label>
                    </div>
                    <div id="em-progress-wrp" class="loading-bar">
                        <div class="progress-bar"></div>
                        <div class="status">0%</div>
                    </div>
                </div>


                <!-- Get a file from setup_attachments -->
                <?php if (EmundusHelperAccess::asAccessAction(4, 'r')) : ?>
                    <div class="hidden" id="candidate_file">


                        <label for="candidate_file" ><?php echo JText::_('UPLOAD'); ?></label>
                        <select id="em-select_candidate_file" name="candidate_file" class="form-control download">
                            <?php if (!$setup_attachments) :?>
                                <option value="%"> <?php echo JText::_('NO_FILES_FOUND'); ?> </option>
                            <?php else: ?>
                                <option value="%"> <?php echo JText::_('PLEASE_SELECT'); ?> </option>
                                <?php foreach ($setup_attachments as $attachment): ?>
                                    <option value="<?php echo $attachment->id; ?>"> <?php echo $attachment->value; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>

                        <span class="input-group-btn">
							        <a class="btn btn-grey hidden" type="button" id="uploadButton" style="top:23px; float: right;" onClick="addFile();"><?= JText::_('ADD_ATTACHMENT'); ?></a>
						        </span>

                    </div>



                <?php endif; ?>

                <!-- Get a file from setup_letters -->
                <?php if (EmundusHelperAccess::asAccessAction(4, 'c') && EmundusHelperAccess::asAccessAction(27, 'c')) : ?>
                <div class="hidden" id="setup_letters">

                    <label for="setup_letters" ><?php echo JText::_('UPLOAD'); ?></label>
                    <select id="em-select_setup_letters" name="setup_letters" class="form-control">
                        <?php if (!$setup_letters) :?>
                            <option value="%"> <?php echo JText::_('NO_FILES_FOUND'); ?> </option>
                        <?php else: ?>
                            <option value="%"> <?php echo JText::_('PLEASE_SELECT'); ?> </option>
                            <?php foreach ($setup_letters as $letter): ?>
                                <option value="<?php echo $letter->id; ?>"> <?php echo $letter->title; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>

                    <span class="input-group-btn">
							<a class="btn btn-grey" type="button" id="uploadButton" style="top:23px; float: right;" onClick="addFile();"><?= JText::_('ADD_ATTACHMENT'); ?></a>
						</span>

                    <?php endif; ?>

                </div>
            </div>
        </div>
        <br>
        <hr>
        <div class="form-group attachment">
            <ul class="list-group" id="em-attachment-list">
                <!-- Files to be attached will be added here. -->
            </ul>
        </div>

        <input type="hidden" name="task" value=""/>
</form>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<script type="text/javascript">

    // Editor loads disabled by default, we apply must toggle it active on page load.
    $(document).ready(function() {
        tinyMCE.execCommand('mceToggleEditor', true, 'mail_body');
    });

    // Change file upload string to selected file and reset the progress bar.
    $('#em-file_to_upload').change(function() {
        $('#em-filename').html(this.value.match(/([^\/\\]+)$/)[1]);
        $("#em-progress-wrp .progress-bar").css("width", + 0 + "%");
        $("#em-progress-wrp .status").text(0 + "%");
    });

    // Loads the template and updates the WYSIWYG editor
    function getTemplate(select) {

        $.ajax({
            type: "POST",
            url : "index.php?option=com_emundus&controller=messages&task=gettemplate",
            data : {
                select : select.value
            },
            success: function (email) {

                email = JSON.parse(email);

                var email_block = document.getElementById("em_email_block");
                $("#mail_subject").text(email.tmpl.subject);
                $("#mail_from").text(email.tmpl.emailfrom);
                $("#mail_from_name").text(email.tmpl.name);
                $("#mail_body").val(email.tmpl.message);
                tinyMCE.execCommand("mceSetContent", false, email.tmpl.message);
                tinyMCE.execCommand("mceRepaint");

                //Reset attachments.
                $('#em-attachment-list').each(function(idx, li) {
                    var attachment = $(li);

                    if (attachment.hasClass('candidate_file')) {

                        // Remove 'disabled' attr from select options.
                        $('#em-select_candidate_file option[value="'+attachment.find('.value').text()+'"]').prop('disabled', false);

                    } else if (attachment.hasClass('setup_letters')) {

                        // Remove 'disabled' attr from select options.
                        $('#em-select_setup_letters option[value="'+attachment.find('.value').text()+'"]').prop('disabled', false);

                    }
                });

                // Remove all attachments from list.
                $('#em-attachment-list').empty();

                // Get the attached uploaded file if there is one.
                if (typeof(email.tmpl.attachment) != 'undefined' && email.tmpl.attachment != null) {
                    $('#em-attachment-list').append('<li class="list-group-item upload"><div class="value hidden">'+email.tmpl.attachment+'</div>'+ email.tmpl.attachment.split('\\').pop().split('/').pop() +'<span class="badge btn-danger" onClick="removeAttachment(this);"><span class="glyphicon glyphicon-remove"></span></span><span class="badge"><span class="glyphicon glyphicon-saved"></span></span></li>');
                }

                <?php if (EmundusHelperAccess::asAccessAction(4, 'r')) : ?>
                // Get the attached candidate files if there are any.
                if (typeof(email.tmpl.candidate_attachments) != 'undefined' && email.tmpl.candidate_attachments != null) {

                    // We need another AJAX to get the info about the attachments, we only have the IDs and we need the names.
                    $.ajax({
                        type: 'POST',
                        url: 'index.php?option=com_emundus&controller=messages&task=getcandidatefilenames',
                        data : {
                            attachments : email.tmpl.candidate_attachments
                        },
                        success: function (attachments) {
                            attachments = JSON.parse(attachments);
                            if (attachments.status) {

                                // Add the attachments to the list and deselect the corresponding selects from the option.
                                attachments.attachments.forEach(function(attachment) {
                                    $('#em-attachment-list').append('<li class="list-group-item candidate_file"><div class="value hidden">'+attachment.id+'</div>'+attachment.value+'<span class="badge btn-danger" onClick="removeAttachment(this);"><span class="glyphicon glyphicon-remove"></span></span><span class="badge"><span class="glyphicon glyphicon-paperclip"></span></span></li>');
                                    $('#em-select_candidate_file option[value="'+attachment.id+'"]').prop('disabled', true);
                                });

                            }
                        }

                    })

                }
                <?php endif; ?>

                // TODO: Rights?
                // Get the attached candidate files if there are any.
                if (typeof(email.tmpl.letter_attachments) != 'undefined' && email.tmpl.letter_attachments != null) {

                    // We need another AJAX to get the info about the letter, we only have the IDs and we need the names.
                    $.ajax({
                        type: 'POST',
                        url: 'index.php?option=com_emundus&controller=messages&task=getletterfilenames',
                        data : {
                            attachments : email.tmpl.letter_attachments
                        },
                        success: function (attachments) {
                            attachments = JSON.parse(attachments);
                            if (attachments.status) {

                                // Add the attachments to the list and deselect the corresponding selects from the option.
                                attachments.attachments.forEach(function(attachment) {
                                    $('#em-attachment-list').append('<li class="list-group-item setup_letters"><div class="value hidden">'+attachment.id+'</div>'+attachment.title+'<span class="badge btn-danger" onClick="removeAttachment(this);"><span class="glyphicon glyphicon-remove"></span></span><span class="badge"><span class="glyphicon glyphicon-envelope"></span></span></li>');
                                    $('#em-select_setup_letters option[value="'+attachment.id+'"]').prop('disabled', true);
                                });

                            }
                        }

                    })

                }

            },
            error: function () {
                // handle error
                $("#message_template").append('<span class="alert"> <?php echo JText::_('ERROR'); ?> </span>')
            }
        });

    }

    // Used for toggling the options dipslayed in the message templates dropdown.
    function setCategory(element) {

        if (element.value == "%")
            category = 'all';
        else
            category = element.value;

        $.ajax({
            type: "GET",
            url: "index.php?option=com_emundus&controller=messages&task=setcategory&category="+category,
            success: function (data) {

                data = JSON.parse(data);

                if (data.status) {

                    var $el = $("#message_template");
                    $('#message_template option:gt(0)').remove();

                    $.each(data.templates, function(key,value) {
                        $el.append($("<option></option>")
                            .attr("value", value.id).text(value.subject));
                    });
                } else {
                    $("#message_template").append('<span class="alert"> <?php echo JText::_('ERROR'); ?> </span>')
                }
            },
            error: function (error) {
                // handle error
                $("#message_template").append('<span class="alert"> <?php echo JText::_('ERROR'); ?> </span>')
            },

        });

    }

    function SubmitForm() {
        // Form submission has been moved to em_files.js under the modal submission listener.
    }


    // Used for reseting a File upload input.
    function resetFileInput(e) {
        e.wrap('<form>').closest('form').get(0).reset();
        e.unwrap();
    }


    // Change the attachment type being uploaded.
    function toggleAttachmentType(toggle) {

        switch (toggle.value) {

            case 'upload' :
                $('#upload_file').removeClass('hidden')
                $('#candidate_file').addClass('hidden')
                $('#setup_letters').addClass('hidden')
                $('#uploadButton').removeClass('hidden')
                break;

            case 'candidate_file' :
                resetFileInput($('#upload_file'))
                $('#upload_file').addClass('hidden')
                $('#candidate_file').removeClass('hidden')
                $('#setup_letters').addClass('hidden')
                $('#uploadButton').removeClass('hidden')
                break;

            case 'setup_letters' :
                resetFileInput($('#upload_file'))
                $('#upload_file').addClass('hidden')
                $('#candidate_file').addClass('hidden')
                $('#setup_letters').removeClass('hidden')
                $('#uploadButton').removeClass('hidden')
                break;

            default :
                resetFileInput($('#upload_file'))
                $('#upload_file').addClass('hidden')
                $('#candidate_file').addClass('hidden')
                $('#setup_letters').addClass('hidden')
                $('#uploadButton').addClass('hidden')
                break;

        }

    }


    // Add file to the list being attached.
    function addFile() {

        switch ($('#em-select_attachment_type :selected').val()) {

            case 'upload' :

                // We need to get the file uploaded by the user.
                var file = $("#em-file_to_upload")[0].files[0];
                var upload = new Upload(file);
                // Verification of style size and type can be done here.
                upload.doUpload();

                break;


            case 'candidate_file' :

                // we just need to note the reference to the setup_attachment file.
                var file = $('#em-select_candidate_file :selected');

                var alreadyPicked = $('#em-attachment-list li.candidate_file').find('.value:contains("'+file.val()+'")');

                if (alreadyPicked.text() != '') {

                    // Flash the line a certain color to show it's already picked.
                    alreadyPicked.parent().attr("style", "background-color: #C5EFF7");
                    setTimeout(function(){
                        alreadyPicked.parent().attr("style", "");
                    }, 500);

                } else {

                    // Disable the file from the dropdown.
                    file.prop('disabled', true);
                    // Add the file to the list.
                    $('#em-attachment-list').append('<li class="list-group-item candidate_file"><div class="value hidden">'+file.val()+'</div>'+file.text()+'<span class="badge btn-danger" onClick="removeAttachment(this);"><span class="glyphicon glyphicon-remove"></span></span><span class="badge"><span class="glyphicon glyphicon-paperclip"></span></span></li>');

                }


                break;

            case 'setup_letters' :

                // We need to note the reference to the setup_letters file.
                var file = $('#em-select_setup_letters :selected');

                var alreadyPicked = $('#em-attachment-list li.setup_letters').find('.value:contains("'+file.val()+'")');

                if (alreadyPicked.text() != '') {

                    // Flash the line a certain color to show it's already picked.
                    alreadyPicked.parent().attr("style", "background-color: #C5EFF7");
                    setTimeout(function(){
                        alreadyPicked.parent().attr("style", "");
                    }, 500);

                } else {

                    // Disable the file from the dropdown.
                    file.prop('disabled', true);
                    // Add the file to the list.
                    $('#em-attachment-list').append('<li class="list-group-item setup_letters"><div class="value hidden">'+file.val()+'</div>'+file.text()+'<span class="badge btn-danger" onClick="removeAttachment(this);"><span class="glyphicon glyphicon-remove"></span></span><span class="badge"><span class="glyphicon glyphicon-envelope"></span></span></li>');

                }

                break;

            default :

                // Nothing selected, this case should not happen.
                $("#em-attachment-list").append('<span class="alert alert-danger"> <?php echo JText::_('ERROR'); ?> </span>')

                break;

        }

    }

    function removeAttachment(element) {

        element = $(element);

        if (element.parent().hasClass('candidate_file')) {

            // Remove 'disabled' attr from select options.
            $('#em-select_candidate_file option[value="'+element.parent().find('.value').text()+'"]').prop('disabled', false);

        } else if (element.parent().hasClass('setup_letters')) {

            // Remove 'disabled' attr from select options.
            $('#em-select_setup_letters option[value="'+element.parent().find('.value').text()+'"]').prop('disabled', false);

        }

        $(element).parent().remove();
    }


    // Helper function for uploading a file via AJAX.
    var Upload = function (file) {
        this.file = file;
    };

    Upload.prototype.getType = function() {
        return this.file.type;
    };
    Upload.prototype.getSize = function() {
        return this.file.size;
    };
    Upload.prototype.getName = function() {
        return this.file.name;
    };
    Upload.prototype.doUpload = function () {
        var that = this;
        var formData = new FormData();

        // add assoc key values, this will be posts values
        formData.append("file", this.file, this.getName());
        formData.append("upload_file", true);

        $.ajax({
            type: "POST",
            url: "index.php?option=com_emundus&controller=messages&task=uploadfiletosend",
            xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', that.progressHandling, false);
                }
                return myXhr;
            },
            success: function (data) {
                data = JSON.parse(data);

                if (data.status) {
                    $('#em-attachment-list').append('<li class="list-group-item upload"><div class="value hidden">'+data.file_path+'</div>'+data.file_name+'<span class="badge btn-danger" onClick="removeAttachment(this);"><span class="glyphicon glyphicon-remove"></span></span><span class="badge"><span class="glyphicon glyphicon-saved"></span></span></li>');
                } else {
                    $("#em-file_to_upload").append('<span class="alert"> <?php echo JText::_('UPLOAD_FAILED'); ?> </span>')
                }
            },
            error: function (error) {
                // handle error
                $("#em-file_to_upload").append('<span class="alert"> <?php echo JText::_('UPLOAD_FAILED'); ?> </span>')
            },
            async: true,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            timeout: 60000
        });
    };

    Upload.prototype.progressHandling = function (event) {
        var percent = 0;
        var position = event.loaded || event.position;
        var total = event.total;
        var progress_bar_id = "";
        if (event.lengthComputable) {
            percent = Math.ceil(position / total * 100);
        }
        // update progressbars classes so it fits your code
        $("#em-progress-wrp .progress-bar").css("width", +percent + "%");
        $("#em-progress-wrp .status").text(percent + "%");
    };
</script>