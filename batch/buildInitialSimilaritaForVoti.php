<?php
/*
 * This file is part of the Openpolis project
 *
 * (c) 2009 Guglielmo Celata <guglielmo.celata@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
?>
<?php
define('SF_ROOT_DIR',    realpath(dirname(__FILE__).'/..'));
define('SF_APP',         'fe');
define('SF_ENVIRONMENT', 'dev');
define('SF_DEBUG',       true);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');
sfContext::getInstance();

require_once("batch/get_args_options.php");
$args = arguments($argv);
$argv = $args['input'];
$argc = count($argv);


if ( array_key_exists('deleteAll', $args) )
{
  OppSimilaritaPeer::doDeleteAll();
  print "rimosse tutte le informazioni di similarita\n";
  exit;
}

# controllo sintassi
if ( $argc < 2 ) 
{
  print "sintassi: php batch/updateMPSDistanceForVotes C|S NLEG [--deleteAll]\n";  
  print "            C|S - (C)amera o (S)enato\n";
  print "            NLEG - 15, 16\n";
  exit;
}

$ramo = $argv[1];
if ($ramo != 'C' && $ramo != 'S')
{
  print "specificare C o S per il ramo (Camera o Senato) \n";  
  exit;  
}

if ($argc == 3)
  $legislatura = $argv[2];
else
  $legislatura = 16;
if ($legislatura != 15 && $legislatura != 16)
{
  print "il secondo argomento (legislatura) deve valere 15 o 16 \n";  
  exit;  
}


$voto['Favorevole'] = 'FAV';
$voto['Contrario'] = 'CON';
$voto['Astenuto'] = 'AST';
$voto['Assente'] = 'NA';
$voto['In missione'] = 'NA';
$voto['Presidente di turno'] = 'NA';
$voto['Richiedente la votazione e non votante'] = 'NA';
$voto['Voto segreto'] = 'SKIP';
$voto['Partecipante votazione non valida'] = 'SKIP';


// estrae le cariche (attuali) per ramo e legislatura
$c = new Criteria();
$c->add(OppCaricaPeer::LEGISLATURA, $legislatura);
$c->add(OppCaricaPeer::DATA_FINE, null, Criteria::ISNULL);
if ($ramo == 'C')
  $c->add(OppCaricaPeer::TIPO_CARICA_ID, 1);
else
  $c->add(OppCaricaPeer::TIPO_CARICA_ID, array(4, 5), Criteria::IN);
$cariche = OppCaricaPeer::doSelect($c);
unset($c);

$ncariche = count($cariche);
echo $ncariche . "\n";

// costruisce array dei politici, con i voti
$politici = array();
foreach ($cariche as $i => $carica) {
  
  $politico = $carica->getOppPolitico();
  $politici[$i]['id'] = $carica->getId(); 
  $politici[$i]['cognome'] = $politico->getCognome(); 
  $politici[$i]['nome'] = $politico->getNome(); 
  echo $i . ": " . $politici[$i]['nome'] . " " . $politici[$i]['cognome'] . ": ";
  
  // legge i voti dal DB e li mette nell'array voti
  $c = new Criteria();
  $c->clearSelectColumns();
  $c->addSelectColumn(OppVotazioneHasCaricaPeer::VOTAZIONE_ID);
  $c->addSelectColumn(OppVotazioneHasCaricaPeer::VOTO);
  $c->add(OppVotazioneHasCaricaPeer::CARICA_ID, $carica->getId());
  $c->addJoin(OppVotazionePeer::ID, OppVotazioneHasCaricaPeer::VOTAZIONE_ID);
  $res = OppVotazioneHasCaricaPeer::doSelectRS($c);

  $voti = array();
  while ($res->next())
  {
    $v = $voto[$res->getString(2)];
    if ($v != 'NA' && $v != 'SKIP')
      $voti[$res->getInt(1)] = $v;
  }  
  $politici[$i]['voti'] = $voti;
  unset($res);
  $nvoti = count($voti);
  
  echo " ($nvoti voti)\n";

}


// scrittura completa della matrice simmetrica delle similarità
// tranne la diagonale che è nulla per default
for ($i = 0; $i<$ncariche; $i++)
{
  $dd = new OppSimilarita();
  $dd->setCaricaFromId($politici[$i]['id']);
  $dd->setCaricaToId($politici[$i]['id']);
  $dd->save();
  $dd = null;
  unset($dd);
  
  for ($j = $i+1; $j<$ncariche; $j++)
  {
    $d = new OppSimilarita();
    $d->setCaricaFromId($politici[$i]['id']);
    $d->setCaricaToId($politici[$j]['id']);
    $d->setVotingSimilarity(OppSimilaritaPeer::similarityForVotes($politici[$i], $politici[$j], $ramo) / $ncariche);
    $d->save();
    
    // scrittura elemento simmetrico
    $ds = new OppSimilarita();
    $ds->setCaricaFromId($d->getCaricaToId());
    $ds->setCaricaToId($d->getCaricaFromId());
    $ds->setVotingSimilarity($d->getVotingSimilarity());
    $ds->save();
  
    $ds = null;
    unset($ds);
  
    $d = null;
    unset($d);
  }
  print ".";
  if ($i > 0 && $i % 10 == 0) print "$i/$ncariche ";
  if ($i > 0 && $i % 50 == 0) print "\n";
}

echo "done\n";

