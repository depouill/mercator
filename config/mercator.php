<?php return array (
  'cert' => 
  array (
    'mail-from' => 'mercator@localhost',
    'mail-to' => 'helpdesk@localhost',
    'mail-subject' => '[Mercator] Certificate expiration',
    'check-frequency' => '0',
    'expire-delay' => '1',
    'group' => '0',
    'repeat-notification' => '0',
  ),
  'cve' => 
  array (
    'mail-from' => 'mercator@localhost',
    'mail-to' => 'secops@localhost',
    'mail-subject' => '[Mercator] Vulnerability detected',
    'check-frequency' => '1',
    'provider' => 'https://vulnerability.circl.lu',
    'guesser' => 'https://cpe-guesser.cve-search.org',
  ),
  'parameters' => 
  array (
    'security_need_auth' => true,
  ),
  'cpe' => 
  array (
    'guesser' => 'https://cpe-guesser.cve-search.org',
  ),
  'cartography' => 
  array (
    'reminders_enabled' => true,
    'reminder_from' => 'mercator@localhost',
    'reminder_subject' => '[Mercator] Rappel',
    'reminder_body' => '<!DOCTYPE html>
<html lang=\'fr\'>
<body>
  <p>Bonjour :name,</p>
  <p>
    :count objet(s) dont tu es le cartographe n\'ont pas été mis à jour depuis plus de :months mois :
  </p>
  :list
  <p>
    Merci de mettre à jour ces objets dans <a href=\'https://www.mercator.localhost\'>Mercator</a>.
  </p>
  <p><em>Ce message a été généré automatiquement par Mercator. Merci de ne pas y répondre directement.<br>
Si tu penses avoir reçu ce message par erreur, contacte ton administrateur.</em></p>
</body>
</html>',
    'reminder_months' => 6,
    'reminder_every_days' => 30,
    'modification_enabled' => true,
    'modification_to' => NULL,
    'modification_from' => 'mercator@localhost',
    'modification_subject' => '[Mercator] Objet modifié',
    'modification_body' => '<!DOCTYPE html>
<html lang=\'fr\'>
<body>
  <p>L\'objet <strong>:objet</strong> a été modifié :history par :user.</p>
</body>
</html>',
    'reminder_last_sent' => '2026-06-03 10:53',
  ),
);