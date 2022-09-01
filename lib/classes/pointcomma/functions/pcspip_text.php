<?php

//
// SPIP text management functions
// Obtained from SPIP's ecrire/inc_texte.php3 version 1-6
// and brutally repackaged for PointComma
//
// This librarycan be used in a site by include()ing this
// file, and using the propre() function on a text that's
// been using SPIP shortcuts.
//
// [link text->8] where 8 is the id of a pointcomma object
// to link to
// [link text->http://www.pointcomma.com/]
// - list item
// -* bulleted list item
// [[text in bold]]
// [[[subtitle]]]
// [[[[subtitle with table of contents]]]]
// Tables can also be made.
// For now, images are not supported.
//

//
// SPIP 1-6 is a free software distributed under GPL license.
// For further information, visit the site http://www.uzine.net/spip-en.
//

$flag_pcre = function_exists("preg_replace");
$langue_site = 'en'; // 'fr' for French typography
$charset = 'iso-8859-1';
$spipMetaValues['langue_site'] = $langue_site;
$spipMetaValues['charset'] = $charset;

function lire_meta($name) {
  global $spipMetaValues;
  return $spipMetaValues[$name];
}

//
// ecrire/inc_filtres.php3

// Echappement des entites HTML avec correction des entites "brutes"
// (generees par les butineurs lorsqu'on rentre des caracteres n'appartenant
// pas au charset de la page [iso-8859-1 par defaut])
function corriger_entites_html($texte) {
  return ereg_replace('&amp;(#[0-9]+;)', '&\1', $texte);
}

function entites_html($texte) {
  return corriger_entites_html(htmlspecialchars($texte));
}

// Transformer les &eacute; dans le charset local
function filtrer_entites($texte) {
  // filtrer
  $texte = html2unicode($texte);
  // remettre le tout dans le charset cible
  return unicode2charset($texte);
}

// Tout mettre en entites pour l'export backend (sauf iso-8859-1)
function entites_unicode($texte) {
  return charset2unicode($texte);
}

// Enleve le numero des titres numerotes ("1. Titre" -> "Titre")
function supprimer_numero($texte) {
  $texte = ereg_replace("^[[:space:]]*[0-9]+[.)".chr(176)."][[:space:]]+", "", $texte);
  return $texte;
}

// Suppression basique et brutale de tous les <...>
function supprimer_tags($texte, $rempl = "") {
  // super gavant : la regexp ci-dessous plante sous php3, genre boucle infinie !
  // $texte = ereg_replace("<([^>\"']*|\"[^\"]*\"|'[^']*')*>", $rempl, $texte);
  $texte = ereg_replace("<[^>]*>", $rempl, $texte);
  return $texte;
}

// Convertit les <...> en la version lisible en HTML
function echapper_tags($texte, $rempl = "") {
  $texte = ereg_replace("<([^>]*)>", "&lt;\\1&gt;", $texte);
  return $texte;
}

// Convertit un texte HTML en texte brut
function textebrut($texte) {
  $texte = ereg_replace("[\n\r]+", " ", $texte);
  $texte = eregi_replace("<(p|br)([[:space:]][^>]*)?".">", "\n\n", $texte);
  $texte = ereg_replace("^\n+", "", $texte);
  $texte = ereg_replace("\n+$", "", $texte);
  $texte = ereg_replace("\n +", "\n", $texte);
  $texte = supprimer_tags($texte);
  $texte = ereg_replace("(&nbsp;| )+", " ", $texte);
  return $texte;
}

// Do not do anything bad at all, just return the links
function liens_ouvrants ($texte) {
	return $texte;
}

// Corrige les caracteres degoutants utilises par les Windozeries
function corriger_caracteres($texte) {
  if (lire_meta('charset') != 'iso-8859-1')
    return $texte;
  // 145,146,180 = simple quote ; 147,148 = double quote ; 150,151 = tiret long
  return strtr($texte, chr(145).chr(146).chr(180).chr(147).chr(148).chr(150).chr(151), "'''".'""--');
}

// Transformer les sauts de paragraphe en simples passages a la ligne
function PtoBR($texte){
  $texte = eregi_replace("</p>", "\n", $texte);
  $texte = eregi_replace("<p([[:space:]][^>]*)?".">", "<br>", $texte);
  $texte = ereg_replace("^[[:space:]]*<br>", "", $texte);
  return $texte;
}

// Majuscules y compris accents, en HTML
function majuscules($texte) {
  if (lire_meta('charset') != 'iso-8859-1')
    return "<span style='text-transform: uppercase'>$texte</span>";

  $suite = htmlentities($texte);
  $suite = ereg_replace('&amp;', '&', $suite);
  $suite = ereg_replace('&lt;', '<', $suite);
  $suite = ereg_replace('&gt;', '>', $suite);
  $texte = '';
  if (ereg('^(.*)&([A-Za-z])([a-zA-Z]*);(.*)$', $suite, $regs)) {
    $texte .= majuscules($regs[1]); // quelle horrible recursion
    $suite = $regs[4];
    $carspe = $regs[2];
    $accent = $regs[3];
    if (ereg('^(acute|grave|circ|uml|cedil|slash|caron|ring|tilde|elig)$', $accent))
      $carspe = strtoupper($carspe);
    if ($accent == 'elig') $accent = 'Elig';
    $texte .= '&'.$carspe.$accent.';';
  }
  $texte .= strtoupper($suite);
  return $texte;
}

// "127.4 ko" ou "3.1 Mo"
function taille_en_octets ($taille) {
  if ($taille < 1024) {$taille = _T('taille_octets', array('taille' => $taille));}
  else if ($taille < 1024*1024) {
    $taille = _T('taille_ko', array('taille' => ((floor($taille / 102.4))/10)));
  } else {
    $taille = _T('taille_mo', array('taille' => ((floor(($taille / 1024) / 102.4))/10)));
  }
  return $taille;
}


// Transforme n'importe quel champ en une chaine utilisable
// en PHP ou Javascript en toute securite
// < ? php $x = '[(#TEXTE|texte_script)]'; ? >
function texte_script($texte) {
  $texte = str_replace('\\', '\\\\', $texte);
  $texte = str_replace('\'', '\\\'', $texte);
  return $texte;
}


// Rend une chaine utilisable sans dommage comme attribut HTML
function attribut_html($texte) {
  $texte = ereg_replace('"', '&quot;', supprimer_tags($texte));
  return $texte;
}

// Vider les url nulles comme 'http://' ou 'mailto:'
function vider_url($url) {
  if (eregi("^(http:?/?/?|mailto:?)$", trim($url)))
    return false;
  else
    return $url;
}



//
// Alignements en HTML
//

function aligner($letexte,$justif) {
  $letexte = eregi_replace("<p([^>]*)", "<p\\1 align='$justif'", trim($letexte));
    if($letexte<>"" AND !ereg("^[[:space:]]*<p", $letexte)) {
    $letexte = "<p class='spip' align='$justif'>" . $letexte . "</p>";
  }
  return $letexte;
}

function justifier($letexte) {
  return aligner($letexte,'justify');
}

function aligner_droite($letexte) {
  return aligner($letexte,'right');
}

function aligner_gauche($letexte) {
  return aligner($letexte,'left');
}

function centrer($letexte) {
  return aligner($letexte,'center');
}

//
// ecrire/inc_texte.php3

//
// Initialisation de quelques variables globales
// (on peut les modifier globalement dans mes_fonctions.php3,
//  OU individuellement pour chaque type de page dans article.php3,
//  rubrique.php3, etc. cf doc...)
// Par securite ne pas accepter les variables passees par l'utilisateur
//
function tester_variable($nom_var, $val){
  if (!isset($GLOBALS[$nom_var])
    OR $_GET[$nom_var] OR $GLOBALS['HTTP_GET_VARS'][$nom_var]
    OR $_PUT[$nom_var] OR $GLOBALS['HTTP_PUT_VARS'][$nom_var]
    OR $_POST[$nom_var] OR $GLOBALS['HTTP_POST_VARS'][$nom_var]
    OR $_COOKIE[$nom_var] OR $GLOBALS['HTTP_COOKIE_VARS'][$nom_var]
    OR $_REQUEST[$nom_var]
  )
    $GLOBALS[$nom_var] = $val;
}

