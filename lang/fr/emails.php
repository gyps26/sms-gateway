<?php

return [
    'messages' => [
        'received' => [
            'subject' => 'Message reçu de :from le :to',
        ],
    ],
    'subscriptions' => [
        'started' => [
            'subject' => 'Abonnement démarré',
            'message' => 'Merci de vous être abonné au plan **:plan** ! Votre abonnement a commencé avec succès le :date.',
            'features' => 'Vous avez maintenant accès à toutes les fonctionnalités incluses dans votre plan. Nous sommes ravis de vous compter parmi nous !',
            'help' => 'Besoin d’aide pour démarrer ? Consultez notre [**documentation**](:docs).',
        ],
        'renewed' => [
            'subject' => 'Abonnement renouvelé',
            'message' => 'Votre abonnement a été renouvelé avec succès.',
            'features' => 'Vos crédits ont été mis à jour, et vous pouvez continuer à profiter de toutes les fonctionnalités incluses dans votre plan. Vous pouvez consulter les détails mis à jour de votre abonnement ci-dessous.',
        ],
        'cancelled' => [
            'subject' => 'Abonnement annulé',
            'message' => 'Votre abonnement a été annulé avec succès. Nous sommes désolés de vous voir partir. Si vous changez d’avis, vous pouvez toujours vous réabonner plus tard.',
            'features' => 'Vous aurez toujours accès aux fonctionnalités de votre plan jusqu’à la fin de votre période de facturation en cours.',
        ],
        'expired' => [
            'subject' => 'Abonnement expiré',
            'message' => 'Votre abonnement a expiré. Nous espérons que vous avez apprécié notre service. Si vous souhaitez continuer à l’utiliser, pensez à vous réabonner.',
        ],
        'next_renewal' => 'Votre prochaine date de renouvellement est le :date.',
        'ends' => 'Votre abonnement se terminera le :date.',
        'contact' => 'Si vous avez des questions, n\'hésitez pas à nous contacter en cliquant sur le bouton "Besoin d\'aide?" ci-dessous.',
        'view' => 'Consulter votre abonnement',
        'need_help' => 'Besoin d\'aide?',
    ],
    'hi' => 'Bonjour :name,',
    'best_regards' => 'Cordialement,',
];
