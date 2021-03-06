<?php
/**
 * @package    eMundus
 * @subpackage Components
 *             components/com_emundus/emundus.php
 * @link       http://www.emundus.fr
 * @license    GNU/GPL
 * @author     Benjamin Rivalland
*/

// no direct access

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Emundus Component
 *
 * @package    Emundus
 */

class EmundusViewExport_select_columns extends JViewLegacy
{
	var $_user = null;
	var $_db = null;

	function __construct($config = array()){
		require_once (JPATH_COMPONENT.DS.'helpers'.DS.'files.php');
		require_once (JPATH_COMPONENT.DS.'helpers'.DS.'access.php');
		require_once (JPATH_COMPONENT.DS.'models'.DS.'programme.php');

		$this->_user = JFactory::getUser();
		$this->_db = JFactory::getDBO();

		parent::__construct($config);
	}

    function display($tpl = null) {
        $m_program  = new EmundusModelProgramme();

        require_once (JPATH_COMPONENT.DS.'models'.DS.'admission.php');
        require_once (JPATH_COMPONENT.DS.'models'.DS.'evaluation.php');

        $jinput = JFactory::getApplication()->input;
        $prg    = $jinput->getVar('code', null);
        $view   = $jinput->getVar('viewcall', null);
        $form   = $jinput->getVar('form', null);
        
        $camp   = $jinput->getVar('camp', null);

        $program    = $m_program->getProgramme($prg);
        $code       = array();
        $camps       = array();
        $code[]     = $prg;
        $camps[]     = $camp;
        //var_dump($camps);
        $current_user = JFactory::getUser();
       
        /*if ($view == "evaluation" || $form == "decision" || $form == "admission") {
            $session = JFactory::getSession();
            $params['programme'] = $code;
            $session->set('filt_params', $params);
        }*/

        
        if (!EmundusHelperAccess::asPartnerAccessLevel($current_user->id))
            die(JText::_('ACCESS_DENIED'));

        $m_admission = new EmundusModelAdmission;
        $m_eval = new EmundusModelEvaluation;
        
        //TODO fix bug when a different application form is created for the same programme. Need to now the campaign id, then associated profile and menu links...
	    // To fix this : Get all campaigns, get profile, get menu, check form IDs, for each unique ID: make an array containing the code below (or some variety of it).
	    // When displaying the results: make tabs or panels separating the different forms for the programme.


        if ($form == "decision")
            $elements = $m_admission->getAdmissionElementsName(0, 0, $code);
        elseif ($form == "admission")
            $elements = $m_admission->getApplicantAdmissionElementsName(0, 0, $code);
        elseif ($form == "evaluation")
            $elements = $m_eval->getEvaluationElementsName(0, 0, $code);
        else
		    $elements = EmundusHelperFiles::getElements($code, $camps);
        
        $this->assignRef('elements', $elements);
        $this->assignRef('form', $form);
        $this->assignRef('program', $program->label);
		parent::display($tpl);
    }
}
?>