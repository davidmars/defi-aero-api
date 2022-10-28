/**
 * Déloggue l'utilisateur et recharge la page
 */
function logout(){
    let fd=new FormData();
    fetch(serverUrl+"api/logout",
        {method: 'POST',body:fd}
        )
        .then(response => {
            document.location.reload();
        })
        .catch(err => {});
}

/**
 * Effacer un Event après confirmation
 * @param {string}eventId
 */
function deleteEvent(eventId){
    if(confirm(`Êtes-vous certain de vouloir supprimer cet event et toutes ses photos ?`)){
        let fd=new FormData();
        fd.set("eventId",eventId);
        fetch(serverUrl+"api/delete-event",
            {method: 'POST',body:fd}
        )
        .then(response => {
            document.location=serverUrl+"admin";
        })
        .catch(err => {});
    }
}
/**
 * Effacer une Photo apres confirmation
 * @param {string} photoId
 */
function deletePhoto(photoId){
    if(confirm(`Êtes-vous certain de vouloir supprimer cette photo ?`)){
        let fd=new FormData();
        fd.set("photoId",photoId);
        fetch(serverUrl+"api/delete-photo",
            {method: 'POST',body:fd}
        )
        .then(response => {
            document.location.reload();
        })
        .catch(err => {});
    }
}

/**
 * Stoppe une event javascript DOM
 * @param {InputEvent} event
 */
function stopEvent(event){
    event.stopPropagation();
    event.preventDefault();
}
