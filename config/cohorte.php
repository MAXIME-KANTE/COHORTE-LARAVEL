<?php

return [
    // Nombre d'appels à l'IA autorisés par membre et par jour
    'quota_ia_quotidien' => (int) env('COHORTE_QUOTA_IA', 10),

    // Nombre de signalements à partir duquel une publication est masquée
    'seuil_signalement' => (int) env('COHORTE_SEUIL_SIGNALEMENT', 3),

    // Score de réputation ouvrant le droit d'épingler
    'seuil_epinglage' => (int) env('COHORTE_SEUIL_EPINGLAGE', 50),

    // Que faire si OpenRouter ne répond pas: publier quand même (true)
    // ou envoyer en file de modération (false)? Décision à justifier.
    'moderation_fail_open' => (bool) env('COHORTE_MODERATION_FAIL_OPEN', false),
];
