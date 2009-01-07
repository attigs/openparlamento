<table id="disegni-decreti" class="column-table">
  <thead>
    <tr>
      <th scope="col"><br />sigla/titolo:</th>
      <th scope="col"><br />data voto:</th>
      <th scope="col">ramo parlamentare:</th>
      <th scope="col">esito in Parlamento:</th>
      <th scope="col">voti di scarto:</th>
      <th scope="col">numero di ribelli:</th>
    </tr>
  </thead>

  <tbody>
    <?php foreach ($pager->getResults() as $votazione): ?>
      <tr>
        <th scope="row"><p><?php echo link_to($votazione->getTitolo(), '@votazione?id='.$votazione->getId()) ?></p></td>
        <td><p><?php echo format_date($votazione->getOppSeduta()->getData(), 'dd/MM/yyyy') ?></p></td>
        <td><p><?php echo ($votazione->getOppSeduta()->getRamo()=='C' ? 'Camera' : 'Senato' ) ?></p></td>
	    <td>
		  <?php if($votazione->getEsito()=='APPROVATA'): ?>
		    <?php $class = "green thumb-approved"; ?>
		  <?php elseif($votazione->getEsito()=='RESPINTA'): ?>
		    <?php $class = "red thumb-rejected"; ?>
		  <?php else: ?>
		    <?php $class = ""; ?>
          <?php endif; ?>					
		  <span class="<?php echo $class ?>"><?php echo $votazione->getEsito() ?></span>
		</td>
        <td><p><?php echo $votazione->getMargine() ?></p></td>
        <td><p><?php echo $votazione->getRibelli() ?></p></td>
      </tr>
    <?php endforeach; ?>
  </tbody>

  <tfoot>		  		  
    <tr>
      <td colspan="6" align="center">
        <?php if ($pager->haveToPaginate()): ?>
          <?php echo link_to('<<', '@votazioni?legislatura='.$sf_user->getAttribute('legislatura').'&ramo='.$sf_user->getAttribute('ramo').'&page=1') ?>
          <?php echo link_to('<', '@votazioni?legislatura='.$sf_user->getAttribute('legislatura').'&ramo='.$sf_user->getAttribute('ramo').'&page='.$pager->getPreviousPage()) ?>
          <?php foreach ($pager->getLinks() as $page): ?>
            <?php echo link_to_unless($page == $pager->getPage(), $page, '@votazioni?legislatura='.$sf_user->getAttribute('legislatura').'&ramo='.$sf_user->getAttribute('ramo').'&page='.$page) ?>
          <?php endforeach; ?>
          <?php echo link_to('>', '@votazioni?legislatura='.$sf_user->getAttribute('legislatura').'&ramo='.$sf_user->getAttribute('ramo').'&page='.$pager->getNextPage()) ?>
          <?php echo link_to('>>', '@votazioni?legislatura='.$sf_user->getAttribute('legislatura').'&ramo='.$sf_user->getAttribute('ramo').'&page='.$pager->getLastPage()) ?>
        <?php endif; ?>    	
      </td>	
    </tr>
    <tr>
      <td colspan="6" align="center">
        <?php echo format_number_choice('[0] nessun risultato|[1] 1 risultato|(1,+Inf] %1% risultati', array('%1%' => $pager->getNbResults()), $pager->getNbResults()) ?>
	  </td>
    </tr>
  </tfoot>  		  		
</table>	  	  