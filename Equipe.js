export default class Equipe{
    constructor() {
        /**
         * L'id automatiquement généré par la base de données (unique)
         * @type {null|Number}
         */
        this.id=null;
        /**
         * Le code barre (unique)
         * @type {null|String}
         */
        this.code=null;
        /**
         *
         * @type {null|String} Le nom de l'équipe choisi par l'utilisateur
         */
        this.name=null;
        /**
         * Le code iso 639-1 de la langue choisie par l'utilisateur.
         * Grâce à ce champ, l'utilisateur n'est pas obligé de choisir sa langue sur chaque borne.
         * En théorie peut être n'importe quelle valeur.
         * En pratique sera 'fr', 'en' ou 'es'.
         * @see https://www.w3schools.com/tags/ref_language_codes.asp
         * @type {null|String|'fr'|'en'|'es'}
         */
        this.lang=null;
        /**
         * Un entier qui permet de savoir où on en est de la progression
         * @type {null,Number}
         */
        this.etape=null;
        /**
         * Projet choisi par le joueur.
         * En théorie peut être n'importe quelle valeur.
         * En pratique sera une des constantes de Equipe.PROJETS.
         * @see Equipe.PROJETS
         * @type {null|String|"AVION_REGIONAL"|"HYDRAVION"|"DRONE_TAXI"}
         */
        this.projet=null;
        /**
         * La liste des membres choisis.
         * En théorie n'importe quelle valeur peut être utilisée dans ce tableau.
         * En pratique les valeurs possibles seront les constantes Equipe.MEMBRES
         * @see Equipe.MEMBRES
         * @type {null|String[]}
         */
        this.membres=[];
        /**
         * Carburant choisi par le joueur.
         * En théorie peut être n'importe quelle valeur.
         * En pratique les valeurs possibles seront les constantes Equipe.CARBURANTS
         * @see Equipe.CARBURANTS
         * @type {null|String|"Biocarburant"|"Kérosène de synthèse"|"Hydrogène"|"Électricité"}
         */
        this.carburant=null;
        /**
         * Les couleurs attribuées aux pièces de l'avion
         * @type {{moteur: string, fuselage: string, empennage: string, aile: string}}
         */
        this.couleurs={};
    }

    /**
     * Charge les données json dans l'équipe
     * @param {Object} json
     * @returns {Equipe}
     */
    load(json){
        for(let prop in json){
            if( json.hasOwnProperty( prop ) && this.hasOwnProperty( prop )){
                this[prop]=json[prop];
            }
        }
        return this;
    }
}
/**
 * Constantes des différentes pièces de l'avion
 */
Equipe.PARTS={
    "AILES":"AILES"
    "EMPENNAGE":"EMPENNAGE"
    "FUSELAGE":"FUSELAGE"
    "MOTEURS":"MOTEURS"
    "MOTEUR":"MOTEUR"
    "AILES_VOLANTE_AV":"AILES_VOLANTE_AV"
    "AILES_VOLANTE_AR":"AILES_VOLANTE_AR"
    "MOTEURS_HYD":"MOTEURS_HYD"
    "FOILS":"FOILS"
    "PALE":"PALE"
    "HELICE":"HELICE"
}
/**
 * Constantes des différentes valeurs pouvant être attribuées au champ membres
 */
Equipe.MEMBRES={
    "CHEFFE_EXPLOIT":"CHEFFE_EXPLOIT"
    "PILOTE_ESSAI":"PILOTE_ESSAI"
    "CHEF_PROJET":"CHEF_PROJET"
    "COMMUNIQUANT":"COMMUNIQUANT"
    "INGENIEUR":"INGENIEUR"
    "MECANICIEN":"MECANICIEN"
}
/**
 * Constantes des différentes valeurs pouvant être attribuées au champ projet
 */
Equipe.PROJETS={
    "AVION_REGIONAL":"AVION_REGIONAL",
    "HYDRAVION":"HYDRAVION",
    "DRONE_TAXI":"DRONE_TAXI"
}
/**
 * Constantes des différentes valeurs pouvant être attribuées au champ carburant
 */
Equipe.CARBURANTS={
    "BIOCA":"BIOCA",
    "KSYNTH":"KSYNTH",
    "HYDRO":"HYDRO",
    "ELEC":"ELEC",
}
