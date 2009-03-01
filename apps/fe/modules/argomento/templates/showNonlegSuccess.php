<?php use_helper('Javascript', 'Date', 'I18N') ?>

<ul class="float-container tools-container" id="content-tabs">
  <li class="current"><h2>Argomento: <?php echo strtolower($argomento->getTripleValue()) ?></h2></li>
</ul>



<div class="tabbed float-container" id="content">
	<div id="main">
			
	  <div class="W100_100 float-left">
	    <?php echo include_partial('secondlevelmenu', 
	                               array('current' => 'nonleg', 
                                       'triple_value' => $triple_value)); ?>
	                                     	
  		<p class="tools-container"><a class="ico-help" href="#">eventuale testo micro-help</a></p>
  		<div style="display: none;" class="help-box float-container">
  			<div class="inner float-container">
	
  				<a class="ico-close" href="#">chiudi</a><h5>eventuale testo micro-help ?</h5>
  				<p>In pan philologos questiones interlingua. Sitos pardona flexione pro de, sitos africa e uno, maximo parolas instituto non un. Libera technic appellate ha pro, il americas technologia web, qui sine vices su. Tu sed inviar quales, tu sia internet registrate, e como medical national per. (fonte: <a href="#">Wikipedia</a>)</p>
  			</div>
  		</div>

      <?php include_partial('nonlegFilter',
                            array('selected_act_nonleg_type' => array_key_exists('act_nonleg_type', $filters)?$filters['act_nonleg_type']:0,                                
                                  'selected_act_ramo' => array_key_exists('act_ramo', $filters)?$filters['act_ramo']:0,
                                  'selected_act_stato' => array_key_exists('act_stato', $filters)?$filters['act_stato']:0)) ?>

      <?php include_partial('attiSort', array('session_namespace' => 'argomento_nonleg/sort', 
                                                'triple_value' => $triple_value,
                                                'route' => '@argomento_nonleg')) ?>
   
      <?php include_partial('nonlegList', 
                            array('pager' => $pager, 'triple_value' => $triple_value)) ?>

	  </div>


    <div class="clear-both"/>			
    </div>
		
  </div>
</div>

<?php slot('breadcrumbs') ?>
  <?php echo link_to("home", "@homepage") ?> /
  <?php echo link_to('argomenti', '@argomenti') ?> /
  <?php echo strtolower($argomento->getTripleValue()) ?>
<?php end_slot() ?>