<?php echo __('Un rendez-vous vous est proposé par') ?> <?php echo $user->getName().' '.$user->getSurname().' ('.$user->getMail().')' ; ?> : 
<?php echo $meeting->getTitle() ?>

<?php echo __("Vous pouvez indiquer vos disponibilités en vous rendant à l'adresse suivante") ?> : http://<?php echo sfConfig::get('app_url').'/'.$meeting->getHash() ?> .

<?php echo __('Le code de ce rendez-vous est') ?> : <?php echo $meeting->getHash() ?> ; <?php echo __('notez le bien, il pourra vous servir à retrouver la page du sondage') ?>.

<?php echo __("Les votes seront disponibles jusqu'au") ?> <?php setlocale(LC_TIME,'fr_FR.utf8','fra') ; $end = $meeting->getDateEnd() ; echo strftime("%A %d %B %Y", strtotime($end)) ?>.

----
<?php echo __('Ceci est un message automatique, veuillez ne pas y répondre') ?>.
