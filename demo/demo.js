import DefiAeroApi from "../DefiAeroApi.js";
import Equipe from "../Equipe.js";
/**
 *
 * @type {DefiAeroApi}
 */
const api=new DefiAeroApi(document.getElementById("serverUrl").value);
window.api=api;

// REMPLIR LE FORM AVEC LES CONSTANTES
let $selectProjets=document.getElementById('set-projet');
for(let proj in Equipe.PROJETS){
    let $opt=document.createElement("option");
    $opt.text=proj;
    $opt.value=proj;
    $selectProjets.append($opt);
}
let $selectCarburants=document.getElementById('set-carburant');
for(let carb in Equipe.CARBURANTS){
    let $opt=document.createElement("option");
    $opt.text=carb;
    $opt.value=carb;
    $selectCarburants.append($opt);
}
let $selectMembres=document.getElementById('set-membres');
for(let membre in Equipe.MEMBRES){
    let $opt=document.createElement("option");
    $opt.text=membre;
    $opt.value=membre;
    $selectMembres.append($opt);
}

let $parts=document.getElementById("parts");
let $templateCouleurField=document.getElementById("templateCouleurField");
$parts.removeChild($templateCouleurField);
for (let part in Equipe.PARTS){
    let $part=$templateCouleurField.cloneNode(true);
    $part.querySelector(".col-form-label").textContent=part;
    $part.querySelector(".form-control").setAttribute("data-couleur-part",part);
    $parts.append($part);
}



//remplir le code
let $constantes=document.getElementById("constantes");
$constantes.textContent=
`\/\/------ Equipe.CARBURANTS --------------
${JSON.stringify(Equipe.CARBURANTS, undefined, 2)}
\/\/------ Equipe.MEMBRES --------------
${JSON.stringify(Equipe.MEMBRES, undefined, 2)}
\/\/------ Equipe.PROJETS --------------
${JSON.stringify(Equipe.PROJETS, undefined, 2)}
`;

let $classes=document.getElementById("classes");
$classes.textContent=`
\/\/------Code source de la classe Equipe --------------
${Equipe}


\/\/------ Code source de la classe DefiAeroApi --------------
${DefiAeroApi}
`
prettyCode();


// SERVER URL
/**
 * Récupère l'url du serveur depuis le champ
 */
function setApiUrl(){
    api.serverUrl=document.getElementById("serverUrl").value;
}
document.getElementById("serverUrl").addEventListener("input",setApiUrl);
prettyCode();

/**
 * Affiche un joli json
 * @param {HTMLElement} $element
 * @param {*} response
 * @param {*} data
 * @param {Boolean} success
 */
function displayJson($element,response,data,success=true){
    $element.textContent=
`\/\/------ 1er argument du callBack ${success?'Success':'Error'} - Type ${response.constructor.name} --------------
${JSON.stringify(response, undefined, 2)}
\/\/------2e argument du callBack------------------------
${JSON.stringify(data, undefined, 2)}`;
    //$element.textContent=`${JSON.stringify(data, undefined, 2)}`;
    $element.classList.remove('success','error');
    $element.classList.add(success===null?'normal':success?'success':'error');
    prettyCode();
}

/**
 * Rend le code joli
 */
function prettyCode(){
    document.querySelectorAll('pre').forEach((el) => {
        console.log("hop")
        hljs.highlightElement(el);
    });
}


//PING
/**
 *
 * @type {HTMLElement}
 */
const $pingBtn=document.getElementById("pingBtn");
const $pingLog=document.getElementById("pingLog");
function doPing(){
    displayJson($pingLog,"loading","",null);
    api.ping(
        /**
         * En cas de success
         * @param {String[]} messages
         * @param {Object} data
         */
        (messages,data)=>{
            displayJson($pingLog,messages,data,true);

        },
        /**
         * En cars d'erreur
         * @param {String[]} erreurs
         * @param {Object}data
         */
        (erreurs,data)=>{
            displayJson($pingLog,erreurs,data,false);
        }
    )
}
$pingBtn.addEventListener('click',doPing);