// PointComma modif: added anchor placeholder
tester_variable('debut_inter_nav', "\n<a name=\"nav__PCSPIPNAV__\"></a><h3>");
tester_variable('fin_inter_nav', "</h3>\n");
tester_variable('debut_intertitre', "\n<h3>");
tester_variable('fin_intertitre', "</h3>\n");
tester_variable('ligne_horizontale', "\n<hr />\n");
tester_variable('ouvre_ref', '&nbsp;[');
tester_variable('ferme_ref', ']');
tester_variable('ouvre_note', '[');
tester_variable('ferme_note', '] ');
tester_variable('les_notes', '');
$marqueur_notes='';
tester_variable('compt_note', 0);
tester_variable('nombre_surligne', 4);
tester_variable('url_glossaire_externe', "http://".lire_meta('langue_site').".wikipedia.org/wiki/");
tester_variable('puce', "-");


//
// Diverses fonctions essentielles
//

// ereg_ ou preg_ ?
function ereg_remplace($cherche_tableau, $remplace_tableau, $texte) {
  global $flag_pcre;

  if ($flag_pcre) return preg_replace($cherche_tableau, $remplace_tableau, $texte);

  $n = count($cherche_tableau);

  for ($i = 0; $i < $n; $i++) {
    $texte = ereg_replace(substr($cherche_tableau[$i], 1, -1), $remplace_tableau[$i], $texte);
  }
  return $texte;
}

// Ne pas afficher le chapo si article virtuel
function nettoyer_chapo($chapo){
  if (substr($chapo,0,1) == "="){
    $chapo = "";
  }
  return $chapo;
}


// Mise de cote des echappements
function echappe_html($letexte,$source) {
  global $flag_pcre;

  if ($flag_pcre) { // beaucoup plus rapide si on a pcre
    $regexp_echap_html = "<html>((.*?))<\/html>";
    $regexp_echap_code = "<code>((.*?))<\/code>";
    $regexp_echap_cadre = "<cadre>((.*?))<\/cadre>";
    $regexp_echap = "/($regexp_echap_html)|($regexp_echap_code)|($regexp_echap_cadre)/si";
  } else {
    $regexp_echap_html = "<html>(([^<]|<[^/]|</[^h]|</h[^t]|</ht[^m]|</htm[^l]|<\/html[^>])*)<\/html>";
    $regexp_echap_code = "<code>(([^<]|<[^/]|</[^c]|</c[^o]|</co[^d]|</cod[^e]|<\/code[^>])*)<\/code>";
    $regexp_echap_cadre = "<cadre>(([^<]|<[^/]|</[^c]|</c[^a]|</ca[^d]|</cad[^r]|</cadr[^e]|<\/cadre[^>])*)<\/cadre>";
    $regexp_echap = "($regexp_echap_html)|($regexp_echap_code)|($regexp_echap_cadre)";
  }

  while (($flag_pcre && preg_match($regexp_echap, $letexte, $regs))
    || (!$flag_pcre && eregi($regexp_echap, $letexte, $regs))) {
    $num_echap++;

    if ($regs[1]) {
      // Echapper les <html>...</ html>
      $les_echap[$num_echap] = $regs[2];
    }
    else
    if ($regs[4]) {
      // Echapper les <code>...</ code>
      $lecode = entites_html($regs[5]);

      // ne pas mettre le <div...> s'il n'y a qu'une ligne
      if (is_int(strpos($lecode,"\n")))
        $lecode = nl2br("<div align='left' class='spip_code'>".trim($lecode)."</div>");

      $lecode = ereg_replace("\t", "&nbsp; &nbsp; &nbsp; &nbsp; ", $lecode);
      $lecode = ereg_replace("  ", " &nbsp;", $lecode);
      $les_echap[$num_echap] = "<tt>".$lecode."</tt>";
    }
    else
    if ($regs[7]) {
      // Echapper les <cadre>...</cadre>
      $lecode = trim(entites_html($regs[8]));
      $total_lignes = count(explode("\n", $lecode)) + 1;

      $les_echap[$num_echap] = "<form><textarea cols='40' rows='$total_lignes' wrap='no' class='spip_cadre'>".$lecode."</textarea></form>";

    }

    $pos = strpos($letexte, $regs[0]);
    $letexte = substr($letexte,0,$pos)."___SPIP_$source$num_echap ___"
      .substr($letexte,$pos+strlen($regs[0]));
  }

  //
  // Echapper les <a href>
  //
  $regexp_echap = "<a[[:space:]][^>]+>";
  while (eregi($regexp_echap, $letexte, $regs)) {
    $num_echap++;
    $les_echap[$num_echap] = $regs[0];
    $pos = strpos($letexte, $les_echap[$num_echap]);
    $letexte = substr($letexte,0,$pos)."___SPIP_$source$num_echap ___"
      .substr($letexte,$pos+strlen($les_echap[$num_echap]));
  }

	if (isset($les_echap)) {
		return array($letexte, $les_echap);
	} 
	else {
		return array($letexte, array());
	}
}

// Traitement final des echappements
function echappe_retour($letexte, $les_echap, $source) {
  while(ereg("___SPIP_$source([0-9]+) ___", $letexte, $match)) {
    $lenum = $match[1];
    $cherche = $match[0];
    $pos = strpos($letexte, $cherche);
    $letexte = substr($letexte, 0, $pos). $les_echap[$lenum] . substr($letexte, $pos + strlen($cherche));
  }
  return $letexte;
}

function couper($texte, $long) {
  $texte2 = substr($texte, 0, $long * 2); /* heuristique pour prendre seulement le necessaire */
  if (strlen($texte2) < strlen($texte)) $plus_petit = true;
  $texte = ereg_replace("\[([^\[]*)->([^]]*)\]","\\1", $texte2);

  // supprimer les notes
  $texte = ereg_replace("\[\[([^]]|\][^]])*\]\]", "", $texte);

  // supprimer les codes typos
  $texte = ereg_replace("[{}]", "", $texte);

  $texte2 = substr($texte." ", 0, $long);
  $texte2 = ereg_replace("([^[:space:]][[:space:]]+)[^[:space:]]*$", "\\1", $texte2);
  if ((strlen($texte2) + 3) < strlen($texte)) $plus_petit = true;
  if ($plus_petit) $texte2 .= ' (...)';
  return $texte2;
}

// prendre <intro>...</intro> sinon couper a la longueur demandee
function couper_intro($texte, $long) {
  $texte = eregi_replace("(</?)intro>", "\\1intro>", $texte); // minuscules
  while ($fin = strpos($texte, "</intro>")) {
    $zone = substr($texte, 0, $fin);
    $texte = substr($texte, $fin + strlen("</intro>"));
    if ($deb = strpos($zone, "<intro>") OR substr($zone, 0, 7) == "<intro>")
      $zone = substr($zone, $deb + 7);
    $intro .= $zone;
  }

  if ($intro)
    $intro = $intro.' (...)';
  else
    $intro = couper($texte, $long);

  // supprimer un eventuel chapo redirecteur =http:/.....
  $intro = ereg_replace("^=http://[^[:space:]]+","",$intro);

  return $intro;
}


//
// Les elements de propre()
//

// Securite : empecher l'execution de code PHP
function interdire_scripts($source) {
  $source = eregi_replace("<(\%|\?|([[:space:]]*)script)", "&lt;\\1", $source);
  return $source;
}


// Correction typographique francaise
function typo_fr($letexte) {

  // nettoyer 160 = nbsp ; 187 = raquo ; 171 = laquo ; 176 = deg
  if (lire_meta('charset') == 'iso-8859-1') {
    $letexte = strtr($letexte,chr(160),"~");
    $chars = array (187,171,176);
    while (list(,$c) = each($chars))
      $letexte = ereg_replace(chr($c),"&#$c;",$letexte);
  }

  // unifier sur la representation unicode
  $letexte = ereg_replace("&nbsp;","~",$letexte);
  $letexte = ereg_replace("&raquo;","&#187;",$letexte);
  $letexte = ereg_replace("&laquo;","&#171;",$letexte);
  $letexte = ereg_replace("&deg;","&#176;",$letexte);

  $cherche1 = array(
    /* 2 */   '/((^|[^\#0-9a-zA-Z\&])[\#0-9a-zA-Z]*)\;/',
    /* 3 */   '/&#187;|[!?]| -,|:([^0-9]|$)/',
    /* 4 */   '/&#171;|(M(M?\.|mes?|r\.?)|[MnN]&#176;) /'
  );
  $remplace1 = array(
    /* 2 */   '\1~;',
    /* 3 */   '~\0',
    /* 4 */   '\0~'
  );

  $letexte = ereg_remplace($cherche1, $remplace1, $letexte);
  $letexte = ereg_replace(" *~+ *", "~", $letexte);

  $cherche2 = array(
    '/(http|ftp|mailto)~:/',
    '/~/'
  );
  $remplace2 = array(
    '\1:',
    '&nbsp;'
  );

  $letexte = ereg_remplace($cherche2, $remplace2, $letexte);

  // remettre le caractere simple ??
  if (lire_meta('charset') == 'iso-8859-1') {
    $chars = array (187,171,176);
    while (list(,$c) = each($chars))
      $letexte = ereg_replace("&#$c;",chr($c),$letexte);
  }

  return ($letexte);
}

