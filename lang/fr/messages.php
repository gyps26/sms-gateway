<?php

return [
    'auto_response' => [
        'created' => 'Réponse automatique créée avec succès.',
        'updated' => 'Réponse automatique mise à jour avec succès.',
        'deleted' => 'Réponse automatique supprimée avec succès.'
    ],
    'blacklist' => [
        'created' => 'Nouvelle entrée ajoutée à la liste noire avec succès.|Nouvelles entrées ajoutées à la liste noire avec succès.',
        'delete' => [
            'failed' => 'Aucun numéro mobile n\'a été supprimé de la liste noire.',
            'success' => 'Un numéro mobile supprimé avec succès.|:count numéros mobiles supprimés avec succès.',
        ]
    ],
    'coupon' => [
        'created' => 'Coupon créé avec succès.',
        'updated' => 'Coupon mis à jour avec succès.'
    ],
    'contact' => [
        'created' => 'Contact ajouté avec succès.',
        'updated' => 'Contact mis à jour avec succès.',
        'import' => [
            'queued' => 'Les contacts sont en cours d\'importation. Vous serez notifié une fois le processus terminé.',
            'cancelled' => 'Le processus d\'importation a été annulé. Cela peut prendre quelques minutes pour arrêter le processus.',
            'not_running' => 'Le travail d\'importation n\'est pas en cours d\'exécution ou il est déjà terminé.',
        ],
        'export' => [
            'queued' => 'Les contacts sont en cours d\'exportation. Vous serez notifié une fois le processus terminé.',
        ],
        'delete' => [
            'failed' => 'Aucun contact n\'a été supprimé de la liste de contacts.',
            'success' => 'Un contact supprimé avec succès.|:count contacts supprimés avec succès.',
        ],
    ],
    'contact_list' => [
        'created' => 'Liste de contacts créée avec succès.',
        'updated' => 'Liste de contacts mise à jour avec succès.',
        'deleted' => 'Liste de contacts supprimée avec succès.'
    ],
    'call' => [
        'delete' => [
            'failed' => 'Aucun appel n\'a été supprimé du journal d\'appels.',
            'success' => 'Un appel supprimé avec succès.|:count appels supprimés avec succès.'
        ]
    ],
    'campaign' => [
        'created' => 'Campagne créée avec succès.',
        'updated' => 'Campagne mise à jour avec succès.',
        'retried' => 'Campagne relancée avec succès.',
        'unable_to_retry' => 'Il n\'est pas possible de relancer cette campagne.',
        'unable_to_cancel' => 'Il n\'est pas possible d\'annuler cette campagne.',
        'delete' => [
            'failed' => 'Aucune campagne n\'a été supprimée.',
            'success' => 'Une campagne supprimée avec succès.|:count campagnes supprimées avec succès.',
        ],
        'cancelling' => 'Campagne marquée pour annulation avec succès.',
    ],
    'device' => [
        'updated' => 'Appareil mis à jour avec succès.',
        'deleted' => 'Appareil supprimé avec succès.',
        'shared' => 'Appareil partagé avec succès.',
        'campaign' => [
            'cancelled' => 'Campagne annulée avec succès pour cet appareil.',
            'cancelling' => 'Campagne marquée pour annulation avec succès pour cet appareil.',
            'retried' => 'Campagne relancée avec succès pour cet appareil.',
            'unable_to_retry' => 'Il n\'est pas possible de relancer cette campagne pour cet appareil.',
        ],
        'register' => [
            'invalid_credentials' => 'L\'adresse e-mail ou le mot de passe est incorrect.',
            'invalid_qr_code' => 'Le code QR est expiré ou non valide.',
            '2fa' => [
                'required' => 'Le code d\'authentification à deux facteurs est requis.',
                'incorrect' => 'Le code d\'authentification à deux facteurs est incorrect.'
            ]
        ]
    ],
    'field' => [
        'created' => 'Champ ajouté avec succès.',
        'updated' => 'Champ mis à jour avec succès.',
        'deleted' => 'Le champ ":label" a été supprimé avec succès.'
    ],
    'message' => [
        'delete' => [
            'failed' => 'Aucun message n\'a été supprimé.',
            'success' => 'Un message supprimé avec succès.|:count messages supprimés avec succès.',
        ],
        'retry' => [
            'queued' => 'Les messages sont en cours de réexpédition. Vous serez averti une fois le processus terminé.',
            'failed' => 'Aucun message n’a été réessayé.',
            'success' => 'Un message a été réessayé avec succès.|:count messages ont été réessayés avec succès.',
        ],
    ],
    'payment' => [
        'approved' => 'Paiement approuvé avec succès.',
        'declined' => 'Paiement refusé avec succès.',
        'completed' => 'Paiement réussi. Votre abonnement sera activé sous peu.',
    ],
    'plan' => [
        'created' => 'Plan créé avec succès.',
        'updated' => 'Plan mis à jour avec succès.',
        'disabled' => 'Il n’est pas possible de s’abonner à ce forfait car il est désactivé.',
        'downgradeNotAllowed' => 'Il n’est pas possible de passer à un forfait inférieur en raison de critères dépassant les limites autorisées.',
        'alreadySubscribed' => 'Un abonnement actif existe déjà pour ce compte utilisateur.',
    ],
    'quota' => [
        'updated' => 'Quota mis à jour avec succès.',
    ],
    'sender_id' => [
        'created' => 'ID d\'expéditeur créé avec succès.',
        'deleted' => 'ID d\'expéditeur supprimé avec succès.',
        'shared' => 'ID d\'expéditeur a été partagé avec succès.',
    ],
    'sending_server' => [
        'created' => 'Serveur d\'envoi créé avec succès.',
        'updated' => 'Serveur d\'envoi mis à jour avec succès.',
        'deleted' => 'Serveur d\'envoi supprimé avec succès.',
        'campaign' => [
            'cancelled' => 'Campagne annulée avec succès pour ce serveur d\'envoi.',
            'cancelling' => 'Campagne marquée pour annulation avec succès pour ce serveur d\'envoi.',
            'retried' => 'Campagne relancée avec succès pour ce serveur d\'envoi.',
            'unable_to_retry' => 'Il n\'est pas possible de relancer cette campagne pour ce serveur d\'envoi.',
        ],
    ],
    'sim' => [
        'updated' => 'Carte SIM mise à jour avec succès.',
    ],
    'subscription' => [
        'assigned' => 'Abonnement attribué avec succès.',
        'started' => 'Abonnement démarré avec succès.',
        'cancelled' => 'Abonnement annulé avec succès.',
        'updated' => 'Abonnement mis à jour avec succès.',
    ],
    'tax' => [
        'created' => 'Taxe créée avec succès.',
        'updated' => 'Taxe mise à jour avec succès.',
        'deleted' => 'Taxe supprimée avec succès.',
    ],
    'template' => [
        'created' => 'Modèle créé avec succès.',
        'updated' => 'Modèle mis à jour avec succès.',
        'deleted' => 'Modèle supprimé avec succès.',
    ],
    'user' => [
        'created' => 'Utilisateur créé avec succès.',
        'deleted' => 'Utilisateur supprimé avec succès.',
    ],
    'ussd_pull' => [
        'delete' => [
            'failed' => 'Aucune requête USSD pull n\'a été supprimée.',
            'success' => 'Une requête USSD pull supprimée avec succès.|:count requêtes USSD pull supprimées avec succès.',
        ],
        'retry' => [
            'queued' => 'Les requêtes d\'extraction USSD sont en cours de réexécution. Vous serez averti une fois le processus terminé.',
            'failed' => 'Aucune requête USSD n’a été réessayée.',
            'success' => 'Une requête USSD a été réessayée avec succès.|:count requêtes USSD ont été réessayées avec succès.',
        ],
    ],
    'webhook_call' => [
        'resent' => 'Appel webhook renvoyé avec succès.',
    ],
    'webhook' => [
        'created' => 'Webhook créé avec succès.',
        'updated' => 'Webhook mis à jour avec succès.',
        'deleted' => 'Webhook supprimé avec succès.'
    ],
    'prompts' => [
        'blacklist' => 'Vous ne recevrez plus de messages de notre part.',
        'whitelist' => 'Votre numéro a été supprimé avec succès de la liste noire.',
        'whitelist_or_subscribe' => 'Répondez simplement ":prompt" si vous changez d\'avis.',
        'subscribe' => 'Votre numéro a été abonné avec succès à la liste de contacts.',
        'unsubscribe' => 'Votre numéro a été désabonné avec succès de la liste de contacts.',
        'general' => 'Faites-nous savoir si vous changez d\'avis.'
    ],
    'payment_gateway' => [
        'currency_not_supported' => 'La devise n\'est pas prise en charge.',
        'crypto_com' => [
            'interval_not_supported' => 'Seuls les plans mensuels sont pris en charge par Crypto.com.'
        ],
        'paypal' => [
            'interval_not_supported' => 'La valeur d\'intervalle n\'est pas prise en charge pour cette unité d\'intervalle.'
        ]
    ],
    'mailer' => [
        'test' => [
            'success' => 'E-mail de test envoyé avec succès.',
        ]
    ],
    'imap' => [
        'test' => [
            'success' => 'Connexion IMAP réussie.',
        ]
    ],
    'installer' => [
        'errors' => [
            'depedencies' => 'Veuillez vous assurer que toutes les dépendances sont installées ou activées avant de continuer.',
            'permissions' => 'Veuillez vous assurer que toutes les autorisations requises sont accordées avant de continuer.',
            'database' => 'La connexion à la base de données a échoué avec l\'erreur suivante : :message',
            'admin' => 'Veuillez créer un compte administrateur pour continuer l\'installation.',
            'completed' => 'Impossible de marquer l\'installation comme terminée. Veuillez réessayer.',
        ],
    ],
    'global' => [
        'error' => 'Une erreur s\'est produite ! Veuillez réessayer plus tard.',
        'limit_exceeded' => 'Vous avez dépassé la limite de votre plan.',
        'not_allowed' => 'Votre plan d\'abonnement ne permet pas cette action.',
    ]
];
