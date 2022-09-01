/*
 * Description de scripts.inc.js
 *
 * auteur MASCLET Sylvain
 * Creation 31-01-2022
 * Derniere MAJ 04/03/2022
 * fichier comprenant toutes les fonctions liées au programme
 */











/*
 * fonction permettant de trier un tableau en fonction du champ sélectionné
 * le tableau doit être fait avec 2 th pour 1 td (qui aura un colspan de 2)
 * 
 */
function sort(event){
    // on affiche temporairement le tableau en entier pour trier correctement toutes les lignes
    $('#'+event.data.tableau+' tbody tr').show();
    
    // récupère la colonne
    var columnIndex= $(this).closest('th').index();

    // offset dut au fait de mettre 2 th par td
    switch(columnIndex){
        case 0:
            columnIndex+=1;
            break;
            
        case 3:
        case 4:
            columnIndex-=1;
            break;
            
        case 5:
        case 6:
            columnIndex-=2;
            break;
        
        case 7:
        case 8:
            columnIndex-=3;
            break;
        
        case 9:
        case 10:
            columnIndex-=4;
            break;
            
        case 12:
            columnIndex-=5;
            break;
            
        case 14:
            columnIndex-=6;
            break;
            
        case 16:
            columnIndex-=7;
            break;
    }
    
    switching = true;
    // boucle pour continuer de trier tant qu'on a encore besoin de le faire
    while (switching) {

        // On présume ne pas avoir de tri à faire
        switching = false;

        // trouve la premiere ligne du corps de la  table
        var row = $(this).closest('table').find('tbody tr:first');        

        // on fait le tour des différentes lignes (quand il n'y a pas de ligne suivante, .length vaut 0, sinon 1 ou plus
        while(row.next('tr').length > 0) {
            // on récupère le td à l'index donné, puis la valeur contenue dans l'input
            // pareil pour le td suivant
           
            // input
            x = row.find('td:nth-child('+ columnIndex +')');
            y = row.next('tr').find('td:nth-child('+ columnIndex +')');
            if(row.find('td:nth-child('+ columnIndex +')').find('input').length !== 0){
                x = x.find('input').val();
                y = y.find('input').val();
            }
            
            // p
            if(row.find('td:nth-child('+ columnIndex +')').find('p').length !== 0){
                x = x.html();
                y = y.html();
            }
            
            // i
            if(row.find('td:nth-child('+ columnIndex +')').find('i').length !== 0){
                x = x.html();
                y = y.html();
            }       
//                console.debug('x',x);
//                console.debug('y',y);
            
            
            // On vérifie si la valeur est un chiffre,
            // Puis on compare la valeur de la ligne, à la suivante
            
            if($('.fa-angle-down').length !== 0){
                if(isNaN(x)){
                    if (x.toLowerCase() > y.toLowerCase()) {
                        // s'il y a besoin de trier, alors on tri et on sort de la boucle
                        switching = true;
                        row.next().insertBefore(row);
                        break;
                    }
                }
                else
                {
                    if (parseInt(x) > parseInt(y)) {
                        // s'il y a besoin de trier, alors on tri et on sort de la boucle
                        switching = true;
                        row.next().insertBefore(row);
                        break;
                    }
                }
            }
            else
            {
                if(isNaN(x)){
                    if (x.toLowerCase() < y.toLowerCase()) {
                        // s'il y a besoin de trier, alors on tri et on sort de la boucle
                        switching = true;
                        row.next().insertBefore(row);
                        break;
                    }
                }
                else
                {
                    if (parseInt(x) < parseInt(y)) {
                        // s'il y a besoin de trier, alors on tri et on sort de la boucle
                        switching = true;
                        row.next().insertBefore(row);
                        break;
                    }
                }
            }
            
            
            
            // on passe au suivant pour ne pas rester sur les deux première lignes
            row=row.next('tr');
        }
    }
    
    // on inverse le signe du bouton 'sort' pour dire à l'utilisateur qu'il pourra
    // faire l'action opposée en cliquant à nouveau sur le bouton
    if($('.fa-angle-down').length !== 0){
        $('.fa-angle-down').addClass('fa-angle-up');
        $('.fa-angle-down').removeClass('fa-angle-down');
    }
    else
    {
       $('.fa-angle-up').addClass('fa-angle-down'); 
       $('.fa-angle-up').removeClass('fa-angle-up');
    }
    
    // enfin on appelle la fonction de pagination pour remettre le tableau par pages
    paginate(0,event.data.tableau);
};