// rien sauf les ~
function typo_en($letexte) {
  $letexte = ereg_replace("&nbsp;","~",$letexte);
  return ereg_replace(" *~+ *", "&nbsp;", $letexte);
}

// Typographie generale : francaise si la langue principale du site est
// 'cpf', 'fr' ou 'eo',
// sinon anglaise (minimaliste)
function typo($letexte) {
  global $langue_site;

  list($letexte, $les_echap) = echappe_html($letexte, "SOURCETYPO");

  if (($langue_site == 'fr') OR ($langue_site == 'eo') OR ($langue_site == 'cpf'))
    $letexte = typo_fr($letexte);
  else
    $letexte = typo_en($letexte);

  $letexte = corriger_caracteres($letexte);
  $letexte = echappe_retour($letexte, $les_echap, "SOURCETYPO");

  return $letexte;
}


// PointComma modif: add function
function pcGenItemLink($itemId) {
  global $pcConfig;
  $rsItemLink = pcdb_select('SELECT `'.addslashes($pcConfig['dbPrefix']).'types`.linkTemplate AS linkTemplate FROM `'.addslashes($pcConfig['dbPrefix']).'types`, `'.addslashes($pcConfig['dbPrefix']).'items` WHERE `'.addslashes($pcConfig['dbPrefix']).'items`.itemId='.((int)$itemId).' && `'.addslashes($pcConfig['dbPrefix']).'types`.typeId=`'.addslashes($pcConfig['dbPrefix']).'items`.typeId LIMIT 0, 1');
  return str_replace('%itemId', $itemId, $rsItemLink[0]['linkTemplate']);
}

// PointComma modif: got function from ecrire/inc_version.php3
// Verifier la conformite d'une ou plusieurs adresses email
function email_valide($adresse) {
  $adresses = explode(',', $adresse);
  if (is_array($adresses)) {
    while (list(, $adresse) = each($adresses)) {
      // RFC 822
      if (!eregi('^[^()<>@,;:\\"/[:space:]]+(@([-_0-9a-z]+\.)*[-_0-9a-z]+)?$', trim($adresse)))
        return false;
    }
    return true;
  }
  return false;
}

// cette fonction est tordue : on lui passe un tableau correspondant au match
// de la regexp ci-dessous, et elle retourne le texte a inserer a la place
// et le lien "brut" a usage eventuel de redirection...
function extraire_lien ($regs) {
  $lien_texte = $regs[1];

  $lien_url = trim($regs[3]);
  $compt_liens++;
  $lien_interne = false;
  if (ereg('^(art(icle)?|rub(rique)?|br(.ve)?|aut(eur)?|mot|site|doc(ument)?|im(age|g))? *([[:digit:]]+)$', $lien_url, $match)) {
    // Should remove art/rub/br/aut/mot/site/doc/img
    // Traitement des liens internes

    // PointComma modif: cut URL rewriting

    $id_lien = $match[8];
    $type_lien = $match[1];
    $lien_interne=true;
    $class_lien = "in";

    // PointComma modif: cut cases of $type_lien other than article

    $lien_url = pcGenItemLink($id_lien);
    if (!$lien_texte) {
      $req = "select titre from spip_articles where id_article=$id_lien";
      $row = @spip_fetch_array(@spip_query($req));
      $lien_texte = $row['titre'];

    }

    // supprimer les numeros des titres
    $lien_texte = supprimer_numero($lien_texte);
  }
  else if (ereg('^\?(.*)$', $lien_url, $regs)) {
    // Liens glossaire
    $lien_url = substr($lien_url, 1);
    $class_lien = "glossaire";
  }
  else {
    // Liens non automatiques
    $class_lien = "out";
    // texte vide ?
    if ((!$lien_texte) and (!$lien_interne)) {
      $lien_texte = ereg_replace('"', '', $lien_url);
      if (strlen($lien_texte)>40)
        $lien_texte = substr($lien_texte,0,35).'...';
      $class_lien = "url";
    }
    // petites corrections d'URL
    if (ereg("^www\.[^@]+$",$lien_url))
      $lien_url = "http://".$lien_url;
    else if (strpos($lien_url, "@") && email_valide($lien_url))
      $lien_url = "mailto:".$lien_url;
  }

  $insert = "<a href=\"$lien_url\""
    .">".typo($lien_texte)."</a>";

  return array($insert, $lien_url);
}

//
// Traitement des listes (merci a Michael Parienti)
//
function traiter_listes ($texte) {
  $parags = split ("\n[[:space:]]*\n", $texte);
  unset($texte);

	$texte ='';
	
  // chaque paragraphe est traite a part
  while (list(,$para) = each($parags)) {
    $niveau = 0;
    $lignes = explode("\n-", "\n" . $para);

    // ne pas toucher a la premiere ligne
    list(,$debut) = each($lignes);
    $texte .= $debut;

    // chaque item a sa profondeur = nb d'etoiles
		$type = '';
		
    while (list(,$item) = each($lignes)) {
      ereg("^([*]*|[#]*)([^*#].*)", $item, $regs);
      $profond = strlen($regs[1]);

      if ($profond > 0) {
        $ajout = '';

        // changement de type de liste au meme niveau : il faut
        // descendre un niveau plus bas, fermer ce niveau, et
        // remonter
        $nouv_type = (substr($item,0,1) == '*') ? 'ul' : 'ol';
        $change_type = ($type AND ($type <> $nouv_type) AND ($profond == $niveau)) ? 1 : 0;
        $type = $nouv_type;

        // d'abord traiter les descentes
        while ($niveau > $profond - $change_type) {
          $ajout .= $pile_li[$niveau];
          $ajout .= $pile_type[$niveau];
          if (!$change_type)
            unset ($pile_li[$niveau]);
          $niveau --;
        }

        // puis les identites (y compris en fin de descente)
        if ($niveau == $profond && !$change_type) {
          $ajout .= $pile_li[$niveau];
        }
				
        // puis les montees (y compris apres une descente un cran trop bas)
        while ($niveau < $profond) {
          $niveau ++;
          $ajout .= "<$type>";
          $pile_type[$niveau] = "</$type>";
        }

        $ajout .= "<li>";
        $pile_li[$profond] = "</li>";
      }
      else {
        $ajout = "\n-"; // puce normale ou <hr>
      }

      $texte .= $ajout . $regs[2];
    }

    // retour sur terre
		$ajout = '';
		
    while ($niveau > 0) {
      $ajout .= $pile_li[$niveau];
      $ajout .= $pile_type[$niveau];
      $niveau --;
    }
    $texte .= $ajout;

    // paragraphe
    $texte .= "\n\n";
  }

  // sucrer les deux derniers \n
  return substr($texte, 0, -2);
}


