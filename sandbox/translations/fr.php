<?php
	
	$_LANG = array();
	$_LANG['COMPANY_NAME']							= "SPR";

	$_LANG['text_export_results']					= "Exporter les résultats";
	
	$_LANG['text_no_result'] 						= "Aucun résultat.";
	$_LANG['text_error_form'] 						= "Les champs ne sont pas correctement renseignés.";
	$_LANG['text_field_required'] 					= "Ce champ est obligatoire.";
	$_LANG['text_field_duplicate'] 					= "Cet élément existe déjà !";
	
	$_LANG['text_all'] 								= "Tous";
	$_LANG['text_browse'] 							= "Parcourir";
	$_LANG['text_validate'] 						= "Valider";
	$_LANG['text_save'] 							= "Enregistrer";
	$_LANG['text_close'] 							= "Fermer";
	$_LANG['text_delete'] 							= "Supprimer";
	$_LANG['delivery'] 							= "Chauffeur";
	$_LANG['manager'] 							= "Encadrant";
	$_LANG['manager_delivery'] 							= "Accès back-office / Miami - Repas";
	$_LANG['manager_telegestion'] 							= "Accès back-office / Miami - Télégestion";
	$_LANG['telegestion'] 							= "Intervenant / Miami - Telegestion";
	
	$_LANG['error_data_missing'] = "Erreur, il manque des données. Contactez un administrateur.";
	$_LANG['error_data_empty'] = "Problème, vous avez oublié de remplir des champs.";
	$_LANG['error_insert'] = "Erreur, l'ajout ne s'est pas appliqué. Contactez un administrateur.";
	$_LANG['error_update'] = "Erreur, la modification ne s'est pas appliqué. Contactez un administrateur.";
	$_LANG['error_delete'] = "Erreur, la suppression ne s'est pas appliqué. Contactez un administrateur.";
	$_LANG['error_action'] = "Erreur, vous tentez d'utiliser une action non présente sur le logiciel.";
	$_LANG['error_rules'] = "Erreur, vous n'avez pas les droits pour suprimer cet élément.";
	$_LANG['text_timer'] = "Vous allez être redirigé dans :";
	$_LANG['text_details'] = "Détail :";
	$_LANG['text_reset_password_mail'] = "Envoyer un mail à l'utilisateur pour réinitialiser son mot de passe";
	$_LANG['text_reset_password_information'] = "Un mail sera envoyé à l'adresse mail renseigné.";
	$_LANG['text_mail_unknown'] = "Ce mail n'est pas valide ou n'existe pas dans notre base de données.";
	
	// *************************************************************************************
	// ErrorController
	
	$_LANG['text_error_page_not_found']				= 'Page inexistante';
	$_LANG['text_error_404']						= "La page demandée n'existe pas. Veuillez contacter votre administrateur.";
	
	// *************************************************************************************
	// AuthController
	
	$_LANG['text_auth_error_too_many_attempts'] 	= "Vous avez dépassé les tentatives infructueuses autorisées.<br />Veuillez réessayer ultérieurement.";
	$_LANG['text_auth_error'] 						= "Identifiants incorrects.";
	$_LANG['text_auth_success'] 					= "Bienvenue sur le CRM.";
	
	$_LANG['text_auth_mail']						= "Adresse mail";
	$_LANG['text_auth_password']					= "Mot de passe";
	$_LANG['text_auth_connect']						= "Se connecter";
	$_LANG['text_auth_forgot']						= "Mot de passe oublié ?";
	
	// *************************************************************************************
	// LostController
	$_LANG['text_password_forgotten'] = "Mot de passe oublié";
	$_LANG['label_login'] = "E-mail";
	$_LANG['text_mail_error_send'] = "Votre compte a été créé mais l'envoi du mail de confirmation a rencontré une erreur, veuillez vérifier vos paramètres de boîte email.";
	$_LANG['ajax_error_mail_unknown'] = "Ce mail n'est pas valide ou n'existe pas dans notre base de données.";
	$_LANG['ajax_sucess_mail_sent'] = "Un mail contenant le lien de réinitialisation vous a été envoyé (pensez à vérifier vos SPAMs).";//mot de passe oublié
	$_LANG['text_mail_sent_user'] = "Le compte a bien été créé.<br><br>Un mail a été envoyé à l'utilisateur afin de l'inviter à personnaliser son mot de passe."; //mot de passe generer
	$_LANG['text_mail_send_change'] = "Un mail a été envoyé à l'utilisateur afin de l'inviter à personnaliser son mot de passe."; //mot de passe generer
	$_LANG['text_mail_sent_user_edit'] = "Le compte a bien été modifié."; //mot de passe generer
	$_LANG['ajax_success_password_change'] = "Votre mot de passe a bien été modifié, un mail de confirmation vous a été envoyé.";
	$_LANG['ajax_error_password_change'] = "Mot de passe incorrect, veuillez respecter les critères de sécurité.";

	// *************************************************************************************
	// ActivitiesController (BACK)
	
	$_LANG['text_search_activity']					= "Rechercher une activité ...";
	
	$_LANG['text_activities']						= "Activités";
	$_LANG['text_activity_add']						= "Ajouter une activité";
	
	$_LANG['text_activity_new']						= "Nouvelle activité";
	$_LANG['text_activity_details']					= "Détails de l'activité [%s] %s";
	
	$_LANG['activity_id']							= "ID";
	$_LANG['activity_code']							= "Code";
	$_LANG['activity_code_placeholder']				= "5";
	$_LANG['activity_name']							= "Nom";
	$_LANG['activity_name_placeholder']				= "Chimie";
	$_LANG['activity_status']						= "Etat";
	
	$_LANG['activity_enabled']						= "Actif";
	$_LANG['activity_disabled']						= "Inactif";
	
	$_LANG['success_activity_insert']				= "L'activité a été créée avec succès.";
	$_LANG['success_activity_update']				= "L'activité a été modifiée avec succès.";
	
	$_LANG['error_activity_insert']					= "Création impossible.";
	$_LANG['error_activity_update']					= "Modification impossible.";
	
	// *************************************************************************************
	// CapacitiesController (BACK)
	
	$_LANG['text_search_activity']					= "Rechercher une fonction ...";
	
	$_LANG['text_capacities']						= "Fonctions";
	$_LANG['text_capacity_add']						= "Ajouter une fonction";
	
	$_LANG['text_capacity_new']						= "Nouvelle fonction";
	$_LANG['text_capacity_details']					= "Détails de la fonction [%s] %s";
	
	$_LANG['capacity_id']							= "ID";
	$_LANG['capacity_code']							= "Code";
	$_LANG['capacity_code_placeholder']				= "A1";
	$_LANG['capacity_name']							= "Nom";
	$_LANG['capacity_name_placeholder']				= "DAF";
	$_LANG['capacity_status']						= "Etat";
	
	$_LANG['capacity_enabled']						= "Actif";
	$_LANG['capacity_disabled']						= "Inactif";
	
	$_LANG['success_capacity_insert']				= "La fonction a été créée avec succès.";
	$_LANG['success_capacity_update']				= "La fonction a été modifiée avec succès.";
	
	$_LANG['error_capacity_insert']					= "Création impossible.";
	$_LANG['error_capacity_update']					= "Modification impossible.";
	
	// *************************************************************************************
	// UsersController (BACK)
	
	$_LANG['text_mail_sent'] = "Un mail contenant le lien de réinitialisation vous a été envoyé (pensez à vérifier vos SPAMs).";//mot de passe oublié
	$_LANG['text_mail_sent_user'] = "Le compte a bien été créé.<br><br>Un mail a été envoyé à l'utilisateur afin de l'inviter à personnaliser son mot de passe."; //mot de passe generer
	$_LANG['text_mail_send_change'] = "Un mail a été envoyé à l'utilisateur afin de l'inviter à personnaliser son mot de passe."; //mot de passe generer
	$_LANG['text_users'] = "Utilisateurs";
	$_LANG['text_add_user'] = "Ajouter un utilisateur";
	$_LANG['action_add_user'] = "Ajouter un utilisateur";
	$_LANG['action_search_user'] = "Rechercher un utilisateur ...";
	$_LANG['text_use_id'] = "Utilisateur ID";
	$_LANG['text_user_firstname'] = "Prénom";
	$_LANG['text_user_lastname'] = "Nom";
	$_LANG['text_user_initials'] = "Initiales";
	$_LANG['text_user_mail'] = "Mail";
	$_LANG['text_user_password'] = "Mot de passe";
	$_LANG['text_user_enabled'] = "État";
	$_LANG['user_enable'] = "Actif";
	$_LANG['user_disable'] = "Désactiver";
	$_LANG['error_user_isset'] = "Un utilisateur existe déjà avec cette adresse mail.";
	// $_LANG['success_insert_user'] = "Ajout d'un utilisateur effectué avec succés";
	$_LANG['success_insert_user'] = $_LANG['text_mail_sent_user'];
	$_LANG['success_udpate_user'] = "L'utilisateur a été modifié avec succès.";
	
	// *************************************************************************************
	// DashboardController (FRONT)
	$_LANG['text_dashboard'] = "Tableau de bord";
	$_LANG['text_dashboard_todos'] = "Ma liste à faire du jour";
	$_LANG['text_dashboard_no_task'] = "Aucune tâche à faire";
	$_LANG['text_dashboard_alerts'] = "Alertes";
	$_LANG['text_dashboard_no_alert'] = "Aucune alerte";
	$_LANG['text_dashboard_update_task'] = "Voulez vous terminer la tâche ?";
	$_LANG['text_dashboard_finish_task'] = "Activités effectuées";
	$_LANG['text_dashboard_current_task'] = "Activités à faire";
	$_LANG['text_dashboard_inialise'] = "Initialement prévu le ";
	
	$_LANG['text_dashboard_current_task_call'] = "Appeler le contact ";
	$_LANG['text_dashboard_current_task_calls'] = "Appeler les contacts ";
	$_LANG['text_dashboard_current_task_visit'] = "Rendre visite à ";
	$_LANG['text_dashboard_current_task_visits'] = "Rendre visite aux ";
	$_LANG['text_dashboard_current_task_order_slip'] = "Faire un devis au ";
	$_LANG['text_dashboard_current_task_orders_slip'] = "Faire un devis aux ";
	
	
	$_LANG['text_dashboard_alert_task_call'] = "n'a pas été rappelé.";
	$_LANG['text_dashboard_alert_task_calls'] = "n'ont pas été rappelés.";
	$_LANG['text_dashboard_alert_task_visit'] = "n'a pas été visité";
	$_LANG['text_dashboard_alert_task_visits'] = "n'ont pas été visités.";
	$_LANG['text_dashboard_alert_task_order_slip'] = "n'a pas reçu de devis.";
	$_LANG['text_dashboard_alert_task_orders_slip'] = "n'ont pas reçu de devis.";
	
	
	// *************************************************************************************
	// DocumentsController (FRONT)
	
	$_LANG['text_documents_sending']				= "Envoi des documents en cours, veuillez patienter";
	$_LANG['text_confirm_document_deletion']		= "Voulez-vous supprimer le document #%s - %s ?";
	
	$_LANG['document_id']							= "ID";
	$_LANG['document_date']							= "Date";
	$_LANG['document_name']							= "Nom";

	// *************************************************************************************
	// CompaniesController (FRONT)
	
	$_LANG['text_search_company']					= "Rechercher un prospect ou un client ...";
	
	$_LANG['company_name']							= "Nom";
	$_LANG['company_city']							= "Ville";
	$_LANG['company_department']					= "Département";
	$_LANG['company_groupname']						= "Nom du groupe";
	$_LANG['activity_code']							= "Code activité";
	$_LANG['company_state']							= "Statut";
	$_LANG['company_substate']						= "Sous-statut";
	$_LANG['company_visitstate']					= "Déjà visité";
	$_LANG['user_initials']							= "Consultant";
	$_LANG['company_date_relaunch']					= "Date de relance";
	
	$_LANG['text_company_new']						= "Nouvelle entreprise";
	$_LANG['text_company_details']					= "Détails de l'entreprise %s";
	
	$_LANG['text_company_add']						= "Ajouter une entreprise";
	
	$_LANG['success_company_update']				= "L'entreprise a été modifiée avec succès.";

	// *************************************************************************************
	// TasksController (FRONT)
	
	$_LANG['text_search_task']						= "Rechercher une tâche ...";
	$_LANG['text_search_contact_or_company']		= "Rechercher un prospect, un client ou un contact";
	
	$_LANG['text_tasks']							= "Tâches";
	$_LANG['text_add_task']							= "Ajouter une tâche";
	$_LANG['text_add_document']						= "Ajouter un document";
	$_LANG['text_new_task']							= "Nouvelle tâche";
	$_LANG['text_task_details']						= "Détail de la tâche #%s";
	
	$_LANG['text_general']							= "Général";
	$_LANG['text_documents']						= "Documents";
	
	$_LANG['text_status']							= "Etat de la tâche";
	$_LANG['text_in_progress']						= "En cours";
	$_LANG['text_achieved']							= "Réalisée";
	$_LANG['text_finished']							= "Terminées";
	
	$_LANG['text_task_current']						= "Activité en cours ?";
	
	$_LANG['task_id']								= "ID";
	$_LANG['task_date']								= "Date";
	$_LANG['task_time']								= "Heure";
	$_LANG['task_type']								= "Opération";
	$_LANG['task_user']								= "Consultant";
	$_LANG['task_relations']						= "Destinataire(s)";
	
	$_LANG['task_content']							= "Détail de la tâche";
	$_LANG['task_action']							= "Action réalisée";
	
	$_LANG['relation_type']							= "Type";
	$_LANG['relation_name']							= "Nom";
	
	$_LANG['success_task_insert']					= "La tâche a été créée avec succès.";
	$_LANG['success_task_update']					= "La tâche a été modifiée avec succès.";
	
	$_LANG['error_task_insert']						= "Création impossible.";
	$_LANG['error_task_update']						= "Modification impossible.";
	
	// *************************************************************************************
	// NetworksController (FRONT)
	
	$_LANG['text_search_network']					= "Rechercher un prospect, un client, un candidat ou une relation dans le réseau ...";
	
	$_LANG['text_networks']							= "Réseaux";

	// *************************************************************************************
	// ContactsController (FRONT)
	
	$_LANG['text_search_contact']					= "Rechercher un candidat ou un réseau ...";
	
	$_LANG['contact_lastname']						= "Nom";
	$_LANG['contact_firstname']						= "Prénom";
	$_LANG['contact_state']							= "Statut";
	$_LANG['contact_substate']						= "Sous-statut";
	$_LANG['contact_capacities']					= "Code fonction";
	$_LANG['contact_phone']							= "Téléphone";
	$_LANG['contact_mobile']						= "Mobile";
	$_LANG['contact_mail']							= "Adresse mail";
	$_LANG['contact_salary']						= "Rénumération";
	$_LANG['contact_date_last']						= "Dernier contact";
	
	$_LANG['text_contacts']							= "Candidats / Réseaux";
	
	$_LANG['text_contact_add']						= "Ajouter un candidat / réseau";
	
	$_LANG['text_contact_company_add']				= "Ajouter une entreprise";
	
	$_LANG['text_contact_new']						= "Nouveau candidat / réseau";
	$_LANG['text_contact_details']					= "Détails du candidat / réseau %s";
	
	$_LANG['success_contact_insert']				= "Le contact a été ajouté avec succès.";
	$_LANG['success_contact_update']				= "Le contact a été modifié avec succès.";

	// *************************************************************************************
	// QueryController (FRONT)
	
	$_LANG['text_search_query']						= "Rechercher un candidat, un prospect ou un client ...";
	
	$_LANG['text_query']							= "Requête";
	$_LANG['text_query_title']						= "Recherche générale";
	
	$_LANG['text_contact_add']						= "Ajouter un candidat / réseau";
	
	$_LANG['text_contact_company_add']				= "Ajouter une entreprise";
	
	$_LANG['text_contact_new']						= "Nouveau candidat / réseau";
	$_LANG['text_contact_details']					= "Détails du candidat / réseau %s";
	
	$_LANG['success_contact_insert']				= "Le contact a été ajouté avec succès.";
	$_LANG['success_contact_update']				= "Le contact a été modifié avec succès.";

	// *************************************************************************************
	$_LANG['text_device_details']					= "Détails de l'appareil [%s] %s";
	$_LANG['text_user_details']						= "Détails de l'utilisateur [%s] %s";
	$_LANG['text_tour_details']						= "Détails de la tournée [%s] %s";
	$_LANG['text_message_details']					= "Détails de la messagerie [%s] %s";
	$_LANG['text_menu_details']						= "Détails du menu [%s] %s";
	$_LANG['text_stock_details']						= "Détails du stock [%s] %s";
	$_LANG['bread_white']							= "Pain blanc";
	$_LANG['bread_soft']							= "Pain Gris";
?>