/* 
 * Fonction pour paginer automatiquement le tableau des clients et
 * rajouter une liste de boutons permettant de passer de l'un à l'autre
 * 
 */    
function paginate(currentPage,tableau){
    $('#nav').remove();
    $('#'+tableau).after('<div id="nav"></div>');
    var rowsShown = 6;
    var rowsTotal = $('#' + tableau + ' tbody tr').length;
    var nbrPages = Math.ceil(rowsTotal/rowsShown);
    for(i = 0;i < nbrPages;i++) {
        var pageNum = i + 1;
        // on affiche les 3 premières et dernières pages et les 3 pages avant et après la page sur laquelle on est
        if((pageNum>=1 && pageNum<=3)||(pageNum>=(currentPage+1)-3 && pageNum<=(currentPage+1)+3)||(pageNum>=nbrPages-2)){
            callTableau = "'"+tableau+"'";
            $('#nav').append('<a onclick="paginate('+i+','+callTableau+')" href="#" rel="'+i+'">'+pageNum+'</a> ');
        }
        else
        {
            // si on est proche du début ou de la fin, on affiche des ... de l'autre côté:
            // je suis proche du début, ... avant les 3 derniers
            // inversement
            // je suis proche des dernières pages, ... après les 3 premiers
            if((currentPage<7 && pageNum===currentPage+5)||(currentPage>=nbrPages-7 && pageNum===currentPage-5)){
                $('#nav').append(' ... ');
            }
            else
            {
                // lorsqu'on est à un chiffre entre le début et la fin 
                // et au dessus des trois premières pages
                // ET en dessous des trois dernières pages
                // alors on met ... après les trois premières pages et avant les trois dernières pages
                if((currentPage>=7 && currentPage<=nbrPages-7)&&(pageNum===currentPage-3 || pageNum===currentPage+5)){
                    $('#nav').append(' ... ');
                }
            }

        }
    }


    $('#' + tableau + ' tbody tr').hide();
    // on affiche les elements du tableau en fonction de la page sur laquelle on est
    $('#'+ tableau + ' tbody tr').slice(currentPage*rowsShown,currentPage*rowsShown+rowsShown).show();
    $('#nav a:first').addClass('active');
    $('#nav a').bind('click', function(){

        $('#nav a').removeClass('active');
        $(this).addClass('active');
        var currPage = $('#' + tableau).attr('rel');
        var startItem = currPage * rowsShown;
        var endItem = startItem + rowsShown;
        $('#' + tableau + ' tbody tr').css('opacity','0.0').hide().slice(startItem, endItem).
                css('display','table-row').animate({opacity:1}, 300);
    });
};









/*
 * la fonction créé une requête GET pour envoyer l'id et la value de l'input auquel elle est rattachée
 * 
 */
function modifInfos(event){
    var infos=$(this).closest('tr td input');
    //console.log(infos.val());
    $.ajax({
        type: "GET",
        url: "?action="+event.data.action+"&id="+infos.attr('id')+"&value="+infos.val()
    });
//    window.location.href = "?action="+event.data.action+"&id="+infos.attr('id')+"&value="+infos.val();
}








/*
 * Permet d'afficher les bons inputs en fonction de la valeur du bouton radio des produits
 * 
 */
function radioProduit(){
    if($(this).val()==="service"){
        $('#valeurVariante').remove();
        var toAppend = '<div id="valeurVariante">';
        toAppend += '<p>taux horaire</p>';
        toAppend += '<input type="checkbox" name="tx_horaire" value="ok">';
        toAppend += '</div>';
        $('td[name="tauxHoraire"]').append(toAppend);
    }
    else
    {
        $('#valeurVariante').remove();
        var toAppend = '<div id="valeurVariante">';
        toAppend += '<h4>Code barre:</h4>';
        toAppend += '<input name="code_barre" type="text" size="20" placeholder="ex: 2200450056">';
        toAppend += '</div>';
        $('td[name="tauxHoraire"]').append(toAppend);
    }
}







