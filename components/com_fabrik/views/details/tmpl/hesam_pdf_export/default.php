<?php
/**
 * Bootstrap Details Template
 *
 * @package     Joomla
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.1
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$form = $this->form;
$model = $this->getModel();

if ($this->params->get('show_page_heading', 1)) : ?>
    <div class="componentheading<?php echo $this->params->get('pageclass_sfx') ?>">
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </div>
<?php
endif;
?>

<?php
echo $form->intro;
if ($this->isMambot) :
    echo '<div class="fabrikForm fabrikDetails fabrikIsMambot" id="' . $form->formid . '">';
else :
    echo '<div class="fabrikForm fabrikDetails" id="' . $form->formid . '">';
endif;
echo $this->plugintop;
echo $this->loadTemplate('buttons');
echo $this->loadTemplate('relateddata');


$db = JFactory::getDBO();

$query = $db->getquery('true');
// Get all uploaded files
$query
    ->select($db->quoteName(array('eup.filename', 'sa.value')))
    ->from($db->quoteName('#__emundus_uploads', 'eup'))
    ->join('LEFT', $db->quoteName('#__emundus_setup_attachments', 'sa') . ' ON (' . $db->quoteName('sa.id') . ' = ' . $db->quoteName('eup.attachment_id') . ')')
    ->where($db->quoteName('fnum') . ' LIKE "' . $this->data['jos_emundus_recherche___fnum_raw'] . '" AND eup.can_be_viewed = 1');

$db->setQuery($query);

try {
    $files = $db->loadAssocList();
} catch (Exection $e) {
    echo "<pre>";
    var_dump($query->__toString());
    echo "</pre>";
    die();
}


?>


<style>
    .fabrikForm.fabrikDetails {
        display: block;
        width: 90%;
        margin-left: auto;
        margin-right: auto;
    }

    .em-pdf-group {
        margin-bottom: 35px;
    }

    .em-pdf-title-div {
        background-color: #e9E9E9;
        border-top: 1px solid;
        border-bottom: 1px solid;
    }

    .em-pdf-title-div h3 {
        margin: 0px 0px 0px 10px;
    }

    .em-pdf-element {
        font-size: 16px;
        border-bottom: 1px solid;
        display: inline-block;
        width: 100%;
    }

    .em-pdf-element-label {
        float: left;
        display: inline-block;
        width: 35%;
        font-weight: bold;
    }

    .em-pdf-element-label p {
        margin: 0px 0px 0px 10px;
    }

    .em-pdf-element-value {
        display: inline-block;
        width: 64%;
    }


</style>

<div class="em-pdf-group">
    <img src="images/custom/Hesam/Logo_1000doctorants.JPG" alt="Logo 1000doctorants" style="vertical-align: top;"
         width="252" height="90">
    <div class="em-pdf-title-div">
        <h3>Récapitulatif de l'annonce déposé sur <a href="<?php echo JPATH_SITE; ?>"><?php echo JPATH_SITE; ?></a></h3>
    </div>

    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Date de dépôt du dossier</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php echo date("d/m/Y", strtotime($this->data["jos_emundus_campaign_candidature___date_submitted_raw"][0])); ?></p>
        </div>

    </div>
</div>


<div class="em-pdf-group">
    <div class="em-pdf-title-div">
        <h3>Auteur de l'annonce</h3>
    </div>

    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Type</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php echo $this->data["jos_emundus_setup_profiles___label_raw"][0]; ?></p>
        </div>

    </div>

    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Date de disponibilité</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php echo $this->data["jos_emundus_projet___limit_date_raw"][0]; ?></p>
        </div>

    </div>

</div>

<div class="em-pdf-group">
    <div class="em-pdf-title-div">
        <h3>Le projet</h3>
    </div>

    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Titre</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php echo $this->data["jos_emundus_projet___titre_raw"][0]; ?></p>
        </div>

    </div>
    <?php if ($this->data["jos_emundus_setup_profiles___id_raw"][0] == '1006') : ?>

        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Enjeu et actualité du sujet</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $this->data["jos_emundus_projet___contexte_raw"][0]; ?></p>
            </div>

        </div>

    <?php endif; ?>


    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Problématique</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php echo $this->data["jos_emundus_projet___question_raw"][0]; ?></p>
        </div>

    </div>

    <?php if ($this->data["jos_emundus_setup_profiles___id_raw"][0] == '1006') : ?>

        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Méthodologie proposée</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $this->data["jos_emundus_projet___methodologie_raw"][0]; ?></p>
            </div>

        </div>

    <?php endif; ?>

    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Thématiques associées</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php echo implode(", ", $this->data["data_thematics___thematic_raw"]); ?></p>
        </div>

    </div>

    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Disciplines sollicitées</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php echo is_array($this->data["em_discipline___disciplines_raw"]) ? implode(', ', $this->data["em_discipline___disciplines_raw"]) : $this->data["em_discipline___disciplines_raw"]; ?></p>
        </div>

    </div>

</div>

<div class="em-pdf-group">

    <div class="em-pdf-title-div">
        <h3>Les partenaires recherchés</h3>
    </div>

    <?php if ($this->data["jos_emundus_setup_profiles___id_raw"][0] != '1006') : ?>

        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Un future doctorant</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $this->data["jos_emundus_recherche___futur_doctorant_yesno"]; ?></p>
            </div>

        </div>

        <?php if ($this->data["jos_emundus_recherche___futur_doctorant_yesno"] == 0) : ?>
            <div class="em-pdf-element">

                <div class="em-pdf-element-label">
                    <p>Nom et prénom du future doctorant</p>
                </div>

                <div class="em-pdf-element-value">
                    <p><?php echo strtoupper($this->data["jos_emundus_recherche___futur_doctorant_nom"]) . " " . $this->data["jos_emundus_recherche___futur_doctorant_prenom"]; ?></p>
                </div>

            </div>
        <?php endif; ?>


    <?php endif; ?>

    <div class="em-pdf-element">

        <div class="em-pdf-element-label">
            <p>Une équipe de recherche</p>
        </div>

        <div class="em-pdf-element-value">
            <p><?php echo $this->data["jos_emundus_recherche___equipe_de_recherche_codirection_yesno"]; ?></p>
        </div>

    </div>


    <?php if ($this->data["jos_emundus_recherche___equipe_de_recherche_codirection_yesno_raw"] == 0) : ?>
        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Nom de l'équipe partenaire</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $this->data["jos_emundus_recherche___equipe_codirection_nom_du_laboratoire"]; ?></p>
            </div>

        </div>
    <?php endif; ?>

    <?php if ($this->data["jos_emundus_setup_profiles___id_raw"][0] != '1008') : ?>
        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Un acteur public ou associatif</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $this->data["jos_emundus_recherche___acteur_public_yesno"]; ?></p>
            </div>

        </div>

        <div class="em-pdf-element">

            <div class="em-pdf-element-label">
                <p>Type</p>
            </div>

            <div class="em-pdf-element-value">
                <p><?php echo $this->data["jos_emundus_recherche___acteur_public_type_raw"]; ?></p>
            </div>

        </div>

        <?php if ($this->data["jos_emundus_recherche___acteur_public_yesno_raw"] == 0) : ?>
            <div class="em-pdf-element">

                <div class="em-pdf-element-label">
                    <p>Nom du partenaire</p>
                </div>

                <div class="em-pdf-element-value">
                    <p><?php echo $this->data["jos_emundus_recherche___acteur_public_nom_de_structure_raw"]; ?></p>
                </div>

            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<div class="em-pdf-group">
    <?php if (!empty($files)) : ?>
        <div class="em-pdf-title-div">
            <h3>Pièces jointes à l'annonce</h3>
        </div>


        <?php foreach ($files as $file) : ?>
            <div class="em-pdf-element">

                <div class="em-pdf-element-label">
                    <p><?php echo $file["value"]; ?></p>
                </div>

                <div class="em-pdf-element-value">
                    <p><?php echo $file["filename"]; ?></p>
                </div>

            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>