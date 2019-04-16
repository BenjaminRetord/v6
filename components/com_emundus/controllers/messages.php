<?php
/**
 * Messages controller used for the creation and emission of messages from the platform.
 *
 * @package    Joomla
 * @subpackage Emundus
 * @link       http://www.emundus.fr
 * @license    GNU/GPL
 * @author     Hugo Moracchini
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * eMundus Component Controller
 *
 * @package    Joomla.eMundus
 * @subpackage Components
 */
class EmundusControllerMessages extends JControllerLegacy {

    /**
     * Constructor
     *
     * @since 3.8.6
     */
    function __construct($config = array()) {
        require_once (JPATH_ROOT.DS.'components'.DS.'com_emundus'.DS.'helpers'.DS.'access.php');
        require_once (JPATH_ROOT.DS.'components'.DS.'com_emundus'.DS.'models'.DS.'messages.php');
        parent::__construct($config);
    }

    /**
     * Get all of the information for an email template.
     *
     * @since 3.8.6
     */
    function gettemplate() {

        require_once (JPATH_COMPONENT.DS.'helpers'.DS.'files.php');

        $jinput = JFactory::getApplication()->input;
        $template_id = $jinput->post->getInt('select', null);

        $m_messages = new EmundusModelMessages();
        $h_files = new EmundusHelperFiles();

        $get_candidate_attachments = $h_files->tableExists('#__emundus_setup_emails_repeat_candidate_attachment');
        $get_letters_attachments = $h_files->tableExists('#__emundus_setup_emails_repeat_letter_attachment');

        $template = $m_messages->getEmail($template_id, $get_candidate_attachments, $get_letters_attachments);

        if (!$template) {
            echo json_encode((object)(['status' => false]));
            exit;
        }

		echo json_encode((object)([
            'status' => true,
            'tmpl' => $template
        ]));
		exit;

    }

    /**
     * Get email templates by category.
     *
     * @since 3.8.6
     */
    public function setcategory() {

        $jinput = JFactory::getApplication()->input;
        $category = $jinput->get->getString('category', 'all');

        $m_messages = new EmundusModelMessages();

        $templates = $m_messages->getEmailsByCategory($category);

        if (!$templates) {
            echo json_encode((object)(['status' => false]));
            exit;
        }

        echo json_encode((object)([
            'status' => true,
            'templates' => $templates
        ]));
        exit;

    }


    /**
     * Upload a file from computer to be attached to the emails sent.
     *
     * @since 3.8.6
     */
    public function uploadfiletosend() {

        $jinput = JFactory::getApplication()->input;

        // If a filetype was sent in POST: check it.
	    $filetype = $jinput->post->get('filetype', null);

        // Get the file sent via AJAX POST
        $file = $jinput->files->get('file');

        // Get the user sent via AJAX POST
        $user = $jinput->post->get('user');

        // Get the user sent via AJAX POST
        $fnum = $jinput->post->get('fnum');

        // Check if an error is present
	    if (!isset($file['error']) || is_array($file['error'])) {
		    echo json_encode(['status' => false]);
		    exit;
	    }

	    // Check if file name is alphanumeric
	    if (!preg_match("`^[-0-9A-Z_\.]+$`i", $file['name'])) {
	    	echo json_encode(['status' => false]);
	    	exit;
	    }

	    // Check if file name is not too long.
	    if (mb_strlen($file['name'], "UTF-8") > 225) {
	    	echo json_encode(['status' => false]);
	    	exit;
	    }

	    // If we specifically are uploading a PDF, check the MIME type.
	    if ($filetype == 'pdf' && $file['type'] != 'application/pdf') {
		    echo json_encode(['status' => false]);
		    exit;
	    }

	    // Check file extension and remove any dengerous ones.
	    if (preg_match("/.exe$|.com$|.bat$|.zip$|.php$|.sh$/i", $file['name'])){
		    exit("You cannot upload this type of file.");
	    }
        // Check if the message attachments directory exists.
        if (!is_dir('images'.DS.'emundus'.DS.'files'.DS.$user.DS.$fnum)) {
            mkdir('images'.DS.'emundus'.DS.'files'.DS.$user.DS.$fnum, 0777, true);
        }

        // Sanitize filename.
        $file['name'] = preg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $file['name']);
        $file['name'] = preg_replace("([\.]{2,})", '', $file['name']);