/*
 * Fonction permettant d'ajouter un produit à la liste des produits qui seront facturés
 * Tout en enlevant ce dernier du select du choix des produits
 * 
 */
function addProdAjout(event){
    var newProdVal = $("select[name='nomProduit'] option:selected").text();
    var newProdId = $("select[name='nomProduit'] option:selected").attr('id');
    var appendstr = '<tr>';
    
    appendstr += '<td>';
    appendstr += '<input form="'+event.data.form+'" type="hidden" name="'+newProdId+'" value="">';
    appendstr += '<input value="'+newProdVal+'"></td>';
    
    appendstr += '<td>';
    appendstr += '<a name="'+newProdId+'" class="btn btn_nav suppr" href="#"><i class="fa-solid fa-trash"></i></a>';
    appendstr += '</td>';
    
    appendstr += '</tr>';
    
    $('#lesProduits').append(appendstr);
    $(document).on('click','a[name='+newProdId+']',{idProd:newProdId,nomProd:newProdVal},removeProdAjout);
    
    $("select[name='nomProduit'] option:selected").remove();
}







/*
 * Fonction permetant d'enlever un produit de la liste des produits qui seront facturés
 * et de le ré-ajouter au select du choix des produits
 * 
 */
function removeProdAjout(event){
    $("select[name='nomProduit']").append(
        $('<option>', {
            id:event.data.idProd,
            text:event.data.nomProd
    }));
    $(this).closest('tr').remove();
}







/*
 * Permet de choisir un client pour la facture à ajouter
 * 
 */
function choixDuClient(){
    $('p[name="leClient"]').text($("select[name='nomClient'] option:selected").text());
    $('input[name="idClient"]').val($("select[name='nomClient'] option:selected").attr('id'));
}








/*
 * Fonction permettant d'associer l'id selectionné
 * à l'input type hidden derrière le select
 * 
 */
function nouvId(){
    var hidInput = $(this).closest('td').find('input');
    // comme toujours on découpe en ancienID @ nouvelID
    var lesIds= hidInput.val().split('@');
    
    // pour l'id client on a besoin de se souvenir de l'ancien id pour le retrouver
    if(hidInput.attr('name') === 'idClient')
        hidInput.val(lesIds[0]+'@'+$(this).find('option:selected').attr('id'));
    else
        hidInput.val($(this).find('option:selected').attr('id') + '@' + $(this).attr('name').split('@')[1]);
    console.debug('id:',hidInput.val());
}







/*
 * Fonction permettant de changer la plage de jours possibles de 
 * la date d'écheance ou de la date de fin de validité
 * en fonction de la date de création choisie
 * 
 */
function modifEcheance(){
    newDateCreation = new Date($(this).val());
    newDateCreation.setDate(newDateCreation.getDate()+30);
    var dd = newDateCreation.getDate();
    var mm = newDateCreation.getMonth() + 1; // les mois vont de 0 à 11 ,-,
    var yyyy = newDateCreation.getFullYear();

    if (dd < 10) {
       dd = '0' + dd;
    }

    if (mm < 10) {
       mm = '0' + mm;
    } 

    nouvMax = yyyy + '-' + mm + '-' + dd;
    console.debug('max',nouvMax);
    $('input[name="dateFin"]').attr('min',$(this).val());
    $('input[name="dateFin"]').attr('max',nouvMax);
}







/*
 * Fonction qui donne à l'input hidden du TTC sa nouvelle valeur que l'on récupèrera
 * en php
 * 
 */
function modifTTC(){
    $(this).closest('td').find('input[type="hidden"]').val($(this).val());
}





/*
 * Fonction permettant l'envoi du changement de produit 
 * dans une facture ou un devis
 * 
 */
