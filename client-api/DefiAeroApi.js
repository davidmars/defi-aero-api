import Equipe from "./Equipe.js";


export default class DefiAeroApi {
    /**
     *
     * @param {string} serverUrl Url absolue vers le serveur
     */
    constructor(serverUrl) {
        /**
         * Version de l'api Javascript
         * @type {string}
         */
        this.clientVersion="0.0.1";
        /**
         * Url absolue vers le serveur
         * @type {string}
         */
        this.serverUrl=serverUrl.replace(/\/$/, '');

        console.log(`DefiAeroApi serverUrl : ${this.serverUrl}`);
        console.log(`DefiAeroApi clientVersion : ${this.clientVersion}`);
    }

    /**
     * Gère les retours de l'API quand il n'y a pas d'erreur
     * @param {Response} response
     * @param {function} cbSuccess Callback de success.
     * @param {function} cbError Callback en cas d'erreur.
     * @private
     */
    _manageResponse(response,cbSuccess,cbError){

        response.json().then((json=>{
            if(json.success){
                cbSuccess(json);
            }else{
                cbError(json);
            }
        })).catch(err=>{
            cbError({errors:[
                response.status,
                "L'API n'a pas retourné un json valide",
                `${err}`
                ]})

        });
    }

    /**
     * Permet de tester la connexion au serveur
     * @param {function} cbSuccess Callback de success, renvoie un tableau qui ressemble à 'pong','http://url.complete/vers-le/serveur'.
     * @param {function} cbError Callback en cas d'erreur.
     */
    ping(cbSuccess=(apiResponse)=>{console.log(`ping->success()`,apiResponse)},cbError=(apiResponse)=>{console.warn(`ping->error()`,apiResponse)}){
        let fd=new FormData();
        fd.set("serverUrl",this.serverUrl);
        fetch(this.serverUrl+"/api/ping",
            {
                method: 'POST',
                //body:data,
                //headers: {"Content-Type": "application/json; charset=utf-8" }
            }
        )
        .then(response => {
            this._manageResponse(response,
                (data)=>{cbSuccess(data.messages,data)},
                (data)=>{cbError(data.errors,data)},
                );

        })
        .catch(err => {
            cbError(["Fetch error",err.message]);
        });
    }

    /**
     * Obtenir une équipe par son code barre.
     * Crée l'équipe au besoin
     * @param {String} code Le code issu du code barre
     * @param {Function} cbSuccess Callback en cas de success, le premier argument est l'Equipe créée
     * @param {Function} cbError Callback en cas d'erreur', le premier argument est le tableau des erreurs rencontrées
     */
    getEquipe(code,cbSuccess=(apiResponse)=>{console.log(`getEquipe->success()`,apiResponse)},cbError=(apiResponse)=>{console.warn(`getEquipe->error()`,apiResponse)}){
        const fd=new FormData();
        fd.set("serverUrl",this.serverUrl);
        fd.set("code",code);
        fetch(this.serverUrl+"/api/get-equipe",{method: 'POST',body:fd})
            .then(response => {
                console.log("get",response)
                this._manageResponse(response,
                    (data)=>{
                        switch (true){
                            case data.body.equipe !== undefined:
                                cbSuccess(new Equipe().load(data.body.equipe),data);
                                break;
                            default:
                                cbError(["pas d'équipe"],data);
                        }
                    }
                    ,(data)=>{
                        cbError(data.errors,data);
                    }
                );
            })
            .catch(err => {
                cbError(["Fetch error getEquipe",err.message]);
            });
    }

    /**
     * Modifier une équipe.
     * Attention
     * Si l'équipe n'est pas trouvée alors une erreur est renvoyées
     * @param {Equipe} equipe
     * @param {Function} cbSuccess Callback en cas de success, le premier argument est l'Equipe modifiée
     * @param {Function} cbError Callback en cas d'erreur', le premier argument est le tableau des erreurs rencontrées
     */
    setEquipe(equipe,cbSuccess=(apiResponse)=>{console.log(`setEquipe->success()`,apiResponse)},cbError=(apiResponse)=>{console.warn(`setEquipe->error()`,apiResponse)}){
        const fd=new FormData();
        fd.set("serverUrl",this.serverUrl);
        fd.set("equipe",JSON.stringify(equipe));
        fetch(this.serverUrl+"/api/set-equipe",{method: 'POST',body:fd})
            .then(response => {
                console.log("set",response)
                this._manageResponse(response,
                    (data)=>{
                    console.log("ok",data)
                        switch (true){
                            case data.body.equipe !== undefined:
                               cbSuccess(new Equipe().load(data.body.equipe),data);
                               break;
                            default:
                                //cbError(["pas d'équipe"],data);
                        }
                    }
                    ,(data)=>{
                        cbError(data.errors,data);
                    }
                );
            })
            .catch(err => {
                cbError(["Fetch error setEquipe",err.message]);
            });
    }

}