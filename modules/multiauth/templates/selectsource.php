<?php
$this->data['header'] = $this->t('{multiauth:multiauth:select_source_header}');

$this->includeAtTemplateBase('includes/header.php');
?>

<div class="wrapper">
  	<div class="logo">
  		<img src="/simplesaml/resources/img/logo.png">
  	</div>
 
  	<div class="social-connect">
  		<h2><?php echo $this->t('{multiauth:multiauth:select_source_header}'); ?></h2>
  		<div class="social-buttons">
  		<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="get">
<input type="hidden" name="AuthState" value="<?php echo htmlspecialchars($this->data['authstate']); ?>" />
<ul>
<?php
foreach($this->data['sources'] as $source) {
	echo '<li class="' . htmlspecialchars($source['css_class']) . ' authsource">';
	if ($source['source'] === $this->data['preferred']) {
		$autofocus = ' autofocus="autofocus"';
	} else {
		$autofocus = '';
	}
	$name = 'src-' . base64_encode($source['source']);
	echo '<input type="submit" name="' . htmlspecialchars($name) . '"' . $autofocus . ' ' .
		'id="button-' . htmlspecialchars($source['source']) . '" ' .
		'value="' . htmlspecialchars($this->t($source['text'])) . '" />';
	echo '</li>';
}
?>
</ul>
</form>
</div>
  		<p><?php echo $this->t('{multiauth:multiauth:select_source_text}'); ?></p>
  	</div>

  	<div class="ssp-logo">
  		<img src="/simplesaml/resources/img/ssp-logo.png">
  	</div>
<?php 
	
	$includeLanguageBar = TRUE;
	if (!empty($_POST)) 
		$includeLanguageBar = FALSE;
	if (isset($this->data['hideLanguageBar']) && $this->data['hideLanguageBar'] === TRUE) 
		$includeLanguageBar = FALSE;
	
	if ($includeLanguageBar) {
		
		
		echo '<div id="languagebar">';
		$languages = $this->getLanguageList();
		$langnames = array(
				
					'en' => 'English',
					'sv' => 'Svenska', // Swedish
					'fi' => 'Suomeksi', // Finnish
					
		);
		
		$textarray = array();
		foreach ($languages AS $lang => $current) {
			$lang = strtolower($lang);
			if ($current) {
				$textarray[] = $langnames[$lang];
			} else {
				$textarray[] = '<a href="' . htmlspecialchars(SimpleSAML_Utilities::addURLparameter(SimpleSAML_Utilities::selfURL(), array($this->languageParameterName => $lang))) . '">' .
					$langnames[$lang] . '</a>';
			}
		}
		echo join('', $textarray);
		echo '</div>';

	}



	?>

</div>
<?php $this->includeAtTemplateBase('includes/footer.php'); ?>