function modifProduit(event){
    var oldId = $(this).closest('td').find('input[type="hidden"]').attr('name').split('@');
    var newProd = oldId[1]+'@'+$(this).closest('td').find('input[type="hidden"]').val();
    console.debug('newProd',newProd);
//    $.ajax({
//        type: "GET",
//        url: "/",
//        data: "action="+event.data.action+"&idFacture="+event.data.id+"&newProd="+newProd
//    });
    
    window.location.href = "?action="+event.data.action+"&id="+event.data.id+"&newProd="+newProd;
    
}





/*
 * Fonction permettant de cocher toutes les cases export du tableau passé
 * en paramètres via event
 * 
 */
function ajoutExport(event){
    $('#'+event.data.tableau+' tbody tr').show();
    
    if($(this).prop('checked') === true){
        $('.checkExport').prop("checked",true);
    }
    else
    {
        $('.checkExport').prop("checked",false);
    }
    
    paginate(0,event.data.tableau);
}





function getDate(typeInterval){
    
    var ajd = new Date();
    var hier = new Date(ajd.getFullYear(),ajd.getMonth(), ajd.getDate() - 1);
    
    var dans7jours = new Date(ajd.getFullYear(),ajd.getMonth(), ajd.getDate() + 7);
    var ilya7jours = new Date(ajd.getFullYear(),ajd.getMonth(), ajd.getDate() - 7);
    
    var premierJourMois = new Date(ajd.getFullYear(),ajd.getMonth(), 1);
    var dernierJourMois = new Date(ajd.getFullYear(),ajd.getMonth()+1, 0);
    
    var premierJourMoisDernier = new Date(ajd.getFullYear(),ajd.getMonth() - 1, 1);
    var dernierJourMoisDernier = new Date(ajd.getFullYear(),ajd.getMonth(), 0);
    
    var dernierJourAnnee = new Date(ajd.getFullYear(),11, 31);
    var premierJourAnnee = new Date(ajd.getFullYear(),0, 1);
    
    var dernierJourAnneeDerniere = new Date(ajd.getFullYear()-1,11, 31);
    var premierJourAnneeDerniere = new Date(ajd.getFullYear()-1,0, 1);
    
    
    switch(typeInterval){
        
        case "Aujourd hui":
            return [ajd,ajd];
        case 'Hier':
            return [hier,hier];
        case 'Semaine courante':
            return [ajd,dans7jours];
        case 'Semaine dernière':
            return [ilya7jours,hier];    
        case 'Mois courant':
            return [premierJourMois,dernierJourMois];
        case 'Mois dernier':
            return [premierJourMoisDernier  ,dernierJourMoisDernier];
        case 'Année courante':
            return [premierJourAnnee,dernierJourAnnee];
        case 'Année précédente':
            return [premierJourAnneeDerniere,dernierJourAnneeDerniere];
        default:
            return [];
    }
}




function formatDate(date){
    var dd = String(date.getDate()).padStart(2, '0');
    var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = date.getFullYear();

    date = yyyy + '-' + mm + '-' + dd;
    return date;
}






function intervalDate()
{
    var dateArray = getDate($(this).val());

    if(dateArray.length !== 0){
        dateArray[0] = formatDate(dateArray[0]);
        dateArray[1] = formatDate(dateArray[1]);
        $('input[name=intervalDebut]').val(dateArray[0]);
        $('input[name=intervalFin]').val(dateArray[1]);
    }   
    
    interval = $('input[name=intervalDebut]').val()+'@'+$('input[name=intervalFin]').val();
    
    if($('#exportValid').length !== 0){
        $('#exportValid').attr('action','./?action=pdfecheances&export=nonvalid&interval='+interval);
    }
    else
    {
        if($('#exportNonValid').length !== 0){
            $('#exportNonValid').attr('action','./?action=pdfecheances&export=valid&interval='+interval);
        }
    }
}









function cbJoursMois(leMois)
{
    switch(strtolower(leMois)){
        case 'january':
        case 'march':
        case 'may':
        case 'july':
        case 'august':
        case 'october':
        case 'december':
            return 31;
        
        case 'february':
            let date = new Date();
            let annee = date.prototype.getYear();
            if((annee % 4 === 0 && annee % 100 !== 0)||(annee % 400 === 0))
                return 29;
            else
                return 28;
            break;
        
        default:
            return 30;
    }
}