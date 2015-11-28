<?php

/**
 * Copyright (C) 2008-2012 FluxBB
 * based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 */
 
/** MODIF RL : fichier créé pour permettre de lister les modifications récentes
 */

define('PUN_ROOT', dirname(__FILE__).'/');
define('PUN_ACTIVE_PAGE', 'edited');
require PUN_ROOT.'include/common.php';
require PUN_ROOT.'lang/'.$pun_user['language'].'/index.php';

if ($pun_config['o_feed_type'] == '1')
        $page_head = array('feed' => '<link rel="alternate" type="application/rss+xml" href="extern.php?action=feed&amp;type=rss" title="'.$lang_common['RSS active topics feed'].'" />');
else if ($pun_config['o_feed_type'] == '2')
        $page_head = array('feed' => '<link rel="alternate" type="application/atom+xml" href="extern.php?action=feed&amp;type=atom" title="'.$lang_common['Atom active topics feed'].'" />');

$page_title = array( pun_htmlspecialchars( 'Messages récemment édités' ), $pun_config['o_board_title'] );
require PUN_ROOT.'header.php';

if (!$pun_user['is_admmod'])
	message($lang_common['No permission']);

if ( $_POST['submit'] ) {

    $posts = array();
    $edited = 30 * 86400;
    $delay = 0;

    if ( isset( $_POST['edited'] ) && $_POST['edited'] != '' )
        $edited = time() - ( $_POST['edited'] * 86400 );
    if ( isset( $_POST['delay'] ) && $_POST['delay'] != '' )
        $delay = ( $_POST['delay'] * 86400 );

    $q = 'SELECT * FROM pun_posts WHERE edited > '.$edited.' AND edited_by != "NULL" ORDER BY edited';

    $result = $db->query( $q ) or error( 'Impossible de retrouver la liste des forums !', __FILE__, __LINE__, $db->error() );
    while( $r = $db->fetch_assoc( $result ) ) {

        if ( ( $delay > 0 ) && ( ( $r['edited'] - $r['posted'] ) < $delay ) ) {
            continue;
        }
        else {
            $message = implode( ' ', array_slice( explode( ' ', $r['message'] ), 0, 10 ) )."…";
            $posts[] = array( 'id' => $r['id'],
                              'message' => $message,
                              'poster' => $r['poster'],
                              'poster_id' => $r['poster_id'],
                              'posted' => $r['posted'],
                              'edited' => $r['edited'],
                              'edited_by' => $r['edited_by'] );
        }
    }

    krsort( $posts );

    $total = count( $posts );
    $s = ( $total > 1 ? 's' : '' );
}
?>
                <div id="searchform" class="blockform">
                  <h2>Éditions récentes</h2>
                  <div class="box">
                    <form id="search" method="post" action="admin_editing.php">
                      <div class="inform">
                        <fieldset>
                          <legend>Choisissez vos critères de recherche</legend>
                          <div class="infldset">
                            <label for="edited">Messages édités les <input id="edited" name="edited" type="text" value="<?php echo $_POST['edited'] != '' ? $_POST['edited'] : '' ; ?>" size="6" style="border:1px solid #ccc;" /> derniers jours.</label>
                            <label for="delay">Edition au moins <input id="delay" name="delay" type="text" value="<?php echo $_POST['delay'] != '' ? $_POST['delay'] : '' ; ?>" size="6" style="border:1px solid #ccc;" /> jours après créations.</label>
                          </div>
                          <input id="submit" name="submit" type="submit" value="Envoyer" />
                        </fieldset>
                      </div>
                    </form>
                  </div>
                </div>
<?php if ( $total > 0 ) { ?>
                <div id="posts" class="blocktable">
                  <div class="box">
                    <h2><span><?php echo "$total Message$s édité$s depuis ".$_POST['edited']." jours, édition ".$_POST['delay']." jours après création"; ?></span></h2>
                    <div class="inbox">
                      <table>
                        <thead>
                          <tr>
                            <th style="width:75px;">ID</th>
                            <th style="width:150px;">Auteur</th>
                            <th style="width:200px;">Date</th>
                            <th style="width:200px;">Édité le </th>
                            <th>Aperçu</th>
                          </tr>
                        </thead>
                        <tbody>
<?php foreach ( $posts as $post ) { ?>
                          <tr id="post_<?php echo $post['id']; ?>" class="post">
                            <td style="width:75px;"><a class="id" href="<?php echo $pun_config['o_base_url']."/viewtopic.php?pid=".$post['id']."#p".$post['id']; ?>">#<?php echo $post['id']; ?></a></td>
                            <td style="width:150px;"><a class="poster" href="<?php echo $pun_config['o_base_url']."/profile.php?id=".$post['poster_id']."#p".$post['id']; ?>"><?php echo $post['poster']; ?></a></td>
                            <td style="width:200px;"><?php echo date( 'j F Y \à H\:i', $post['posted'] ); ?></td>
                            <td style="width:200px;"><?php echo date( 'j F Y \à H\:i', $post['edited'] ); ?></td>
                            <td><?php echo $post['message']; ?></td>
                          </tr>
<?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
<?php
}

$footer_style = 'index';
require PUN_ROOT.'footer.php';