<?php

// Fotoo Gallery
// Traduction française

setlocale (LC_TIME, 'fr_FR.utf8');

$french_strings = array(
    'Upload a file'
        =>  'Envoyer une image',
    'Upload an album'
        =>  'Envoyer un album',
    'Browse pictures'
        =>  'Parcourir les images',
    'Browse albums'
        =>  'Parcourir les albums',
    'Title'
        =>  'Titre',
    'File'
        =>  'Fichier',
    'Files'
        =>  'Fichiers',
    'Name'
        =>  'Nom',
    'Album'
        =>  'Album',
    'Private'
        =>  'Privé',
    'If checked, this album won\'t appear in &quot;browse pictures&quot;'
        =>  'Si coché, cet album n\'apparaîtra pas dans la liste des images',
    'If checked, picture won\'t appear in pictures list'
        =>  'Si coché, l\'image n\'apparaîtra pas dans la liste des images',
    'Please select a picture'
        =>  'Merci de sélectionner une image',
    'Please select some files'
        =>  'Merci de sélectionner des fichiers',
    'Maximum file size'
        =>  'Poids maximum par fichier',
    'Image types accepted'
        =>  'Formats acceptés',
    'only'
        =>  'seulement',
    'Check / uncheck all'
        =>  'Tout sélectionner / désélectionner',
    'Delete'
        =>  'Supprimer',
    'Delete checked pictures'
        =>  'Supprimer les images sélectionnées',
    'Delete all the checked pictures'
        =>  'Supprimer toutes les images sélectionnées',
    'Delete checked albums'
        =>  'Supprimer les albums sélectionnés',
    'Delete all the checked albums'
        =>  'Supprimer tous les albums sélectionnés',
    'Delete picture'
        =>  'Supprimer l\'image',
    'Can\'t delete picture'
        =>  'Impossible de supprimer l\'image',
    'Delete album'
        =>  'Supprimer l\'album',
    'Can\'t delete album'
        =>  'Impossible de supprimer l\'album',
    'OK'
        =>  'Ok',
    'FAIL'
        =>  'Echec',
    'Wrong password'
        =>  'Mauvais mot de passe',
    'Login'
        =>  'Connexion',
    'Logout'
        =>  'Déconnexion',
    'Password'
        =>  'Mot de passe',
    'Short URL for full size'
        =>  'Url courte pour image en taille réelle',
    'HTML code'
        =>  'Code HTML',
    'Share this album using this URL'
        =>  'Partager cet album avec son URL',
    'Keep this URL in your favorites to be able to delete this picture later'
        =>  'Conservez cette url dans vos favoris pour supprimer cette image plus tard',
    'Keep this URL in your favorites to be able to delete this album later'
        =>  'Conservez cette url dans vos favoris pour supprimer cet album plus tard',
    'All pictures for a forum'
        =>  'Toute les images pour un forum',
    'View full size'
        =>  'Voir en taille réelle',
    'Upload'
        =>  'Télécharger',
    'Uploaded'
        =>  'Téléchargé',
    'Uploaded on'
        =>  'Téléchargé le',
    'Uploading is not allowed'
        =>  'Le téléchargement n\'est pas autorisé',
    'Uploading'
        =>  'Téléchargement',
    'Resizing'
        =>  'Redimensionnement',
    'IP address'
        =>  'Adresse IP',
    'Really'
        =>  'Vraiment',
    'picture'
        =>  'image',
    'Not Found'
        =>  'Non trouvé',


    'The uploaded file exceeds the allowed file size (ini).'
        =>  'Le fichier téléchargé dépasse le poids maximum autorisé (ini)',
    'The uploaded file exceeds the allowed file size (html).'
        =>  'Le fichier téléchargé dépasse le poids maximum autorisé (html)',
    'The uploaded file was only partially uploaded.'
        =>  'Le fichier n\'a été que partiellement téléchargé',
    'No file was uploaded.'
        =>  'Aucun fichier n\'a été téléchargé',
    'Missing a temporary folder.'
        =>  'Un dossier temporaire est manquant',
    'Failed to write file to disk.'
        =>  'Erreur lors de l\'écriture sur le disque',
    'A server extension stopped the file upload.'
        =>  'Une extension serveur a stoppé le téléchargement du fichier',
    'Invalid image format.'
        =>  'Format d\'image invalide',
    'Unknown error.'
        =>  'Erreur inconnue',


    'Maximum image width or height, bigger images will be resized.'
        =>  'Largeur et hauteur maximum ? Les images plus grandes seront redimensionnées',
    'Maximum thumbnail size, used for creating thumbnails.'
        =>  'Taille maximum des vigettes, utilisé pour la génération des vignettes.',
    'Maximum uploaded file size (in bytes). By default it\'s the maximum size allowed by the PHP configuration. See the FAQ for more informations.'
        =>  'Poids maximum d\'un fichier téléchargé (en bytes). Par défaut, ce sera la taille maximum autorisée par votre configuration PHP. Voir la FAQ pour plus d\'informations',
    'Number of images to display on each page in the pictures list.'
        =>  'Nombre d\'images à afficher par page dans la liste des images',
    'Path to the SQLite DB file.'
        =>  'Chemin du fichier SQLite',
    'Path to where the pictures are stored.'
        =>  'Chemin vers le dossier de stockage des images',
    'URL of the webservice index.'
        =>  'URL vers le Web service',
    'URL to where the pictures are stored. Filename is added at the end.'
        =>  'URL d\'archivage des images. Le nom du fichier sera ajouté à la fin',
    'Title of the service.'
        =>  'Titre du service',
    'URL to the picture information page, hash is added at the end.'
        =>  'URL vers la page d\'information de l\'image. Le hash sera ajouté à la fin.',
    'URL to the album page, hash is added at the end.'
        =>  'URL vers la page de l\'album. Le hash sera ajouté à la fin.',
    'Allow upload of files? You can use this to restrict upload access. Can be a boolean or a PHP callback. See the FAQ for more informations.'
        =>  'Autoriser le téléchargement de fichiers ? Vous pouvez utiliser ceci afin de restreindre l\'accès aux pages de téléchargement. Cela peut-être une valeur bolléenne ou une fonction de rappel PHP. Voir la FAQ pour plus d\'informations',
    'Password to access admin UI? (edit/delete files, see private pictures)'
        =>  'Mot de passe pour accéder à l\'interface administrateur (éditer/supprimer, voir les images privées)',
    'List of banned IP addresses (netmasks and wildcards accepted, IPv6 supported)'
        =>  'Liste des adresses IP bannies (masques et wildcards acceptés, IPv6 supporté)',
    'Allowed formats, separated by a comma'
        =>  'Formats autorisés, séparés par une virgule',
    'Expiration (in days) of IP storage, after this delay IP addresses will be removed from database'
        =>  'Expiration (en jours) de l\'archivage des adresses IP. Passé ce délai, les adresses IP seront supprimées de la base de données.',


    'You need at least PHP 5.2 to use this application.'
        =>  'Vous avez besoin d\'au moins PHP 5.2 pour utiliser cette application.',
    'You need PHP SQLite3 extension to use this application.'
        =>  'Vous avez besoin de l\'extension PHP SQLite3 pour utiliser cette application.',
    'Invalid call.'
        =>  'Appel invalide.',
    'Default configuration created in config.php file, edit it to change default values.'
        =>  'Les configurations par défaut ont été créées dans le fichier config.php. Editez-le pour modifier ces valeurs par défaut.',
    'Upload not permitted.'
        =>  'Les téléchargements ne sont pas autorisés',
    'Bad Request'
        =>  'Requête erronée',

    'Powered by Fotoo Hosting application from'
        =>  'Propulsé par Fotoo Hosting de',
);