        // Move the uploaded file to the server directory.
        $target = 'images'.DS.'emundus'.DS.'files'.DS.$user.DS.$fnum.DS.$file['name'];

        
        if (file_exists($target)) {
	        unlink($target);
        }

        move_uploaded_file($file['tmp_name'], $target);

        // Send back the info to the frontend.
        echo json_encode(['status' => true, 'file_name' => $file['name'], 'file_path' => $target]);
        exit;

    }


    /**
     * Gets the names of the candidate files.
     * @since 3.8.6
     */
    public function getcandidatefilenames() {

        $m_messages = new EmundusModelMessages();

        $jinput = JFactory::getApplication()->input;
        $attachment_ids = $jinput->post->getString('attachments', null);

        if (empty($attachment_ids)) {
            echo json_encode((object)['status' => false]);
            exit;
        }

        $attachments = $m_messages->getCandidateFileNames($attachment_ids);

        if (!$attachments) {
            echo json_encode((object)['status' => false]);
            exit;
        }

        echo json_encode((object)['status' => true, 'attachments' => $attachments]);
        exit;

    }

    /**
     * Gets the names of the letter files.
     * @since 3.8.6
     */
    public function getletterfilenames() {

        $m_messages = new EmundusModelMessages();

        $jinput = JFactory::getApplication()->input;
        $attachment_ids = $jinput->post->getString('attachments', null);

        if (empty($attachment_ids)) {
            echo json_encode((object)['status' => false]);
            exit;
        }

        $attachments = $m_messages->getLetterFileNames($attachment_ids);

        if (!$attachments) {
            echo json_encode((object)['status' => false]);
            exit;
        }

        echo json_encode((object)['status' => true, 'attachments' => $attachments]);
        exit;

    }


    /**
     * Send the email defined in the dialog.
     *
     * @since 3.8.6
     */
    public function applicantemail() {


        if (!EmundusHelperAccess::asAccessAction(9, 'c')) {
			die(JText::_("ACCESS_DENIED"));
		}

        require_once (JPATH_COMPONENT.DS.'models'.DS.'files.php');
        require_once (JPATH_COMPONENT.DS.'models'.DS.'emails.php');
        require_once (JPATH_COMPONENT.DS.'models'.DS.'campaign.php');
	    require_once (JPATH_COMPONENT.DS.'models'.DS.'logs.php');

        $m_messages = new EmundusModelMessages();
        $m_emails   = new EmundusModelEmails();
        $m_files    = new EmundusModelFiles();
        $m_campaign = new EmundusModelCampaign();

        $user   = JFactory::getUser();
        $config = JFactory::getConfig();
        $jinput = JFactory::getApplication()->input;

        // Get default mail sender info
        $mail_from_sys = $config->get('mailfrom');
        $mail_from_sys_name = $config->get('fromname');

        $fnums  = explode(',',$jinput->post->get('recipients', null, null));
        $bcc    = $jinput->post->getString('Bcc', false);

        // If no mail sender info is provided, we use the system global config.
        $mail_from_name = $jinput->post->getString('mail_from_name', $mail_from_sys_name);
        $mail_from      = $jinput->post->getString('mail_from', $mail_from_sys);

        $mail_subject   = $jinput->post->getString('mail_subject', 'No Subject');
        $template_id    = $jinput->post->getInt('template', null);
        $message        = $jinput->post->get('message', null, 'RAW');
        $attachments    = $jinput->post->get('attachments', null, null);


        // Get additional info for the fnums such as the user email.
        $fnums = $m_files->getFnumsInfos($fnums, 'object');

        // This will be filled with the email adresses of successfully sent emails, used to give feedback to front end.
        $sent = [];
        $failed = [];

        // Loading the message template is not used for getting the message text as that can be modified on the frontend by the user before sending.
        $template = $m_messages->getEmail($template_id);

        // If the email sender has the same domain as the system sender address.
        if (substr(strrchr($mail_from, "@"), 1) === substr(strrchr($mail_from_sys, "@"), 1))
            $mail_from_address = $mail_from;
        else
            $mail_from_address = $mail_from_sys;

        // Set sender
        $sender = [
            $mail_from_address,
            $mail_from_name
        ];

        foreach ($fnums as $fnum) {

            $programme = $m_campaign->getProgrammeByTraining($fnum->training);

            $toAttach = [];

            $post = [
                'FNUM'      => $fnum->fnum,
                'USER_NAME' => $fnum->name,
                'COURSE_LABEL' => $programme->label,
                'CAMPAIGN_LABEL' => $fnum->label,
                'SITE_URL' => JURI::base(),
                'USER_EMAIL' => $fnum->email
            ];

            $tags = $m_emails->setTags($fnum->applicant_id, $post);
            $message = $m_emails->setTagsFabrik($message, [$fnum->fnum]);
            $subject = $m_emails->setTagsFabrik($mail_subject, [$fnum->fnum]);

            // Tags are replaced with their corresponding values using the PHP preg_replace function.
            $subject = preg_replace($tags['patterns'], $tags['replacements'], $subject);
            $body = preg_replace($tags['patterns'], $tags['replacements'], $message);
            if ($template != false)
                $body = preg_replace(["/\[EMAIL_SUBJECT\]/", "/\[EMAIL_BODY\]/"], [$subject, $body], $template->Template);

            // Configure email sender
            $mailer = JFactory::getMailer();
            $mailer->setSender($sender);
            $mailer->addReplyTo($mail_from, $mail_from_name);
            $mailer->addRecipient($fnum->email);
            $mailer->setSubject($subject);
            $mailer->isHTML(true);
            $mailer->Encoding = 'base64';
            $mailer->setBody($body);

            if ($bcc === 'true')
                $mailer->addBCC($user->email);

            // Files uploaded from the frontend.
            if (!empty($attachments['upload'])) {

                // In the case of an uploaded file, just add it to the email.
                foreach ($attachments['upload'] as $upload) {
                    if (file_exists(JPATH_BASE.DS.$upload))
                        $toAttach[] = JPATH_BASE.DS.$upload;
                }

            }

            // Files gotten from candidate files, requires attachment read rights.
            if (EmundusHelperAccess::asAccessAction(4, 'r')) {
                if (!empty($attachments['candidate_file'])) {

                    // Get from DB by fnum.
                    foreach ($attachments['candidate_file'] as $candidate_file) {

                        $filename = $m_messages->get_upload($fnum->fnum, $candidate_file);

                        if ($filename != false) {

                            // Build the path to the file we are searching for on the disk.
                            $path = EMUNDUS_PATH_ABS.$fnum->applicant_id.DS.$filename;

                            if (file_exists($path)) {
                                $toAttach[] = $path;
                            }

                        }

                    }

                }
            }

            // Files generated using the Letters system. Requires attachment creation and doc generation rights.
            if (EmundusHelperAccess::asAccessAction(4, 'c') && EmundusHelperAccess::asAccessAction(27, 'c')) {
                if (!empty($attachments['setup_letters'])) {

                    // Get from DB and generate using the tags.
                    foreach ($attachments['setup_letters'] as $setup_letter) {

                        $letter = $m_messages->get_letter($setup_letter);

                        // We only get the letters if they are for that particular programme.
                        if ($letter != false && in_array($fnum->training, explode('","',$letter->training))) {

                            // Some letters are only for files of a certain status, this is where we check for that.
                            if ($letter->status != null && !in_array($fnum->step, explode(',',$letter->status)))
                                continue;

                            // A different file is to be generated depending on the template type.
                            switch ($letter->template_type) {

                                case '1':
                                    // This is a static file, we just need to find its path add it as an attachment.
                                    if (file_exists(JPATH_BASE.$letter->file))
                                        $toAttach[] = JPATH_BASE.$letter->file;
                                break;

                                case '2':
                                    // This is a PDF to be generated from HTML.
                                    require_once (JPATH_LIBRARIES.DS.'emundus'.DS.'pdf.php');

                                    $path = generateLetterFromHtml($letter, $fnum->fnum, $fnum->applicant_id, $fnum->training);

                                    if ($path != false && file_exists($path))
                                        $toAttach[] = $path;
                                break;

                                case '3':
                                    // This is a DOC template to be completed with applicant information.
                                    $path = $m_messages->generateLetterDoc($letter, $fnum->fnum);

                                    if ($path != false && file_exists($path))
                                        $toAttach[] = $path;
                                break;

                            }
                        }
                    }
                }
            }

            $mailer->addAttachment($toAttach);

            // Send and log the email.
            $send = $mailer->Send();
            if ($send !== true) {
                $failed[] = $fnum->email;
                echo 'Error sending email: ' . $send->__toString();
                JLog::add($send->__toString(), JLog::ERROR, 'com_emundus');
            } else {
                $sent[] = $fnum->email;
                $log = [
                    'user_id_from'  => $user->id,
                    'user_id_to'    => $fnum->applicant_id,
                    'subject'       => $subject,
                    'message'       => '<i>'.JText::_('MESSAGE').' '.JText::_('SENT').' '.JText::_('TO').' '.$fnum->email.'</i><br>'.$body,
                    'type'          => $template->type
                ];
                $m_emails->logEmail($log);
	            // Log the email in the eMundus logging system.
	            EmundusModelLogs::log($user->id, $fnum->applicant_id, $fnum->fnum, 9, 'c', 'COM_EMUNDUS_LOGS_SEND_EMAIL');
            }
        }

        echo json_encode(['status' => true, 'sent' => $sent, 'failed' => $failed]);
        exit;

    }

	/** The generic function used for sending emails.
	 *
	 * @param       $fnum
	 * @param       $email_id
	 * @param null  $post
	 * @param array $attachments
	 * @param bool  $bcc
	 *
	 * @return bool
	 * @throws \PhpOffice\PhpWord\Exception\CopyFileException
	 * @throws \PhpOffice\PhpWord\Exception\CreateTemporaryFileException
	 * @throws \PhpOffice\PhpWord\Exception\Exception
	 */
    function sendEmail($fnum, $email_id, $post = null, $attachments = [], $bcc = false) {
	    require_once (JPATH_COMPONENT.DS.'models'.DS.'files.php');
	    require_once (JPATH_COMPONENT.DS.'models'.DS.'emails.php');
	    require_once (JPATH_COMPONENT.DS.'models'.DS.'campaign.php');
	    require_once (JPATH_COMPONENT.DS.'models'.DS.'logs.php');
	    require_once (JPATH_COMPONENT.DS.'helpers'.DS.'files.php');

	    $m_messages = new EmundusModelMessages();
	    $m_emails   = new EmundusModelEmails();
	    $m_files    = new EmundusModelFiles();
	    $h_files    = new EmundusHelperFiles();
	    $m_campaign = new EmundusModelCampaign();

	    $user   = JFactory::getUser();
	    $config = JFactory::getConfig();

	    // Get additional info for the fnums such as the user email.
	    $fnum = $m_files->getFnumInfos($fnum);

	    $get_candidate_attachments = $h_files->tableExists('#__emundus_setup_emails_repeat_candidate_attachment');
	    $get_letters_attachments = $h_files->tableExists('#__emundus_setup_emails_repeat_letter_attachment');
	    $template = $m_messages->getEmail($email_id, $get_candidate_attachments, $get_letters_attachments);

	    // Get default mail sender info
	    $mail_from_sys = $config->get('mailfrom');
	    $mail_from_sys_name = $config->get('fromname');

	    // If no mail sender info is provided, we use the system global config.
	    $mail_from_name = $user->name;
	    $mail_from      = $template->emailfrom;

	    // If the email sender has the same domain as the system sender address.
	    if (substr(strrchr($mail_from, "@"), 1) === substr(strrchr($mail_from_sys, "@"), 1)) {
		    $mail_from_address = $mail_from;
	    } else {
		    $mail_from_address = $mail_from_sys;
		    $mail_from_name = $mail_from_sys_name;
	    }

	    // Set sender
	    $sender = [
		    $mail_from_address,
		    $mail_from_name
	    ];

	    $programme = $m_campaign->getProgrammeByTraining($fnum['training']);

	    if (!empty($attachments) && is_array($attachments)) {
	        $toAttach = $attachments;
	    } else {
		    $toAttach[] = $attachments;
	    }

	    // In case no post value is supplied
	    if (empty($post)) {
		    $post = [
			    'FNUM'           => $fnum['fnum'],
			    'USER_NAME'      => $fnum['name'],
			    'COURSE_LABEL'   => $programme['label'],
			    'CAMPAIGN_LABEL' => $fnum['label'],
			    'SITE_URL'       => JURI::base(),
			    'USER_EMAIL'     => $fnum['email']
		    ];
	    }

	    $tags = $m_emails->setTags($fnum['applicant_id'], $post);
	    $message = $m_emails->setTagsFabrik($template->message, [$fnum['fnum']]);
	    $subject = $m_emails->setTagsFabrik($template->subject, [$fnum['fnum']]);

	    // Tags are replaced with their corresponding values using the PHP preg_replace function.
	    $subject = preg_replace($tags['patterns'], $tags['replacements'], $subject);
	    $body = preg_replace($tags['patterns'], $tags['replacements'], $message);
	    if ($template != false) {
		    $body = preg_replace(["/\[EMAIL_SUBJECT\]/", "/\[EMAIL_BODY\]/"], [$subject, $body], $template->Template);
	    }

	    // Configure email sender
	    $mailer = JFactory::getMailer();
	    if ($bcc) {
		    $mailer->addBCC($user->email);
	    }
	    $mailer->setSender($sender);
	    $mailer->addReplyTo($mail_from, $mail_from_name);
	    $mailer->addRecipient($fnum['email']);
	    $mailer->setSubject($subject);
	    $mailer->isHTML(true);
	    $mailer->Encoding = 'base64';
	    $mailer->setBody($body);

        
        // Get any candidate files included in the message.
        if (!empty($template->candidate_file)) {
            foreach ($template->candidate_file as $candidate_file) {

                $filename = $m_messages->get_upload($fnum['fnum'], $candidate_file);
    
                if ($filename != false) {
    
                    // Build the path to the file we are searching for on the disk.
                    $path = EMUNDUS_PATH_ABS.$fnum['applicant_id'].DS.$filename;
    
                    if (file_exists($path)) {
                        $toAttach[] = $path;
                    }
                }
            }
        }
	    

	    // Files generated using the Letters system. Requires attachment creation and doc generation rights.
        // Get from DB and generate using the tags.
        if (!empty($template->setup_letters)) {
            foreach ($template->setup_letters as $setup_letter) {

                $letter = $m_messages->get_letter($setup_letter);
    
                // We only get the letters if they are for that particular programme.
                if ($letter != false && in_array($fnum['training'], explode('","', $letter->training))) {
    
                    // Some letters are only for files of a certain status, this is where we check for that.
                    if ($letter->status != null && !in_array($fnum['step'], explode(',', $letter->status)))
                        continue;
    
                    // A different file is to be generated depending on the template type.
                    switch ($letter->template_type) {
    
                        case '1':
                            // This is a static file, we just need to find its path add it as an attachment.
                            if (file_exists(JPATH_BASE.$letter->file))
                                $toAttach[] = JPATH_BASE.$letter->file;
                            break;
    
                        case '2':
                            // This is a PDF to be generated from HTML.
                            require_once (JPATH_LIBRARIES.DS.'emundus'.DS.'pdf.php');
    
                            $path = generateLetterFromHtml($letter, $fnum['fnum'], $fnum['applicant_id'], $fnum['training']);
    
                            if ($path != false && file_exists($path))
                                $toAttach[] = $path;
                            break;
    
                        case '3':
                            // This is a DOC template to be completed with applicant information.
                            $path = $m_messages->generateLetterDoc($letter, $fnum['fnum']);
    
                            if ($path != false && file_exists($path))
                                $toAttach[] = $path;
                            break;
    
                    }
    
                }
    
            }
        }

        if(!empty($toAttach))
	        $mailer->addAttachment($toAttach);
	    // Send and log the email.
        $send = $mailer->Send();
        
	    if ($send !== true) {

		    JLog::add($send->__toString(), JLog::ERROR, 'com_emundus');
		    return false;

	    } else {
            
		    $sent[] = $fnum['email'];
		    $log = [
			    'user_id_from'  => $user->id,
			    'user_id_to'    => $fnum['applicant_id'],
			    'subject'       => $subject,
			    'message'       => '<i>'.JText::_('MESSAGE_SENT_TO').' '.$fnum['email'].'</i><br>'.$body,
			    'type'          => $template->type
		    ];
		    $m_emails->logEmail($log);

		    // Log the email in the eMundus logging system.
		    EmundusModelLogs::log($user->id, $fnum['applicant_id'], $fnum['fnum'], 9, 'c', 'COM_EMUNDUS_LOGS_SEND_EMAIL');

		    return true;
	    }
    }

	/** The generic function used for sending emails outside of emundus.
	 *
	 * @param String $email_address
	 * @param Mixed $email If a numeric ID is provided, use that, if a string is provided, get the email with that label.
	 * @param null $post
	 * @param array $attachments
	 *
	 * @return bool
	 */
	function sendEmailNoFnum($email_address, $email, $post = null, $attachments = []) {

		$m_messages = new EmundusModelMessages();

		$user   = JFactory::getUser();
		$config = JFactory::getConfig();

		$template = $m_messages->getEmail($email);

		// Get default mail sender info
		$mail_from_sys = $config->get('mailfrom');
		$mail_from_sys_name = $config->get('fromname');

		// If no mail sender info is provided, we use the system global config.
		$mail_from_name = $user->name;
		$mail_from      = $template->emailfrom;

		// If the email sender has the same domain as the system sender address.
		if (substr(strrchr($mail_from, "@"), 1) === substr(strrchr($mail_from_sys, "@"), 1))
			$mail_from_address = $mail_from;
		else {
			$mail_from_address = $mail_from_sys;
			$mail_from_name = $mail_from_sys_name;
		}

		// Set sender
		$sender = [
			$mail_from_address,
			$mail_from_name
		];

		if (!empty($attachments) && is_array($attachments))
			$toAttach = $attachments;
		else
			$toAttach[] = $attachments;

		// In case no post value is supplied
		if (empty($post)) {
			$post = [
				'SITE_URL'      => JURI::base(),
				'USER_EMAIL'    => $email_address
			];
		}

		// Handle [] in post keys.
		$keys = [];
		foreach (array_keys($post) as $key) {
			$keys[] = '/\['.$key.'\]/';
		}

		// Tags are replaced with their corresponding values using the PHP preg_replace function.
		$subject = preg_replace($keys, $post, $template->subject);
		$body = preg_replace($keys, $post, $template->message);
		if ($template != false)
			$body = preg_replace(["/\[EMAIL_SUBJECT\]/", "/\[EMAIL_BODY\]/"], [$subject, $body], $template->Template);

		// Configure email sender
		$mailer = JFactory::getMailer();
		$mailer->setSender($sender);
		$mailer->addReplyTo($mail_from, $mail_from_name);
		$mailer->addRecipient($email_address);
		$mailer->setSubject($subject);
		$mailer->isHTML(true);
		$mailer->Encoding = 'base64';
		$mailer->setBody($body);

		if (!empty($toAttach))
			$mailer->addAttachment($toAttach);

		// Send and log the email.
		$send = $mailer->Send();
		if ($send !== true) {
			JLog::add($send->getMessage(), JLog::ERROR, 'com_emundus');
			return false;
		} else {
			return true;
		}
	}


/////// chat functions
    /** send message in chat
     *
     */
    public function sendMessage() {

        $m_messages = new EmundusModelMessages();
        $jinput = JFactory::getApplication()->input;
        $message = $jinput->post->getRaw('message', null);
        $receiver = $jinput->post->get('receiver', null);
        $message = str_replace("&nbsp;", "", $message);

        echo json_encode((object)['status' => $m_messages->sendMessage($receiver, $message)]);
        exit;
    }

    /** update message list
     *
     */
    public function updatemessages() {

        $m_messages = new EmundusModelMessages();

        $jinput = JFactory::getApplication()->input;
        $lastId = $jinput->post->get('id', null);
        $messages = $m_messages->updateMessages($lastId);

        if (!empty($messages)) {
            foreach ($messages as $message) {
                $message->date_time = date("d/m/Y", strtotime($message->date_time));
            }
            echo json_encode((object)['status' => 'true', 'messages' => $messages]);
        } else {
            echo json_encode((object)['status' => 'false']);
        }

        exit;
    }

}