//get equipe
const $getEquipeBtn=document.getElementById("getEquipeBtn");
const $getEquipeLog=document.getElementById("getEquipeLog");
const $getEquipeCodeField=document.getElementById("getEquipeCodeField");
function doGetEquipe(){
    displayJson($getEquipeLog,"loading","",null);
    api.getEquipe(
        $getEquipeCodeField.value,
        (equipe,data)=>{
            displayJson($getEquipeLog,equipe,data,true);
            fillEquipeForm(equipe);
        },
        (erreurs,data)=>{
            displayJson($getEquipeLog,erreurs,data,false);
        },
    )
}
$getEquipeBtn.addEventListener('click',doGetEquipe);

//set equipe
const $setEquipeBtn=document.getElementById("setEquipeBtn");
const $setEquipeLog=document.getElementById("setEquipeLog");
function doSetEquipe(){
    displayJson($setEquipeLog,"loading","",null);
    const equipe=new Equipe();
    for(let prop in equipe){
        if( equipe.hasOwnProperty( prop ) ){
            let $field=document.getElementById(`set-${prop}`);
            if($field){
                equipe[prop]=$field.value;
            }
        }
    }
    let $fieldGroups=document.querySelectorAll(".js-set-equipe-var-value");
    $fieldGroups.forEach(($el)=>{
        let $var=$el.querySelector(".js-var")
        let $val=$el.querySelector(".js-val")
        equipe[$var.value]=$val.value;
    });

    //les membres
    const $$membres = document.querySelectorAll('#set-membres option:checked');
    equipe.membres = Array.from($$membres).map(el => el.value);
    //les couleurs
    for (let piece in equipe.couleurs){
        let $input=document.querySelector(`[data-couleur-part="${piece}"]`)
        equipe.couleurs[piece]=$input.value;
    }

    api.setEquipe(
        equipe,
        (equipe,data)=>{
            displayJson($setEquipeLog,equipe,data,true);
            fillEquipeForm(equipe);
        },
        (erreurs,data)=>{
            displayJson($setEquipeLog,erreurs,data,false);
        },
    )
}
$setEquipeBtn.addEventListener('click',doSetEquipe);

//get equipes
const $getEquipesBtn=document.getElementById("getEquipesBtn");
const $getEquipesLog=document.getElementById("getEquipesLog");
const $getEquipesHowMany=document.getElementById("getEquipesHowMany");
const $getEquipesEtape=document.getElementById("getEquipesEtape");
function doGetEquipes(){
    displayJson($getEquipesLog,"loading","",null);
    api.getEquipes(
        $getEquipesHowMany.value,
        $getEquipesEtape.value,
        (equipes,stats,data)=>{
            displayJson($getEquipesLog,equipes,stats,true);
        },
        (erreurs,data)=>{
            displayJson($getEquipesLog,erreurs,data,false);
        },
    )
}
$getEquipesBtn.addEventListener('click',doGetEquipes);

/**
 * Rempli le formulaire de l'équipe
 * @param {Equipe} equipe
 */
function fillEquipeForm(equipe){
    for(let prop in equipe){
        if( equipe.hasOwnProperty( prop ) ){
            let $field=document.getElementById(`set-${prop}`);
            if($field){
                $field.value=equipe[prop];
            }
        }
    }
    //les membres
    document.querySelectorAll('#set-membres option').selected = '';
    equipe.membres.forEach((memb=>{
        document.querySelector(`#set-membres option[value='${memb}']`).selected = 'selected';
    }));
    //les couleurs
    for (let piece in equipe.couleurs){
        let $input=document.querySelector(`[data-couleur-part="${piece}"]`)
        if($input){
            $input.value=equipe.couleurs[piece];
        }else{
           console.warn(`attention la piece d'avion nommée ${piece} n'est pas une constante valide`)
        }

    }
}