// Days of the week translations
$french_days = array(
    'Monday'    =>  'lundi',
    'Tuesday'   =>  'mardi',
    'Wednesday' =>  'mercredi',
    'Thursday'  =>  'jeudi',
    'Friday'    =>  'vendredi',
    'Saturday'  =>  'samedi',
    'Sunday'    =>  'dimanche',
);

// Months of the year translations
$french_months = array(
    'January'   =>  'janvier',
    'February'  =>  'février',
    'March'     =>  'mars',
    'April'     =>  'avril',
    'May'       =>  'mai',
    'June'      =>  'juin',
    'July'      =>  'juillet',
    'August'    =>  'août',
    'September' =>  'septembre',
    'October'   =>  'octobre',
    'November'  =>  'novembre',
    'December'  =>  'décembre',
);

function __($str, $mode=false, $datas=false)
{
    global $french_strings, $french_days, $french_months;

    if (isset($french_strings[$str]))
        $tr = $french_strings[$str];
    else
        $tr = $str;

    if ($mode == 'TIME')
    {
        $tr = strftime($tr, $datas);
        $tr = strtr($tr, $french_days);
        $tr = strtr($tr, $french_months);
    }
    elseif ($mode == 'REPLACE')
    {
        foreach ($datas as $key => $value)
        {
            if (is_float($value))
                $value = str_replace('.', ',', (string)$value);

            $tr = str_replace($key, $value, $tr);
        }
    }

    return $tr;
}

?>