// Nettoie un texte, traite les raccourcis spip, la typo, etc.
function traiter_raccourcis($letexte, $les_echap = false, $traiter_les_notes = 'oui') {
  global $puce;
  global $debut_intertitre, $debut_inter_nav, $fin_intertitre, $fin_inter_nav, $ligne_horizontale, $url_glossaire_externe;
  global $compt_note;
  global $les_notes;
  global $marqueur_notes;
  global $ouvre_ref;
  global $ferme_ref;
  global $ouvre_note;
  global $ferme_note;
  global $flag_pcre;

  // Harmoniser les retours chariot
  $letexte = ereg_replace ("\r\n?", "\n",$letexte);

  // echapper les <a href>, <html>...< /html>, <code>...< /code>
  if (!$les_echap)
    list($letexte, $les_echap) = echappe_html($letexte, "SOURCEPROPRE");

  // Corriger HTML
  $letexte = eregi_replace("</?p>","\n\n\n",$letexte);

  //
  // Notes de bas de page
  //
  $regexp = "\[\[(([^]]|[^]]\][^]])*)\]\]";
  /* signifie : deux crochets ouvrants, puis pas-crochet-fermant ou
    crochet-fermant entoure de pas-crochets-fermants (c'est-a-dire
    tout sauf deux crochets fermants), puis deux fermants */
  while (ereg($regexp, $letexte, $regs)){
    $note_texte = $regs[1];
    $num_note = false;

    // note auto ou pas ?
    if (ereg("^ *<([^>]*)>",$note_texte,$regs)){
      $num_note=$regs[1];
      $note_texte = ereg_replace ("^ *<([^>]*)>","",$note_texte);
    } else {
      $compt_note++;
      $num_note=$compt_note;
    }

    // preparer la note
    if ($num_note) {
      if ($marqueur_notes)
        $mn = $marqueur_notes.'-';
      $insert = "$ouvre_ref<a href='#nb$mn$num_note' name='nh$mn$num_note' class='spip_note'>$num_note</a>$ferme_ref";
      $appel = "<html>$ouvre_note<a href='#nh$mn$num_note' name='nb$mn$num_note' class='spip_note'>$num_note</a>$ferme_note</html>";
    } else {
      $insert = '';
      $appel = '';
    }

    // l'ajouter "brut" dans les notes
    if ($note_texte) {
      if ($mes_notes)
        $mes_notes .= "\n\n";
      $mes_notes .= $appel . $note_texte;
    }

    // dans le texte, mettre l'appel de note a la place de la note
    $letexte = implode($insert, split($regexp, $letexte, 2));
  }

  //
  // Raccourcis automatiques vers un glossaire
  // (on traite ce raccourci en deux temps afin de ne pas appliquer
  //  la typo sur les URLs, voir raccourcis liens ci-dessous)
  //
  if ($url_glossaire_externe) {
    $regexp = "\[\?+([^][<>]+)\]";
    while (ereg($regexp, $letexte, $regs)) {
      $terme = trim($regs[1]);
      $url = $url_glossaire_externe.urlencode(ereg_replace('[[:space:]]+', '_', $terme));
      $code = "[$terme->?$url]";
      $letexte = str_replace($regs[0], $code, $letexte);
    }
  }

  //
  // Raccourcis liens (cf. fonction extraire_lien ci-dessus)
  //
  $regexp = "\[([^][]*)->(>?)([^]]*)\]";
  $texte_a_voir = $letexte;
  $texte_vu = '';
  while (ereg($regexp, $texte_a_voir, $regs)){
    list($insert, $lien) = extraire_lien($regs);
    $zetexte = split($regexp,$texte_a_voir,2);

    // typo en-dehors des notes
    $texte_vu .= typo($zetexte[0]).$insert;
    $texte_a_voir = $zetexte[1];
  }
  $letexte = $texte_vu.typo($texte_a_voir); // typo de la queue du texte

  //
  // Insertion d'images et de documents utilisateur
  //
  while (eregi("<(IMG|DOC|EMB)([0-9]+)(\|([^\>]*))?".">", $letexte, $match)) {
//    include('inc_documents.php3');
    $letout = quotemeta($match[0]);
    $letout = ereg_replace("\|", "\|", $letout);
    $id_document = $match[2];
    $align = $match[4];
    if (eregi("emb", $match[1]))
      $rempl = embed_document($id_document, $align);
    else
      $rempl = integre_image($id_document, $align, $match[1]);
    $letexte = ereg_replace($letout, $rempl, $letexte);
  }

  //
  // Tableaux
  //
  $letexte = ereg_replace("^\n?\|", "\n\n|", $letexte);
  $letexte = ereg_replace("\|\n?$", "|\n\n", $letexte);

  $tableBeginPos = strpos($letexte, "\n\n|");
  $tableEndPos = strpos($letexte, "|\n\n");
  while (is_integer($tableBeginPos) && is_integer($tableEndPos) && $tableBeginPos < $tableEndPos + 3) {
    $textBegin = substr($letexte, 0, $tableBeginPos);
    $textTable = substr($letexte, $tableBeginPos + 2, $tableEndPos - $tableBeginPos);
    $textEnd = substr($letexte, $tableEndPos + 3);

    $newTextTable = "\n<p><table>";
    $rowId = 0;
    $lineEnd = strpos($textTable, "|\n");
    while (is_integer($lineEnd)) {
      $rowId++;
      $row = substr($textTable, 0, $lineEnd);
      $textTable = substr($textTable, $lineEnd + 2);
      if ($rowId == 1 && ereg("^(\\|[[:space:]]*\\{\\{[^}]+\\}\\}[[:space:]]*)+$", $row)) {
        $newTextTable .= '<tr class="row_first">';
      } else {
        $newTextTable .= '<tr class="row_'.($rowId % 2 ? 'odd' : 'even').'">';
      }
      $newTextTable .= ereg_replace("\|([^\|]+)", "<td>\\1</td>", $row);
      $newTextTable .= '</tr>';
      $lineEnd = strpos($textTable, "|\n");
    }
    $newTextTable .= "</table>\n<p>\n";

    $letexte = $textBegin . $newTextTable . $textEnd;

    $tableBeginPos = strpos($letexte, "\n\n|");
    $tableEndPos = strpos($letexte, "|\n\n");
  }


  //
  // Ensemble de remplacements implementant le systeme de mise
  // en forme (paragraphes, raccourcis...)
  //
  // ATTENTION : si vous modifiez cette partie, modifiez les DEUX
  // branches de l'alternative (if (!flag_pcre).../else).
  //

  $letexte = trim($letexte);


  // les listes
  if (ereg("\n-[*#]", "\n".$letexte))
    $letexte = traiter_listes($letexte);

  // autres raccourcis
  if (!$flag_pcre) {
    /* note : on pourrait se passer de cette branche, car ereg_remplace() fonctionne
       sans pcre ; toutefois les elements ci-dessous sont un peu optimises (str_replace
       est plus rapide que ereg_replace), donc laissons les deux branches cohabiter, ca
       permet de gagner un peu de temps chez les hergeurs nazes */
    $letexte = ereg_replace("(^|\n)(-{4,}|_{4,})", "___SPIP_ligne_horizontale___", $letexte);
    $letexte = ereg_replace("^- *", "$puce&nbsp;", $letexte);
    $letexte = ereg_replace("\n- *", "\n<br />$puce&nbsp;",$letexte);
    $letexte = ereg_replace("\n_ +", "\n<br />",$letexte);
    $letexte = ereg_replace("(( *)\n){2,}", "\n<p>", $letexte);
    $letexte = str_replace("{{{{", "___SPIP_debut_inter_nav___", $letexte);
    $letexte = str_replace("}}}}", "___SPIP_fin_inter_nav___", $letexte);
    $letexte = str_replace("{{{", "___SPIP_debut_intertitre___", $letexte);
    $letexte = str_replace("}}}", "___SPIP_fin_intertitre___", $letexte);
    $letexte = str_replace("{{", "<b>", $letexte);
    $letexte = str_replace("}}", "</b>", $letexte);
    $letexte = str_replace("{", "<i>", $letexte);
    $letexte = str_replace("}", "</i>", $letexte);
    $letexte = eregi_replace("(<br[[:space:]]*/?".">)+(<p>|<br[[:space:]]*/?".">)", "\n<p>", $letexte);
    $letexte = str_replace("<p>", "<p>", $letexte);
    $letexte = str_replace("\n", " ", $letexte);
  }
  else {
    $cherche1 = array(
      /* 0 */   "/(^|\n)(----+|____+)/",
      /* 1 */   "/^- */",
      /* 2 */   "/\n- */",
      /* 3 */   "/\n_ +/",
      /* 4 */   "/(( *)\n){2,}/",
      /* 4bis */  "/\{\{\{\{/",
      /* 4ter */  "/\}\}\}\}/",
      /* 5 */   "/\{\{\{/",
      /* 6 */   "/\}\}\}/",
      /* 7 */   "/\{\{/",
      /* 8 */   "/\}\}/",
      /* 9 */   "/\{/",
      /* 10 */  "/\}/",
      /* 11 */  "/(<br[[:space:]]*\/?".">){2,}/",
      /* 12 */  "/<p>([\n]*)(<br[[:space:]]*\/?".">)+/",
      /* 13 */  "/<p>/",
      /* 14 */  "/\n/"
    );
    $remplace1 = array(
      /* 0 */   "___SPIP_ligne_horizontale___",
      /* 1 */   "$puce&nbsp;",
      /* 2 */   "\n<br />$puce&nbsp;",
      /* 3 */   "\n<br />",
      /* 4 */   "\n<p>",
      /* 4bis */  "___SPIP_debut_inter_nav___",
      /* 4ter */  "___SPIP_fin_inter_nav___",
      /* 5 */   "___SPIP_debut_intertitre___",
      /* 6 */   "___SPIP_fin_intertitre___",
      /* 7 */   "<b>",
      /* 8 */   "</b>",
      /* 9 */   "<i>",
      /* 10 */  "</i>",
      /* 11 */  "\n<p>",
      /* 12 */  "\n<p>",
      /* 13 */  "<p>",
      /* 14 */  " "
    );

    //
    // PointComma modif: grab subtitles
    global $pcSpipSubtitles;
    $test = preg_match_all('/{{{{([^\}]*)}}}}/', $letexte, $pcSpipSubtitles);

    $letexte = ereg_remplace($cherche1, $remplace1, $letexte);
  }

  // paragrapher
  if (ereg('<p>',$letexte))
    $letexte = '<p>'.ereg_replace('<p>', "</p>\n".'<p>',$letexte).'</p>';

  // intertitres & hr compliants
  $letexte = ereg_replace('(<p>)?[[:space:]]*___SPIP_debut_intertitre___', $debut_intertitre, $letexte);
  $letexte = ereg_replace('___SPIP_fin_intertitre___[[:space:]]*(</p>)?', $fin_intertitre, $letexte);
  $letexte = ereg_replace('(<p>)?[[:space:]]*___SPIP_debut_inter_nav___', $debut_inter_nav, $letexte);
  $letexte = ereg_replace('___SPIP_fin_inter_nav___[[:space:]]*(</p>)?', $fin_inter_nav, $letexte);
  $letexte = ereg_replace('(<p>)?[[:space:]]*___SPIP_ligne_horizontale___[[:space:]]*(</p>)?', $ligne_horizontale, $letexte);

  // Reinserer les echappements
  $letexte = echappe_retour($letexte, $les_echap, "SOURCEPROPRE");

  if (isset($mes_notes) && $mes_notes) {
    $mes_notes = traiter_raccourcis($mes_notes, $les_echap, 'non');
    if (ereg('<p>',$mes_notes))
      $mes_notes = ereg_replace('<p>', '<p class="spip_note">', $mes_notes);
    else
      $mes_notes = '<p class="spip_note">'.$mes_notes."</p>\n";
    $mes_notes = echappe_retour($mes_notes, $les_echap, "SOURCEPROPRE");
    $les_notes .= interdire_scripts($mes_notes);
  }

  //Nel edit add for pointComma:
  //if no <p> in the modified text just add it
  if (!ereg('<p',$letexte))
    $letexte = '<p>'.$letexte.'</p>';

  return $letexte;
}


