<?php use_helper('I18N', 'Date') ?> 

<?php echo $sf_params->get('ramo') ?>

<?php if ($tipo=='votes_16_C') :?>
  <?php $ramo=1 ?>
<?php else : ?>
  <?php $ramo=2 ?>
<?php endif; ?>  


<?php include_partial('parlamentare/tabs',array('ramo'=> $ramo,'gruppi'=>false)) ?>

<div class="row">
	<div class="twelvecol">
		
		
		<div style="width: 870px;">
			
			<?php echo include_partial('parlamentare/secondLevelMenuParlamentari', 
			                             array('current' => 'distanze',
			                             'ramo' => $ramo)); ?>

			<p class="tools-container" style="padding: 10px;"><a href="#" class="ico-help">cos'&egrave; il grafico delle distanze</a></p>
			<div style="display: block;" class="help-box float-container float-left">
			  <div class="inner float-container">
			    <a href="#" class="ico-close">chiudi</a>	   	  
			    <h5>cos'&egrave; il grafico delle distanze ?</h5> 
			<p>
			Il grafico mostra le distanze tra i <?php echo ($tipo=="votes_16_C" ? 'deputati' : 'senatori') ?> ricavate confrontando i voti espressi nelle 
			<?php if ($tipo=="votes_16_C") : ?>
			   <b><?php echo OppVotazionePeer::doSelectCountVotazioniPerPeriodo('','','16','C') ?> votazioni elettroniche d'aula finora svolte (ultima votazione del <?php echo format_date(OppVotazionePeer::doSelectDataUltimaVotazione('','','16','C'), 'dd/MM/yyyy') ?>).</b>
			<?php else : ?>
			   <b><?php echo OppVotazionePeer::doSelectCountVotazioniPerPeriodo('','','16','S') ?> votazioni elettroniche d'aula finora svolte (ultima votazione del <?php echo format_date(OppVotazionePeer::doSelectDataUltimaVotazione('','','16','S'), 'dd/MM/yyyy') ?>).</b>
			<?php endif; ?><br />
			Esplorando l'immagine si scopre come i <?php echo ($tipo=="votes_16_C" ? 'deputati' : 'senatori') ?> si distribuiscono nello spazio in base ai loro voti. Si possono notare le distanze tra
			le nuvole di colore omogeneo ognuna delle quali corrisponde ad un gruppo parlamentare diverso ma anche verificare all'interno della stessa nuvola (gruppo) le prossimit&agrave; e le lontananze di voto tra
			un parlamentare e l'altro.<br />
			Prende forma in questo modo uno spazio politico inedito in cui &egrave; possibile confrontare e verificare,
			con <?php echo link_to('approssimazione affidabile','/static/faq#11a') ?>, i comportamenti di voto di singoli rappresentanti e gruppi.
			Uno spazio in cui le coordinate geografiche (destra/sinistra e alto/basso) non contano nulla e in cui
			semplicemente chi vota nello stesso modo si trova pi&ugrave; vicino e chi vota in maniera difforme &egrave; pi&ugrave; lontanto.<br />
			<span style="font-size:14px; font-weight:bold;">Dal calcolo e dal grafico sono stati esclusi quei parlamentari con meno del 50% di presenze nelle votazioni elettroniche d'aula.<span>
			</p>
			  </div>
			</div>
			<div id="distanceGraph">
			<div class="intro-box">
			<br/>
			<h5 class="subsection">
			OOPS! .... per visualizzare il grafico delle distanze<br /><br />&egrave; necessario <a href="http://get.adobe.com/flashplayer/">installare il Flash player </a>versione 9 o superiore</h5>
			</div>
			</div>
			
			
		</div>
		
		
	</div>
</div>

		
    <script type="text/javascript">
			var flashvars = {};
			flashvars.xmlfilepath = "<?php echo sfConfig::get('sf_resources_host'); ?>/posizioni/opp_<?php echo($tipo) ?>.xml";
			flashvars.imgfilepath = "http://op_openparlamento_images.s3.amazonaws.com/parlamentari/thumb/";
			flashvars.linkfilepath = "/parlamentare/";
			var params = {};
			params.play = "true";
			params.scale = "noscale";
			params.wmode = "gpu";
			params.devicefont = "true";
			var attributes = {};
			swfobject.embedSWF("<?php echo sfConfig::get('sf_resources_host'); ?>/swf/DistanceGraph.swf", "distanceGraph", "870", "540", "9.0.0", "<?php echo sfConfig::get('sf_resources_host'); ?>/swf/expressInstall.swf", flashvars, params, attributes);
		</script>



<?php slot('breadcrumbs') ?>
  <?php echo link_to("home", "@homepage") ?> /
  <?php echo link_to("parlamentari", "@parlamentari") ?>/
    le distanze tra i parlamentari  
<?php end_slot() ?>
