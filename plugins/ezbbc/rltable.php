<?php
define('PUN_ROOT', '../../');
require PUN_ROOT.'include/common.php';
// Retrieving style folder
$config_content = trim(file_get_contents(PUN_ROOT.'plugins/ezbbc/config.php'));
$config_item = explode(";", $config_content);
$ezbbc_style_folder = $config_item[2];
// Getting the textarea name from get string
$textarea_name = pun_htmlspecialchars($_GET['textarea_name']);
// Language file load
$ezbbc_language_folder = (file_exists(PUN_ROOT.'plugins/ezbbc/lang/'.$pun_user['language'].'/ezbbc_plugin.php')) ? $pun_user['language'] : 'English';    
require PUN_ROOT.'plugins/ezbbc/lang/'.$ezbbc_language_folder.'/ezbbc_plugin.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title><?php echo $lang_ezbbc['EZBBC RLTable'] ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="<?php echo PUN_ROOT.'style/'.$pun_user['style'].'.css' ?>" />
  <link rel="stylesheet" type="text/css" href="<?php echo PUN_ROOT.'plugins/ezbbc/style/'.$ezbbc_style_folder.'/ezbbc.css' ?>" />
  <script type="text/javascript">
  /* <![CDATA[ */
  // Function to retrieve the selection in opener and add it to the right field    
  function getSelection() {
    var field  = window.opener.document.getElementsByName('<?php echo $textarea_name ?>')[0]; 
    var scroll = field.scrollTop;
    field.focus();
    /* get the selection */
    if (window.ActiveXObject) { //For IE
      var textRange = window.opener.document.selection.createRange();
      var currentSelection = textRange.text;
    } else { //For other browsers
      var startSelection   = field.value.substring(0, field.selectionStart);
      var currentSelection = field.value.substring(field.selectionStart, field.selectionEnd);
      var endSelection     = field.value.substring(field.selectionEnd);
    }
    /* Add the selection to the label field */
    document.rltableform.rltable.value = currentSelection;
    (currentSelection == '') ? document.rltableform.rltable.focus() : document.rltableform.param.focus();
  }
  // Function to insert The rltabletags and selection in opener Window
  function insertRltableTag() {
    var field  = window.opener.document.getElementsByName('<?php echo $textarea_name ?>')[0]; 
    var scroll = field.scrollTop;
    field.focus();
  
    /* === Part 1: get the selection === */
    if (window.ActiveXObject) { //For IE
      var textRange = window.opener.document.selection.createRange();
      var currentSelection = textRange.text;
    } else { //For other browsers
      var startSelection   = field.value.substring(0, field.selectionStart);
      var currentSelection = field.value.substring(field.selectionStart, field.selectionEnd);
      var endSelection     = field.value.substring(field.selectionEnd);
    }
  
    /* === Part 2: creating tagged element === */
    var startTag = endTag = '';
    var param = document.rltableform.param.value;
    var rltable = document.rltableform.rltable.value;
    if (param != '') { //A param has been entered
      if (rltable != '') {
	startTag = '[rltable=' + param + ']\n';
	currentSelection = rltable;
	endTag = '\n[/rltable]';
      } else {
	alert("<?php echo $lang_ezbbc['Ask quotation'] ?>");
	document.rltableform.rltable.focus();
	return false;
      }
    } else { //No param entered
      alert("<?php echo $lang_ezbbc['Ask rltable param'] ?>");
      document.rltableform.rltable.focus();
      return false;
    }
  
  
    /* === Part 3: adding what was produced to the opener === */
    if (window.ActiveXObject) { //For IE
      textRange.text = startTag + currentSelection + endTag;
      textRange.moveStart('character', -endTag.length - currentSelection.length);
      textRange.moveEnd('character', -endTag.length);
      textRange.select();     
    } else { //For other browsers
      field.value = startSelection + startTag + currentSelection + endTag + endSelection;
      field.focus();
      field.setSelectionRange(startSelection.length + startTag.length, startSelection.length + startTag.length + currentSelection.length);
    } 
  
    field.scrollTop = scroll;
    self.close();		
  }

  // Remplacer les tabs par des pipes
  function tabsToPipes() {
    var table = document.rltableform.rltable;
    var nval = table.value.
      replace(/\n\t+\n/g, "\n\n").
      replace(/\t/g, "  |  ");
    table.value = nval;
  }

/* ]]> */
</script>
</head>
<body onload="getSelection()">
  <div class="pun">
  <div class="punwrap">
  <div id="brdmain">
  <div id="ezbbcrltable">
  <form name="rltableform">
  <p>
  <?php echo $lang_ezbbc['Ask rltable param'] ?><br />
  <input type="text" name="param"  size="15" />
  </p>
  <p>
  <?php echo $lang_ezbbc['Ask rltable'] ?><br />
  <textarea name="rltable" cols="80" rows="16" onpaste="tabsToPipesHandler()"></textarea>
    <br/>
    <a href="#" onclick="tabsToPipes()" />Conversion TAB → |</a>
  </p>
  <p class="buttons">
    <input type="button" value="<?php echo $lang_ezbbc['OK'] ?>" onclick="insertRltableTag()" /> <input type="button" value="<?php echo $lang_ezbbc['Cancel'] ?>" onclick="self.close()" /></p>
  </form>
  </div>
  </div>
  </div>
  </div>
  </body>
  </html>