function numeroteToc($subj) {
  global $pcSpipUnique;
  static $numerotation;
  $numerotation[$pcSpipUnique]++;
  return $numerotation[$pcSpipUnique];
}

// Filtre a appliquer aux champs du type #TEXTE*
function propre($letexte, &$tableOfContents, $hLevel = 3, $classModif = '') {
  // PointComma modif: define subtitles level
  if ($hLevel != 3 || !empty($classModif)) {
		global $debut_inter_nav, $fin_inter_nav, $debut_intertitre, $fin_intertitre;
		if (!empty($classModif)) {
			$classModif = ' class="'.$classModif.'"';
		}
		$debut_inter_nav = "\n<a name=\"nav__PCSPIPNAV__\"></a><h$hLevel$classModif>";
		$fin_inter_nav = "</h$hLevel>\n";
		$debut_intertitre = "\n<h$hLevel$classModif>";
		$fin_intertitre = "</h$hLevel>\n";
	}
  // PointComma modif: manage subtitles
  $nouveauTexte = interdire_scripts(traiter_raccourcis(trim($letexte)));
  global $pcSpipSubtitles;
  $tocArray = array();
  if (is_array($pcSpipSubtitles[1])) {
		foreach ($pcSpipSubtitles[1] as $subTitle) {
			$subCounter++;
			$tocArray[] = '<a href="#nav'.$subCounter.'" target="_self">'.$subTitle.'</a>';
		}
  }
  if (count($tocArray)) {
    $tableOfContents = '<p class="toc">'.implode('<br>', $tocArray).'</p>';
  } else {
    $tableOfContents = false;
  }
  global $pcSpipUnique;
  list($pcSpipUnique, $temp) = explode(' ', microtime());
  $nouveauTexte = preg_replace_callback('/__PCSPIPNAV__/', 'numeroteToc', $nouveauTexte);
  return $nouveauTexte;
}


//
// ecrire/inc_charsets.php3

