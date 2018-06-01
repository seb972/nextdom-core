<?php
if (!isConnect()) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
?>
<legend>
    Mes premiers pas dans NextDom
    <a class='btn btn-default btn-xs pull-right' id='bt_doNotDisplayFirstUse'><i class="fa fa-eye-slash"></i> Ne plus afficher</a>
</legend>
<div id="div_alertFirstUse"></div>
<center>
    {{Bienvenue dans NextDom, et merci d'avoir choisi cet outil pour votre habitat connecté. Voici 3 guides pour bien débuter avec NextDom : }}
</center>
<br/><br/>

<div class="row">
    <div class="col-xs-4">
       <center>
        <a href="https://nextdom.com/start" target="_blank">
            <i class="fa fa-picture-o" style="font-size:12em;"></i><br/>
            {{Guide de démarrage}}
        </a>
    </center>
</div>
<div class="col-xs-4">
   <center>
    <a href="https://nextdom.github.io/documentation/premiers-pas/fr_FR/index" target="_blank">
        <i class="fa fa-check-square" style="font-size:12em;"></i><br/>
        {{Documentation de démarrage}}
    </a>
</center>
</div>
<div class="col-xs-4">
 <center>
    <a href="https://nextdom.github.io/documentation" target="_blank">
        <i class="fa fa-book" style="font-size:12em;"></i><br/>
        {{Documentation}}
    </a>
</center>
</div>
</div>

<script>
    $('#bt_doNotDisplayFirstUse').on('click', function () {
        nextdom.config.save({
            configuration: {'nextdom::firstUse': 0},
            error: function (error) {
                $('#div_alertFirstUse').showAlert({message: error.message, level: 'danger'});
            },
            success: function () {
                $('#div_alertFirstUse').showAlert({message: '{{Sauvegarde réussie}}', level: 'success'});
            }
        });
});
</script>