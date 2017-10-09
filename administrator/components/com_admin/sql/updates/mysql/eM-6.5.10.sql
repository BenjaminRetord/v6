INSERT INTO `jos_emundus_setup_emails` (`id`, `lbl`, `subject`, `emailfrom`, `message`, `name`, `type`, `published`) VALUES (NULL, 'booking_created_user', 'Confirmation de date et horaire d\'entretien.', NULL, '<H2> Bonjour [USER_NAME] </H2>\r\n\r\n<p> Votre reservation d\'entretien pour et admis dans le programme [PROGRAM] a bien été pris en compte. </p>\r\n\r\n<p> Rendez vous le [EVENT_DATE] à [EVENT_TIME]. </p>\r\n', NULL, '2', '1');
INSERT INTO `jos_emundus_setup_emails` (`id`, `lbl`, `subject`, `emailfrom`, `message`, `name`, `type`, `published`) VALUES (NULL, 'booking_created_recipient', 'Réservation d\'entretien: [USER_NAME]', NULL, '<H2> Bonjour [RECIPIENT_NAME]! </H2>\r\n\r\n<p> Le candidat [USER_NAME] a réservé un entretien le [EVENT_DATE] à [EVENT_TIME]</p>\r\n\r\n<p> Programme: [PROGRAM] </p>', NULL, '2', '1');
INSERT INTO `jos_emundus_setup_emails` (`id`, `lbl`, `subject`, `emailfrom`, `message`, `name`, `type`, `published`) VALUES (NULL, 'booking_deleted_user', 'Confirmation d\'annulation d\'entretien.', NULL, '<H2> Bonjour [USER_NAME] </H2>\r\n\r\n<p> Votre annulation d\'entretien à [EVENT_TIME] le [EVENT_DATE] pour le programme [PROGRAM] a bien été pris en compte. </p>\r\n\r\n<p> Vous pouvez toujours réserver un autre rendez-vous. </p>\r\n', NULL, '2', '1');
INSERT INTO `jos_emundus_setup_emails` (`id`, `lbl`, `subject`, `emailfrom`, `message`, `name`, `type`, `published`) VALUES (NULL, 'booking_deleted_recipient', 'Annulation d\'entretien: [USER_NAME]', NULL, '<H2> Bonjour [RECIPIENT_NAME] </H2>\r\n\r\n<p> Le candidat [USER_NAME] a annulé un entretien le [EVENT_DATE] à [EVENT_TIME]</p>\r\n\r\n<p> Programme: [PROGRAM] </p>', NULL, '2', '1')