/* charsets supportes :
  utf-8 ;
  iso-8859-1 ; iso-8859-15 ;
  windows-1251  = CP1251 ;
*/
function load_charset ($charset = 'AUTO', $langue_site = 'AUTO') {
  if ($charset == 'AUTO')
    $charset = lire_meta('charset');
  if (is_array($GLOBALS['CHARSET'][$charset]))
    return $charset;

  if ($langue_site == 'AUTO')
    $langue_site = lire_meta('langue_site');

  switch (strtolower($charset)) {
  case 'utf-8':
    $GLOBALS['CHARSET'][$charset] = array();
    return $charset;

  // iso latin 1
  case 'iso-8859-1':
  case '':
    $GLOBALS['CHARSET'][$charset] = array (
    128=>128, 129=>129, 130=>130, 131=>131, 132=>132, 133=>133, 134=>134, 135=>135,
    136=>136, 137=>137, 138=>138, 139=>139, 140=>140, 141=>141, 142=>142, 143=>143,
    144=>144, 145=>145, 146=>146, 147=>147, 148=>148, 149=>149, 150=>150, 151=>151,
    152=>152, 153=>153, 154=>154, 155=>155, 156=>156, 157=>157, 158=>158, 159=>159,
    160=>160, 161=>161, 162=>162, 163=>163, 164=>164, 165=>165, 166=>166, 167=>167,
    168=>168, 169=>169, 170=>170, 171=>171, 172=>172, 173=>173, 174=>174, 175=>175,
    176=>176, 177=>177, 178=>178, 179=>179, 180=>180, 181=>181, 182=>182, 183=>183,
    184=>184, 185=>185, 186=>186, 187=>187, 188=>188, 189=>189, 190=>190, 191=>191,
    192=>192, 193=>193, 194=>194, 195=>195, 196=>196, 197=>197, 198=>198, 199=>199,
    200=>200, 201=>201, 202=>202, 203=>203, 204=>204, 205=>205, 206=>206, 207=>207,
    208=>208, 209=>209, 210=>210, 211=>211, 212=>212, 213=>213, 214=>214, 215=>215,
    216=>216, 217=>217, 218=>218, 219=>219, 220=>220, 221=>221, 222=>222, 223=>223,
    224=>224, 225=>225, 226=>226, 227=>227, 228=>228, 229=>229, 230=>230, 231=>231,
    232=>232, 233=>233, 234=>234, 235=>235, 236=>236, 237=>237, 238=>238, 239=>239,
    240=>240, 241=>241, 242=>242, 243=>243, 244=>244, 245=>245, 246=>246, 247=>247,
    248=>248, 249=>249, 250=>250, 251=>251, 252=>252, 253=>253, 254=>254, 255=>255
    );
    return $charset;


  // iso latin 15 - Gaetan Ryckeboer <gryckeboer@virtual-net.fr>
  case 'iso-8859-15':
    load_charset('iso-8859-1');
    $trans = $GLOBALS['CHARSET']['iso-8859-1'];
    $trans[164]=8364;
    $trans[166]=352;
    $trans[168]=353;
    $trans[180]=381;
    $trans[184]=382;
    $trans[188]=338;
    $trans[189]=339;
    $trans[190]=376;
    $GLOBALS['CHARSET'][$charset] = $trans;
    return $charset;


  // cyrillic - ref. http://czyborra.com/charsets/cyrillic.html
  case 'windows-1251':
  case 'cp1251':
    $GLOBALS['CHARSET'][$charset] = array (
    0x80=>0x0402, 0x81=>0x0403, 0x82=>0x201A, 0x83=>0x0453, 0x84=>0x201E,
    0x85=>0x2026, 0x86=>0x2020, 0x87=>0x2021, 0x88=>0x20AC, 0x89=>0x2030,
    0x8A=>0x0409, 0x8B=>0x2039, 0x8C=>0x040A, 0x8D=>0x040C, 0x8E=>0x040B,
    0x8F=>0x040F, 0x90=>0x0452, 0x91=>0x2018, 0x92=>0x2019, 0x93=>0x201C,
    0x94=>0x201D, 0x95=>0x2022, 0x96=>0x2013, 0x97=>0x2014, 0x99=>0x2122,
    0x9A=>0x0459, 0x9B=>0x203A, 0x9C=>0x045A, 0x9D=>0x045C, 0x9E=>0x045B,
    0x9F=>0x045F, 0xA0=>0x00A0, 0xA1=>0x040E, 0xA2=>0x045E, 0xA3=>0x0408,
    0xA4=>0x00A4, 0xA5=>0x0490, 0xA6=>0x00A6, 0xA7=>0x00A7, 0xA8=>0x0401,
    0xA9=>0x00A9, 0xAA=>0x0404, 0xAB=>0x00AB, 0xAC=>0x00AC, 0xAD=>0x00AD,
    0xAE=>0x00AE, 0xAF=>0x0407, 0xB0=>0x00B0, 0xB1=>0x00B1, 0xB2=>0x0406,
    0xB3=>0x0456, 0xB4=>0x0491, 0xB5=>0x00B5, 0xB6=>0x00B6, 0xB7=>0x00B7,
    0xB8=>0x0451, 0xB9=>0x2116, 0xBA=>0x0454, 0xBB=>0x00BB, 0xBC=>0x0458,
    0xBD=>0x0405, 0xBE=>0x0455, 0xBF=>0x0457, 0xC0=>0x0410, 0xC1=>0x0411,
    0xC2=>0x0412, 0xC3=>0x0413, 0xC4=>0x0414, 0xC5=>0x0415, 0xC6=>0x0416,
    0xC7=>0x0417, 0xC8=>0x0418, 0xC9=>0x0419, 0xCA=>0x041A, 0xCB=>0x041B,
    0xCC=>0x041C, 0xCD=>0x041D, 0xCE=>0x041E, 0xCF=>0x041F, 0xD0=>0x0420,
    0xD1=>0x0421, 0xD2=>0x0422, 0xD3=>0x0423, 0xD4=>0x0424, 0xD5=>0x0425,
    0xD6=>0x0426, 0xD7=>0x0427, 0xD8=>0x0428, 0xD9=>0x0429, 0xDA=>0x042A,
    0xDB=>0x042B, 0xDC=>0x042C, 0xDD=>0x042D, 0xDE=>0x042E, 0xDF=>0x042F,
    0xE0=>0x0430, 0xE1=>0x0431, 0xE2=>0x0432, 0xE3=>0x0433, 0xE4=>0x0434,
    0xE5=>0x0435, 0xE6=>0x0436, 0xE7=>0x0437, 0xE8=>0x0438, 0xE9=>0x0439,
    0xEA=>0x043A, 0xEB=>0x043B, 0xEC=>0x043C, 0xED=>0x043D, 0xEE=>0x043E,
    0xEF=>0x043F, 0xF0=>0x0440, 0xF1=>0x0441, 0xF2=>0x0442, 0xF3=>0x0443,
    0xF4=>0x0444, 0xF5=>0x0445, 0xF6=>0x0446, 0xF7=>0x0447, 0xF8=>0x0448,
    0xF9=>0x0449, 0xFA=>0x044A, 0xFB=>0x044B, 0xFC=>0x044C, 0xFD=>0x044D,
    0xFE=>0x044E, 0xFF=>0x044F); // fin windows-1251
    return $charset;


  // ------------------------------------------------------------------

  // cas particulier pour les entites html (a completer eventuellement)
  case 'html':
    $GLOBALS['CHARSET'][$charset] = array (
    'cent'=>'&#162;', 'pound'=>'&#163;', 'curren'=>'&#164;', 'yen'=>'&#165;', 'brvbar'=>'&#166;',
    'sect'=>'&#167;', 'uml'=>'&#168;', 'ordf'=>'&#170;', 'laquo'=>'&#171;', 'not'=>'&#172;',
    'shy'=>'&#173;', 'macr'=>'&#175;', 'deg'=>'&#176;', 'plusmn'=>'&#177;', 'sup2'=>'&#178;',
    'sup3'=>'&#179;', 'acute'=>'&#180;', 'micro'=>'&#181;', 'para'=>'&#182;', 'middot'=>'&#183;',
    'cedil'=>'&#184;', 'sup1'=>'&#185;', 'ordm'=>'&#186;', 'raquo'=>'&#187;', 'iquest'=>'&#191;',
    'Agrave'=>'&#192;', 'Aacute'=>'&#193;', 'Acirc'=>'&#194;', 'Atilde'=>'&#195;', 'Auml'=>'&#196;',
    'Aring'=>'&#197;', 'AElig'=>'&#198;', 'Ccedil'=>'&#199;', 'Egrave'=>'&#200;', 'Eacute'=>'&#201;',
    'Ecirc'=>'&#202;', 'Euml'=>'&#203;', 'Igrave'=>'&#204;', 'Iacute'=>'&#205;', 'Icirc'=>'&#206;',
    'Iuml'=>'&#207;', 'ETH'=>'&#208;', 'Ntilde'=>'&#209;', 'Ograve'=>'&#210;', 'Oacute'=>'&#211;',
    'Ocirc'=>'&#212;', 'Otilde'=>'&#213;', 'Ouml'=>'&#214;', 'times'=>'&#215;', 'Oslash'=>'&#216;',
    'Ugrave'=>'&#217;', 'Uacute'=>'&#218;', 'Ucirc'=>'&#219;', 'Uuml'=>'&#220;', 'Yacute'=>'&#221;',
    'THORN'=>'&#222;', 'szlig'=>'&#223;', 'agrave'=>'&#224;', 'aacute'=>'&#225;', 'acirc'=>'&#226;',
    'atilde'=>'&#227;', 'auml'=>'&#228;', 'aring'=>'&#229;', 'aelig'=>'&#230;', 'ccedil'=>'&#231;',
    'egrave'=>'&#232;', 'eacute'=>'&#233;', 'ecirc'=>'&#234;', 'euml'=>'&#235;', 'igrave'=>'&#236;',
    'iacute'=>'&#237;', 'icirc'=>'&#238;', 'iuml'=>'&#239;', 'eth'=>'&#240;', 'ntilde'=>'&#241;',
    'ograve'=>'&#242;', 'oacute'=>'&#243;', 'ocirc'=>'&#244;', 'otilde'=>'&#245;', 'ouml'=>'&#246;',
    'divide'=>'&#247;', 'oslash'=>'&#248;', 'ugrave'=>'&#249;', 'uacute'=>'&#250;',
    'ucirc'=>'&#251;', 'uuml'=>'&#252;', 'yacute'=>'&#253;', 'thorn'=>'&#254;',
    'nbsp' => " ", 'copy' => "(c)", 'reg' => "(r)", 'frac14' => "1/4",
    'frac12' => "1/2", 'frac34' => "3/4", 'amp' => '&', 'quot' => '"',
    'apos' => "'", 'lt' => '<', 'gt' => '>'
    );
    return $charset;

  // cas particulier pour la translitteration
  case 'translit':
    $GLOBALS['CHARSET'][$charset] = array (
    // latin
    128=>'euro', 131=>'f', 140=>'OE', 153=>'TM', 156=>'oe', 159=>'Y', 160=>' ',
    161=>'!', 162=>'c', 163=>'L', 164=>'O', 165=>'yen',166=>'|',
    167=>'p',169=>'(c)', 171=>'<<',172=>'-',173=>'-',174=>'(R)',
    176=>'o',177=>'+-',181=>'mu',182=>'p',183=>'.',187=>'>>', 192=>'A',
    193=>'A', 194=>'A', 195=>'A', 196=>'A', 197=>'A', 198=>'AE', 199=>'C',
    200=>'E', 201=>'E', 202=>'E', 203=>'E', 204=>'I', 205=>'I', 206=>'I',
    207=>'I', 209=>'N', 210=>'O', 211=>'O', 212=>'O', 213=>'O', 214=>'O',
    216=>'O', 217=>'U', 218=>'U', 219=>'U', 220=>'U', 223=>'B', 224=>'a',
    225=>'a', 226=>'a', 227=>'a', 228=>'a', 229=>'a', 230=>'ae', 231=>'c',
    232=>'e', 233=>'e', 234=>'e', 235=>'e', 236=>'i', 237=>'i', 238=>'i',
    239=>'i', 241=>'n', 242=>'o', 243=>'o', 244=>'o', 245=>'o', 246=>'o',
    248=>'o', 249=>'u', 250=>'u', 251=>'u', 252=>'u', 255=>'y',

    // esperanto
    264 => 'Cx',265 => 'cx',
    284 => 'Gx',285 => 'gx',
    292 => 'Hx',293 => 'hx',
    308 => 'Jx',309 => 'jx',
    348 => 'Sx',349 => 'sx',
    364 => 'Ux',365 => 'ux',

    // cyrillique
    1026=>'D%', 1027=>'G%', 8218=>'\'', 1107=>'g%', 8222=>'"', 8230=>'...',
    8224=>'/-', 8225=>'/=',  8364=>'EUR', 8240=>'0/00', 1033=>'LJ',
    8249=>'<', 1034=>'NJ', 1036=>'KJ', 1035=>'Ts', 1039=>'DZ',  1106=>'d%',
    8216=>'`', 8217=>'\'', 8220=>'"', 8221=>'"', 8226=>' o ', 8211=>'-',
    8212=>'--', 8212=>'~',  8482=>'(TM)', 1113=>'lj', 8250=>'>', 1114=>'nj',
    1116=>'kj', 1115=>'ts', 1119=>'dz',  1038=>'V%', 1118=>'v%', 1032=>'J%',
    1168=>'G3', 1025=>'IO',  1028=>'IE', 1031=>'YI', 1030=>'II',
    1110=>'ii', 1169=>'g3', 1105=>'io', 8470=>'No.', 1108=>'ie',
    1112=>'j%', 1029=>'DS', 1109=>'ds', 1111=>'yi', 1040=>'A', 1041=>'B',
    1042=>'V', 1043=>'G', 1044=>'D',  1045=>'E', 1046=>'ZH', 1047=>'Z',
    1048=>'I', 1049=>'J', 1050=>'K', 1051=>'L', 1052=>'M', 1053=>'N',
    1054=>'O', 1055=>'P', 1056=>'R', 1057=>'S', 1058=>'T', 1059=>'U',
    1060=>'F', 1061=>'H', 1062=>'C',  1063=>'CH', 1064=>'SH', 1065=>'SCH',
    1066=>'"', 1067=>'Y', 1068=>'\'', 1069=>'`E', 1070=>'YU',  1071=>'YA',
    1072=>'a', 1073=>'b', 1074=>'v', 1075=>'g', 1076=>'d', 1077=>'e',
    1078=>'zh', 1079=>'z',  1080=>'i', 1081=>'j', 1082=>'k', 1083=>'l',
    1084=>'m', 1085=>'n', 1086=>'o', 1087=>'p', 1088=>'r',  1089=>'s',
    1090=>'t', 1091=>'u', 1092=>'f', 1093=>'h', 1094=>'c', 1095=>'ch',
    1096=>'sh', 1097=>'sch',  1098=>'"', 1099=>'y', 1100=>'\'', 1101=>'`e',
    1102=>'yu', 1103=>'ya'
    );

    // translitteration specifique du vietnamien
    // (necessaire pour le moteur de recherche car les mots sont tous tres courts)
    if ($langue_site == 'vi') {
      $translit_vi = array (225=>"a'", 224=>"a`",7843=>"a?",227=>"a~",7841=>"a.",
      226=>"a^",7845=>"a^'",7847=>"a^`",7849=>"a^?",7851=>"a^~",7853=>"a^.",259=>"a(",
      7855=>"a('",7857=>"a(`",7859=>"a(?",7861=>"a(~",7863=>"a(.",193=>"A'",192=>"A`",
      7842=>"A?",195=>"A~",7840=>"A.",194=>"A^",7844=>"A^'",7846=>"A^`",7848=>"A^?",
      7850=>"A^~",7852=>"A^.",258=>"A(",7854=>"A('",7856=>"A(`",7858=>"A(?",7860=>"A(~",
      7862=>"A(.",233=>"e'",232=>"e`",7867=>"e?",7869=>"e~",7865=>"e.",234=>"e^",
      7871=>"e^'",7873=>"e^`",7875=>"e^?",7877=>"e^~",7879=>"e^.",201=>"E'",200=>"E`",
      7866=>"E?",7868=>"E~",7864=>"E.",202=>"E^",7870=>"E^'",7872=>"E^`",7874=>"E^?",
      7876=>"E^~",7878=>"E^.",237=>"i'",236=>"i`",7881=>"i?",297=>"i~",7883=>"i.",
      205=>"I'",204=>"I`",7880=>"I?",296=>"I~",7882=>"I.",243=>"o'",242=>"o`",
      7887=>"o?",245=>"o~",7885=>"o.",244=>"o^",7889=>"o^'",7891=>"o^`",7893=>"o^?",
      7895=>"o^~",7897=>"o^.",417=>"o+",7899=>"o+'",7901=>"o+`",7903=>"o+?",7905=>"o+~",
      7907=>"o+.",211=>"O'",210=>"O`",7886=>"O?",213=>"O~",7884=>"O.",212=>"O^",
      7888=>"O^'",7890=>"O^`",7892=>"O^?",7894=>"O^~",7896=>"O^.",416=>"O+",7898=>"O+'",
      7900=>"O+`",7902=>"O+?",7904=>"O+~",7906=>"O+.",250=>"u'",249=>"u`",7911=>"u?",
      361=>"u~",7909=>"u.",432=>"u+",7913=>"u+'",7915=>"u+`",7917=>"u+?",7919=>"u+~",
      7921=>"u+.",218=>"U'",217=>"U`",7910=>"U?",360=>"U~",7908=>"U.",431=>"U+",
      7912=>"U+'",7914=>"U+`",7916=>"U+?",7918=>"U+~",7920=>"U+.",253=>"y'",7923=>"y`",
      7927=>"y?",7929=>"y~",7925=>"y.",221=>"Y'",7922=>"Y`",7926=>"Y?",7928=>"Y~",
      7924=>"Y.",273=>"d-",208=>"D-");
      while (list($u,$t) = each($translit_vi))
        $GLOBALS['CHARSET'][$charset][$u] = $t;
    }
    return $charset;

  default:
    spip_log("erreur charset $charset non supporte");
    $GLOBALS['CHARSET'][$charset] = array();
    return $charset;
  }
}


