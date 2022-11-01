import DefiAeroApi from "../client-api/DefiAeroApi.js";
import Equipe from "../client-api/Equipe.js";
/**
 *
 * @type {DefiAeroApi}
 */
const api=new DefiAeroApi(document.getElementById("serverUrl").value);

// REMPLIR LE FORM AVEC LES CONSTANTES

let $selectProjets=document.getElementById('set-projet');
for(let proj in Equipe.PROJETS){
    let $opt=document.createElement("option");
    $opt.text=proj;
    $opt.value=proj;
    $selectProjets.append($opt);
}



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
            displayJson($getEquipeLog,equipe,data.body.equipe,true);
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
    api.setEquipe(
        equipe,
        (equipe,data)=>{
            console.log("a")
            displayJson($setEquipeLog,equipe,data.body.equipe,true);
            fillEquipeForm(equipe);
        },
        (erreurs,data)=>{
            console.log("b")
            displayJson($setEquipeLog,erreurs,data,false);
        },
    )
}
$setEquipeBtn.addEventListener('click',doSetEquipe);

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
}

/**
 * Récupère la valeur d'un champ
 * @param field
 * @param value
 */
function getFieldValue(field,value){
    const $var = document.querySelector(`.js-set-equipe-var-value .js-var[value="${field}"]`)
    console.log($var);
    if($var){
        const $value = $var.closest(".input-group").querySelector(".js-val")
        console.log($value);
        return $value.value;
    }
    return null;
}








//un petit ping pour commencer...
//doPing();