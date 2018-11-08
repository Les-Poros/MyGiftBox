let btnAttention = $("#btnAttention");
let btnActivite = $("#btnActivite");
let btnRestauration = $("#btnRestauration");
let btnHebergement = $("#btnHebergement");
let selectTri = $("#tri");

let tabPrestations = [];

btnAttention.click(function() {
    if ($(':first', this).hasClass("actif")) {
        $(':first', this).removeClass("actif");
        tabPrestVariable =tabPrestations;
    } else {
        $(".actif").removeClass("actif");
        equalCategory('Attention')
        $(':first', this).addClass("actif");
    }
    tri(selectTri.val());
});

btnActivite.click(function() {
    if ($(':first', this).hasClass("actif")) {
        $(':first', this).removeClass("actif");
        tabPrestVariable =tabPrestations;
    } else {
        $(".actif").removeClass("actif");
        equalCategory('Activité')
        $(':first', this).addClass("actif");
    }
    tri(selectTri.val());
});

btnRestauration.click(function() {
    if ($(':first', this).hasClass("actif")) {
        $(':first', this).removeClass("actif");
        tabPrestVariable =tabPrestations;
    } else {
        $(".actif").removeClass("actif");
        equalCategory('Restauration')
        $(':first', this).addClass("actif");
    }
    tri(selectTri.val());
});

btnHebergement.click(function() {
    if ($(':first', this).hasClass("actif")) {
        $(':first', this).removeClass("actif");
        tabPrestVariable =tabPrestations;
    } else {
        $(".actif").removeClass("actif");
        equalCategory('Hébergement')
        $(':first', this).addClass("actif");
    }
    tri(selectTri.val());
});

$('.tabPrestations').each(function() {
    tabPrestations.push($(this));
});

let tabPrestVariable = tabPrestations;

function equalCategory(element) {
    tabPrestVariable = tabPrestations.filter(function(prest) {
        return prest.find(".detailsPrestationsCateg").html() === element;
    });
}

let select='.grid_container_prestations';
if(window.location.pathname.includes("ConsultCatalogPurchase"))
    select='.grid_container_prestations_buy';

function renderPrestaCateg(tableau) {
    $(select).html('');
    for (let i = 0; i < tableau.length; i++) {
        $(select).append(tableau[i]);
    }
    if(select=='.grid_container_prestations_buy')
    clickPresta();
}

function tri(value){
    let tab = new Array();
    tabPrestVariable.forEach(function (t){
        tab.push(t);
    });
    if (value === "0"){
        renderPrestaCateg(tabPrestVariable);
    }
    else if (value === "1"){
        renderPrestaCateg(tab.sort(increasing));
    }
    else{
        renderPrestaCateg(tab.sort(decrease));
    }
}

function increasing(x, y) {
    return x.find(".detailsPrestationsPrix").html() - y.find(".detailsPrestationsPrix").html();
}

function decrease(x, y) {
    return y.find(".detailsPrestationsPrix").html() - x.find(".detailsPrestationsPrix").html();
}

if(select=='.grid_container_prestations_buy'){
    let tabAchat = [];

    
    $(".buy").each(function(){
        tabAchat.push([$(this).find(".presta").html(),$(this).find(".nbPresta").html(),$(this).find(".prixPresta").html()])
    });
    
    function renderBuy(){
        let prixFinal=0;
        let nbAct=0;
        $(".grid_container_buy").find("div").html('');
        $(".grid_container_buy").find("form").html('');
        tabAchat.forEach(function(elem){
            prixFinal=prixFinal+parseInt(elem[2],10);
            $(".grid_container_buy").find("div").append('<p class="buy">-<span class="presta">'+elem[0]+'</span> x<span class="nbPresta">'+elem[1]+'</span> : <span class="prixPresta">'+elem[2]+'</span>€ <i class="cross fas fa-times"></i></p>');
            $(".grid_container_buy").find("form").append('<input id="presta'+nbAct+'" name="presta'+nbAct+'" type="hidden" value="'+$(".tabPrestations").filter(function( index ) {
                return $( this ).find(".detailsPrestations").html() == elem[0];
              }).find(".detailsPrestationsId").html()+'"><input id="nbpresta'+nbAct+'" name="nbpresta'+nbAct+'" type="hidden" value="'+elem[1]+'"> ');
              nbAct++;
        });
        $(".grid_container_buy").find("div").append("<br><p>TOTAL : "+prixFinal+"€</p>");
        $(".grid_container_buy").find("form").append('<input id="nbAct'+'" name="nbAct'+'" type="hidden" value="'+nbAct+'"><button type="submit" class="boutton">Sauvegarder</button>');
        clickCross();
    }
    function clickPresta(){
    $('.tabPrestations').click(function(e) {
        let add=false;
        for(let i=0;i<tabAchat.length;i++){
            if(tabAchat[i][0]==$(this).find(".detailsPrestations").html()){
                add=true;
                tabAchat[i][1]=parseInt(tabAchat[i][1],10)+1;
                tabAchat[i][2]=parseInt(tabAchat[i][2],10)+parseInt($(this).find(".detailsPrestationsPrix").html(),10);
            }
        };
        if(!add){
            tabAchat.push([$(this).find(".detailsPrestations").html(),1,$(this).find(".detailsPrestationsPrix").html()])
        }
        renderBuy();
    }).on('click', 'a', function(e) {
        e.stopPropagation();
    });
}
function clickCross(){
$('.cross').click(function() {
    for(let i=0;i<tabAchat.length;i++){
        if(tabAchat[i][0]==$(this).parent().find(".presta").html()){
            tabAchat[i][1]=parseInt(tabAchat[i][1],10)-1;
            if(tabAchat[i][1]==0){
                tabAchat.splice(i,1);
            }
            else
            tabAchat[i][2]=parseInt(tabAchat[i][2],10)-parseInt($(".tabPrestations").filter(function( index ) {
                return $( this ).find(".detailsPrestations").html() == tabAchat[i][0];
              }).find(".detailsPrestationsPrix").html(),10);
        }
    };
    renderBuy();
});
}
    clickPresta();
    clickCross();

}