// Detecter les versions buggees d'iconv
function test_iconv() {
  static $iconv_ok;

  if (!$iconv_ok) {
    if (!$GLOBALS['flag_iconv']) $iconv_ok = -1;
    else {
      $s = 'chaine de test';
      if (utf_32_to_unicode(iconv('utf-8', 'utf-32', $s)) == 'chaine de test')
        $iconv_ok = 1;
      else
        $iconv_ok = -1;
    }
  }
  return $iconv_ok == 1;
}


//
// Transformer les &eacute; en &#123;
//
function html2unicode($texte) {
  static $trans;
  if (!$trans) {
    global $CHARSET;
    load_charset('html');
    reset($CHARSET['html']);
    while (list($key, $val) = each($CHARSET['html'])) {
      $trans["&$key;"] = $val;
    }
  }

  if ($GLOBALS['flag_strtr2']) return strtr($texte, $trans);

  reset($trans);
  while (list($from, $to) = each($trans)) {
    $texte = str_replace($from, $to, $texte);
  }
  return $texte;
}


//
// Transforme une chaine en entites unicode &#129;
//
function charset2unicode($texte, $charset='AUTO', $forcer = false) {
  static $trans;

  if ($charset == 'AUTO')
    $charset = lire_meta('charset');

  switch ($charset) {
  case 'utf-8':
    // Le passage par utf-32 devrait etre plus rapide
    // (traitements PHP reduits au minimum)
    if (test_iconv()) {
      $s = iconv('utf-8', 'utf-32', $texte);
      if ($s) return utf_32_to_unicode($s);
    }
    return utf_8_to_unicode($texte);

  case 'iso-8859-1':
    // On commente cet appel tant qu'il reste des spip v<1.5 dans la nature
    // pour que le filtre |entites_unicode donne des backends lisibles sur ces spips.
    if (!$forcer) return $texte;

  default:
    if (test_iconv()) {
      $s = iconv($charset, 'utf-32', $texte);
      if ($s) return utf_32_to_unicode($s);
    }

    if (!$trans[$charset]) {
      global $CHARSET;
      load_charset($charset);
      reset($CHARSET[$charset]);
      while (list($key, $val) = each($CHARSET[$charset])) {
        $trans[$charset][chr($key)] = '&#'.$val.';';
      }
    }
    if ($trans[$charset]) {
      if ($GLOBALS['flag_strtr2'])
        $texte = strtr($texte, $trans[$charset]);
      else {
        reset($trans[$charset]);
        while (list($from, $to) = each($trans[$charset])) {
          $texte = str_replace($from, $to, $texte);
        }
      }
    }
    return $texte;
  }
}

//
// Transforme les entites unicode &#129; dans le charset specifie
//
function unicode2charset($texte, $charset='AUTO') {
  static $CHARSET_REVERSE;
  if ($charset == 'AUTO')
    $charset=lire_meta('charset');

  switch($charset) {
  case 'utf-8':
    return unicode_to_utf_8($texte);
    break;

  default:
    $charset = load_charset($charset);

    // array_flip
    if (!is_array($CHARSET_REVERSE[$charset])) {
      $trans = $GLOBALS['CHARSET'][$charset];
      while (list($chr,$uni) = each($trans))
        $CHARSET_REVERSE[$charset][$uni] = $chr;
    }

    while ($a = strpos(' '.$texte, '&')) {
      $traduit .= substr($texte,0,$a-1);
      $texte = substr($texte,$a-1);
      if (eregi('^&#0*([0-9]+);',$texte,$match) AND ($s = $CHARSET_REVERSE[$charset][$match[1]]))
        $texte = str_replace($match[0], chr($s), $texte);
      // avancer d'un cran
      $traduit .= $texte[0];
      $texte = substr($texte,1);
    }
    return $traduit.$texte;
  }
}


// Importer un texte depuis un charset externe vers le charset du site
// (les caracteres non resolus sont transformes en &#123;)
function importer_charset($texte, $charset = 'AUTO', $forcer = false) {
  return unicode2charset(charset2unicode($texte, $charset, $forcer));
}

// UTF-8
function utf_8_to_unicode($source) {
  static $decrement;
  static $shift;

  // Cf. php.net, par Ronen. Adapte pour compatibilite php3
  if (!is_array($decrement)) {
    // array used to figure what number to decrement from character order value
    // according to number of characters used to map unicode to ascii by utf-8
    $decrement[4] = 240;
    $decrement[3] = 224;
    $decrement[2] = 192;
    $decrement[1] = 0;
    // the number of bits to shift each charNum by
    $shift[1][0] = 0;
    $shift[2][0] = 6;
    $shift[2][1] = 0;
    $shift[3][0] = 12;
    $shift[3][1] = 6;
    $shift[3][2] = 0;
    $shift[4][0] = 18;
    $shift[4][1] = 12;
    $shift[4][2] = 6;
    $shift[4][3] = 0;
  }

  $pos = 0;
  $len = strlen ($source);
  $encodedString = '';
  while ($pos < $len) {
    $char = '';
    $ischar = false;
    $asciiPos = ord (substr ($source, $pos, 1));
    if (($asciiPos >= 240) && ($asciiPos <= 255)) {
      // 4 chars representing one unicode character
      $thisLetter = substr ($source, $pos, 4);
      $pos += 4;
    }
    else if (($asciiPos >= 224) && ($asciiPos <= 239)) {
      // 3 chars representing one unicode character
      $thisLetter = substr ($source, $pos, 3);
      $pos += 3;
    }
    else if (($asciiPos >= 192) && ($asciiPos <= 223)) {
      // 2 chars representing one unicode character
      $thisLetter = substr ($source, $pos, 2);
      $pos += 2;
    }
    else {
      // 1 char (lower ascii)
      $thisLetter = substr ($source, $pos, 1);
      $pos += 1;
      $char = $thisLetter;
      $ischar = true;
    }

    if ($ischar)
      $encodedString .= $char;
    else {  // process the string representing the letter to a unicode entity
      $thisLen = strlen ($thisLetter);
      $thisPos = 0;
      $decimalCode = 0;
      while ($thisPos < $thisLen) {
        $thisCharOrd = ord (substr ($thisLetter, $thisPos, 1));
        if ($thisPos == 0) {
          $charNum = intval ($thisCharOrd - $decrement[$thisLen]);
          $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
        } else {
          $charNum = intval ($thisCharOrd - 128);
          $decimalCode += ($charNum << $shift[$thisLen][$thisPos]);
        }
        $thisPos++;
      }
      $encodedLetter = "&#". ereg_replace('^0+', '', $decimalCode) . ';';
      $encodedString .= $encodedLetter;
    }
  }
  return $encodedString;
}

// UTF-32 : utilise en interne car plus rapide qu'UTF-8
function utf_32_to_unicode($source) {
  $words = unpack("V*", $source);
  if (is_array($words)) {
    reset($words);
    while (list(, $word) = each($words)) {
      if ($word < 128) $texte .= chr($word);
      else if ($word != 65279) $texte .= '&#'.$word.';';
    }
  }
  return $texte;
}


// Ce bloc provient de php.net, auteur Ronen
function caractere_utf_8($num) {
  if($num<128)
    return chr($num);
  if($num<2048)
    return chr(($num>>6)+192).chr(($num&63)+128);
  if($num<32768)
    return chr(($num>>12)+224).chr((($num>>6)&63)+128).chr(($num&63)+128);
  if($num<2097152)
    return chr($num>>18+240).chr((($num>>12)&63)+128).chr(($num>>6)&63+128). chr($num&63+128);
  return '';
}

function unicode_to_utf_8($texte) {
  while (ereg('&#0*([0-9]+);', $texte, $regs) AND !$vu[$regs[1]]) {
    $num = $regs[1];
    $vu[$num] = true;
    $s = caractere_utf_8($num);
    $texte = str_replace($regs[0], $s, $texte);
  }
  return $texte;
}

// convertit les &#264; en \u0108
function unicode_to_javascript($texte) {
  while (ereg('&#0*([0-9]+);', $texte, $regs) AND !$vu[$regs[1]]) {
    $num = $regs[1];
    $vu[$num] = true;
    $s = '\u'.sprintf("%04x", $num);
    $texte = str_replace($regs[0], $s, $texte);
  }
  return $texte;
}

//
// Translitteration charset => ascii (pour l'indexation)
// Attention les caracteres non reconnus sont renvoyes en utf-8
//
function translitteration($texte, $charset='AUTO') {
  static $trans;
  if ($charset == 'AUTO')
    $charset = lire_meta('charset');

  // 1. Passer le charset et les &eacute en utf-8
  $texte = unicode_to_utf_8(html2unicode(charset2unicode($texte, $charset, true)));

  // 2. Translitterer grace a la table predefinie
  if (!$trans) {
    global $CHARSET;
    load_charset('translit');
    reset($CHARSET['translit']);
    while (list($key, $val) = each($CHARSET['translit'])) {
      $trans[caractere_utf_8($key)] = $val;
    }
  }
  if ($GLOBALS['flag_strtr2'])
    $texte = strtr($texte, $trans);
  else {
    reset($trans);
    while (list($from, $to) = each($trans)) {
      $texte = str_replace($from, $to, $texte);
    }
  }

/*
  // Le probleme d'iconv c'est qu'il risque de nous renvoyer des ? alors qu'on
  // prefere garder l'utf-8 pour que la chaine soit indexable.
  // 3. Translitterer grace a iconv
  if ($GLOBALS['flag_iconv'] && ereg('&#0*([0-9]+);', $texte)) {
    $texte = iconv('utf-8', 'ascii//translit', $texte);
  }
*/

  return $texte;
}

// Initialisation
$GLOBALS['CHARSET'] = Array();


?>
